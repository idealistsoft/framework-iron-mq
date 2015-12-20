<?php

namespace App\Iron\Services;

class IronMQ
{
    public function __invoke($app)
    {
        return new \IronMQ($app['config']->get('ironmq'));
    }
}
