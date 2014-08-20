<?php

namespace app\iron;

use IronMQ;

use App;

class Controller
{
	public static $properties = [
		'routes' => [
			'post /iron/message' => 'message',
			'get /iron/install' => 'setupQueues',
			'get /iron/processQueues' => 'processQueues',
		]
	];

	private $app;

	function __construct( App $app )
	{
		$this->app = $app;
	}

	function middleware( $req, $res )
	{
		$this->app[ 'ironmq' ] = function( $c ) {
			return new IronMQ( [
				'token' => $c[ 'config' ]->get( 'queue.token' ),
				'project_id' => $c[ 'config' ]->get( 'queue.project' ) ] );
		};
	}

	function message( $req, $res )
	{
		// verify auth token
		if( $req->query( 'auth' ) != $this->app[ 'config' ]->get( 'iron.auth_token' ) )
			return $res->setCode( 401 );

		// construct the message from the request
		$message = new \stdClass;
		$message->id = $req->headers( 'Iron_Message_Id' );
		$message->body = json_decode( $req->request() );

		// grab the message from the post body
		$this->app[ 'queue' ]->receiveMessage( $req->query( 'q' ), $message );

		$res->setCode( 200 );
	}

    function setupQueues( $req, $res )
    {
    	if( !$req->isCli() )
    		return $res->setCode( 404 );

    	if( $this->app[ 'queue' ]->install() )
    	{
    		foreach( $this->app[ 'queue' ]->subscribers() as $q => $subscribers )
    		{
				echo "Installed $q with subscribers:\n";
				print_r( $subscribers );
    		}
    	}
    }

    function processQueues( $req, $res )
    {
    	if( !$req->isCli() )
    		return $res->setCode( 404 );

		foreach( $this->app[ 'config' ]->get( 'queue.queues' ) as $q )
		{
			echo "Processing messages for $q queue:\n";

			$messages = $this->app[ 'queue' ]->dequeue( $q, 10 );

			print_r( $messages );

			foreach( (array)$messages as $message )
				$this->app[ 'queue' ]->receiveMessage( $q, $message );
		}
    }
}