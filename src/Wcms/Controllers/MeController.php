<?php

namespace App\Wcms\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\WcmsModule;
use App\Models\WcmsDefaultModule;

use Exception;
use UnexpectedValueException;
use Respect\Validation\Exceptions\NestedValidationException;

class MeController {

    public function show(Request $request, Response $response, Array $args) {
        $user = $request->getAttribute('user');

        return $response->withJson($user);
    }

    public function update(Request $request, Response $response, Array $args) {
        $user = $request->getAttribute('user');
        $params = $request->getParams();

        $params = array_filter($params);


        $validator = Validator::attribute('name', Validator::stringType()->notEmpty())
                            ->attribute('email', Validator::email())
                            ->attribute('password', Validator::stringType()->notEmpty(), false);

        try {
            $validator->assert((object)$params);
        } catch (NestedValidationException $e) {
            return $response->withJson(['message' => $e->getMessages()], 400);
        }

        $user->name  = $params['name'];
        $user->email = $params['email'];
        if(isset($params['password'])) $user->password = $params['password'];
        $user->save();

        return $response->withJson($user);
    }

    public function modules(Request $request, Response $response, Array $args) {
        $user = $request->getAttribute('user');

        if(is_null($user->role) || !($permissions = $user->role->permissions)) {
            return $response->withJson(['message' => 'unauthorize'], 401);
        }

        $permission_ids = $permissions->map(function($item) {
            return $item->action_view ? $item->module_id : null;
        })->filter(function($item) {
            return !is_null($item);
        })->toArray();

        $filter_permission = function($item) use ($permissions) {
            $permission = $permissions->where('module_id', $item->id)->first();

            if(is_null($permission)) return false;
            return $permission->action_view;
        };

        $modules = WcmsModule::whereNull('parent_id')
            ->with(['child' => function($query) use ($permission_ids) {
                $query->whereIn('id', $permission_ids);
                $query->orderBy('ordering');
            }])
            ->orderBy('ordering')
            ->get()
            ->filter(function($item) use ($permission_ids) {
                return $item->child->count() || in_array($item->id, $permission_ids);
            })
            ->toArray();

        $modules = array_values($modules);

        return $response->withJson($modules);
    }

    public function moduleInfo(Request $request, Response $response, Array $args) {
        $user = $request->getAttribute('user');
        $name = $args['name'];

        $module = WcmsDefaultModule::where('name', $name)->first();
        if(!is_null($module)) return $module;

        $module = WcmsModule::where('name', $name)->first();
        if(is_null($module)) return $response->withJson(['message' => 'module not found'], 404);

        $permission = $user->role->permissions->where('module_id', $module->id)->first();
        if(is_null($permission)) return $response->withJson(['message' => 'unauthorize'], 401);

        $module->action_view &= $permission->action_view;
        $module->action_create &= $permission->action_create;
        $module->action_update &= $permission->action_update;
        $module->action_delete &= $permission->action_delete;
        $module->action_export &= $permission->action_export;

        return $response->withJson($module);
    }

}
