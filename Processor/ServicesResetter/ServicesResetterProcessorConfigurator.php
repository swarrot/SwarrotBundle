<?php

declare(strict_types=1);

namespace Swarrot\SwarrotBundle\Processor\ServicesResetter;

use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @author Pierrick Vignand <pierrick.vignand@gmail.com>
 */
class ServicesResetterProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;
    /** @var ResetInterface */
    private $servicesResetter;

    public function __construct(string $processorClass, ResetInterface $servicesResetter)
    {
        $this->processorClass = $processorClass;
        $this->servicesResetter = $servicesResetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        return [
            $this->processorClass,
            $this->servicesResetter,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandOptions()
    {
        return [
            ['no-reset', null, InputOption::VALUE_NONE, 'Deactivate services reset after processing.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(InputInterface $input)
    {
        $this->enabled = !$input->getOption('no-reset');

        return $this->getExtras();
    }
}
