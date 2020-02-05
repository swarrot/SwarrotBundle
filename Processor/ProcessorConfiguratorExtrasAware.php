<?php

namespace Swarrot\SwarrotBundle\Processor;

trait ProcessorConfiguratorExtrasAware
{
    /** @var array */
    private $extras = [];

    public function setExtras(array $extras): void
    {
        $this->extras = $extras;
    }

    public function getExtras(): array
    {
        return $this->extras;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getExtra(string $name, $default = null)
    {
        return isset($this->extras[$name]) ? $this->extras[$name] : $default;
    }
}
