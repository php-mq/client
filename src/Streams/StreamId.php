<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Streams;

use PHPMQ\Client\Interfaces\IdentifiesStream;
use PHPMQ\Client\Traits\StringRepresenting;

/**
 * Class StreamId
 * @package PHPMQ\Client\Streams
 */
final class StreamId implements IdentifiesStream
{
	use StringRepresenting;

	/** @var string */
	private $streamId;

	public function __construct( string $streamId )
	{
		$this->streamId = $streamId;
	}

	public function toString() : string
	{
		return $this->streamId;
	}

	public function equals( IdentifiesStream $other ) : bool
	{
		return (get_class( $other ) === self::class && $other->toString() === $this->toString());
	}
}
