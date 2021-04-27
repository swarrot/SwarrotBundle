<?php

namespace Swarrot\SwarrotBundle\Event;

use Swarrot\Broker\Message;
use Symfony\Contracts\EventDispatcher\Event;

class MessagePublishedEvent extends Event
{
    public const NAME = 'swarrot.message_published';

    private $messageType;
    private $message;
    private $connection;
    private $exchange;
    private $routingKey;

    public function __construct(string $messageType, Message $message, string $connection, string $exchange, ?string $routingKey)
    {
        $this->messageType = $messageType;
        $this->message = $message;
        $this->connection = $connection;
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function getRoutingKey(): ?string
    {
        return $this->routingKey;
    }
}
