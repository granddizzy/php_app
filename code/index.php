<?php

require 'vendor/autoload.php';

use GB\App\Application;

$app = new Application();
echo $app->run();