<?php
namespace MD\GenryDocsModule\Templating;

use Twig_Extension;
use Twig_SimpleFunction;

use MD\Genry\Page;

use MD\GenryDocsModule\Generator\Project;
use MD\GenryDocsModule\Generator\Router;

class DocsExtension extends Twig_Extension
{

    protected $project;

    protected $router;

    public function __construct(Project $project, Router $router) {
        $this->project = $project;
        $this->router = $router;
    }

    public function getGlobals() {
        return array(
            'docs' => $this->project
        );
    }

    /**
     * Returns Twig functions registered by this extension.
     * 
     * @return array
     */
    public function getFunctions() {
        return array(
            new Twig_SimpleFunction('link_namespace', array($this, 'linkNamespace'), array('needs_context' => true)),
            new Twig_SimpleFunction('link_class', array($this, 'linkClass'), array('needs_context' => true))
        );
    }

    public function linkNamespace($context, $namespace) {
        if (!isset($context['_genry_page']) || !$context['_genry_page'] instanceof Page) {
            throw new \RuntimeException('Twig link_namespace() function requires "_genry_page" variable in the template to be set to the current rendered page. It must have been overwritten in the context.');
        }

        return $this->router->generateNamespaceLink($namespace, $context['_genry_page']);
    }

    public function linkClass($context, $class) {
        if (!isset($context['_genry_page']) || !$context['_genry_page'] instanceof Page) {
            throw new \RuntimeException('Twig link_class() function requires "_genry_page" variable in the template to be set to the current rendered page. It must have been overwritten in the context.');
        }

        return $this->router->generateClassLink($class, $context['_genry_page']);
    }

    /**
     * Returns the name of this extension.
     * 
     * @return string
     */
    public function getName() {
        return 'genry_docs_module.extension';
    }

}