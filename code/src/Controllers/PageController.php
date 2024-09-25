<?php

namespace GB\App\Controllers;

use GB\App\Render;

class PageController {
  public function actionIndex(): string {
    $render = new Render();
    $currentTime = new \DateTime();
    return $render->renderPage('page-index.twig', ['title' => 'Главная страница',
      'currentTime' => $currentTime->format('H:i:s')]);
  }

  public function actionError404(): string {
    $render = new Render();
    header("HTTP/1.0 404 Not Found");
    return $render->renderPage('page-error404.twig', []);
  }
}