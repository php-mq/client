<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Interfaces;

/**
 * Interface IdentifiesMessageType
 * @package PHPMQ\Client\Protocol\Interfaces
 */
interface IdentifiesMessageType
{
	public function getType() : int;

	public function getPacketCount() : int;
}
