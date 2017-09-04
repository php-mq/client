<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Run;

use PHPMQ\Client\Client;
use PHPMQ\Client\Sockets\ClientSocket;
use PHPMQ\Client\Sockets\Types\NetworkSocket;
use PHPMQ\Client\Types\QueueName;
use PHPMQ\Protocol\Messages\MessageServerToClient;

require __DIR__ . '/../../vendor/autoload.php';

$clientSocket = new ClientSocket(
	new NetworkSocket( '192.168.3.13', 9100 )
);

$client    = new Client( $clientSocket );
$queueName = new QueueName( $argv[1] );

$client->requestMessages( $queueName, (int)($argv[2] ?? 1) );

$client->registerMessageHandlers(
	function ( MessageServerToClient $message, Client $client )
	{
		echo 'Received:' . $message->toString() . "\n\n";

		$client->acknowledgeMessage( $message->getQueueName(), $message->getMessageId() );
	}
);

$client->handleMessages();
