<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client;

use PHPMQ\Client\Builders\MessageBuilder;
use PHPMQ\Client\Exceptions\ServerDisconnectedException;
use PHPMQ\Client\Sockets\Interfaces\EstablishesStream;
use PHPMQ\Protocol\Constants\PacketLength;
use PHPMQ\Protocol\Interfaces\BuildsMessages;
use PHPMQ\Protocol\Interfaces\DefinesMessage;
use PHPMQ\Protocol\Interfaces\IdentifiesMessage;
use PHPMQ\Protocol\Interfaces\IdentifiesQueue;
use PHPMQ\Protocol\Interfaces\ProvidesMessageData;
use PHPMQ\Protocol\Messages\Acknowledgement;
use PHPMQ\Protocol\Messages\ConsumeRequest;
use PHPMQ\Protocol\Messages\MessageClientToServer;
use PHPMQ\Protocol\Messages\MessageServerToClient;
use PHPMQ\Protocol\Types\MessageHeader;
use PHPMQ\Protocol\Types\PacketHeader;
use PHPMQ\Stream\Constants\ChunkSize;
use PHPMQ\Stream\Interfaces\TransfersData;

/**
 * Class Client
 * @package PHPMQ\Client
 */
final class Client
{
	private const STREAM_SELECT_TIMEOUT_USEC = 200000;

	/** @var EstablishesStream */
	private $clientSocket;

	/** @var array|callable[] */
	private $messageHandlers;

	/** @var BuildsMessages */
	private $messageBuilder;

	/** @var bool */
	private $handlingStarted = false;

	public function __construct( EstablishesStream $clientSocket )
	{
		$this->clientSocket    = $clientSocket;
		$this->messageHandlers = [];
		$this->messageBuilder  = new MessageBuilder();
	}

	public function sendMessage( IdentifiesQueue $queueName, string $content ) : void
	{
		$message = new MessageClientToServer( $queueName, $content );

		$this->sendToServer( $message );
	}

	private function sendToServer( ProvidesMessageData $message ) : void
	{
		$stream = $this->clientSocket->getStream();

		$stream->writeChunked( $message->toString(), ChunkSize::WRITE );
	}

	public function requestMessages( IdentifiesQueue $queueName, int $amount ) : void
	{
		$consumeRequest = new ConsumeRequest( $queueName, $amount );

		$this->sendToServer( $consumeRequest );
	}

	public function registerMessageHandlers( callable ...$messageHandlers ) : void
	{
		$this->messageHandlers = array_merge( $this->messageHandlers, $messageHandlers );
	}

	public function handleMessages() : void
	{
		$this->registerSignalHandler();

		$this->handlingStarted = true;

		declare(ticks=1);

		while ( $this->handlingStarted )
		{
			$messages = $this->readMessages();

			$this->processMessages( $messages );
		}
	}

	private function registerSignalHandler() : void
	{
		if ( function_exists( 'pcntl_signal' ) )
		{
			pcntl_signal( SIGTERM, [$this, 'shutDownBySignal'] );
			pcntl_signal( SIGINT, [$this, 'shutDownBySignal'] );
		}
	}

	public function shutDownBySignal( int $signal ) : void
	{
		if ( in_array( $signal, [SIGINT, SIGTERM, SIGKILL], true ) )
		{
			$this->disconnect();
		}
	}

	public function disconnect() : void
	{
		$stream = $this->clientSocket->getStream();
		$stream->shutDown();
		$stream->close();
	}

	public function readMessages() : \Generator
	{
		$stream = $this->clientSocket->getStream();

		if ( !$this->isStreamActive( $stream ) )
		{
			return;
		}

		do
		{
			$headerBytes = $stream->read( PacketLength::MESSAGE_HEADER );

			$this->guardReadBytes( $headerBytes );

			$messageHeader = MessageHeader::fromString( $headerBytes );

			yield $this->readMessage( $messageHeader, $stream );
		}
		while ( $stream->hasUnreadBytes() );
	}

	private function isStreamActive( TransfersData $stream ) : bool
	{
		$readStreams  = [];
		$writeStreams = null;
		$except       = null;

		$stream->collectRawStream( $readStreams );

		usleep( self::STREAM_SELECT_TIMEOUT_USEC );

		$active = @stream_select( $readStreams, $writeStreams, $except, 0, self::STREAM_SELECT_TIMEOUT_USEC );

		return (bool)$active;
	}

	/**
	 * @param bool|null|int $bytes
	 *
	 * @throws \PHPMQ\Client\Exceptions\ServerDisconnectedException
	 */
	private function guardReadBytes( $bytes ) : void
	{
		if ( !$bytes )
		{
			throw new ServerDisconnectedException( 'Message queue server disconnected.' );
		}
	}

	/**
	 * @param DefinesMessage $messageHeader
	 * @param TransfersData  $stream
	 *
	 * @throws \PHPMQ\Client\Exceptions\ServerDisconnectedException
	 * @throws \PHPMQ\Stream\Exceptions\ReadTimedOutException
	 * @return ProvidesMessageData
	 */
	private function readMessage( DefinesMessage $messageHeader, TransfersData $stream ) : ProvidesMessageData
	{
		$packetCount = $messageHeader->getMessageType()->getPacketCount();
		$packets     = [];
		for ( $i = 0; $i < $packetCount; $i++ )
		{
			$bytes        = $stream->readChunked(
				PacketLength::PACKET_HEADER,
				ChunkSize::READ
			);
			$packetHeader = PacketHeader::fromString( $bytes );
			$bytes        = $stream->readChunked( $packetHeader->getContentLength(), ChunkSize::READ );

			$packets[ $packetHeader->getPacketType() ] = $bytes;
		}

		return $this->messageBuilder->buildMessage( $messageHeader, $packets );
	}

	private function processMessages( iterable $messages ) : void
	{
		foreach ( $messages as $message )
		{
			$this->notifyMessageHandlers( $message );
		}
	}

	private function notifyMessageHandlers( MessageServerToClient $message ) : void
	{
		foreach ( $this->messageHandlers as $messageHandler )
		{
			$messageHandler( $message, $this );
		}
	}

	public function acknowledgeMessage( IdentifiesQueue $queueName, IdentifiesMessage $messageId ) : void
	{
		$acknowledgement = new Acknowledgement( $queueName, $messageId );

		$this->sendToServer( $acknowledgement );
	}

	public function stopHandlingMessages() : void
	{
		$this->handlingStarted = false;
	}
}
