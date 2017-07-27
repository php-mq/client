<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Interfaces;

/**
 * Interface IdentifiesQueue
 * @package PHPMQ\Client\Interfaces
 */
interface IdentifiesQueue extends RepresentsString
{
	public function equals( IdentifiesQueue $other ) : bool;
}
