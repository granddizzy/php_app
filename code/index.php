<?php

require_once (__DIR__ . '/vendor/autoload.php');

use GB\App\Application;

$app = new Application();
echo $app->run();