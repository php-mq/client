<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Builders;

use PHPMQ\Client\Builders\Exceptions\MessageTypeNotImplementedException;
use PHPMQ\Client\Builders\Exceptions\PacketCountMismatchException;
use PHPMQ\Client\Types\MessageId;
use PHPMQ\Client\Types\QueueName;
use PHPMQ\Protocol\Constants\PacketType;
use PHPMQ\Protocol\Interfaces\BuildsMessages;
use PHPMQ\Protocol\Interfaces\DefinesMessage;
use PHPMQ\Protocol\Interfaces\ProvidesMessageData;
use PHPMQ\Protocol\Messages\MessageServerToClient;
use PHPMQ\Protocol\Types\MessageType;

/**
 * Class MessageBuilder
 * @package PHPMQ\Client\Builders
 */
final class MessageBuilder implements BuildsMessages
{
	public function buildMessage( DefinesMessage $messageHeader, array $packets ) : ProvidesMessageData
	{
		$this->guardPacketCountMatchesMessageType( $messageHeader, $packets );

		$type = $messageHeader->getMessageType()->getType();

		if ( $type === MessageType::MESSAGE_SERVER_TO_CLIENT )
		{
			return new MessageServerToClient(
				new MessageId( (string)$packets[ PacketType::MESSAGE_ID ] ),
				new QueueName( (string)$packets[ PacketType::QUEUE_NAME ] ),
				(string)$packets[ PacketType::MESSAGE_CONTENT ]
			);
		}

		throw new MessageTypeNotImplementedException(
			'Message type not implemented: '
			. $messageHeader->getMessageType()->getType()
		);
	}

	private function guardPacketCountMatchesMessageType( DefinesMessage $messageHeader, array $packets ) : void
	{
		if ( $messageHeader->getMessageType()->getPacketCount() !== count( $packets ) )
		{
			throw new PacketCountMismatchException(
				'Packet count does not match expectation of message type.'
			);
		}
	}
}
