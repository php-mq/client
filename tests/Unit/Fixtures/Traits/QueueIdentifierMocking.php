<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Fixtures\Traits;

use PHPMQ\Client\Interfaces\IdentifiesQueue;
use PHPMQ\Client\Traits\StringRepresenting;

/**
 * Trait QueueIdentifierMocking
 * @package PHPMQ\Client\Tests\Unit\Fixtures\Traits
 */
trait QueueIdentifierMocking
{
	protected function getQueueName( string $queueName ) : IdentifiesQueue
	{
		return new class($queueName) implements IdentifiesQueue
		{
			use StringRepresenting;

			/** @var string */
			private $queueName;

			public function __construct( string $queueName )
			{
				$this->queueName = $queueName;
			}

			public function equals( IdentifiesQueue $other ) : bool
			{
				return ($other->toString() === $this->toString());
			}

			public function toString() : string
			{
				return $this->queueName;
			}
		};
	}
}
