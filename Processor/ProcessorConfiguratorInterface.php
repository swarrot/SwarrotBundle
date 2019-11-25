<?php

namespace Swarrot\SwarrotBundle\Processor;

use Symfony\Component\Console\Input\InputInterface;

interface ProcessorConfiguratorInterface
{
    /**
     * Define extra parameters to the configurator.
     */
    public function setExtras(array $extras);

    /**
     * Retrieves the processor's class name and list of constructors arguments.
     * The class name should be first, and is mandatory.
     *
     * @return array
     */
    public function getProcessorArguments(array $options);

    /**
     * Retrieves the list of additional options to add to the CLI command.
     *
     * @return array
     */
    public function getCommandOptions();

    /**
     * Resolve user input parameters to returns an array of options.
     *
     * @return array
     */
    public function resolveOptions(InputInterface $input);

    /**
     * @return bool
     */
    public function isEnabled();
}
