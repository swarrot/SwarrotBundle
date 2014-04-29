<?php

namespace Swarrot\SwarrotBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ProviderPass;
use Swarrot\SwarrotBundle\DependencyInjection\Compiler\ConsumerCommandPass;

class SwarrotBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderPass, PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new ConsumerCommandPass, PassConfig::TYPE_OPTIMIZE);
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
