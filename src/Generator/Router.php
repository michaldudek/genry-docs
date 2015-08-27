<?php
namespace Genry\Docs\Generator;

use Genry\Routing\Router as GenryRouter;

use Genry\Page;

/**
 * Docs specific Router.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
class Router
{

    /**
     * Genry router.
     *
     * @var GenryRouter
     */
    protected $router;

    /**
     * Output dir for docs.
     *
     * @var string
     */
    protected $outputDir;

    /**
     * Constructor.
     *
     * @param GenryRouter $router    Genry router.
     * @param string      $outputDir Output dir for docs.
     */
    public function __construct(GenryRouter $router, $outputDir)
    {
        $this->router = $router;
        $this->outputDir = !empty($outputDir) ? rtrim($outputDir, DS) . DS : '';
    }

    /**
     * Generates a link to a namespace.
     *
     * @param  string $namespace  Namespace.
     * @param  Page   $relativeTo Page to which it is relative.
     *
     * @return string
     */
    public function generateNamespaceLink($namespace, Page $relativeTo)
    {
        $path = $this->outputDir . str_replace(NS, DS, $namespace) .'.namespace.html';
        return $this->router->generateLink($path, $relativeTo);
    }

    /**
     * Generates a link to a class.
     *
     * @param  string $class      Class name.
     * @param  Page   $relativeTo Page to which it is relative.
     *
     * @return string
     */
    public function generateClassLink($class, Page $relativeTo)
    {
        $path = $this->outputDir . str_replace(NS, DS, $class) .'.html';
        return $this->router->generateLink($path, $relativeTo);
    }
}
