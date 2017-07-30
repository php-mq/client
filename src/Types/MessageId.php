<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Types;

use PHPMQ\Client\Traits\StringRepresenting;
use PHPMQ\Protocol\Interfaces\IdentifiesMessage;

/**
 * Class MessageId
 * @package PHPMQ\Client\Types
 */
final class MessageId implements IdentifiesMessage
{
	use StringRepresenting;

	/** @var string */
	private $messageId;

	public function __construct( string $messageId )
	{
		$this->messageId = $messageId;
	}

	public function toString() : string
	{
		return $this->messageId;
	}

	public function equals( IdentifiesMessage $other ) : bool
	{
		return ($other->toString() === $this->toString());
	}
}
