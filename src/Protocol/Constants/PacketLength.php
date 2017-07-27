<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Constants;

/**
 * Class PacketLength
 * @package PHPMQ\Client\Protocol\Constants
 */
abstract class PacketLength
{
	public const MESSAGE_HEADER = 8;

	public const PACKET_HEADER  = 32;
}
