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
        foreach ($this->app['config']->get('queue.queues') as $q) {
            $output->writeln("Processing messages for $q queue:");

            $messages = $this->app['queue']->dequeue($q, 10);

            foreach ((array) $messages as $message) {
                $this->app['queue']->receiveMessage($q, $message);
            }
        }

        return 0;
    }
}
