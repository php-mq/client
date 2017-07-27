<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client;

use PHPMQ\Client\Exceptions\RuntimeException;
use PHPMQ\Client\Interfaces\IdentifiesMessage;
use PHPMQ\Client\Interfaces\IdentifiesQueue;
use PHPMQ\Client\Protocol\Constants\PacketLength;
use PHPMQ\Client\Protocol\Headers\MessageHeader;
use PHPMQ\Client\Protocol\Headers\PacketHeader;
use PHPMQ\Client\Protocol\Interfaces\BuildsMessages;
use PHPMQ\Client\Protocol\Interfaces\CarriesMessageData;
use PHPMQ\Client\Protocol\Messages\MessageBuilder;
use PHPMQ\Client\Protocol\Messages\MessageC2E;
use PHPMQ\Client\Protocol\Messages\MessageReceipt;
use PHPMQ\Client\Sockets\Interfaces\EstablishesStream;
use PHPMQ\Client\Streams\Constants\ChunkSize;
use PHPMQ\Client\Timers\TimeoutTimer;

/**
 * Class Client
 * @package PHPMQ\Client
 */
final class Client
{
	private const LOOP_WAIT_USEC  = 200000;

	private const RECEIPT_TIMEOUT = 10000000;

	/** @var EstablishesStream */
	private $clientSocket;

	/** @var BuildsMessages */
	private $messageBuilder;

	public function __construct( EstablishesStream $clientSocket )
	{
		$this->clientSocket   = $clientSocket;
		$this->messageBuilder = new MessageBuilder();
	}

	public function sendMessage( IdentifiesQueue $queueName, string $content ) : IdentifiesMessage
	{
		$message = new MessageC2E( $queueName, $content );

		return $this->writeMessage( $message );
	}

	private function writeMessage( CarriesMessageData $message ) : IdentifiesMessage
	{
		$stream       = $this->clientSocket->getStream();
		$timeoutTimer = new TimeoutTimer( self::RECEIPT_TIMEOUT );

		$stream->writeChunked( $message->toString(), ChunkSize::WRITE );

		$timeoutTimer->start();

		while ( !$timeoutTimer->timedOut() )
		{
			$reads  = [];
			$writes = $excepts = null;
			$stream->collectRawStream( $reads );

			if ( !@stream_select( $reads, $writes, $excepts, 0, self::LOOP_WAIT_USEC ) )
			{
				usleep( self::LOOP_WAIT_USEC );
				continue;
			}

			/** @var MessageReceipt $receipt */
			$receipt = $this->readMessageReceipt();

			return $receipt->getMessageId();
		}

		throw new RuntimeException( 'Reading message receipt timed out.' );
	}

	private function readMessageReceipt() : CarriesMessageData
	{
		$stream        = $this->clientSocket->getStream();
		$bytes         = $stream->read( PacketLength::MESSAGE_HEADER );
		$messageHeader = MessageHeader::fromString( $bytes );
		$packets       = [];

		for ( $i = 0; $i < $messageHeader->getMessageType()->getPacketCount(); $i++ )
		{
			$bytes = $stream->readChunked( PacketLength::PACKET_HEADER, ChunkSize::READ );

			$packetHeader = PacketHeader::fromString( $bytes );

			$bytes = $stream->readChunked( $packetHeader->getContentLength(), ChunkSize::READ );

			$packets[ $packetHeader->getPacketType() ] = $bytes;
		}

		return $this->messageBuilder->buildMessage( $messageHeader, $packets );
	}

	public function disconnect() : void
	{
		$stream = $this->clientSocket->getStream();
		$stream->shutDown();
		$stream->close();
	}
}
