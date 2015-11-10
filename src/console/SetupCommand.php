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

        $baseUrl = $config->get('ironmq.push_listener');
        if (!$baseUrl) {
            $output->writeln('Cannot install iron.io push queues because the ironmq.push_listener setting has not been set');

            return 1;
        }

        $authToken = $config->get('ironmq.auth_token');
        if (!$authToken) {
            $output->writeln('Cannot install iron.io push queues because the ironmq.auth_token setting has not been set');

            return 1;
        }

        $queues = $config->get('queue.queues');
        $pushType = $config->get('ironmq.push_type');
        if (!$pushType) {
            $pushType = 'unicast';
        }

        $ironDriver = new IronDriver($this->app);
        if (!$ironDriver->install($queues, $baseUrl, $authToken, $pushType)) {
            $output->writeln('Could not install iron.io push queues.');

            return 1;
        }

        foreach ($queues as $queue) {
            $url = $ironDriver->getPushQueueUrl($queue, $baseUrl, $authToken);
            $output->writeln("Installed '$queue' queue with listener: $url");
        }

        $output->writeln('Iron.io push queues installed successfully');

        return 0;
    }
}
