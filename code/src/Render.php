<?php

namespace GB\App;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Render
{
  private $viewFolder = '/src/Views';
  private FilesystemLoader $loader;

  private Environment $environment;


  public function __construct()
  {
    $this->loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . $this->viewFolder);

    $this->environment = new Environment($this->loader, ['cache' => $_SERVER['DOCUMENT_ROOT'] . '/cache']);
  }

  public function renderPage(string $contentTemplateName = "page-index.tpl", array $templateVariables = []): string
  {
    $template = $this->environment->load('main.tpl');

    $templateVariables['content_template_name'] = $contentTemplateName;

    return $template->render($templateVariables);
  }
}