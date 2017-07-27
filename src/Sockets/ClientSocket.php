<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Sockets;

use PHPMQ\Client\Exceptions\RuntimeException;
use PHPMQ\Client\Interfaces\TransfersData;
use PHPMQ\Client\Sockets\Interfaces\EstablishesStream;
use PHPMQ\Client\Sockets\Interfaces\IdentifiesSocketAddress;
use PHPMQ\Client\Streams\Stream;

/**
 * Class ClientSocket
 * @package PHPMQ\Client\Sockets
 */
final class ClientSocket implements EstablishesStream
{
	/** @var resource */
	private $stream;

	/** @var string */
	private $socketAddress;

	public function __construct( IdentifiesSocketAddress $socketAddress )
	{
		$this->socketAddress = $socketAddress;
	}

	/**
	 * @return resource
	 */
	private function establishSocket()
	{
		$errorNumber = $errorString = null;

		$socket = @stream_socket_client(
			$this->socketAddress->getSocketAddress(),
			$errorNumber,
			$errorString
		);

		$this->guardSocketEstablished( $socket, $errorNumber, $errorString );

		return $socket;
	}

	private function guardSocketEstablished( $socket, ?int $errorNumber, ?string $errorString ) : void
	{
		if ( false === $socket )
		{
			throw new RuntimeException(
				sprintf(
					'Could not establish server socket at %s: %s [%s].',
					$this->socketAddress->getSocketAddress(),
					$errorString,
					$errorNumber
				)
			);
		}
	}

	private function makeSocketNonBlocking( $socket ) : void
	{
		if ( !stream_set_blocking( $socket, false ) )
		{
			throw new RuntimeException(
				sprintf(
					'Could not set server socket at %s to non-blocking.',
					$this->socketAddress->getSocketAddress()
				)
			);
		}
	}

	public function getStream() : TransfersData
	{
		if ( null === $this->stream )
		{
			$socket = $this->establishSocket();
			$this->makeSocketNonBlocking( $socket );

			$this->stream = new Stream( $socket );
		}

		return $this->stream;
	}
}
