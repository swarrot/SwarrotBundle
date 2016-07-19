<?php

namespace Swarrot\SwarrotBundle\Command;

use Swarrot\Consumer;
use Swarrot\Processor\ProcessorInterface;
use Swarrot\Processor\Stack\Builder;
use Swarrot\SwarrotBundle\Processor\ProcessorConfiguratorInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwarrotCommand extends ContainerAwareCommand
{
    protected $name;
    protected $connectionName;
    protected $processor;
    protected $processorConfigurators;
    protected $extras;
    protected $queue;

    public function __construct(
        $name,
        $connectionName,
        ProcessorInterface $processor,
        array $processorConfigurators,
        array $extras,
        $queue = null
    ) {
        $this->name = $name;
        $this->connectionName = $connectionName;
        $this->processor = $processor;
        $this->processorConfigurators = $processorConfigurators;
        $this->extras = $extras;
        $this->queue = $queue;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swarrot:consume:'.$this->name)
            ->setDescription(sprintf('Consume message of type "%s" from a given queue', $this->name))
            ->addArgument('queue', InputArgument::OPTIONAL, 'Queue to consume', $this->queue)
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection to use', $this->connectionName)
            ->addOption(
                'poll-interval',
                null,
                InputOption::VALUE_REQUIRED,
                'Poll interval (in micro-seconds)',
                (isset($this->extras['poll_interval'])) ? $this->extras['poll_interval'] : 500000
            );

        /** @var ProcessorConfiguratorInterface $processorConfigurator */
        foreach ($this->processorConfigurators as $processorConfigurator) {
            foreach ($processorConfigurator->getCommandOptions() as $args) {
                call_user_func_array([$this, 'addOption'], $args);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->getOptions($input);

        $stack = new Builder();
        /** @var ProcessorConfiguratorInterface $processorConfigurator */
        foreach ($this->processorConfigurators as $processorConfigurator) {
            if ($processorConfigurator->isEnabled()) {
                call_user_func_array([$stack, 'push'], $processorConfigurator->getProcessorArguments($options));
            }
        }

        $processor = $stack->resolve($this->processor);
        $optionsResolver = new OptionsResolver();
        if (method_exists($optionsResolver, 'setDefined')) {
            $optionsResolver->setDefined(['queue', 'connection']);
        } else {
            $optionsResolver->setOptional(['queue', 'connection']);
        }

        $factory = $this->getContainer()->get('swarrot.factory.default');
        $messageProvider = $factory->getMessageProvider($options['queue'], $options['connection']);

        $consumer = new Consumer($messageProvider, $processor, $optionsResolver);

        $consumer->consume($options);
    }

    /**
     * getOptions.
     *
     * @param InputInterface $input
     *
     * @return array
     */
    protected function getOptions(InputInterface $input)
    {
        $options = $this->extras + [
            'queue' => $input->getArgument('queue'),
            'connection' => $input->getArgument('connection'),
            'poll_interval' => (int) $input->getOption('poll-interval'),
        ];

        /** @var ProcessorConfiguratorInterface $processorConfigurator */
        foreach ($this->processorConfigurators as $processorConfigurator) {
            $processorOptions = $processorConfigurator->resolveOptions($input);

            if ($processorConfigurator->isEnabled()) {
                $options += $processorOptions;
            }
        }

        return $options;
    }
}
