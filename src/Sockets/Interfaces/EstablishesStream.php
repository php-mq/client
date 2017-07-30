<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Sockets\Interfaces;

use PHPMQ\Stream\Interfaces\TransfersData;

/**
 * Class ClientSocket
 * @package PHPMQ\Client\Sockets\Interfaces
 */
interface EstablishesStream
{
	public function getStream() : TransfersData;
}
