<?php

namespace Swarrot\SwarrotBundle;

use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SwarrotBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderCompilerPass());
    }

    public function registerCommands(Application $application)
    {
        $container = $application->getKernel()->getContainer();

        $commands = $container->getParameter('swarrot.commands');
        foreach ($commands as $command) {
            $application->add($container->get($command));
        }
    }
}
