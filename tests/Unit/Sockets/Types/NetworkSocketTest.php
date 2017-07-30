<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Sockets\Types;

use PHPMQ\Client\Sockets\Types\NetworkSocket;
use PHPUnit\Framework\TestCase;

/**
 * Class NetworkSocketTest
 * @package PHPMQ\Client\Tests\Unit\Sockets\Types
 */
final class NetworkSocketTest extends TestCase
{
	public function testCanGetSocketAddress() : void
	{
		$networkSocket = new NetworkSocket( '127.0.0.1', 9100 );

		$this->assertSame( 'tcp://127.0.0.1:9100', $networkSocket->getSocketAddress() );
	}
}
