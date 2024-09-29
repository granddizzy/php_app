<?php

require_once(__DIR__ . '/vendor/autoload.php');

use GB\App\Application\Application;
use GB\App\Application\Render;

try {
  $app = new Application();
  echo $app->run();
} catch (Exception $e) {
  $render = new Render();
  echo  $render->renderExceptionPage($e);
}