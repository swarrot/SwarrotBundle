<?php

namespace Swarrot\SwarrotBundle\Processor;

use Symfony\Component\Console\Input\InputInterface;

interface ProcessorConfiguratorInterface
{
    /**
     * @param array $extras
     */
    public function setExtras(array $extras);

    /**
     * @return array
     */
    public function getExtras();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * Retrieves the processor's class name and list of constructors arguments.
     *
     * @param array $options
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
     * @param InputInterface $input
     *
     * @return array
     */
    public function resolveOptions(InputInterface $input);
}
