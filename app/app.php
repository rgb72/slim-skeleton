<?php

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/essential.php';

$env = new \Dotenv\Dotenv(__DIR__.'/..');
$env->load();

if(getenv('TIMEZONE')) date_default_timezone_set(getenv('TIMEZONE'));
