<?php

namespace Infuse\IronMQ;

use Infuse\HasApp;
use Infuse\Queue\Driver\IronDriver;
use Infuse\Queue;

class Controller
{
    use HasApp;

    public function message($req, $res)
    {
        // verify auth token
        if ($req->query('auth_token') != $this->app['config']->get('ironmq.auth_token')) {
            return $res->setCode(401);
        }

        // parse the message from the request
        $ironDriver = new IronDriver($this->app);
        $message = $ironDriver->buildMessageFromRequest($req);

        // notify the queue listeners of the message
        Queue::receiveMessage($message);
    }
}
