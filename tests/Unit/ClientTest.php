<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit;

use PHPMQ\Client\Client;
use PHPMQ\Client\Exceptions\ServerDisconnectedException;
use PHPMQ\Client\Sockets\ClientSocket;
use PHPMQ\Client\Sockets\Types\NetworkSocket;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\MessageIdentifierMocking;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\QueueIdentifierMocking;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\SocketMocking;
use PHPMQ\Protocol\Messages\MessageServerToClient;
use PHPMQ\Stream\Constants\ChunkSize;
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
	use MessageIdentifierMocking;

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
		$expectedMessage = 'H0100102'
		                   . 'P0100000000000000000000000000015'
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

	public function testCanRequestMessagesFromServer() : void
	{
		$serverStream    = new Stream( $this->serverSocket );
		$clientSocket    = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client          = new Client( $clientSocket );
		$queueName       = $this->getQueueName( 'Unit-Test-Queue' );
		$expectedMessage = 'H0100202'
		                   . 'P0100000000000000000000000000015'
		                   . 'Unit-Test-Queue'
		                   . 'P0400000000000000000000000000001'
		                   . '5';

		$client->requestMessages( $queueName, 5 );

		$serverClientStream = $serverStream->acceptConnection();

		$message = $serverClientStream->read( 1024 );

		$this->assertSame( $expectedMessage, $message );

		$serverClientStream->close();
		$client->disconnect();
	}

	public function testCanHandleMessagesByCallback() : void
	{
		$serverStream = new Stream( $this->serverSocket );
		$clientSocket = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client       = new Client( $clientSocket );
		$queueName    = $this->getQueueName( 'Unit-Test-Queue' );
		$messageId    = $this->getMessageId( 'Unit-Test-ID' );
		$message      = new MessageServerToClient(
			$messageId,
			$queueName,
			'Hello World'
		);

		$expectedMessage = 'H0100303'
		                   . 'P0100000000000000000000000000015'
		                   . 'Unit-Test-Queue'
		                   . 'P0200000000000000000000000000011'
		                   . 'Hello World'
		                   . 'P0300000000000000000000000000012'
		                   . 'Unit-Test-ID';

		$client->registerMessageHandlers(
			function ( MessageServerToClient $message, Client $client )
			{
				echo $message->toString();

				$client->stopHandlingMessages();
			}
		);

		$client->requestMessages( $queueName, 1 );

		$serverClientStream = $serverStream->acceptConnection();
		$serverClientStream->writeChunked( $message->toString(), ChunkSize::WRITE );

		$client->handleMessages();

		$this->expectOutputString( $expectedMessage );

		$serverClientStream->close();
		$client->disconnect();
	}

	public function testCanAcknowledgeMessages() : void
	{
		$serverStream    = new Stream( $this->serverSocket );
		$clientSocket    = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client          = new Client( $clientSocket );
		$queueName       = $this->getQueueName( 'Unit-Test-Queue' );
		$messageId       = $this->getMessageId( 'Unit-Test-ID' );
		$expectedMessage = 'H0100402'
		                   . 'P0100000000000000000000000000015'
		                   . 'Unit-Test-Queue'
		                   . 'P0300000000000000000000000000012'
		                   . 'Unit-Test-ID';

		$client->acknowledgeMessage( $queueName, $messageId );

		$serverClientStream = $serverStream->acceptConnection();

		$message = $serverClientStream->read( 1024 );

		$this->assertSame( $expectedMessage, $message );

		$serverClientStream->close();
		$client->disconnect();
	}

	public function testCanPushBackMessage() : void
	{
		$serverStream          = new Stream( $this->serverSocket );
		$clientSocket          = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client                = new Client( $clientSocket );
		$queueName             = $this->getQueueName( 'Unit-Test-Queue' );
		$messageId             = $this->getMessageId( 'Unit-Test-ID' );
		$messageServerToClient = new MessageServerToClient( $messageId, $queueName, 'Unit-Test' );

		$expectedAcknowledgement = 'H0100402'
		                           . 'P0100000000000000000000000000015'
		                           . 'Unit-Test-Queue'
		                           . 'P0300000000000000000000000000012'
		                           . 'Unit-Test-ID';

		$expectedMessage = 'H0100102'
		                   . 'P0100000000000000000000000000015'
		                   . 'Unit-Test-Queue'
		                   . 'P0200000000000000000000000000009'
		                   . 'Unit-Test';

		$client->pushBackMessage( $messageServerToClient );

		$serverClientStream = $serverStream->acceptConnection();

		$acknowledgement = $serverClientStream->read( 99 );
		$message         = $serverClientStream->read( 96 );

		$this->assertSame( $expectedAcknowledgement, $acknowledgement );
		$this->assertSame( $expectedMessage, $message );

		$serverClientStream->close();
		$client->disconnect();
	}

	public function testReadMessagesReturnsIfStreamIsNotActive() : void
	{
		$clientSocket = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client       = new Client( $clientSocket );

		$this->assertSame( 0, iterator_count( $client->readMessages() ) );

		$client->disconnect();
	}

	public function testServerDisconnectThrowsException() : void
	{
		$serverStream = new Stream( $this->serverSocket );
		$clientSocket = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client       = new Client( $clientSocket );
		$queueName    = $this->getQueueName( 'Unit-Test-Queue' );

		$client->requestMessages( $queueName, 1 );

		$serverClientStream = $serverStream->acceptConnection();
		$serverClientStream->shutDown();
		$serverClientStream->close();

		$this->expectException( ServerDisconnectedException::class );

		iterator_count( $client->readMessages() );
	}

	/**
	 * @param int $signal
	 *
	 * @dataProvider signalProvider
	 */
	public function testCanShutdownBySignal( int $signal ) : void
	{
		$clientSocket = new ClientSocket( new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT ) );
		$client       = new Client( $clientSocket );

		$streams = [];
		$clientSocket->getStream()->collectRawStream( $streams );

		$this->assertTrue( is_resource( reset( $streams ) ) );

		$client->shutDownBySignal( $signal );

		$streams = [];
		$clientSocket->getStream()->collectRawStream( $streams );

		$this->assertFalse( is_resource( reset( $streams ) ) );
	}

	public function signalProvider() : array
	{
		return [
			[ SIGINT ],
			[ SIGTERM ],
			[ SIGKILL ],
		];
	}
}
