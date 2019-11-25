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

    /**
     * @param string $processorClass
     */
    public function __construct($processorClass, ManagerRegistry $managerRegistry)
    {
        $this->processorClass = $processorClass;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessorArguments(array $options)
    {
        return [
            $this->processorClass,
            $this->managerRegistry,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandOptions()
    {
        return [
            ['no-reset', null, InputOption::VALUE_NONE, 'Deactivate object manager reset after processing.'],
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
