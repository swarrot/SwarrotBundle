<?php

namespace Swarrot\SwarrotBundle\Processor\NewRelic;

use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class NewRelicProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    public function __construct()
    {
        @trigger_error(sprintf('"%s" have been deprecated since SwarrotBundle 1.8', __CLASS__), E_USER_DEPRECATED);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        return [
            'Swarrot\Processor\NewRelic\NewRelicProcessor',
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
        return $this->getExtras();
    }
}
