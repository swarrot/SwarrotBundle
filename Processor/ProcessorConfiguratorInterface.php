<?php

namespace Swarrot\SwarrotBundle\Processor;

use Symfony\Component\Console\Input\InputInterface;

interface ProcessorConfiguratorInterface
{
    /**
     * Define extra parameters to the configurator.
     */
    public function setExtras(array $extras): void;

    /**
     * Retrieves the processor's class name and list of constructors arguments.
     * The class name should be first, and is mandatory.
     */
    public function getProcessorArguments(array $options): array;

    /**
     * Retrieves the list of additional options to add to the CLI command.
     */
    public function getCommandOptions(): array;

    /**
     * Resolve user input parameters to returns an array of options.
     */
    public function resolveOptions(InputInterface $input): array;

    public function isEnabled(): bool;
}
