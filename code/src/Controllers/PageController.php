<?php

namespace GB\App\Controllers;

use GB\App\Render;

class PageController
{
  public function actionIndex(): string
  {
    $render = new Render();
    return $render->renderPage('page-index.tpl', ['title' => 'Главная страница']);
  }
}