<?php

namespace Infuse\IronMQ\Services;

class IronMQ
{
    public function __invoke($app)
    {
        return new \IronMQ($app['config']->get('ironmq'));
    }
}
