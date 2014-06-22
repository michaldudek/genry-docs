<?php
namespace MD\GenryDocsModule\Generator;

use MD\Genry\Genry;

use MD\GenryDocsModule\Generator\Project;

class Generator
{

    protected $genry;

    protected $project;

    protected $templates;

    protected $outputDir;

    protected $verbose = false;

    public function __construct(Genry $genry, Project $project, array $templates, $outputDir, $verbose = false) {
        $this->genry = $genry;
        $this->project = $project;
        $this->templates = $templates;
        $this->outputDir = !empty($outputDir) ? rtrim($outputDir, DS) . DS : '';
        $this->verbose = $verbose;
    }

    public function generate() {
        $this->queueNamespaces();
        $this->queueClasses();

        // generate / process queue
        $this->genry->processQueue();
    }

    protected function queueNamespaces() {
        if (!isset($this->templates['namespace'])) {
            return false;
        }

        foreach($this->project->getNamespaces() as $namespace) {
            $parameters = array(
                'namespace'  => $namespace,
                'classes'    => $this->project->getNamespaceClasses($namespace),
                'interfaces' => $this->project->getNamespaceInterfaces($namespace),
                'exceptions' => $this->project->getNamespaceExceptions($namespace),
            );

            $this->genry->addToQueue($this->templates['namespace'], $parameters, $this->outputDir . str_replace(NS, DS, $namespace) .'.namespace.html');
        }
    }

    protected function queueClasses() {
        if (!isset($this->templates['class'])) {
            return false;
        }

        foreach($this->project->getClasses() as $class) {
            $parameters = array(
                'class'      => $class,
                'properties' => $class->getProperties($this->verbose),
                'methods'    => $class->getMethods($this->verbose),
                'constants'  => $class->getConstants($this->verbose)
            );

            $this->genry->addToQueue($this->templates['class'], $parameters, $this->outputDir . str_replace(NS, DS, $class) .'.html');
        }
    }

}