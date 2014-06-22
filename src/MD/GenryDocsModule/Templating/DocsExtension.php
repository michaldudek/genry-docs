<?php
namespace MD\GenryDocsModule\Templating;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

use MD\Genry\Page;

use MD\GenryDocsModule\Generator\Project;
use MD\GenryDocsModule\Generator\Router;

class DocsExtension extends Twig_Extension
{

    protected $project;

    protected $router;

    protected $namespace;

    public function __construct(Project $project, Router $router, $namespace = '') {
        $this->project = $project;
        $this->router = $router;
        $this->namespace = trim($namespace, NS);
    }

    public function getGlobals() {
        return array(
            'docs' => $this->project
        );
    }

    public function getFunctions() {
        return array(
            new Twig_SimpleFunction('link_namespace', array($this, 'linkNamespace'), array('needs_context' => true)),
            new Twig_SimpleFunction('link_class', array($this, 'linkClass'), array('needs_context' => true))
        );
    }

    public function getFilters() {
        return array(
            new Twig_SimpleFilter('strip_namespace', array($this, 'stripNamespace'))
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

    public function stripNamespace($class) {
        // ensure string
        $class = (string)$class;
        $rawClass = trim($class, NS);

        // if namespace not defined then just return the full class
        if (empty($this->namespace)) {
            return $class;
        }

        // if class not in namespace then return the class
        if (stripos($rawClass, $this->namespace) !== 0) {
            return $class;
        }

        $shortClass = mb_substr($rawClass, mb_strlen($this->namespace));
        $shortClass = trim($shortClass, NS);
        return $shortClass;
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