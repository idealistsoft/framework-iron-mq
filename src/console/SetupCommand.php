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

class SetupCommand extends Command
{
    use \InjectApp;

    protected function configure()
    {
        $this
            ->setName('iron-setup')
            ->setDescription('Installs Iron.io message queues');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->app['queue']->install()) {
            $output->writeln('Could not setup queues.');

            return 1;
        }

        foreach ($this->app['queue']->pushQueueSubscribers() as $q => $listener) {
            $output->writeln("Installed $q with listener {$listener['url']}");
        }

        $output->writeln('Queues installed successfully');

        return 0;
    }
}
