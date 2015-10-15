<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\Broker\Message;

class BlackholePublisher extends Publisher
{
    /**
     * {@inheritDoc}
     */
    public function publish($messageType, Message $message, array $overridenConfig = array())
    {
    }
}
