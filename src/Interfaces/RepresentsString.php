<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Interfaces;

/**
 * Interface RepresentsString
 * @package PHPMQ\Client\Interfaces
 */
interface RepresentsString extends \JsonSerializable
{
	public function toString() : string;

	public function __toString() : string;
}
