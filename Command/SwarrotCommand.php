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
    protected $logger;

    public function __construct($name, $connectionName, ProcessorInterface $processor, LoggerInterface $logger = null)
    {
        $this->name           = $name;
        $this->connectionName = $connectionName;
        $this->processor      = $processor;
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
            ->addOption('max-execution-time', 't', InputOption::VALUE_REQUIRED, 'Max execution time (seconds) before exit', 300)
            ->addOption('max-messages', 'm', InputOption::VALUE_REQUIRED, 'Max messages to process before exit', 300)
            ->addOption('requeue-on-error', 'r', InputOption::VALUE_NONE, 'Requeue in the same queue on error')
            ->addOption('no-catch', 'C', InputOption::VALUE_NONE, 'Deactivate exception catching.')
            ->addOption('poll-interval', null, InputOption::VALUE_REQUIRED, 'Poll interval (in micro-seconds)', 500000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue');
        $connection = $input->getArgument('connection');

        $factory = $this->getContainer()->get('swarrot.channel_factory.default');
        $messageProvider = $factory->getMessageProvider($queue, $connection);

        $stack = new Builder();

        //$stack->push('Cappl\Swarrot\Processor\CapplProcessor', $this->container);
        $stack->push('Swarrot\Processor\SignalHandler\SignalHandlerProcessor', $this->logger);

        if (!$input->getOption('no-catch')) {
            $stack->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor', $this->logger);
        }

        $stack
            ->push('Swarrot\Processor\MaxMessages\MaxMessagesProcessor', $this->logger)
            ->push('Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor', $this->logger)
            ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider, $this->logger)
        ;

        //$config = $this->container['config'];
        //if (isset($config['swarrot']['retry'])) {
            //$retryConfig = $config['swarrot']['retry'];
            //$messagePublisher = $this->getMessagePublisher(
                //$retryConfig['exchange'],
                //$input->getArgument('connection')
            //);

            //$stack->push('Swarrot\Processor\Retry\RetryProcessor', $messagePublisher, $logger);
        //}

        $processor = $stack->resolve($this->processor);

        $consumer = new Consumer($messageProvider, $processor);

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
            'max_messages'       => (int) $input->getOption('max-messages'),
            'max_execution_time' => (int) $input->getOption('max-execution-time'),
            'poll_interval'      => (int) $input->getOption('poll-interval'),
        );

        if ($input->getOption('requeue-on-error')) {
            $options['requeue_on_error'] = true;
        }

        return $options;
    }
}
