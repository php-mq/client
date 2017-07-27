<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Messages;

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
 * Class ConsumeRequest
 * @package PHPMQ\Client\Protocol\Messages
 */
final class ConsumeRequest implements CarriesMessageData
{
	use StringRepresenting;

	/** @var IdentifiesQueue */
	private $queueName;

	/** @var int */
	private $messageCount;

	/** @var IdentifiesMessageType */
	private $messageType;

	public function __construct( IdentifiesQueue $queueName, int $messageCount )
	{
		$this->queueName    = $queueName;
		$this->messageCount = $messageCount;
		$this->messageType  = new MessageType( MessageType::CONSUME_REQUEST );
	}

	public function getMessageType() : IdentifiesMessageType
	{
		return $this->messageType;
	}

	public function getQueueName() : IdentifiesQueue
	{
		return $this->queueName;
	}

	public function getMessageCount() : int
	{
		return $this->messageCount;
	}

	public function toString() : string
	{
		$messageHeader            = new MessageHeader( ProtocolVersion::VERSION_1, $this->messageType );
		$queuePacketHeader        = new PacketHeader( PacketType::QUEUE_NAME, strlen( (string)$this->queueName ) );
		$messageCountPacketHeader = new PacketHeader(
			PacketType::MESSAGE_CONSUME_COUNT,
			strlen( (string)$this->messageCount )
		);

		return $messageHeader
			. $queuePacketHeader
			. $this->queueName
			. $messageCountPacketHeader
			. $this->messageCount;
	}
}
