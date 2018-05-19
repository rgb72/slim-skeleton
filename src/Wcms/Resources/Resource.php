<?php

namespace App\Wcms\Resources;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\Model;

use \Exception;
use \UnexpectedValueException;

class Resource {

    protected $container;
    protected $endpoint;

    public function __construct($container) {
        $this->container = $container;
        $this->endpoint = new EndpointPrefix;
    }

    public function __invoke(Request $request, Response $response, Array $args) {
        try {
            $model = $this->getModelFromResouces($args['resource']);
        } catch(UnexpectedValueException $e) {
            return $response->withStatus(404);
        }

        $method = $request->getMethod();
        $params = $request->getParams();

        $handler = new ResourceHandler;

        switch (strtoupper($method)) {
            case 'GET':
                $path = (string)$request->getUri()->withQuery('');
                $handler->get($model, $params, $path);
                break;

            case 'POST':
                $handler->create($model, $params);
                break;

            case 'PUT':
                $handler->updateOrCreate($model, $params);
                break;

            case 'PATCH':
                $handler->update($model, $params);
                break;

            case 'DELETE':
                $handler->delete($model);
                break;

            case 'OPTIONS':
                $response = $handler->option($response);
                break;

            default:
                $handler->methodNotAllow();
                break;
        }

        return $response->withJson($handler->getResponseData(), $handler->getResponseCode());
    }

    protected function getModelFromResouces($resources) {
        $resources = explode('/', $resources);

        if(!($class_name = $this->endpoint->getClassName(reset($resources))))
            throw new UnexpectedValueException('Not found');

        if(!$this->isEloquent($class_name))
            throw new UnexpectedValueException('Not found');

        $model = new $class_name;
        array_shift($resources);

        if(empty($resources)) return $model;

        foreach ($resources as $key => $resource) {
            switch (true) {
                case $model instanceOf Model:
                    if(!$model->exists) {
                        $model = $model->find($resource);
                    } else {
                        $resource = str_replace('-', '_', $resource);
                        if($this->hasRelation($model, $resource)) $model = $model->{$resource}();
                        else throw new UnexpectedValueException('Not found');
                    }
                    break;
                case $model instanceOf Relations\HasMany:
                case $model instanceOf Relations\BelongsToMany:
                    $model = $model->findOrNew($resource);
                    break;
                case $model instanceOf Relations\HasOne:
                case $model instanceOf Relations\BelongsTo:
                    $resource = str_replace('-', '_', $resource);
                    if($this->hasRelation($model, $resource)) $model = $model->first()->{$resource}();
                    else throw new UnexpectedValueException('Not found');
                    break;
            }
        }

        switch (true) {
            case $model instanceOf Model:
                if(!$model->exists) throw new UnexpectedValueException('Not found');
                break;
            case $model instanceOf Relations\HasMany:
            case $model instanceOf Relations\BelongsToMany:
                break;
            case $model instanceOf Relations\HasOne:
            case $model instanceOf Relations\BelongsTo:
                break;
        }

        if(is_null($model)) throw new UnexpectedValueException('Not found');

        return $model;
    }

    protected function isEloquent($class_name) {
        try {
            return new $class_name instanceOf Model;
        } catch (Exception $e) {
            return false;
        }
    }

}
