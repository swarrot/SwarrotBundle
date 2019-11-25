<?php

namespace Swarrot\SwarrotBundle\Processor\ExceptionCatcher;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class ExceptionCatcherProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param string $processorClass
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
            ['no-catch', 'C', InputOption::VALUE_NONE, 'Deactivate exception catching.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(InputInterface $input)
    {
        $this->enabled = !$input->getOption('no-catch');

        return $this->getExtras();
    }
}
