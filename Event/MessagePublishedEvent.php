<?php

namespace Swarrot\SwarrotBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Swarrot\Broker\Message;

class MessagePublishedEvent extends Event
{
    const NAME = 'swarrot.message_published';

    protected $messageType;
    protected $message;
    protected $connection;
    protected $exchange;
    protected $routingKey;

    /**
     * __construct.
     *
     * @param string  $messageType
     * @param Message $message
     * @param string  $connection
     * @param string  $exchange
     * @param string  $routingKey
     */
    public function __construct($messageType, Message $message, $connection, $exchange, $routingKey)
    {
        $this->messageType = $messageType;
        $this->message = $message;
        $this->connection = $connection;
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
    }

    /**
     * getMessageType.
     *
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * getMessage.
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * getConnection.
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * getExchange.
     *
     * @return string
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * getRoutingKey.
     *
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }
}
