<?php
namespace MD\GenryDocsModule\Commands;

use Splot\Framework\Console\AbstractCommand;

class Docs extends AbstractCommand 
{

    protected static $name = 'docs';
    protected static $description = 'Generate documentation based on config in .genry.yml';

    public function execute() {
        $config = $this->get('config');
        $source = $config->get('docs.source');
        $this->writeln('Generating documentation for <info>'. $source .'</info>...');

        // @todo
        $this->get('docs.generator')->generate();

        $this->writeln('Done.');
        return;

        $this->writeln('Generating...');

        $genry = $this->get('genry');
        $templatesDir = $this->container->getParameter('templates_dir');

        $templates = FilesystemUtils::glob($templatesDir .'{,**/}*.html.twig', GLOB_BRACE);

        foreach($templates as $template) {
            // exclude if a partial template, ie. ends with ".inc.html.twig"
            if (preg_match('/\.inc\.html\.twig$/i', $template)) {
                continue;
            }

            $genry->addToQueue($template);
        }

        $output = $this;
        $genry->processQueue(function(Page $page) use ($output, $genry) {
            $output->writeln('Generated <info>'. $page->getOutputName() .'</info> from <comment>'. $page->getTemplateName() .'</comment>...');
        });

        $this->writeln('Done.');
    }

}