<?php declare(strict_types=1);
/**
 * @author h.woltersdorf
 */

namespace PHPMQ\Client\Timers;

/**
 * Class TimeoutTimer
 * @package PHPMQ\Client\Timers
 */
final class TimeoutTimer
{
	private const MICROSECOND_FACTOR = 1000000;

	/** @var float */
	private $startTime;

	/** @var float */
	private $timeout;

	public function __construct( int $microSeconds )
	{
		$this->timeout = round( $microSeconds / self::MICROSECOND_FACTOR, 6 );
	}

	public function start() : void
	{
		if ( null === $this->startTime )
		{
			$this->startTime = microtime( true );
		}
	}

	public function reset() : void
	{
		$this->startTime = null;
	}

	public function restart() : void
	{
		$this->reset();
		$this->start();
	}

	public function timedOut() : bool
	{
		if ( null === $this->startTime )
		{
			return false;
		}

		return (($this->startTime + $this->timeout) < microtime( true ));
	}
}
