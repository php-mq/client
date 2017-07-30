<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Sockets\Types;

use PHPMQ\Client\Sockets\Types\UnixDomainSocket;
use PHPUnit\Framework\TestCase;

/**
 * Class UnixDomainSocketTest
 * @package PHPMQ\Client\Tests\Unit\Sockets\Types
 */
final class UnixDomainSocketTest extends TestCase
{
	public function testCanGetSocketAddress() : void
	{
		$unixSocket = new UnixDomainSocket( '/var/run/unit/test.sock' );

		$this->assertSame( 'unix:///var/run/unit/test.sock', $unixSocket->getSocketAddress() );
	}
}
