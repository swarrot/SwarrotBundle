<?php

namespace Swarrot\SwarrotBundle\Tests\Command;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\Command\SwarrotCommand;
use Symfony\Component\Console\Tester\CommandTester;

class SwarrotCommandTest extends ProphecyTestCase
{
    public function testDefinition()
    {
        $processorProphecy = $this->prophesize('Swarrot\Processor\ProcessorInterface');
        $factoryProphecy = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');

        $command = new SwarrotCommand(
            'foobar',
            'connection',
            $factoryProphecy->reveal(),
            $processorProphecy->reveal(),
            array(),
            array(),
            null,
            'default_queue'
        );

        $this->assertEquals('swarrot:consume:foobar', $command->getName());
        $this->assertEquals('default_queue', $command->getDefinition()->getArgument('queue')->getDefault());
    }

    public function test()
    {
        $processorProphecy = $this->prophesize('Swarrot\Processor\ProcessorInterface');
        $factoryProphecy = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $messageProviderProphecy = $this->prophesize('Swarrot\Broker\MessageProvider\MessageProviderInterface');

        $factoryProphecy
            ->getMessageProvider('my_queue', 'my_connection')
            ->willReturn($messageProviderProphecy)
        ;
        $messageProviderProphecy
            ->get()
            ->willReturn($message = new Message())
        ;
        $processorProphecy
            ->process($message, Argument::type('array'))
            ->willReturn(false)
            ->shouldBeCalledTimes(1)
        ;

        $command = new SwarrotCommand(
            'foobar',
            'connection',
            $factoryProphecy->reveal(),
            $processorProphecy->reveal(),
            [],
            []
        );
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'queue' => 'my_queue',
            'connection' => 'my_connection',
        ]);
    }

    public function testInsomniac()
    {
        $processorProphecy = $this->prophesize('Swarrot\Processor\ProcessorInterface');
        $factoryProphecy = $this->prophesize('Swarrot\SwarrotBundle\Broker\FactoryInterface');
        $messageProviderProphecy = $this->prophesize('Swarrot\Broker\MessageProvider\MessageProviderInterface');

        $factoryProphecy
            ->getMessageProvider(Argument::cetera())
            ->willReturn($messageProviderProphecy)
        ;
        $messageProviderProphecy->get()->willReturn(null);
        $processorProphecy->process(Argument::cetera())->shouldNotBeCalled();

        $command = new SwarrotCommand(
            'foobar',
            'connection',
            $factoryProphecy->reveal(),
            $processorProphecy->reveal(),
            [
                'insomniac' => 'Swarrot\Processor\Insomniac\InsomniacProcessor',
            ],
            []
        );
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'queue' => 'foo',
            '--exit-when-empty' => null,
        ]);
    }
}
