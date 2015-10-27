<?php

namespace app\iron;

use IronMQ;

class Controller
{
    use \InjectApp;

    public function middleware($req, $res)
    {
        // add routes
        $this->app->post('/iron/message', ['iron\\Controller', 'message']);

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
        $this->app['queue']->receiveMessage($req->query('q'), $message);

        $res->setCode(200);
    }
}
