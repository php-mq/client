<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Types;

use PHPMQ\Client\Interfaces\IdentifiesQueue;
use PHPMQ\Client\Traits\StringRepresenting;

/**
 * Class QueueName
 * @package PHPMQ\Client\Types
 */
final class QueueName implements IdentifiesQueue
{
	use StringRepresenting;

	/** @var string */
	private $queueName;

	public function __construct( string $queueName )
	{
		$this->queueName = $queueName;
	}

	public function toString() : string
	{
		return $this->queueName;
	}

	public function equals( IdentifiesQueue $other ) : bool
	{
		return ($this->toString() === $other->toString());
	}
}
