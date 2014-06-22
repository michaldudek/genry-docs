<?php
namespace MD\GenryDocsModule\Generator;

use Sami\Project as SamiProject;
use Sami\Indexer;
use Sami\Tree;

class Project
{

    protected $project;

    protected $indexer;

    protected $indexData;

    protected $tree;

    protected $treeData;

    protected $parsed = false;

    public function __construct(SamiProject $project, Indexer $indexer, Tree $tree) {
        $this->project = $project;
        $this->indexer = $indexer;
        $this->tree = $tree;
    }

    protected function parse() {
        if ($this->parsed) {
            return true;
        }

        $this->project->parse();
        $this->parsed = true;
    }

    public function getNamespaces() {
        $this->parse();
        return $this->project->getNamespaces();
    }

    public function getClasses() {
        $this->parse();
        return $this->project->getProjectClasses();
    }

    public function getInterfaces() {
        $this->parse();
        return $this->project->getProjectInterfaces();
    }

    public function getNamespaceClasses($namespace) {
        $this->parse();
        return $this->project->getNamespaceClasses($namespace);
    }

    public function getNamespaceInterfaces($namespace) {
        $this->parse();
        return $this->project->getNamespaceInterfaces($namespace);
    }

    public function getNamespaceExceptions($namespace) {
        $this->parse();
        return $this->project->getNamespaceExceptions($namespace);
    }

    public function getIndex() {
        if ($this->indexData) {
            return $this->indexData;
        }
        $this->parse();
        $this->indexData = $this->indexer->getIndex($this->project);
        return $this->indexData;
    }

    public function getItems() {
        $items = array();
        foreach($this->getClasses() as $class) {
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

    public function getTree() {
        if ($this->treeData) {
            return $this->treeData;
        }
        $this->parse();
        $this->treeData = $this->tree->getTree($this->project);
        return $this->treeData;
    }

}