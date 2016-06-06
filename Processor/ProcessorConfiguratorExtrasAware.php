<?php

namespace Swarrot\SwarrotBundle\Processor;

trait ProcessorConfiguratorExtrasAware
{
    /** @var array */
    protected $extras;

    /**
     * @param array $extras
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;
    }

    /**
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getExtra($name, $default = null)
    {
        return isset($this->extras[$name]) ? $this->extras[$name] : $default;
    }
}
