<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Run;

use PHPMQ\Client\Client;
use PHPMQ\Client\Sockets\ClientSocket;
use PHPMQ\Client\Sockets\Types\NetworkSocket;
use PHPMQ\Client\Types\QueueName;

require __DIR__ . '/../../vendor/autoload.php';

$clientSocket = new ClientSocket(
	new NetworkSocket( '192.168.3.13', 9100 )
);

$sender    = new Client( $clientSocket );
$queueName = new QueueName( $argv[1] );

$sender->sendMessage( $queueName, bin2hex( random_bytes( 256 ) ) );

echo "√ Sent message 'This is a first test\n";

$sender->sendMessage( $queueName, 'This is a second test' );

echo "√ Sent message 'This is a second test'";

$sender->disconnect();
