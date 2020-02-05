<?php

namespace Swarrot\SwarrotBundle\Processor\Sentry;

use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class SentryProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;

    /**
     * @var \Raven_Client|null
     */
    private $client;

    /**
     * @param string $processorClass
     */
    public function __construct($processorClass, \Raven_Client $client = null)
    {
        @trigger_error(sprintf('"%s" have been deprecated since SwarrotBundle 1.8', __CLASS__), E_USER_DEPRECATED);

        $this->processorClass = $processorClass;
        $this->client = $client;
        $this->enabled = null !== $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        return [
            $this->processorClass,
            $this->client,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(InputInterface $input)
    {
        return [];
    }
}
