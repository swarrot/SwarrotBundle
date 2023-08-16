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

    public function getProcessorArguments(array $options): array
    {
        return [
            $this->processorClass,
            $this->servicesResetter,
        ];
    }

    public function getCommandOptions(): array
    {
        return [
            ['no-reset', null, InputOption::VALUE_NONE, 'Deactivate services reset after processing.'],
        ];
    }

    public function resolveOptions(InputInterface $input): array
    {
        $this->enabled = !$input->getOption('no-reset');

        return $this->getExtras();
    }
}
