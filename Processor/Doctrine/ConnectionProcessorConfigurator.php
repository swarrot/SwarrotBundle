<?php

namespace Swarrot\SwarrotBundle\Processor\Doctrine;

use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class ConnectionProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;

    private $connections;

    public function __construct(string $processorClass, $connections)
    {
        $this->processorClass = $processorClass;
        $this->connections = $connections;
    }

    public function getProcessorArguments(array $options): array
    {
        return [
            $this->processorClass,
            $this->connections,
        ];
    }

    public function getCommandOptions(): array
    {
        return [];
    }

    public function resolveOptions(InputInterface $input): array
    {
        return $this->getExtras();
    }
}
