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
 * Class MessageC2E
 * @package PHPMQ\Client\Protocol\Messages
 */
final class MessageC2E implements CarriesMessageData
{
	use StringRepresenting;

	/** @var IdentifiesQueue */
	private $queueName;

	/** @var string */
	private $content;

	/** @var IdentifiesMessageType */
	private $messageType;

	public function __construct( IdentifiesQueue $queueName, string $content )
	{
		$this->queueName   = $queueName;
		$this->content     = $content;
		$this->messageType = new MessageType( MessageType::MESSAGE_C2E );
	}

	public function getMessageType() : IdentifiesMessageType
	{
		return $this->messageType;
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
		$messageHeader       = new MessageHeader( ProtocolVersion::VERSION_1, $this->messageType );
		$queuePacketHeader   = new PacketHeader( PacketType::QUEUE_NAME, strlen( (string)$this->queueName ) );
		$contentPacketHeader = new PacketHeader( PacketType::MESSAGE_CONTENT, strlen( $this->content ) );

		return $messageHeader
			. $queuePacketHeader
			. $this->queueName
			. $contentPacketHeader
			. $this->content;
	}
}
