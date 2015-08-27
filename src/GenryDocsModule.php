<?php
namespace Genry\Docs;

use Splot\Framework\Modules\AbstractModule;

use Genry\Events\DidGenerate;

/**
 * Docs module for Genry.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
class GenryDocsModule extends AbstractModule
{

    /**
     * Configures the module.
     */
    public function configure()
    {
        parent::configure();

        $config = $this->getConfig();
        foreach ([
            'source',
            'namespace',
            'templates',
            'output_dir',
            'verbose'
        ] as $key) {
            $this->container->setParameter('docs.'. $key, $config->get($key));
        }
        $this->container->setParameter('docs.source', $config->get('source'));
    }

    /**
     * Runs the module.
     */
    public function run()
    {
        parent::run();

        $container = $this->container;

        // after normal generation has finished, also trigger docs generation
        $container->get('event_manager')->subscribe(DidGenerate::getName(), function () use ($container) {
            $container->get('console')->call('docs');
        });
    }
}
