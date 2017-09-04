<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Sockets;

use PHPMQ\Client\Exceptions\RuntimeException;
use PHPMQ\Client\Sockets\ClientSocket;
use PHPMQ\Client\Sockets\Interfaces\IdentifiesSocketAddress;
use PHPMQ\Client\Sockets\Types\NetworkSocket;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\SocketMocking;
use PHPMQ\Stream\Interfaces\TransfersData;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientSocketTest
 * @package PHPMQ\Client\Tests\Unit\Sockets
 */
final class ClientSocketTest extends TestCase
{
	use SocketMocking;

	protected function setUp() : void
	{
		$this->setUpServerSocket();
	}

	protected function tearDown() : void
	{
		$this->tearDownServerSocket();
	}

	public function testCanGetStream() : void
	{
		$networkSocket = new NetworkSocket( self::$SERVER_HOST, self::$SERVER_PORT );
		$clientSocket  = new ClientSocket( $networkSocket );

		$stream = $clientSocket->getStream();

		$this->assertInstanceOf( TransfersData::class, $stream );
	}

	public function testInvalidSocketAddressThrowsException() : void
	{
		$socketAddress = $this->getMockBuilder( IdentifiesSocketAddress::class )->getMockForAbstractClass();
		$socketAddress->expects( $this->any() )->method( 'getSocketAddress' )->willReturn( 'php://stdin' );

		/** @var IdentifiesSocketAddress $socketAddress */
		$clientSocket = new ClientSocket( $socketAddress );

		$this->expectException( RuntimeException::class );

		$clientSocket->getStream();
	}
}
