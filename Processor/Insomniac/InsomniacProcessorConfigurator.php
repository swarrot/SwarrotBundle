<?php

namespace Swarrot\SwarrotBundle\Processor\Insomniac;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class InsomniacProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getProcessorArguments(array $options): array
    {
        return [
            'Swarrot\Processor\Insomniac\InsomniacProcessor',
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
