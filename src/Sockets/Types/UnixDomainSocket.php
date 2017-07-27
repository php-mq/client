<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Sockets\Types;

use PHPMQ\Client\Sockets\Interfaces\IdentifiesSocketAddress;

/**
 * Class UnixDomainAddress
 * @package PHPMQ\Client\Sockets\Types
 */
final class UnixDomainSocket implements IdentifiesSocketAddress
{
	/** @var string */
	private $socketPath;

	public function __construct( string $socketPath )
	{
		$this->socketPath = $socketPath;
	}

	public function getSocketAddress() : string
	{
		return sprintf( 'unix://%s', $this->socketPath );
	}
}
