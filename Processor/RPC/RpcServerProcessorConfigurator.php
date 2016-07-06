<?php

namespace Swarrot\SwarrotBundle\Processor\RPC;

use Psr\Log\LoggerInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;

class RpcServerProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorExtrasAware;

    /** @var FactoryInterface */
    private $factory;
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(FactoryInterface $factory, LoggerInterface $logger)
    {
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        $exchange = $this->getExtra('rpc_exchange', 'retry');

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
        return $this->getExtras();
    }
}
