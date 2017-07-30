<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Sockets;

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
		$serverSocket  = new ClientSocket( $networkSocket );

		$stream = $serverSocket->getStream();

		$this->assertInstanceOf( TransfersData::class, $stream );
	}

	/**
	 * @expectedException \PHPMQ\Client\Exceptions\RuntimeException
	 */
	public function testInvalidSocketAddressThrowsException() : void
	{
		$socketAddress = $this->getMockBuilder( IdentifiesSocketAddress::class )->getMockForAbstractClass();
		$socketAddress->expects( $this->any() )->method( 'getSocketAddress' )->willReturn( 'php://stdin' );

		/** @var IdentifiesSocketAddress $socketAddress */
		$serverSocket = new ClientSocket( $socketAddress );

		$serverSocket->getStream();
	}
}
