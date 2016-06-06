<?php

namespace Swarrot\SwarrotBundle\Processor;

trait ProcessorConfiguratorEnableAware
{
    /** @var bool */
    protected $enabled = true;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
