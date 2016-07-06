<?php

namespace Swarrot\SwarrotBundle\Processor;

use Symfony\Component\Console\Input\InputInterface;

interface ProcessorConfiguratorEnablableInterface
{
    /**
     * @return bool
     */
    public function isEnabled();
}
