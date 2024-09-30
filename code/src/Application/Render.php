<?php

namespace GB\App\Application;

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
    $debug = Application::$config->get()['debug']['DEBUG'];

    $templateVariables['content_template_name'] = $contentTemplateName;
    $templateVariables['baseUrl'] = $baseUrl;
    $templateVariables['debug'] = $debug;

    return $this->environment->render($contentTemplateName, $templateVariables);
  }

  public static function renderExceptionPage(\Exception $e): string {
    $renderer = new self();

    $templateVariables = [
      "errorMessage" => $e->getMessage(),
      "errorFile" => $e->getFile(),
      "errorLine" => $e->getLine(),
      "errorTrace" => $e->getTraceAsString(),
    ];

    return $renderer->renderPage("page-exception.twig", $templateVariables);
  }
}