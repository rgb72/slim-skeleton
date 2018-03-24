<?php

namespace Bootstrap\Containers;

class Database {

    public function __invoke($container) {
        $setting = require_once __DIR__.'/../../config/database.php';
        $capsule = new \Illuminate\Database\Capsule\Manager;

        $capsule->addConnection($setting);
        $capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $container['database'] = function ($container) use ($capsule) {
            return $capsule;
        };
    }

}
