<?php

namespace App\Wcms\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Helpers\JsonWebToken\JsonWebToken;
use App\Models\WcmsUser;
use App\Models\WcmsUserKey;
use Respect\Validation\Validator;

use Carbon\Carbon;

use Exception;
use UnexpectedValueException;
use Respect\Validation\Exceptions\NestedValidationException;

class AuthController {

    public function login(Request $request, Response $response, Array $args) {
        $params = $request->getParams();

        $validator = Validator::attribute('username', Validator::stringType()->notEmpty())
                        ->attribute('password', Validator::stringType()->notEmpty());

        try {
            $validator->assert((object)$params);
        } catch(NestedValidationException $e) {
            return $response->withJson(['message' => $e->getMessages()], 400);
        }

        $user = WcmsUser::active()->whereUsername($params['username'])->first();

        if(is_null($user) || sha1(trim($params['password'])) !== $user->password)
            return $response->withJson(['message' => 'Invalid username or password'], 403);

        $jwt = new JsonWebToken(getenv('JWT_WCMS_KEY'));

        $userKey = new WcmsUserKey;
        $userKey->key = \App\Helpers\Str::random();
        $userKey->expired_at = $jwt->getExpiredAt();

        $user->keys()->save($userKey);

        $jwt->user = [
            'id' => $user->id,
            'username' => $user->username
        ];

        $jwt->role = [
            'id' => $user->role->id,
            'name' => $user->role->name
        ];

        $jwt->key  = $userKey->key;

        $token = $jwt->encode();

        return $response->withJson(['token' => $token]);
    }

    public function token(Request $request, Response $response, Array $args) {
        $token = $request->getAttribute('token');

        $user = $token->user;
        $key = $token->key;

        $jwt = new JsonWebToken(getenv('JWT_WCMS_KEY'));

        $user = WcmsUser::active()->whereUsername($user->username)->first();

        $userKey = $user->keys->where('key', $key)->where('expired_at', '>=', Carbon::now())->first();

        if(is_null($userKey)) return $response->withJson(['message' => 'key not found.'], 403);

        $userKey->expired_at = $jwt->getExpiredAt();
        $userKey->save();

        $jwt->user = [
            'id' => $user->id,
            'username' => $user->username
        ];

        $jwt->role = [
            'id' => $user->role->id,
            'name' => $user->role->name
        ];

        $jwt->key = $key;

        $token = $jwt->encode();

        return $response->withJson(['token' => $token]);
    }

    public function logout(Request $request, Response $response, Array $args) {
        $token = $request->getAttribute('token');
        $user = WcmsUser::active()->whereUsername($token->user->username)->first();

        $key = $user->keys->where('key', $args['key'])->first();
        $key->delete();

        return $response->withStatus(204);
    }

}
