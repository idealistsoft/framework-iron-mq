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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCommand extends Command
{
    use \InjectApp;

    protected function configure()
    {
        $this
            ->setName('iron-process')
            ->setDescription('Processes recent messages in Iron.io message queues');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->app['config'];
        $ironDriver = new IronDriver($this->app);

        foreach ($config->get('queue.queues') as $q) {
            $output->writeln("Processing messages for '$q' queue:");

            $queue = new Queue($q);
            $messages = $queue->dequeue(10);

            $n = 0;
            foreach ($messages as $message) {
                $queue->receiveMessage($message);
            }

            $output->writeln("- Processed $n message(s)");
        }

        return 0;
    }
}
