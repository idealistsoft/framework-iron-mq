<?php

namespace App\Iron;

use Infuse\Queue\Driver\IronDriver;
use Infuse\Queue;

class Controller
{
    use \InjectApp;

    public function middleware($req, $res)
    {
        // add routes
        $this->app->post('/iron/message', ['App\Iron\Controller', 'message']);
    }

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

        $res->setCode(200);
    }
}
