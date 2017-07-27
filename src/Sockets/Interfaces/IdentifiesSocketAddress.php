<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Sockets\Interfaces;

/**
 * Interface IdentifiesSocketAddress
 * @package PHPMQ\Client\Sockets\Interfaces
 */
interface IdentifiesSocketAddress
{
	public function getSocketAddress() : string;
}
