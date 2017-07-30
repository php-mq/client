<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit;

use PHPMQ\Client\Client;
use PHPMQ\Client\Sockets\ClientSocket;
use PHPMQ\Client\Sockets\Types\NetworkSocket;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\QueueIdentifierMocking;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\SocketMocking;
use PHPMQ\Stream\Stream;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 * @package PHPMQ\Client\Tests\Unit
 */
final class ClientTest extends TestCase
{
	use SocketMocking;
	use QueueIdentifierMocking;

	protected function setUp() : void
	{
		$this->setUpServerSocket();
	}

	protected function tearDown() : void
	{
		$this->tearDownServerSocket();
	}

	public function testCanSendMessageToServer() : void
	{
		$serverStream    = new Stream( $this->serverSocket );
		$clientSocket    = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client          = new Client( $clientSocket );
		$queueName       = $this->getQueueName( 'Unit-Test-Queue' );
		$expectedMessage = 'H0100102P0100000000000000000000000000015'
		                   . 'Unit-Test-Queue'
		                   . 'P0200000000000000000000000000009'
		                   . 'Unit-Test';

		$client->sendMessage( $queueName, 'Unit-Test' );

		$serverClientStream = $serverStream->acceptConnection();

		$message = $serverClientStream->read( 1024 );

		$this->assertSame( $expectedMessage, $message );

		$serverClientStream->close();
		$client->disconnect();
	}
}
