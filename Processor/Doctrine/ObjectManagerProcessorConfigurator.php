<?php

namespace Swarrot\SwarrotBundle\Processor\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorEnableAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorExtrasAware;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class ObjectManagerProcessorConfigurator implements ProcessorConfiguratorInterface
{
    use ProcessorConfiguratorEnableAware;
    use ProcessorConfiguratorExtrasAware;

    /** @var string */
    private $processorClass;
    /** @var mixed */
    private $managerRegistry;

    public function __construct(string $processorClass, ManagerRegistry $managerRegistry)
    {
        $this->processorClass = $processorClass;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options): array
    {
        return [
            $this->processorClass,
            $this->managerRegistry,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandOptions(): array
    {
        return [
            ['no-reset', null, InputOption::VALUE_NONE, 'Deactivate object manager reset after processing.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(InputInterface $input): array
    {
        $this->enabled = !$input->getOption('no-reset');

        return $this->getExtras();
    }
}
