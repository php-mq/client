<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

namespace PHPMQ\Client\Protocol\Headers;

use PHPMQ\Client\Interfaces\RepresentsString;
use PHPMQ\Client\Traits\StringRepresenting;

/**
 * Class AbstractHeader
 * @package PHPMQ\Client\Protocol\Headers
 */
abstract class AbstractHeader implements RepresentsString
{
	use StringRepresenting;

	/** @var string */
	private $identifier;

	public function __construct( string $identifier )
	{
		$this->identifier = $identifier;
	}

	final protected function getIdentifier() : string
	{
		return $this->identifier;
	}
}
