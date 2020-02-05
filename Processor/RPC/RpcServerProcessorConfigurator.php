<?php

namespace Swarrot\SwarrotBundle\Processor\RPC;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class RpcServerProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var FactoryInterface */
    private $factory;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(FactoryInterface $factory, LoggerInterface $logger)
    {
        @trigger_error(sprintf('"%s" have been deprecated since SwarrotBundle 1.8', __CLASS__), E_USER_DEPRECATED);

        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        $exchange = $this->getExtra('rpc_exchange', '');

        return [
            'Swarrot\Processor\RPC\RpcServerProcessor',
            $this->factory->getMessagePublisher($exchange, $options['connection']),
            $this->logger,
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
