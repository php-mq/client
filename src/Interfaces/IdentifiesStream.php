<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Interfaces;

/**
 * Interface IdentifiesStream
 * @package PHPMQ\Client\Interfaces
 */
interface IdentifiesStream extends RepresentsString
{
	public function equals( IdentifiesStream $other ) : bool;
}
