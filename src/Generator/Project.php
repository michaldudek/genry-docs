<?php
namespace Genry\Docs\Generator;

use Sami\Sami;
use Sami\Project as SamiProject;
use Sami\Indexer;
use Sami\Tree;

use MD\Foundation\Debug\Debugger;

/**
 * Wrapper around a Sami project.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
class Project
{

    /**
     * Sami Project.
     *
     * @var SamiProject
     */
    protected $project;

    /**
     * Indexer.
     *
     * @var Indexer
     */
    protected $indexer;

    /**
     * Index data.
     *
     * @var mixed
     */
    protected $indexData;

    /**
     * Tree.
     *
     * @var Tree
     */
    protected $tree;

    /**
     * Tree data.
     *
     * @var mixed
     */
    protected $treeData;

    /**
     * Is the project already parsed or not?
     *
     * @var boolean
     */
    protected $parsed = false;

    /**
     * Constructor.
     *
     * @param Sami $sami Sami instance.
     */
    public function __construct(Sami $sami)
    {
        $this->project = $sami['project'];
        $this->indexer = $sami['indexer'];
        $this->tree = $sami['tree'];
    }

    /**
     * Parse the project (if not parsed before).
     *
     * @return boolean
     */
    protected function parse()
    {
        if ($this->parsed) {
            return true;
        }

        $this->project->parse();
        $this->parsed = true;
        return true;
    }

    /**
     * Get all namespaces in the project.
     *
     * @return array
     */
    public function getNamespaces()
    {
        $this->parse();
        return $this->project->getNamespaces();
    }

    /**
     * Get all classes in the project.
     *
     * @return array
     */
    public function getClasses()
    {
        $this->parse();
        return $this->project->getProjectClasses();
    }

    /**
     * Get all interfaces in the project.
     *
     * @return array
     */
    public function getInterfaces()
    {
        $this->parse();
        return $this->project->getProjectInterfaces();
    }

    /**
     * Get all classes in a namespace.
     *
     * @param  string $namespace Namespace.
     *
     * @return array
     */
    public function getNamespaceAllClasses($namespace)
    {
        $this->parse();
        return $this->project->getNamespaceAllClasses($namespace);
    }

    /**
     * Get classess directly in the namespace.
     *
     * @param  string $namespace Namespace.
     *
     * @return array
     */
    public function getNamespaceClasses($namespace)
    {
        $this->parse();
        return $this->project->getNamespaceClasses($namespace);
    }

    /**
     * Get all interfaces in the namespace.
     *
     * @param  string $namespace Namespace.
     *
     * @return array
     */
    public function getNamespaceInterfaces($namespace)
    {
        $this->parse();
        return $this->project->getNamespaceInterfaces($namespace);
    }

    /**
     * Get all exceptions in the namespace.
     *
     * @param  string $namespace Namespace.
     *
     * @return string
     */
    public function getNamespaceExceptions($namespace)
    {
        $this->parse();
        return $this->project->getNamespaceExceptions($namespace);
    }

    /**
     * Returns the index data.
     *
     * @return mixed
     */
    public function getIndex()
    {
        if ($this->indexData) {
            return $this->indexData;
        }
        $this->parse();
        $this->indexData = $this->indexer->getIndex($this->project);
        return $this->indexData;
    }

    /**
     * Returns items.
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        foreach ($this->getClasses() as $class) {
            $letter = mb_strtoupper(mb_substr($class->getShortName(), 0, 1));
            $items[$letter][] = array('class', $class);

            foreach ($class->getProperties() as $property) {
                $letter = mb_strtoupper(mb_substr($property->getName(), 0, 1));
                $items[$letter][] = array('property', $property);
            }

            foreach ($class->getMethods() as $method) {
                $letter = mb_strtoupper(mb_substr($method->getName(), 0, 1));
                $items[$letter][] = array('method', $method);
            }
        }
        ksort($items);

        return $items;
    }

    /**
     * Returns a tree.
     *
     * @return mixed
     */
    public function getTree()
    {
        if ($this->treeData) {
            return $this->treeData;
        }
        $this->parse();
        $this->treeData = $this->tree->getTree($this->project);
        return $this->treeData;
    }

    /**
     * Returns all paretns of a class.
     *
     * @param  string $class Class name.
     *
     * @return array
     */
    public function getClassParents($class)
    {
        $class = (string)$class;
        return Debugger::getObjectAncestors($class);
    }

    /**
     * Checks if the type is a php type.
     *
     * @param  string  $type The type.
     *
     * @return boolean
     */
    public function isPhpType($type)
    {
        return SamiProject::isPhpTypeHint($type);
    }

    /**
     * Checks if the class belongs to the project.
     *
     * @param  string  $class Class name.
     *
     * @return boolean
     */
    public function isProjectClass($class)
    {
        $class = (string)$class;
        if (empty($class)) {
            return false;
        }
        $projectClasses = $this->getClasses();
        return isset($projectClasses[$class]);
    }
}
