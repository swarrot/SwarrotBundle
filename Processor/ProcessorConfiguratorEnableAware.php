<?php

namespace Swarrot\SwarrotBundle\Processor;

trait ProcessorConfiguratorEnableAware
{
    /** @var bool */
    private $enabled = true;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
