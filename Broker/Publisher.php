<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

class Publisher
{
    protected $factory;
    protected $eventDispatcher;
    protected $messageTypes;
    protected $logger;

    /**
     * __construct.
     *
     * @param FactoryInterface         $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $messageTypes
     * @param LoggerInterface          $logger
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher, array $messageTypes = array(), LoggerInterface $logger = null)
    {
        $this->factory = $factory;
        $this->eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);
        $this->messageTypes = $messageTypes;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * publish.
     *
     * @param string  $messageType
     * @param Message $message
     */
    public function publish($messageType, Message $message, array $overridenConfig = array())
    {
        if (!$this->isKnownMessageType($messageType)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown message type "%s". Available are [%s].',
                $messageType,
                implode(',', array_keys($this->messageTypes))
            ));
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

    /**
     * isKnownMessageType.
     *
     * @param string $messageType
     *
     * @return bool
     */
    public function isKnownMessageType($messageType)
    {
        return isset($this->messageTypes[$messageType]);
    }

    /**
     * getConfigForMessageType.
     *
     * @param string $messageType
     *
     * @return array
     */
    public function getConfigForMessageType($messageType)
    {
        if (!$this->isKnownMessageType($messageType)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown message type "%s". Available are [%s].',
                $messageType,
                implode(array_keys($this->messageTypes))
            ));
        }

        return $this->messageTypes[$messageType];
    }
}
