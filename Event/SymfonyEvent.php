<?php

namespace Swarrot\SwarrotBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\Event as DeprecatedEvent;

if (interface_exists(Event::class)) {
    abstract class SymfonyEvent extends Event
    {

    }
} else {
    /**
     * For BC with Symfony < 4.3
     */
    abstract class SymfonyEvent extends DeprecatedEvent
    {

    }
}
