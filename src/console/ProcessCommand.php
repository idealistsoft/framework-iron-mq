<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
namespace app\iron\console;

use Infuse\Queue;
use Infuse\Queue\Driver\IronDriver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCommand extends Command
{
    use \InjectApp;

    protected function configure()
    {
        $this
            ->setName('iron-process')
            ->setDescription('Processes messages from Iron.io message queues')
            ->addArgument(
                'n',
                InputArgument::OPTIONAL,
                'Number of messages to dequeue',
                10
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $n = $input->getArgument('n');
        $config = $this->app['config'];
        $ironDriver = new IronDriver($this->app);

        foreach ($config->get('queue.queues') as $q) {
            $output->writeln("Processing messages for '$q' queue:");

            $queue = new Queue($q);
            $messages = $queue->dequeue($n);

            $m = 0;
            foreach ($messages as $message) {
                $queue->receiveMessage($message);
                ++$m;
            }

            $output->writeln("- Processed $m message(s)");
        }

        return 0;
    }
}
