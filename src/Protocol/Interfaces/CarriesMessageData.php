<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Interfaces;

use PHPMQ\Client\Interfaces\RepresentsString;

/**
 * Interface CarriesMessageData
 * @package PHPMQ\Client\Protocol\Interfaces
 */
interface CarriesMessageData extends RepresentsString
{
	public function getMessageType() : IdentifiesMessageType;
}
