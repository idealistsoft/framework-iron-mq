<?php

use Infuse\IronMQ\Controller;
use Infuse\Application;
use Infuse\Queue;
use Infuse\Request;
use Infuse\Response;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    public static $received;

    public function testIron()
    {
        $app = new Application([
            'services' => [
                'ironmq' => 'Infuse\IronMQ\Services\IronMQ',
            ],
            'ironmq' => [
                'project_id' => 'test',
                'token' => 'token', ], ]);

        $this->assertInstanceOf('IronMQ\IronMQ', $app['ironmq']);
    }

    public function testMessageNoAuth()
    {
        $app = new Application([
            'ironmq' => [
                'auth_token' => 'secret', ], ]);

        $req = new Request(['auth_token' => 'wrong_secret']);

        $res = new Response();

        $controller = new Controller();
        $controller->setApp($app);

        $controller->message($req, $res);

        $this->assertEquals(401, $res->getCode());
    }

    public function testMessage()
    {
        $app = new Application([
            'ironmq' => [
                'auth_token' => 'secret', ], ]);

        $query = [
            'auth_token' => 'secret',
            'q' => 'test',
        ];
        $body = 'body';
        $req = new Request($query, $body, [], [], ['HTTP_IRON_MESSAGE_ID' => 'id']);

        $res = new Response();

        $controller = new Controller();
        $controller->setApp($app);

        Queue::listen('test', function ($message) {
            self::$received = $message;
        });

        $controller->message($req, $res);

        $this->assertEquals(200, $res->getCode());
        $this->assertInstanceOf('Infuse\Queue\Message', self::$received);
        $this->assertEquals('test', self::$received->getQueue()->getName());
        $this->assertEquals('id', self::$received->getId());
        $this->assertEquals('body', self::$received->getBody());
    }
}
