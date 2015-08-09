<?php

namespace app\iron;

use IronMQ;

class Controller
{
    use \InjectApp;

    public function middleware($req, $res)
    {
        // add routes
        $this->app->post('/iron/message', 'message')
                  ->get('/iron/install', 'setupQueues')
                  ->get('/iron/processQueues', 'processQueues');

        $this->app[ 'ironmq' ] = function ($c) {
            return new IronMQ($c[ 'config' ]->get('ironmq'));
        };
    }

    public function message($req, $res)
    {
        // verify auth token
        if ($req->query('auth') != $this->app[ 'config' ]->get('iron.auth_token')) {
            return $res->setCode(401);
        }

        // construct the message from the request
        $message = new \stdClass();
        $message->id = $req->headers('Iron_Message_Id');
        $message->body = json_decode($req->request());

        // grab the message from the post body
        $this->app[ 'queue' ]->receiveMessage($req->query('q'), $message);

        $res->setCode(200);
    }

    public function setupQueues($req, $res)
    {
        if (!$req->isCli()) {
            return $res->setCode(404);
        }

        if ($this->app[ 'queue' ]->install()) {
            foreach ($this->app[ 'queue' ]->pushQueueSubscribers() as $q => $subscribers) {
                echo "Installed $q with subscribers:\n";
                print_r($subscribers);
            }
        }
    }

    public function processQueues($req, $res)
    {
        if (!$req->isCli()) {
            return $res->setCode(404);
        }

        foreach ($this->app[ 'config' ]->get('queue.queues') as $q) {
            echo "Processing messages for $q queue:\n";

            $messages = $this->app[ 'queue' ]->dequeue($q, 10);

            print_r($messages);

            foreach ((array) $messages as $message) {
                $this->app[ 'queue' ]->receiveMessage($q, $message);
            }
        }
    }
}
