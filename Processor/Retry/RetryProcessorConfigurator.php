<?php

namespace Swarrot\SwarrotBundle\Processor\Retry;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class RetryProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;
    /** @var FactoryInterface */
    private $factory;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(string $processorClass, FactoryInterface $factory, LoggerInterface $logger)
    {
        $this->processorClass = $processorClass;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    public function getProcessorArguments(array $options): array
    {
        $exchange = $this->getExtra('retry_exchange', 'retry');

        return [
            $this->processorClass,
            $this->factory->getMessagePublisher($exchange, $options['connection']),
            $this->logger,
        ];
    }

    public function getCommandOptions(): array
    {
        return [
            ['no-retry', 'R', InputOption::VALUE_NONE, 'Deactivate retry.'],
            [
                'retry-attempts',
                null,
                InputOption::VALUE_REQUIRED,
                'Number of maximum retry attempts (if it exists, override the extra data parameter)',
                $this->getExtra('retry_attempts', 3),
            ],
        ];
    }

    public function resolveOptions(InputInterface $input): array
    {
        $this->enabled = !$input->getOption('no-retry');

        $key = $this->getExtra('retry_routing_key_pattern', 'retry_%attempt%s');

        return [
            'retry_key_pattern' => str_replace('%queue%', (string) $input->getArgument('queue'), $key),
            'retry_attempts' => (int) $input->getOption('retry-attempts'),
            'retry_log_levels_map' => $this->getExtra('retry_log_levels_map', []),
            'retry_fail_log_levels_map' => $this->getExtra('retry_fail_log_levels_map', []),
        ];
    }
}
