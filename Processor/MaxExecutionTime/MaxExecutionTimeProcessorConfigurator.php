<?php

namespace Swarrot\SwarrotBundle\Processor\MaxExecutionTime;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MaxExecutionTimeProcessorConfigurator implements ProcessorConfiguratorInterface
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
        return [
            [
                'max-execution-time',
                't',
                InputOption::VALUE_REQUIRED,
                'Max execution time (seconds) before exit',
                $this->getExtra('max_execution_time', 300),
            ],
        ];
    }

    public function resolveOptions(InputInterface $input): array
    {
        return [
            'max_execution_time' => (int) $input->getOption('max-execution-time'),
        ] + $this->getExtras();
    }
}
