<?php

namespace Swarrot\SwarrotBundle\Processor\SignalHandler;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class SignalHandlerProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(string $processorClass, LoggerInterface $logger)
    {
        $this->processorClass = $processorClass;
        $this->logger = $logger;
    }

    public function getProcessorArguments(array $options): array
    {
        return [
            $this->processorClass,
            $this->logger,
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
