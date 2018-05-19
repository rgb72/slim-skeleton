<?php

namespace App\Helpers\Twig;

use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Symfony\Component\Yaml\Yaml;

class Twig {

    public function __construct($container) {
        $this->container = $container;
    }

    public function staticFunction() {
        $container = $this->container;

        return new Twig_SimpleFunction('static', function($parameter, $lang = null) use ($container) {
            if(!preg_match('/^([a-zA-Z0-9_\-\/]+)\.([a-zA-Z0-9_\-\.]+)$/', $parameter, $match)) return null;

            $file = sprintf('%s/%s.yaml', __DIR__.'/../../../static', $match[1]);
            if(!realpath($file)) return null;

            try {
                $static = function_exists('yaml_parse_file') ?
                            yaml_parse_file($file) :
                            Yaml::parse(file_get_contents($file));
            } catch(\Exception $e) {
                return null;
            }

            $static = new \Adbar\Dot($static);

            $data = $static->get($match[2]);

            if(getenv('IS_MULTIPLE_LANG') !== 'false') {
                if(!is_array($data)) return $data;
                if(is_null($lang)) $lang = $container->lang;
                if(in_array($lang, array_keys($data), true)) return $data[$lang];
                if(isset($data['default'])) return $data['default'];
                $avaliable_langs = array_intersect(array_keys($data), explode(',', getenv('LANG_AVALIABLE')));
                if(!empty($avaliable_langs)) return $data[$avaliable_langs[0]];
            } else {
                return $data;
            }
        });
    }

    public function datetimeFilter() {
        return new Twig_SimpleFilter('datetime', function($datetime, $format, $lang = null) {
            $datetime = new DateTime\DateTime($datetime);
            return $datetime->format($format)->lang($lang)->get();
        });
    }

    public function withUrl() {
        return new Twig_SimpleFilter('with_url', function($string, $url) {
            if(is_null($string)) return null;
            if(filter_var($string, FILTER_VALIDATE_URL)) return $string;

            $string = ltrim($string, '/');
            $string = rtrim($string, '/');
            $url = rtrim($url, '/');

            $url = sprintf('%s/%s', $url, $string);
            return $url;
        });
    }

}
