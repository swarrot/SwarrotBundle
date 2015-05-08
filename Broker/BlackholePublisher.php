<?php

namespace Swarrot\SwarrotBundle\Broker;

use Swarrot\SwarrotBundle\Broker\Publisher;
use Swarrot\Broker\Message;

class BlackholePublisher extends Publisher
{
    public function publish($messageType, Message $message, array $overridenConfig = array())
    {
    }
}
