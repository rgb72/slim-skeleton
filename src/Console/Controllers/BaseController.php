<?php

namespace App\Console\Controllers;

use Wujunze\Colors;

class BaseController {

    public function __construct() {
        $this->color = new Colors;
    }

    public function write() {
        echo $this->color->getColoredString(...func_get_args()) . PHP_EOL;
    }

    public function notice($message) {
        echo $this->color->notice($message) . PHP_EOL;
    }
    public function error($message) {
        echo $this->color->error($message) . PHP_EOL;
    }
    public function warn($message) {
        echo $this->color->warn($message) . PHP_EOL;
    }
    public function success($message) {
        echo $this->color->success($message) . PHP_EOL;
    }

}
