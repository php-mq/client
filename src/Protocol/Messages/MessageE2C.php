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
 * Class MessageE2C
 * @package PHPMQ\Client\Protocol\Messages
 */
final class MessageE2C implements CarriesMessageData
{
	use StringRepresenting;

	/** @var IdentifiesMessage */
	private $messageId;

	/** @var IdentifiesQueue */
	private $queueName;

	/** @var string */
	private $content;

	/** @var IdentifiesMessageType */
	private $messageType;

	public function __construct( IdentifiesMessage $messageId, IdentifiesQueue $queueName, string $content )
	{
		$this->messageId   = $messageId;
		$this->queueName   = $queueName;
		$this->content     = $content;
		$this->messageType = new MessageType( MessageType::MESSAGE_E2C );
	}

	public function getMessageType() : IdentifiesMessageType
	{
		return $this->messageType;
	}

	public function getMessageId() : IdentifiesMessage
	{
		return $this->messageId;
	}

	public function getQueueName() : IdentifiesQueue
	{
		return $this->queueName;
	}

	public function getContent() : string
	{
		return $this->content;
	}

	public function toString() : string
	{
		$messageHeader         = new MessageHeader( ProtocolVersion::VERSION_1, $this->messageType );
		$queuePacketHeader     = new PacketHeader( PacketType::QUEUE_NAME, strlen( $this->queueName->toString() ) );
		$contentPacketHeader   = new PacketHeader( PacketType::MESSAGE_CONTENT, strlen( $this->content ) );
		$messageIdPacketHeader = new PacketHeader( PacketType::MESSAGE_ID, strlen( $this->messageId->toString() ) );

		return $messageHeader
			. $queuePacketHeader
			. $this->queueName
			. $contentPacketHeader
			. $this->content
			. $messageIdPacketHeader
			. $this->messageId;
	}
}
