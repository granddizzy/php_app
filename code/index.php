<?php

$memory_start = memory_get_usage();

require_once(__DIR__ . '/vendor/autoload.php');

use GB\App\Application\Application;
use GB\App\Application\Render;

try {
  $app = new Application();
  echo $app->run();
} catch (Exception $e) {
  echo Render::renderExceptionPage($e);
}

$memory_end = memory_get_usage();
echo "Память: " . $memory_end - $memory_start;