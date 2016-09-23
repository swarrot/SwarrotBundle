<?php

namespace Swarrot\SwarrotBundle\Tests\Broker;

use Swarrot\SwarrotBundle\Broker\SqsFactory;
use Phake;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\SwarrotBundle\Broker\FactoryInterface;

/**
 * Class SqsFactoryTest
 */
class SqsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqsFactory
     */
    protected $factory;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->factory = new SqsFactory();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(FactoryInterface::CLASS, $this->factory);
    }

    /**
     * Test get message provider
     */
    public function testGetMessageProvider()
    {
        $this->factory->addConnection('sqs', [
            'login' => 'key',
            'password' => 'secret',
            'host' => 'eu-west-1',
            'vhost' => 'localhost/',
        ]);

        $messageProvider = $this->factory->getMessageProvider('workers-test', 'sqs');

        $this->assertInstanceOf(MessageProviderInterface::CLASS, $messageProvider);
        $this->assertSame('localhost/workers-test', $messageProvider->getQueueName());
    }
}
