<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;

interface FactoryInterface
{
    public function addConnection(string $name, array $connection): void;

    public function getMessageProvider(string $name, string $connection): MessageProviderInterface;

    public function getMessagePublisher(string $name, string $connection): MessagePublisherInterface;
}
