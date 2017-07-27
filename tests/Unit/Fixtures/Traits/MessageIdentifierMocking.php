<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Fixtures\Traits;

use PHPMQ\Client\Interfaces\IdentifiesMessage;
use PHPMQ\Client\Traits\StringRepresenting;

/**
 * Trait MessageIdentifierMocking
 * @package PHPMQ\Client\Tests\Unit\Fixtures\Traits
 */
trait MessageIdentifierMocking
{
	protected function getMessageId( string $messageId ) : IdentifiesMessage
	{
		return new class($messageId) implements IdentifiesMessage
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
		};
	}
}
