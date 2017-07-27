<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Sockets\Interfaces;

use PHPMQ\Client\Interfaces\TransfersData;

/**
 * Class ClientSocket
 * @package PHPMQ\Client\Endpoint\Sockets
 */
interface EstablishesStream
{
	public function getStream() : TransfersData;
}
