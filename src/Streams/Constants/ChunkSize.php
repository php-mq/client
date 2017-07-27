<?php declare(strict_types=1);
/**
 * @author h.woltersdorf
 */

namespace PHPMQ\Client\Streams\Constants;

/**
 * Class ChunkSize
 * @package PHPMQ\Client\Streams\Constants
 */
abstract class ChunkSize
{
	public const READ  = 1024;

	public const WRITE = 1024;
}
