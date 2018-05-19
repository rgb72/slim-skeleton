<?php

namespace App\Wcms\resources;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

use UnexpectedValueException;

class ResourceHandler {

    private $per_page = 20;
    protected $response_data = [];
    protected $response_code = 200;

    public function get($model, $params, $path = null) {
        $data = $this->getDataFromModel($model, $params, $path);
        if($data instanceOf Builder) $data = $data->get();

        $this->response_data = $data->toArray();
        $this->response_code = 200;

        return $data;
    }

    public function create($model, $params) {
        if($model instanceOf Model && $model->exists) {
            $this->response_data = ['message' => 'Method should be PUT, PATCH'];
            $this->response_code = 400;
            return;
        }

        $fillable = array_filter($params, function($item) use ($model) {
            return $model->isFillable($item);
        }, ARRAY_FILTER_USE_KEY);

        try {
            $model = $model->create($fillable);

            $this->response_data = $model->toArray();
            $this->response_code = 201;
            return $model;
        } catch(QueryException $e) {
            $this->response_data = ['message' => $e->getMessage()];
            $this->response_code = 400;

            return;
        }
    }

    public function update($model, $params) {
        if($model instanceOf Model === false || !$model->exists) {
            $this->response_data = ['message' => 'Method should be POST, PUT'];
            $this->response_code = 400;
            return;
        }

        $fillable = array_filter($params, function($item) use ($model) {
            return $model->isFillable($item);
        }, ARRAY_FILTER_USE_KEY);

        try {
            $model = $model->update($fillable);

            $this->response_data = $model->toArray();
            $this->response_code = 200;

            return $model;
        } catch(QueryException $e) {
            $this->response_data = ['message' => $e->getMessage()];
            $this->response_code = 400;

            return;
        }
    }

    public function updateOrCreate($model, $params) {
        $self = $this;
        $updateOrCreate = function($model, $params) use ($self, &$updateOrCreate) {
            if($model instanceOf Model) $key = $model->getKeyName();
            else $key = $model->getModel()->getKeyName();

            if(!$self->isAssoc($params)) {
                $model->whereNotIn($key, array_filter(array_map(function($item) use ($key) {
                    return isset($item[$key]) ? $item[$key] : null;
                }, $params)))->delete();

                $relation_model = $model->getModel();
                $params = array_filter($params);

                $save_data = array_map(function($params) use ($relation_model, $key) {
                    $id = isset($params[$key]) ? $params[$key] : null;

                    $params = array_filter($params, function($item) use ($relation_model) {
                        return $relation_model->isFillable($item);
                    }, ARRAY_FILTER_USE_KEY);

                    $model = $relation_model->findOrNew($id);

                    $model->fill($params);
                    return $model;
                }, $params);

                $model->saveMany($save_data);
            } else {
                $attributes = [];
                if(isset($params[$key])) $attributes[$key] = $params[$key];

                $fillable = array_filter($params, function($item) use ($model) {
                    return $model->isFillable($item);
                }, ARRAY_FILTER_USE_KEY);

                $model = $model->updateOrCreate($attributes, $fillable);
            }

            $relationships = array_filter($params, function($item) use ($model) {
                return method_exists($model, $item);
            }, ARRAY_FILTER_USE_KEY);

            foreach ($relationships as $relationship => $value) {
                $updateOrCreate($model->{$relationship}(), $value);
            }

            return $model;
        };

        try {
            $model = $updateOrCreate($model, $params);

            $this->response_data = $model->toArray();
            $this->response_code = 200;

            return $model;
        } catch(QueryException $e) {
            $this->response_data = ['message' => $e->getMessage()];
            $this->response_code = 400;

            return;
        }
    }

    public function delete($model) {
        $data = $this->getDataFromModel($model, [
            'options' => [
                'pagination' => false
            ]
        ]);

        try {
            $data->delete();
            $this->response_code = 204;
            return;
        } catch(QueryException $e) {
            $this->response_data = ['message' => $e->getMessage()];
            $this->response_code = 400;

            return;
        }
    }

    public function option(Response $response) {
        return $response->withHeader('Access-Control-Allow-Origin', getenv('ACCESS_CONTROL_ALLOW_ORIGIN'))
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
    }

    public function getResponseData() {
        return $this->response_data;
    }

    public function getResponseCode() {
        return $this->response_code;
    }

