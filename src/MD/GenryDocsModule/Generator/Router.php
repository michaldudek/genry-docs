<?php
namespace MD\GenryDocsModule\Generator;

use MD\Genry\Routing\Router as GenryRouter;

use MD\Genry\Page;

class Router
{

    protected $router;

    protected $outputDir;

    public function __construct(GenryRouter $router, $outputDir) {
        $this->router = $router;
        $this->outputDir = !empty($outputDir) ? rtrim($outputDir, DS) . DS : '';
    }

    public function generateNamespaceLink($namespace, Page $relativeTo) {
        $path = $this->outputDir . str_replace(NS, DS, $namespace) .'.namespace.html';
        return $this->router->generateLink($path, $relativeTo);
    }

    public function generateClassLink($class, Page $relativeTo) {
        $path = $this->outputDir . str_replace(NS, DS, $class) .'.html';
        return $this->router->generateLink($path, $relativeTo);
    }

}