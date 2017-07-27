<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Sockets\Types;

use PHPMQ\Client\Sockets\Interfaces\IdentifiesSocketAddress;

/**
 * Class NetworkSocket
 * @package PHPMQ\Client\Sockets\Types
 */
final class NetworkSocket implements IdentifiesSocketAddress
{
	/** @var string */
	private $ipAddress;

	/** @var int */
	private $port;

	public function __construct( string $ipAddress, int $port )
	{
		$this->ipAddress = $ipAddress;
		$this->port      = $port;
	}

	public function getSocketAddress() : string
	{
		return sprintf( 'tcp://%s:%s', $this->ipAddress, $this->port );
	}
}
