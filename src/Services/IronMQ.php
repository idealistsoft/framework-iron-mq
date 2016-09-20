<?php

namespace Infuse\IronMQ\Services;

use IronMQ\IronMQ as Client;

class IronMQ
{
    public function __invoke($app)
    {
        return new Client($app['config']->get('ironmq'));
    }
}
