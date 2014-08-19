<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;

class Publisher
{
    protected $factory;
    protected $eventDispatcher;
    protected $messageTypes;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher, array $messageTypes = array())
    {
        $this->factory         = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->messageTypes    = $messageTypes;
    }

    /**
     * publish
     *
     * @param string  $messageType
     * @param Message $message
     *
     * @return void
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

        $exchange   = isset($overridenConfig['exchange'])? $overridenConfig['exchange'] : $config['exchange'];
        $connection = isset($overridenConfig['connection'])? $overridenConfig['connection'] : $config['connection'];
        $routingKey = isset($overridenConfig['routing_key'])? $overridenConfig['routing_key'] : $config['routing_key'];

        $messagePublisher = $this->factory->getMessagePublisher($exchange, $connection);

        $messagePublisher->publish($message, $routingKey);

        $this->eventDispatcher->dispatch(
            MessagePublishedEvent::NAME,
            new MessagePublishedEvent($messageType, $message, $connection, $exchange, $routingKey)
        );
    }

    /**
     * isKnownMessageType
     *
     * @param string $messageType
     *
     * @return boolean
     */
    public function isKnownMessageType($messageType)
    {
        return isset($this->messageTypes[$messageType]);
    }

    /**
     * getConfigForMessageType
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
