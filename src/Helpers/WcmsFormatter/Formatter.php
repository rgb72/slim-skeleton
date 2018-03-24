<?php

namespace App\Helpers\WcmsFormatter;

class Formatter {

    protected static $per_page = 20;

    public static function get($model, Array $params = [], callable $mapping = null, callable $filter = null) {
        $object = new $model;

        if(isset($params['q'])) $object = static::withCondition($params['q'], $object);

        try {
            if(!isset($params['total_with_condition']) || $params['total_with_condition'] === true) $all = $object->get();
            else $all = call_user_func([$model, 'all']);
        } catch (\Exception $exception) {
            throw $exception;
        }

        if(!is_null($mapping)) $all = $all->map($mapping);
        if(!is_null($filter)) $all = $all->filter($filter);

        $total = $all->count();
        if(!isset($params['offset'])) $params['offset'] = 0;
        if(!isset($params['limit']) && static::isPagination($params)) $params['limit'] = static::$per_page;
        if(!isset($params['limit']) && !static::isPagination($params)) $params['limit'] = $total - $params['offset'];

        if(isset($params['sort'])) {
            if(!is_array($params['sort'])) $sort = explode(',', $params['sort']);
            $sort = array_map(function($item) {return trim($item); }, $sort);
            foreach ($sort as $field) {
                switch (substr($field, 0, 1)) {
                    case '-':
                        $field = substr($field, 1);
                        $direction = 'DESC';
                        break;
                    case '+':
                        $field = substr($field, 1);
                        $direction = 'ASC';
                        break;

                    default:
                        $direction = 'ASC';
                        break;
                }

                if(preg_match('/\./', $field)) {
                    list($relation, $sub_field) = explode('.', $field, 2);
                    $object = $object->load([$relation => function($query) use ($sub_field, $direction) {
                        $query->orderBy($sub_field, $direction);
                    }]);
                } else {
                    $object = $object->orderBy($field, $direction);
                }
            }
        }

        try {
            $object = $object->skip($params['offset'])->take($params['limit'])->get();
        } catch (\Exception $exception) {
            throw $exception;
        }

        if(!is_null($mapping)) $object = $object->map($mapping);
        if(!is_null($filter)) $object = $object->filter($filter);

        if(isset($params['fields'])) {
            if(!is_array($params['fields'])) $fields = explode(',', $params['fields']);
            else $fields = $params['fields'];

            $fields = array_map(function($item) {return trim($item); }, $fields);
            $fields = array_filter($fields);
            if(!empty($fields)) $object = $object->map(static::withFields($fields));
        }

        if(isset($params['includes'])) {
            if(!is_array($params['includes'])) $includes = explode(',', $params['includes']);
            else $includes = $params['includes'];

            array_map(function($item) {return trim($item); }, $includes);

            $object = $object->map(static::withIncludes($includes));
        }

        if(!static::isPagination($params)) {
            $response = $object;
        } else {
            $response = [
                'pagination' => [
                    'current_page' => floor($params['offset'] / $params['limit']) + 1,
                    'last_page' => ceil($total/$params['limit'])
                ],
                'total' => $total,
                'data' => $object
            ];
        }

        return $response;
    }

    public static function find($model, $id, Array $params = [], callable $mapping = null) {
        $object = call_user_func_array([$model, 'find'], [$id]);
        if(is_null($object)) return null;

        if(isset($params['includes'])) {
            if(!is_array($params['includes'])) $includes = explode(',', $params['includes']);
            else $includes = $params['includes'];

            array_map(function($item) {return trim($item); }, $includes);

            $object = $object->load($includes);
        }

        return $object;
    }

    protected static function withCondition($conditions, $object) {
        foreach ($conditions as $field => $condition) {
            if (!is_array($condition)) {
                switch ($field) {
                    case 'has':
                        $object = $object->has($condition);
                        break;
                    case 'not_has':
                        $object = $object->doesntHave($condition);
                        break;
                    case 'keyword':
                        try {
                            $object = $object->keyword($condition);
                        } catch (\Exception $exception) {

                        }
                        break;
                    default:
                        $object = $object->where($field, $condition);
                        break;
                }

                continue;
            }

            $object = static::conditionBuilder($field, $condition, $object);
        }

        return $object;
    }

    protected static function conditionBuilder($field, Array $condition, $object) {
        if(preg_match('/\./', $field)) {
            list($relation, $sub_field) = explode('.', $field, 2);
            $object = $object->whereHas($relation, function($query) use ($sub_field, $condition) {
                $query = static::conditionBuilder($sub_field, $condition, $query);
            });

            return $object;
        }

        foreach ($condition as $operation => $value) {
            $syntax = 'where';
            switch ($operation) {
                case 'eq':
                    $operator = '=';
                    break;
                case 'not_eq':
                    $operator = '!=';
                    break;
                case 'gt':
                    $operator = '>';
                    break;
                case 'gte':
                    $operator = '>=';
                    break;
                case 'lt':
                    $operator = '<';
                    break;
                case 'lte':
                    $operator = '<=';
                    break;
                case 'like':
                    $operator = 'like';
                    break;
                case 'not_like':
                    $operator = 'not_like';
                    break;
                case 'in':
                    $operator = null;
                    $syntax = 'whereIn';
                    if(!is_array($value)) $value = [$value];
                    break;
                case 'not_in':
                    $operator = null;
                    $syntax = 'whereNotIn';
                    if(!is_array($value)) $value = [$value];
                    break;
                default:
                    $operator = '=';
                    break;
            }

            if(is_null($value)) {
                switch ($operation) {
                    case 'eq':
                        $syntax = 'whereNull';
                        $operator = null;
                        break;
                    case 'not_eq':
                        $syntax = 'whereNotNull';
                        $operator = null;
                        break;
                }
            }

            $object = call_user_func_array([$object, $syntax], array_filter([$field, $operator, $value]));
        }

        return $object;
    }

    protected static function withIncludes($includes) {
        return function($item) use ($includes) {
            try {
                $item = $item->load($includes);
            } catch (\Exception $e) {
                return $item;
            }
            return $item;
        };
    }

    protected static function withFields($fields) {
        return function($item) use ($fields) {
            $attributes = $item->getAttributes();
            $keys = array_keys($attributes);
            foreach ($keys as $key) {
                if(!in_array($key, $fields)) unset($item->{$key});
            }

            return $item;
        };
    }

    protected static function isPagination(Array $params = []) {
        return !isset($params['options']) || !isset($params['options']['pagination']) || static::stringToBool($params['options']['pagination']) !== false;
    }

    private static function stringToBool($string) {
        if(is_bool($string)) return $string;
        return $string === 'true' || $string == 1;
    }

}
