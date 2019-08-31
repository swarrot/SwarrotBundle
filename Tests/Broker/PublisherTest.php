<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Prophecy\Argument;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\Event\MessagePublishedEvent;
use PHPUnit\Framework\TestCase;

class PublisherTest extends TestCase
{
    public function test_it_is_initializable()
    {
        $publisher = new Publisher(
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface')->reveal()
        );

        $this->assertInstanceOf('Swarrot\SwarrotBundle\Broker\Publisher', $publisher);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown message type "message_type". Available are [].
     */
    public function test_publish_with_unknown_message_type()
    {
        $publisher = new Publisher(
            $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface')->reveal(),
            $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface')->reveal()
        );

        $message = new Message();
        $publisher->publish('message_type', $message);
    }

    public function test_publish_with_valid_message_type()
    {
        $message = new Message();

        $eventDispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcher
            ->dispatch(
                Argument::exact(new MessagePublishedEvent('message_type', $message, 'connection', 'exchange', 'routing_key')),
                Argument::exact('swarrot.message_published')
            )
            ->shouldBeCalledTimes(1)
        ;

        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $logger
            ->debug(
                Argument::exact('Publish message in {exchange}:{routing_key} (connection {connection})'),
                Argument::exact([
                    'exchange' => 'exchange',
                    'routing_key' => 'routing_key',
                    'connection' => 'connection',
                ])
            )
            ->shouldBeCalledTimes(1)
        ;

        $messagePublisher = $this->prophesize('Swarrot\Broker\MessagePublisher\MessagePublisherInterface');
        $messagePublisher
            ->publish($message, 'routing_key')
            ->shouldBeCalledTimes(1)
        ;

        $factory = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $factory
            ->getMessagePublisher('exchange', 'connection')
            ->shouldBeCalledTimes(1)
            ->willReturn($messagePublisher->reveal())
        ;

        $publisher = new Publisher(
            $factory->reveal(),
            $eventDispatcher->reveal(),
            [
                'message_type' => [
                    'connection' => 'connection',
                    'exchange' => 'exchange',
                    'routing_key' => 'routing_key',
                ],
            ],
            $logger->reveal()
        );

        $publisher->publish('message_type', $message);
    }
}
