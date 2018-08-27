<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;
use Swarrot\SwarrotBundle\Broker\SqsFactory;

class SqsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqsFactory
     */
    protected $factory;

    /**
     * Set up the test.
     */
    protected function setUp()
    {
        $this->factory = new SqsFactory();
    }

    /**
     * Test instance.
     */
    public function testInstance()
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    /**
     * Test get message provider.
     */
    public function testGetMessageProvider()
    {
        $this->factory->addConnection('sqs', [
            'login' => 'key',
            'password' => 'secret',
            'region' => 'eu-west-1',
            'host' => 'localhost/',
        ]);

        $messageProvider = $this->factory->getMessageProvider('workers-test', 'sqs');

        $this->assertInstanceOf(MessageProviderInterface::class, $messageProvider);
        $this->assertSame('localhost/workers-test', $messageProvider->getQueueName());
    }
}
