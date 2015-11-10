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

use Infuse\Queue\Driver\IronDriver;
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
        $config = $this->app['config'];
        $ironDriver = new IronDriver($this->app);

        // build options
        $options = [];

        $queueType = $config->get('ironmq.queue_type');
        if ($queueType) {
            $options['type'] = $queueType;
        }

        // optional push queue options
        $baseUrl = $config->get('ironmq.push_listener');
        $authToken = $config->get('ironmq.auth_token');

        $success = true;
        $queues = $config->get('queue.queues');
        foreach ($queues as $queue) {
            if ($ironDriver->install($queue, $options, $baseUrl, $authToken)) {
                $output->writeln("Installed '$queue'");
            } else {
                $output->writeln("Could not install '$queue'");
                $success = false;
            }
        }

        return $success ? 0 : 1;
    }
}
