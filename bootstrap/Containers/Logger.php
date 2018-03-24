<?php

namespace Bootstrap\Containers;

class Logger {

    public function __invoke($container) {
        $container['logger'] = function($container) {
            $setting = require_once __DIR__.'/../../config/log.php';

            $logger       = new \Monolog\Logger('logger');
            $path         = $setting['directory'];
            $rotation_day = $setting['rotation_day'];

            foreach (['info', 'notice', 'warning', 'error', 'critical'] as $type) {
                $filename = sprintf('%s/%s.log', $path, $type);
                $level = constant(sprintf('Logger::%s', strtoupper($type)));

                $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler($filename, $rotation_day, $level));
            }
        };
    }

}
