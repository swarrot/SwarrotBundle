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
    /** @var mixed */
    private $connections;

    /**
     * @param string $processorClass
     * @param mixed  $connections
     */
    public function __construct($processorClass, $connections)
    {
        $this->processorClass = $processorClass;
        $this->connections = $connections;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        return [
            $this->processorClass,
            $this->connections,
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
