<?php

require_once(__DIR__ . '/vendor/autoload.php');

use GB\App\Application\Application;

try {
  $app = new Application();
  echo $app->run();
} catch (Exception $e) {
  echo $e->getMessage();
}