<?php

namespace Swarrot\SwarrotBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;

class SwarrotBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $container = $application->getKernel()->getContainer();

        $commands = $container->getParameter('swarrot.commands');
        foreach ($commands as $command) {
            $application->add($container->get($command));
        }
    }
}
