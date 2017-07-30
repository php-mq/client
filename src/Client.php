<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client;

use PHPMQ\Client\Sockets\Interfaces\EstablishesStream;
use PHPMQ\Client\Streams\Constants\ChunkSize;
use PHPMQ\Protocol\Interfaces\IdentifiesQueue;
use PHPMQ\Protocol\Interfaces\ProvidesMessageData;
use PHPMQ\Protocol\Messages\MessageClientToServer;

/**
 * Class Client
 * @package PHPMQ\Client
 */
final class Client
{
	/** @var EstablishesStream */
	private $clientSocket;

	public function __construct( EstablishesStream $clientSocket )
	{
		$this->clientSocket = $clientSocket;
	}

	public function sendMessage( IdentifiesQueue $queueName, string $content ) : void
	{
		$message = new MessageClientToServer( $queueName, $content );

		$this->writeMessage( $message );
	}

	private function writeMessage( ProvidesMessageData $message ) : void
	{
		$stream = $this->clientSocket->getStream();

		$stream->writeChunked( $message->toString(), ChunkSize::WRITE );
	}

	public function disconnect() : void
	{
		$stream = $this->clientSocket->getStream();
		$stream->shutDown();
		$stream->close();
	}
}
