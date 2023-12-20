<?php

namespace Swarrot\SwarrotBundle\Broker;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Publisher
{
    /** @var FactoryInterface */
    protected $factory;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var array */
    protected $messageTypes;
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher, array $messageTypes = [], LoggerInterface $logger = null)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageTypes = $messageTypes;
        $this->logger = $logger ?: new NullLogger();
    }

    public function publish(string $messageType, Message $message, array $overridenConfig = []): void
    {
        if (!$this->isKnownMessageType($messageType)) {
            throw new \InvalidArgumentException(sprintf('Unknown message type "%s". Available are [%s].', $messageType, implode(',', array_keys($this->messageTypes))));
        }

        $config = $this->messageTypes[$messageType];

        $exchange = isset($overridenConfig['exchange']) ? $overridenConfig['exchange'] : $config['exchange'];
        $connection = isset($overridenConfig['connection']) ? $overridenConfig['connection'] : $config['connection'];
        $routingKey = isset($overridenConfig['routing_key']) ? $overridenConfig['routing_key'] : $config['routing_key'];

        $messagePublisher = $this->factory->getMessagePublisher($exchange, $connection);

        $this->logger->debug('Publish message in {exchange}:{routing_key} (connection {connection})', [
            'exchange' => $exchange,
            'routing_key' => $routingKey,
            'connection' => $connection,
        ]);

        $messagePublisher->publish($message, $routingKey);

        $this->eventDispatcher->dispatch(
            new MessagePublishedEvent($messageType, $message, $connection, $exchange, $routingKey),
            MessagePublishedEvent::NAME
        );
    }

    public function isKnownMessageType(string $messageType): bool
    {
        return isset($this->messageTypes[$messageType]);
    }

    public function getConfigForMessageType(string $messageType): array
    {
        if (!$this->isKnownMessageType($messageType)) {
            throw new \InvalidArgumentException(sprintf('Unknown message type "%s". Available are [%s].', $messageType, implode(array_keys($this->messageTypes))));
        }

        return $this->messageTypes[$messageType];
    }
}
