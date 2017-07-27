<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Interfaces;

use PHPMQ\Client\Protocol\Headers\MessageHeader;

/**
 * Interface BuildsMessages
 * @package PHPMQ\Client\Endpoint\Interfaces
 */
interface BuildsMessages
{
	public function buildMessage( MessageHeader $messageHeader, array $packets ) : CarriesMessageData;
}
