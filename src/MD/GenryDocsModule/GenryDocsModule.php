<?php
namespace MD\GenryDocsModule;

use Splot\Framework\Modules\AbstractModule;

use Sami\Sami;

use MD\Genry\Events\DidGenerate;

use MD\GenryDocsModule\Generator\Generator;
use MD\GenryDocsModule\Generator\Project;
use MD\GenryDocsModule\Generator\Router;
use MD\GenryDocsModule\Templating\DocsExtension;

class GenryDocsModule extends AbstractModule
{

    public function configure() {
        parent::configure();

        $this->container->set('docs.sami', function($c) {
            $config = $c->get('config');
            $source = $c->getParameter('root_dir') . ltrim($config->get('docs.source'), DS);
            return new Sami($source, array(
                'build_dir' => $c->getParameter('cache_dir') .'sami/build',
                'cache_dir' => $c->getParameter('cache_dir') .'sami/cache',
                'default_opened_level' => 2
            ));
        });

        $this->container->set('docs.project', function($c) {
            $sami = $c->get('docs.sami');
            return new Project(
                $sami['project'],
                $sami['indexer'],
                $sami['tree']
            );
        });

        $this->container->set('docs.generator', function($c) {
            $config = $c->get('config');
            return new Generator(
                $c->get('genry'),
                $c->get('docs.project'),
                $config->get('docs.templates', array()),
                $config->get('docs.output_dir', ''),
                $config->get('docs.verbose', false)
            );
        });

        $this->container->set('docs.router', function($c) {
            return new Router(
                $c->get('genry.router'),
                $c->get('config')->get('docs.output_dir', '')
            );
        });

        $this->container->set('docs.twig_extension', function($c) {
            return new DocsExtension(
                $c->get('docs.project'),
                $c->get('docs.router'),
                $c->get('config')->get('docs.namespace', '')
            );
        });
    }

    public function run() {
        parent::run();

        $container = $this->container;

        if ($container->has('twig')) {
            $container->get('twig')->addExtension($container->get('docs.twig_extension'));
        }

        // after normal generation has finished, also trigger docs generation
        $container->get('event_manager')->subscribe(DidGenerate::getName(), function($event) use ($container) {
            $container->get('console')->call('docs');
        });
    }

}