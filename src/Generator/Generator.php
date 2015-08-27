<?php
namespace Genry\Docs\Generator;

use Genry\Genry;

use Genry\Docs\Generator\Project;

/**
 * Docs generator.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
class Generator
{

    /**
     * Genry.
     *
     * @var Genry
     */
    protected $genry;

    /**
     * Docs project.
     *
     * @var Project
     */
    protected $project;

    /**
     * Array of templates.
     *
     * @var array
     */
    protected $templates;

    /**
     * Output dir.
     *
     * @var string
     */
    protected $outputDir;

    /**
     * Verbose?
     *
     * @var boolean
     */
    protected $verbose = false;

    /**
     * Constructor.
     *
     * @param Genry   $genry     Genry.
     * @param Project $project   Docs project.
     * @param array   $templates Array of templates.
     * @param string  $outputDir Output dir.
     * @param boolean $verbose   Verbose?
     */
    public function __construct(Genry $genry, Project $project, array $templates, $outputDir, $verbose = false)
    {
        $this->genry = $genry;
        $this->project = $project;
        $this->templates = $templates;
        $this->outputDir = !empty($outputDir) ? rtrim($outputDir, DS) . DS : '';
        $this->verbose = $verbose;
    }

    /**
     * Generate the docs.
     */
    public function generate()
    {
        $this->queueNamespaces();
        $this->queueClasses();

        // generate / process queue
        $this->genry->processQueue();
    }

    /**
     * Queue all namespaces for processing.
     */
    protected function queueNamespaces()
    {
        if (!isset($this->templates['namespace'])) {
            return false;
        }

        foreach ($this->project->getNamespaces() as $namespace) {
            $parameters = array(
                'namespace'  => $namespace,
                'classes'    => $this->project->getNamespaceClasses($namespace),
                'interfaces' => $this->project->getNamespaceInterfaces($namespace),
                'exceptions' => $this->project->getNamespaceExceptions($namespace),
            );

            $this->genry->addToQueue(
                $this->templates['namespace'],
                $parameters,
                $this->outputDir . str_replace(NS, DS, $namespace) .'.namespace.html'
            );
        }
    }

    /**
     * Queue all classes for processing.
     */
    protected function queueClasses()
    {
        if (!isset($this->templates['class'])) {
            return false;
        }

        foreach ($this->project->getClasses() as $class) {
            $parameters = array(
                'class'      => $class,
                'properties' => $class->getProperties($this->verbose),
                'methods'    => $class->getMethods($this->verbose),
                'constants'  => $class->getConstants($this->verbose)
            );

            $this->genry->addToQueue(
                $this->templates['class'],
                $parameters,
                $this->outputDir . str_replace(NS, DS, $class) .'.html'
            );
        }
    }
}
