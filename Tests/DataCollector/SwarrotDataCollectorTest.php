<?php

namespace Swarrot\SwarrotBundle\Tests\DataCollector;

use PHPUnit\Framework\TestCase;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\DataCollector\SwarrotDataCollector;
use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;

class SwarrotDataCollectorTest extends TestCase
{
    public function test()
    {
        $message = new Message();

        $dataCollector = new SwarrotDataCollector();

        $this->assertSame(0, $dataCollector->getNbMessages());
        $this->assertSame([], $dataCollector->getMessages());

        $dataCollector->onMessagePublished(new MessagePublishedEvent(
            'my_message_type',
            $message,
            'my_connection',
            'my_exchange',
            'my_routing_key'
        ));

        $this->assertSame(1, $dataCollector->getNbMessages());
        $this->assertSame([
            [
                'message_type' => 'my_message_type',
                'message' => $message,
                'connection' => 'my_connection',
                'exchange' => 'my_exchange',
                'routing_key' => 'my_routing_key',
            ],
        ], $dataCollector->getMessages());

        $dataCollector->reset();

        $this->assertSame(0, $dataCollector->getNbMessages());
        $this->assertSame([], $dataCollector->getMessages());
    }
}
