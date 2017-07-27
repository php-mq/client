<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Protocol\Messages;

use PHPMQ\Client\Protocol\Constants\PacketType;
use PHPMQ\Client\Protocol\Constants\ProtocolVersion;
use PHPMQ\Client\Protocol\Headers\MessageHeader;
use PHPMQ\Client\Protocol\Interfaces\CarriesMessageData;
use PHPMQ\Client\Protocol\Messages\MessageBuilder;
use PHPMQ\Client\Protocol\Messages\MessageE2C;
use PHPMQ\Client\Protocol\Messages\MessageReceipt;
use PHPMQ\Client\Protocol\Types\MessageType;
use PHPMQ\Client\Tests\Unit\Fixtures\Traits\MessageIdentifierMocking;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageBuilderTest
 * @package PHPMQ\MessageQueueServer\Tests\Unit\Protocol\Messages
 */
final class MessageBuilderTest extends TestCase
{
	use MessageIdentifierMocking;

	public function testCanBuildMessageE2C() : void
	{
		$builder = new MessageBuilder();

		$messageHeader = new MessageHeader(
			ProtocolVersion::VERSION_1,
			new MessageType( MessageType::MESSAGE_E2C )
		);

		$messageId = $this->getMessageId( 'Unit-Test-ID' );

		$packets = [
			PacketType::QUEUE_NAME      => 'Test-Queue',
			PacketType::MESSAGE_CONTENT => 'Unit-Test',
			PacketType::MESSAGE_ID      => $messageId->toString(),
		];

		/** @var MessageE2C $message */
		$message = $builder->buildMessage( $messageHeader, $packets );

		$this->assertInstanceOf( CarriesMessageData::class, $message );
		$this->assertInstanceOf( MessageE2C::class, $message );

		$this->assertSame( 'Test-Queue', $message->getQueueName()->toString() );
		$this->assertSame( 'Unit-Test', $message->getContent() );
		$this->assertSame( $messageId->toString(), $message->getMessageId()->toString() );
		$this->assertSame( MessageType::MESSAGE_E2C, $message->getMessageType()->getType() );
	}

	public function testCanBuildMessageReceipt() : void
	{
		$builder = new MessageBuilder();

		$messageHeader = new MessageHeader(
			ProtocolVersion::VERSION_1,
			new MessageType( MessageType::MESSAGE_RECEIPT )
		);

		$messageId = $this->getMessageId( 'Unit-Test-ID' );

		$packets = [
			PacketType::QUEUE_NAME => 'Test-Queue',
			PacketType::MESSAGE_ID => $messageId->toString(),
		];

		/** @var MessageReceipt $message */
		$message = $builder->buildMessage( $messageHeader, $packets );

		$this->assertInstanceOf( CarriesMessageData::class, $message );
		$this->assertInstanceOf( MessageReceipt::class, $message );

		$this->assertSame( 'Test-Queue', $message->getQueueName()->toString() );
		$this->assertSame( $messageId->toString(), $message->getMessageId()->toString() );
		$this->assertSame( MessageType::MESSAGE_RECEIPT, $message->getMessageType()->getType() );
	}

	/**
	 * @expectedException \PHPMQ\Client\Protocol\Exceptions\MessageTypeNotImplementedException
	 */
	public function testUnknowMessageTypeThrowsException() : void
	{
		$builder = new MessageBuilder();

		$messageHeader = new MessageHeader(
			ProtocolVersion::VERSION_1,
			new MessageType( 123 )
		);

		$builder->buildMessage( $messageHeader, [] );
	}

	/**
	 * @expectedException \PHPMQ\Client\Exceptions\LogicException
	 */
	public function testWrongPacketCountThrowsException() : void
	{
		$builder = new MessageBuilder();

		$messageHeader = new MessageHeader(
			ProtocolVersion::VERSION_1,
			new MessageType( MessageType::MESSAGE_C2E )
		);

		$builder->buildMessage( $messageHeader, [] );
	}
}
