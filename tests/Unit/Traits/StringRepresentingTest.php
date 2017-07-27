<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Traits;

use PHPMQ\Client\Interfaces\RepresentsString;
use PHPMQ\Client\Traits\StringRepresenting;
use PHPUnit\Framework\TestCase;

/**
 * Class StringRepresentingTest
 * @package PHPMQ\MessageQueueServer\Tests\Unit\Traits
 */
final class StringRepresentingTest extends TestCase
{
	public function testCanRepresentValueAsString() : void
	{
		$implementation = new class implements RepresentsString
		{
			use StringRepresenting;

			public function toString() : string
			{
				return 'Unit-Test';
			}
		};

		$this->assertSame( 'Unit-Test', (string)$implementation );
		$this->assertSame( 'Unit-Test', $implementation->toString() );
		$this->assertSame( '"Unit-Test"', json_encode( $implementation ) );
	}
}
