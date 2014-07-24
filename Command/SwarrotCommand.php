<?php

namespace Swarrot\SwarrotBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Swarrot\Processor\ProcessorInterface;
use Swarrot\Consumer;
use Swarrot\Processor\Stack\Builder;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SwarrotCommand.
 *
 * @author Olivier Dolbeau <contact@odolbeau.fr>
 */
class SwarrotCommand extends ContainerAwareCommand
{
    protected $name;
    protected $connectionName;
    protected $processor;
    protected $processorStack;
    protected $extras;
    protected $logger;

    public function __construct($name, $connectionName, ProcessorInterface $processor, array $processorStack, array $extras, LoggerInterface $logger = null)
    {
        $this->name           = $name;
        $this->connectionName = $connectionName;
        $this->processor      = $processor;
        $this->processorStack = $processorStack;
        $this->extras         = $extras;
        $this->logger         = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('swarrot:consume:'.$this->name)
            ->setDescription(sprintf('Consume message of type "%s" from a given queue', $this->name))
            ->addArgument('queue', InputArgument::REQUIRED, 'Queue to consume')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Connection to use', $this->connectionName)
            ->addOption('poll-interval', null, InputOption::VALUE_REQUIRED, 'Poll interval (in micro-seconds)', 500000)
        ;

        if (array_key_exists('ack', $this->processorStack)) {
            $this->addOption('requeue-on-error', 'r', InputOption::VALUE_NONE, 'Requeue in the same queue on error');
        }
        if (array_key_exists('max_execution_time', $this->processorStack)) {
            $this->addOption('max-execution-time', 't', InputOption::VALUE_REQUIRED, 'Max execution time (seconds) before exit', 300);
        }
        if (array_key_exists('max_messages', $this->processorStack)) {
            $this->addOption('max-messages', 'm', InputOption::VALUE_REQUIRED, 'Max messages to process before exit', 300);
        }
        if (array_key_exists('exception_catcher', $this->processorStack)) {
            $this->addOption('no-catch', 'C', InputOption::VALUE_NONE, 'Deactivate exception catching.');
        }
        if (array_key_exists('retry', $this->processorStack)) {
            $this->addOption('no-retry', 'R', InputOption::VALUE_NONE, 'Deactivate retry.');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue');
        $connection = $input->getArgument('connection');

        $factory = $this->getContainer()->get('swarrot.factory.default');
        $messageProvider = $factory->getMessageProvider($queue, $connection);

        $stack = new Builder();

        if (array_key_exists('signal_handler', $this->processorStack)) {
            $stack->push($this->processorStack['signal_handler'], $this->logger);
        }
        if (array_key_exists('max_messages', $this->processorStack)) {
            $stack->push($this->processorStack['max_messages'], $this->logger);
        }
        if (array_key_exists('max_execution_time', $this->processorStack)) {
            $stack->push($this->processorStack['max_execution_time'], $this->logger);
        }
        if (array_key_exists('exception_catcher', $this->processorStack) && !$input->getOption('no-catch')) {
            $stack->push($this->processorStack['exception_catcher'], $this->logger);
        }
        if (array_key_exists('ack', $this->processorStack)) {
            $stack->push($this->processorStack['ack'], $messageProvider, $this->logger);
        }

        if (array_key_exists('retry', $this->processorStack) && !$input->getOption('no-retry')) {
            $exchange = 'retry';
            if (isset($this->extras['retry_exchange'])) {
                $exchange = $this->extras['retry_exchange'];
            }
            $messagePublisher = $factory->getMessagePublisher($exchange, $connection);

            $stack->push($this->processorStack['retry'], $messagePublisher, $this->logger);
        }

        $processor = $stack->resolve($this->processor);

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setOptional(array('queue', 'connection'));

        $consumer = new Consumer($messageProvider, $processor, $optionsResolver);

        $consumer->consume($this->getOptions($input));
    }

    /**
     * getOptions
     *
     * @param InputInterface $input
     *
     * @return array
     */
    protected function getOptions(InputInterface $input)
    {
        $options = array(
            'queue'         => $input->getArgument('queue'),
            'connection'    => $input->getArgument('connection'),
            'poll_interval' => (int) $input->getOption('poll-interval'),
        );

        if ($input->hasOption('max-execution-time')) {
            $options['max_execution_time'] = (int) $input->getOption('max-execution-time');
        }
        if ($input->hasOption('max-messages')) {
            $options['max_messages'] = (int) $input->getOption('max-messages');
        }
        if ($input->hasOption('requeue-on-error')) {
            $options['requeue_on_error'] = (bool) $input->getOption('requeue-on-error');
        }

        if (array_key_exists('retry', $this->processorStack) && !$input->getOption('no-retry')) {
            $key = 'retry_%attempt%s';
            if (isset($this->extras['retry_routing_key_pattern'])) {
                $key = $this->extras['retry_routing_key_pattern'];
            }
            $key = str_replace('%queue%', $input->getArgument('queue'), $key);
            $options['retry_key_pattern'] = $key;

            $attempts = 3;
            if (isset($this->extras['retry_attempts'])) {
                $attempts = $this->extras['retry_attempts'];
            }
            $options['retry_attempts'] = $attempts;
        }

        return $options;
    }
}
