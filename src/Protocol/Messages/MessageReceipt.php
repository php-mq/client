<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Messages;

use PHPMQ\Client\Interfaces\IdentifiesMessage;
use PHPMQ\Client\Interfaces\IdentifiesQueue;
use PHPMQ\Client\Protocol\Constants\PacketType;
use PHPMQ\Client\Protocol\Constants\ProtocolVersion;
use PHPMQ\Client\Protocol\Headers\MessageHeader;
use PHPMQ\Client\Protocol\Headers\PacketHeader;
use PHPMQ\Client\Protocol\Interfaces\CarriesMessageData;
use PHPMQ\Client\Protocol\Interfaces\IdentifiesMessageType;
use PHPMQ\Client\Protocol\Types\MessageType;
use PHPMQ\Client\Traits\StringRepresenting;

/**
 * Class MessageReceipt
 * @package PHPMQ\Client\Protocol\Messages
 */
final class MessageReceipt implements CarriesMessageData
{
	use StringRepresenting;

	/** @var IdentifiesQueue */
	private $queueName;

	/** @var IdentifiesMessage */
	private $messageId;

	/** @var IdentifiesMessageType */
	private $messageType;

	public function __construct( IdentifiesQueue $queueName, IdentifiesMessage $messageId )
	{
		$this->queueName   = $queueName;
		$this->messageId   = $messageId;
		$this->messageType = new MessageType( MessageType::MESSAGE_RECEIPT );
	}

	public function getMessageType() : IdentifiesMessageType
	{
		return $this->messageType;
	}

	public function getQueueName() : IdentifiesQueue
	{
		return $this->queueName;
	}

	public function getMessageId() : IdentifiesMessage
	{
		return $this->messageId;
	}

	public function toString() : string
	{
		$messageHeader         = new MessageHeader( ProtocolVersion::VERSION_1, $this->messageType );
		$queuePacketHeader     = new PacketHeader( PacketType::QUEUE_NAME, strlen( (string)$this->queueName ) );
		$messageIdPacketHeader = new PacketHeader( PacketType::MESSAGE_ID, strlen( (string)$this->messageId ) );

		return $messageHeader
			. $queuePacketHeader
			. $this->queueName
			. $messageIdPacketHeader
			. $this->messageId;
	}
}
