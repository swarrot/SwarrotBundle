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
    use ProcessorConfiguratorEnableAware, ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param string          $processorClass
     * @param LoggerInterface $logger
     */
    public function __construct($processorClass, LoggerInterface $logger)
    {
        $this->processorClass = $processorClass;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        return [
            $this->processorClass,
            $this->logger,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandOptions()
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

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(InputInterface $input)
    {
        return [
            'max_execution_time' => (int) $input->getOption('max-execution-time'),
        ] + $this->getExtras();
    }
}
