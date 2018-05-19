<?php

namespace App\Wcms\Middlewares;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Helpers\JsonWebToken\JsonWebToken;
use App\Models\WcmsUser;

use Carbon\Carbon;

class AuthMiddleware {

    public function __invoke(Request $request, Response $response, callable $next) {
        $authorization = $request->getHeader('authorization');

        if(empty($authorization)) return $response->withStatus(401);

        list($token) = sscanf($authorization[0], 'Bearer %s');

        $jwt = new JsonWebToken(getenv('JWT_WCMS_KEY'));
        $token = $jwt->decode($token);

        if(!$token)
            return $this->unauthorize($response, $jwt->getErrorMessage());

        if(is_null($token->user))
            return $this->unauthorize($response, 'no user');

        $user = WcmsUser::active()->whereUsername($token->user->username)->first();

        if(is_null($user))
            return $this->unauthorize($response, 'no user');

        $userKey = $user->keys->where('key', $token->key)->first();

        if(is_null($userKey))
            return $this->unauthorize($response, 'no key');

        $expired_at = Carbon::createFromTimestamp($token->exp);
        $now = Carbon::now();

        if($userKey->expired_at->lt($now) || $expired_at->lt($now)) {
            $userKey->delete();
            return $this->unauthorize($response, 'key is expired');
        }

        $userKey->expired_at = $jwt->getExpiredAt();
        $userKey->save();

        $request = $request->withAttribute('token', $token)->withAttribute('user', $user);

        return $next($request, $response);
    }

    public function unauthorize(Response $response, $message) {
        return $response->withJson(['message' => $message], 401);
    }

}
