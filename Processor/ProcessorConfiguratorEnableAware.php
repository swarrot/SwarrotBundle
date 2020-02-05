<?php

namespace Swarrot\SwarrotBundle\Processor;

trait ProcessorConfiguratorEnableAware
{
    /** @var bool */
    private $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
