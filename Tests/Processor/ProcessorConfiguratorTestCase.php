<?php

namespace Swarrot\SwarrotBundle\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

abstract class ProcessorConfiguratorTestCase extends TestCase
{
    use ProphecyTrait;

    protected function createProcessor(ProcessorConfiguratorInterface $configurator, array $options = [])
    {
        $stubProcessor = $this->prophesize('Swarrot\Processor\ProcessorInterface')->reveal();

        $arguments = $configurator->getProcessorArguments($options);

        $reflection = new \ReflectionClass($arguments[0]);

        return $reflection->newInstanceArgs(array_merge([$stubProcessor], array_slice($arguments, 1)));
    }

    protected function getUserInput(array $input, ProcessorConfiguratorInterface $configurator)
    {
        if (!array_key_exists('queue', $input)) {
            $input['queue'] = uniqid();
        }

        return new ArrayInput(
            $input,
            new InputDefinition(
                array_merge(
                    array_map(
                        function ($option) {
                            $option += [null, null, null, '', null];

                            return new InputOption($option[0], $option[1], $option[2], $option[3], $option[4]);
                        },
                        $configurator->getCommandOptions()
                    ),
                    [
                        new InputArgument('queue', InputArgument::OPTIONAL, 'Queue to consume', uniqid()),
                        new InputArgument('connection', InputArgument::OPTIONAL, 'Connection to use', uniqid()),
                    ]
                )
            )
        );
    }
}