    protected function getModel($class_name, $relationships, $find_or_new = false) {
        $model = new $class_name;
        $key_name = $model->getKeyName();

        foreach ($relationships as $key => $value) {

            if($key === 0) {
                $model = $model->find($value);
                continue;
            }

            if ($model instanceOf Collection) {
                $model = $model->where($key_name, $value)->first();
                continue;
            }

            if($model instanceOf Model) {
                $value = str_replace('-', '_', $value);
                $model = $model->{$value}();
                continue;
            }
        }

        return $model;
    }

    protected function getDataFromModel($model, Array $params = [], $path = null) {
        $is_paginate = $this->isPagination($params);
        $is_collection = false;

        if(isset($params['q'])) $model = $this->conditions($model, $params['q']);
        if(isset($params['includes']) && !empty($params['includes'])) {
            $includes = $params['includes'];
            $model = $this->loadInclude($model, $includes);
        }
        if(isset($params['sorting']) && !empty($params['sorting'])) {
            $model = $this->sorting($model, $params['sorting']);
        }

        switch (true) {
            case $model instanceOf Model:
                if($model->exists) $is_paginate = false;
                else $model = $model->get();
                break;

            case $model instanceOf Relations\HasOne:
            case $model instanceOf Relations\BelongsTo:
                $is_paginate = false;
                $model = $model->first();
                break;

            case $model instanceOf Relations\HasMany:
            case $model instanceOf Relations\BelongsToMany:
                $is_collection = true;
                break;
        }

        if($is_paginate) $data = $this->paginate($model, $params, $path);
        else $data = $model;

        return $data;
    }

    protected function loadInclude($model, $includes) {
        if(!is_array($includes)) $includes = explode(',', $includes);
        $includes = array_map(function($item) {
            return str_replace('-', '_', $item);
        }, $includes);

        switch (true) {
            case $model instanceOf Model:
                if($model->exists) return $model->load($includes);
                else return $model->with($includes);

            case $model instanceOf Builder:
                return $model->with($includes);

            case $model instanceOf Relations\HasOne:
            case $model instanceOf Relations\BelongsTo:
                return $model->load($includes);

            case $model instanceOf Relations\HasMany:
            case $model instanceOf Relations\BelongsToMany:
                return $model->with($includes);
        }
    }

    protected function conditions($model, $query) {
        $camelize = function($input, $separator = '_', $uc_first = true) {
            $str = str_replace($separator, '', ucwords($input, $separator));

            if(!$uc_first) $str = lcfirst($str);
            return $str;
        };

        foreach ($query as $field => $value) {

            if(!$value) continue;
            if(method_exists($model, 'scope'.$camelize($field)) || ($model instanceof Builder && method_exists($model->getModel(), 'scope'.$camelize($field)))) {
                $model = $model->{$camelize($field, '_', false)}($value);
            } else {
                $model = $model->where($field, $value);
            }
        }

        return $model;
    }

    protected function sorting($model, $sort) {
        if(!is_array($sort)) $sort = explode(',', $sort);
        foreach ($sort as $field) {
            $direction = substr($field, 0, 1) === '-' ? 'desc' : 'asc';
            if(in_array(substr($field, 0, 1), ['+', '-'])) $field = substr($field, 1);
            $model = $model->orderBy($field, $direction);
        }

        return $model;
    }

    protected function isPagination(Array $params) {
        return !isset($params['options']) || !isset($params['options']['pagination']) || $params['options']['pagination'] == 'true';
    }

    protected function hasRelation(Model $model, $relation) {
        return method_exists($model, $relation);
    }

    protected function paginate($model, $params, $path = null) {
        $per_page = isset($params['per_page']) ? (int) $params['per_page'] : $this->per_page;
        $page     = isset($params['page']) && (int) $params['page'] > 0 ? (int) $params['page'] : 1;
        $fields   = isset($params['fields']) ? $params['fields'] : ['*'];
        if(!is_array($fields)) $fields = array_map('trim', explode(',', $fields));

        if($model instanceOf Collection) {
            $data = new LengthAwarePaginator(
                $model->forPage($page, $per_page),
                $model->count(),
                $per_page, $page,
                [
                    'path' => $path,
                    'pageName' => 'page'
                ]);
        } else {
            $data = $model->paginate($per_page, $fields, 'page', $page);
            $data->setPath($path);
        }

        return $data;
    }

    private function isAssoc(Array $array) {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

}
