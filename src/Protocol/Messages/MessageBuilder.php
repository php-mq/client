<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Messages;

use PHPMQ\Client\Protocol\Constants\PacketType;
use PHPMQ\Client\Protocol\Exceptions\MessageTypeNotImplementedException;
use PHPMQ\Client\Protocol\Exceptions\PacketCountMismatchException;
use PHPMQ\Client\Protocol\Headers\MessageHeader;
use PHPMQ\Client\Protocol\Interfaces\BuildsMessages;
use PHPMQ\Client\Protocol\Interfaces\CarriesMessageData;
use PHPMQ\Client\Protocol\Types\MessageType;
use PHPMQ\Client\Types\MessageId;
use PHPMQ\Client\Types\QueueName;

/**
 * Class MessageBuilder
 * @package PHPMQ\Client\Protocol\Messages
 */
final class MessageBuilder implements BuildsMessages
{
	public function buildMessage( MessageHeader $messageHeader, array $packets ) : CarriesMessageData
	{
		$this->guardPacketCountMatchesMessageType( $messageHeader, $packets );

		switch ( $messageHeader->getMessageType()->getType() )
		{
			case MessageType::MESSAGE_E2C:
			{
				return new MessageE2C(
					new MessageId( (string)$packets[ PacketType::MESSAGE_ID ] ),
					new QueueName( (string)$packets[ PacketType::QUEUE_NAME ] ),
					(string)$packets[ PacketType::MESSAGE_CONTENT ]
				);
				break;
			}

			case MessageType::MESSAGE_RECEIPT:
			{
				return new MessageReceipt(
					new QueueName( (string)$packets[ PacketType::QUEUE_NAME ] ),
					new MessageId( (string)$packets[ PacketType::MESSAGE_ID ] )
				);
				break;
			}

			default:
			{
				throw new MessageTypeNotImplementedException(
					'Message type not implemented: '
					. $messageHeader->getMessageType()->getType()
				);
			}
		}
	}

	private function guardPacketCountMatchesMessageType( MessageHeader $messageHeader, array $packets ) : void
	{
		if ( $messageHeader->getMessageType()->getPacketCount() !== count( $packets ) )
		{
			throw new PacketCountMismatchException(
				'Packet count does not match expectation of message type.'
			);
		}
	}
}
