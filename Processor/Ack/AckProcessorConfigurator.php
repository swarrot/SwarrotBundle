<?php

namespace Swarrot\SwarrotBundle\Processor\Ack;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class AckProcessorConfigurator implements ProcessorConfiguratorInterface
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

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options): array
    {
        return [
            $this->processorClass,
            $this->factory->getMessageProvider($options['queue'], $options['connection']),
            $this->logger,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandOptions(): array
    {
        return [
            ['no-ack', 'A', InputOption::VALUE_NONE, 'Deactivate ack.'],
            ['requeue-on-error', 'r', InputOption::VALUE_NONE, 'Requeue in the same queue on error'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(InputInterface $input): array
    {
        $this->enabled = !$input->getOption('no-ack');

        return [
            'requeue_on_error' => $this->getExtra('requeue_on_error', false) || $input->getOption('requeue-on-error'),
        ] + $this->getExtras();
    }
}
