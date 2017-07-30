<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Builders;

use PHPMQ\Client\Builders\MessageBuilder;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\MessageIdentifierMocking;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\QueueIdentifierMocking;
use PHPMQ\Protocol\Constants\PacketType;
use PHPMQ\Protocol\Constants\ProtocolVersion;
use PHPMQ\Protocol\Interfaces\DefinesMessage;
use PHPMQ\Protocol\Messages\MessageServerToClient;
use PHPMQ\Protocol\Types\MessageType;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageBuilderTest
 * @package PHPMQ\Client\Tests\Unit\Builders
 */
final class MessageBuilderTest extends TestCase
{
	use QueueIdentifierMocking;
	use MessageIdentifierMocking;

	public function testCanBuildMessageServerToClient() : void
	{
		$builder       = new MessageBuilder();
		$queueName     = $this->getQueueName( 'Unit-Test-Queue' );
		$messageId     = $this->getMessageId( 'Unit-Test-ID' );
		$messageType   = new MessageType( MessageType::MESSAGE_SERVER_TO_CLIENT );
		$messageHeader = $this->getMockBuilder( DefinesMessage::class )->getMockForAbstractClass();
		$messageHeader->expects( $this->any() )->method( 'getMessageType' )->willReturn( $messageType );
		$messageHeader->expects( $this->any() )->method( 'getProtocolVersion' )->willReturn(
			ProtocolVersion::VERSION_1
		);

		$packets = [
			PacketType::MESSAGE_ID      => $messageId->toString(),
			PacketType::QUEUE_NAME      => $queueName->toString(),
			PacketType::MESSAGE_CONTENT => 'Unit-Test',
		];

		/** @var DefinesMessage $messageHeader */
		/** @var MessageServerToClient $message */
		$message = $builder->buildMessage( $messageHeader, $packets );

		$this->assertInstanceOf( MessageServerToClient::class, $message );
		$this->assertEquals( $messageType, $message->getMessageType() );
		$this->assertTrue( $queueName->equals( $message->getQueueName() ) );
		$this->assertTrue( $messageId->equals( $message->getMessageId() ) );
		$this->assertSame( 'Unit-Test', $message->getContent() );
	}

	/**
	 * @expectedException \PHPMQ\Client\Builders\Exceptions\MessageTypeNotImplementedException
	 */
	public function testNotImplementedMessageTypeThrowsException() : void
	{
		$builder       = new MessageBuilder();
		$messageType   = new MessageType( 99999 );
		$messageHeader = $this->getMockBuilder( DefinesMessage::class )->getMockForAbstractClass();
		$messageHeader->expects( $this->any() )->method( 'getMessageType' )->willReturn( $messageType );
		$messageHeader->expects( $this->any() )->method( 'getProtocolVersion' )->willReturn(
			ProtocolVersion::VERSION_1
		);

		/** @var DefinesMessage $messageHeader */
		$builder->buildMessage( $messageHeader, [] );
	}

	/**
	 * @expectedException \PHPMQ\Client\Builders\Exceptions\PacketCountMismatchException
	 */
	public function testPacketCountMismatchThrowsException() : void
	{
		$builder       = new MessageBuilder();
		$messageType   = new MessageType( MessageType::MESSAGE_SERVER_TO_CLIENT );
		$messageHeader = $this->getMockBuilder( DefinesMessage::class )->getMockForAbstractClass();
		$messageHeader->expects( $this->any() )->method( 'getMessageType' )->willReturn( $messageType );
		$messageHeader->expects( $this->any() )->method( 'getProtocolVersion' )->willReturn(
			ProtocolVersion::VERSION_1
		);

		/** @var DefinesMessage $messageHeader */
		$builder->buildMessage( $messageHeader, [] );
	}
}
