<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Tests\Unit\Protocol\Headers;

use PHPMQ\Client\Protocol\Constants\ProtocolVersion;
use PHPMQ\Client\Protocol\Headers\MessageHeader;
use PHPMQ\Client\Protocol\Types\MessageType;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageHeaderTest
 * @package PHPMQ\MessageQueueServer\Tests\Unit\Protocol\Headers
 */
final class MessageHeaderTest extends TestCase
{
	/**
	 * @param int         $version
	 * @param MessageType $messageType
	 * @param string      $expectedPacket
	 *
	 * @dataProvider messageTypeProvider
	 */
	public function testCanConvertMessageHeaderToString(
		int $version,
		MessageType $messageType,
		string $expectedPacket
	) : void
	{
		$header = new MessageHeader( $version, $messageType );

		$this->assertSame( 8, strlen( (string)$header ) );
		$this->assertSame( 8, strlen( $header->toString() ) );
		$this->assertSame( $expectedPacket, $header->toString() );
	}

	public function messageTypeProvider() : array
	{
		return [
			[
				'version'        => ProtocolVersion::VERSION_1,
				'messageType'    => new MessageType( MessageType::MESSAGE_C2E ),
				'expectedPacket' => 'H0100102',
			],
			[
				'version'        => ProtocolVersion::VERSION_1,
				'messageType'    => new MessageType( MessageType::CONSUME_REQUEST ),
				'expectedPacket' => 'H0100202',
			],
			[
				'version'        => ProtocolVersion::VERSION_1,
				'messageType'    => new MessageType( MessageType::MESSAGE_E2C ),
				'expectedPacket' => 'H0100303',
			],
			[
				'version'        => ProtocolVersion::VERSION_1,
				'messageType'    => new MessageType( MessageType::ACKNOWLEDGEMENT ),
				'expectedPacket' => 'H0100402',
			],
			[
				'version'        => ProtocolVersion::VERSION_1,
				'messageType'    => new MessageType( MessageType::MESSAGE_RECEIPT ),
				'expectedPacket' => 'H0100502',
			],
		];
	}

	/**
	 * @param string      $string
	 * @param int         $expectedVersion
	 * @param MessageType $expectedMessageType
	 *
	 * @dataProvider stringProvider
	 */
	public function testCanGetMessageHeaderFromString(
		string $string,
		int $expectedVersion,
		MessageType $expectedMessageType
	) : void
	{
		$messageHeader = MessageHeader::fromString( $string );

		$this->assertSame( $expectedVersion, $messageHeader->getVersion() );
		$this->assertSame( $expectedMessageType->getType(), $messageHeader->getMessageType()->getType() );
		$this->assertSame( $expectedMessageType->getPacketCount(), $messageHeader->getMessageType()->getPacketCount() );
		$this->assertSame( $string, $messageHeader->toString() );
	}

	public function stringProvider() : array
	{
		return [
			[
				'string'              => 'H0100102',
				'expectedVersion'     => ProtocolVersion::VERSION_1,
				'expetcedMessageType' => new MessageType( MessageType::MESSAGE_C2E ),
			],
			[
				'string'              => 'H0100202',
				'expectedVersion'     => ProtocolVersion::VERSION_1,
				'expetcedMessageType' => new MessageType( MessageType::CONSUME_REQUEST ),
			],
			[
				'string'              => 'H0100303',
				'expectedVersion'     => ProtocolVersion::VERSION_1,
				'expetcedMessageType' => new MessageType( MessageType::MESSAGE_E2C ),
			],
			[
				'string'              => 'H0100402',
				'expectedVersion'     => ProtocolVersion::VERSION_1,
				'expetcedMessageType' => new MessageType( MessageType::ACKNOWLEDGEMENT ),
			],
			[
				'string'              => 'H0100502',
				'expectedVersion'     => ProtocolVersion::VERSION_1,
				'expetcedMessageType' => new MessageType( MessageType::MESSAGE_RECEIPT ),
			],
		];
	}
}
