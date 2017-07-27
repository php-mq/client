<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Types;

use PHPMQ\Client\Protocol\Interfaces\IdentifiesMessageType;

/**
 * Class MessageType
 * @package PHPMQ\Client\Protocol\Types
 */
final class MessageType implements IdentifiesMessageType
{
	public const  MESSAGE_C2E      = 1;

	public const  CONSUME_REQUEST  = 2;

	public const  MESSAGE_E2C      = 3;

	public const  ACKNOWLEDGEMENT  = 4;

	public const  MESSAGE_RECEIPT  = 5;

	private const PACKET_COUNT_MAP = [
		self::MESSAGE_C2E     => 2,
		self::CONSUME_REQUEST => 2,
		self::MESSAGE_E2C     => 3,
		self::ACKNOWLEDGEMENT => 2,
		self::MESSAGE_RECEIPT => 2,
	];

	/** @var int */
	private $type;

	public function __construct( int $type )
	{
		$this->type = $type;
	}

	public function getType() : int
	{
		return $this->type;
	}

	public function getPacketCount() : int
	{
		return self::PACKET_COUNT_MAP[ $this->type ] ?? 0;
	}
}
