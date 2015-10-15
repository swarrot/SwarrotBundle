<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;

interface FactoryInterface
{
    /**
     * addConnection.
     *
     * @param string $name       A name for the connection
     * @param array  $connection An array containing connection informations
     *
     * @return FactoryInterface
     */
    public function addConnection($name, array $connection);

    /**
     * getMessageProvider.
     *
     * @param string $name       The name of the queue where the MessageProviderInterface will found messages
     * @param string $connection The name of the connection to use
     *
     * @return MessageProviderInterface
     */
    public function getMessageProvider($name, $connection);

    /**
     * getMessagePublisher.
     *
     * @param string $name       The name of the exchange where the MessagePublisher will publish
     * @param string $connection The name of the connection to use
     *
     * @return MessagePublisherInterface
     */
    public function getMessagePublisher($name, $connection);
}
