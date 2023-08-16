<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\Message;

class BlackholePublisher extends Publisher
{
    public function publish(string $messageType, Message $message, array $overridenConfig = []): void
    {
    }
}
