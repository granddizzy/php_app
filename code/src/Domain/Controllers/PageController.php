<?php

namespace GB\App\Domain\Controllers;

use GB\App\Application\Render;

class PageController {
  public function actionIndex(): string {
    $render = new Render();
    $currentTime = new \DateTime();
    return $render->renderPage('page-index.twig',
      ['title' => 'Главная страница',
      'currentTime' => $currentTime->format('H:i:s')]);
  }

  public function actionError404(): string {
    $render = new Render();
    header("HTTP/1.1 404 Not Found");
    return $render->renderPage('page-error404.twig');
  }

  public function actionError(string $errorMessage): string {
    $render = new Render();
    return $render->renderPage('page-exception.twig',
      ['errorMessage' => $errorMessage]);
  }
}