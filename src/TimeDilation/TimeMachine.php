<?php
/**
 * TimeDilation/TimeMachine - mocking time() for PHPUnit
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace TimeDilation;

/**
 * Time Machine that provides methods to play with time.
 *
 * This has effect on all calls to time(), date(), strtotime() in all namespaces
 * that where infected with infectNamespace().
 *
 * @package TimeDilation
 */
class TimeMachine
{
	static private $now=null;
	static private $timeAnchor=0;
	static private $frozen=false;

	/**
	 * Infect the given namespace so that all calls within that namespace
	 * will in fact run through the time machine.
	 *
	 * @param $_namespace
	 */
	static function infectNamespace($_namespace)
	{
		$virus=file_get_contents(__DIR__."/namespace-infection.template.php");
		$virus=str_replace("//{{namespace}};", "namespace $_namespace;",$virus);
		$infection=__DIR__."/virus_".str_replace("\\","_",$_namespace).".inc.php";
		file_put_contents($infection,$virus);
		include_once($infection);
		unlink($infection);
	}


	/**
	 * Allows to set now.
	 *
	 * @param mixed $_now This can either be a string that is parsable with strtotime() or a unix timestamp or a float as from microtime(true)
	 * @param int $_milliSeconds The milliseconds of \c $_now. This is especially useful if you want to set the time with a string and also set the milliseconds with one call.
	 */
	static function setNow($_now,$_milliSeconds=0)
	{
		if (!self::$frozen) self::$timeAnchor=\microtime(true);
		if (is_string($_now))
		{
			self::$now=(double)\strtotime($_now);
			self::$now+=$_milliSeconds/1000;
		}
		else if (is_double($_now))
		{
			self::$now=$_now;
		}
		else self::$now=(double)$_now+$_milliSeconds/1000;
	}

	/**
	 * Returns the current time.
	 *
	 * @return int the current time as unix timestamp
	 */
	static function now()
	{
		if (!self::$now) return \time();
		else
		{
			if (self::$timeAnchor)
			{ //calculate relative time
				$timePassed=\microtime(true)-self::$timeAnchor;
				return self::$now+floor($timePassed);
			}
			else return self::$now;
		}
	}

	/**
	 * Resets the TimeMachine.
	 * After a call to this method all methods should work as expected again.
	 */
	static function reset()
	{
		self::$now=null;
		self::$timeAnchor=0;
		self::$frozen=false;
	}


	/**
	 * Freezes the time.
	 * After a call to this method no time will pass anymore, regardless how long you
	 * sleep() or usleep().
	 * To get the time running again, use unfreeze().
	 */
	static function freeze()
	{
		self::$now=self::now();
		self::$timeAnchor=0;
		self::$frozen=true;
	}

	/**
	 * Unfreezes the time.
	 * This means time is running again after having called freeze() before.
	 */
	static function unfreeze()
	{
		self::$timeAnchor=\microtime(true);
		self::$frozen=false;
	}



	/**
	 * Handy method to fast-forward the time the given \c $_seconds.
	 *
	 * @param float $_seconds How many seconds to fast forward.
	 */
	static function fastForward($_seconds)
	{
		self::$now+=$_seconds;
	}

	/**
	 * Handy method to rewind time the given \c $_seconds.
	 *
	 * @param float $_seconds How many seconds to rewind.
	 */
	static function rewind($_seconds)
	{
		self::$now-=$_seconds;
	}


	/**
	 * Replacement for php's native date() method.
	 * Should work exactly as phps date() but be constrained to the time-machine.
	 *
	 * @param $_format
	 * @param $_now
	 * @return string
	 */
	static function date($_format,$_now)
	{
		$now=$_now;
		if (!$_now) $now=self::now();
		return \date($_format,$now);
	}

	/**
	 * Replacement for php's native strtotime() method.
	 * Should work exactly as phps strtotime() but be constrained to the time-machine.
	 *
	 * @param $_time
	 * @param null $_now
	 * @return int
	 */
	static function strtotime($_time,$_now=null)
	{
		$now=$_now;
		if (!$_now) $now=self::now();
		return \strtotime($_time,$now);
	}

	/**
	 * Replacement for php's native microtime() method.
	 * Should work exaclty as php's microtime() but be constrained to the time-machine.
	 *
	 * @param bool $_asFloat
	 * @return float|null|string
	 */
	static function microtime($_asFloat=false)
	{
		if ($_asFloat==true)
		{
			if (self::$timeAnchor)
			{ //calculate relative time
				$timePassed=\microtime(true)-self::$timeAnchor;
				return self::$now+$timePassed;
			}
			else
			{
				$mt=(float)\TimeDilation\TimeMachine::now();
				return $mt;
			}
		}
		else
		{ //we have to return the microtime as string
			$mt=explode(".",self::microtime(true));
			if (!isset($mt[1])) return "0.0 $mt[0]";

			$fraction=str_pad("0.".$mt[1],10,"0");
			return $fraction." ".$mt[0];
		}
	}
}

//now infect our own namespace so that time(), date() and strotime() behave properly
TimeMachine::infectNamespace("TimeDilation");


