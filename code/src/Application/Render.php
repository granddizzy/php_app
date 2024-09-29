<?php

namespace GB\App\Application;

use http\Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Render {
  private $viewFolder = '/src/Domain/Views';
  private FilesystemLoader $loader;

  private Environment $environment;


  public function __construct() {
    $this->loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . $this->viewFolder);

    $this->environment = new Environment($this->loader,
    //      ['cache' => $_SERVER['DOCUMENT_ROOT'] . '/cache']
    );
  }

  public function renderPage(string $contentTemplateName = "page-index.twig", array $templateVariables = []): string {
    $baseUrl = Application::$config->get()['app']['BASE_URL'];

    $templateVariables['content_template_name'] = $contentTemplateName;
    $templateVariables['baseUrl'] = $baseUrl;

    return $this->environment->render($contentTemplateName, $templateVariables);
  }

  public function renderExceptionPage(\Exception $e): string {
    return $this->renderPage("page-exception.twig", ["errorMessage" => $e->getMessage()]);
  }
}