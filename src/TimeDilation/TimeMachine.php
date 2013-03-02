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
	static private $milliSeconds=0;
	static private $timeAnchor=0;

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
	 * @param mixed $_now This can either be a string that is parsable with strtotime() or a unix timestamp.
	 */
	static function setNow($_now,$_milliSeconds=0)
	{
		self::$timeAnchor=\microtime(true);
		if (is_string($_now))
		{
			self::$now=\strtotime($_now);
			self::$now+=$_milliSeconds/1000;
		}
		else self::$now=$_now+$_milliSeconds/1000;
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
	 * Freezes the time.
	 *
	 */
	static function freeze()
	{
		self::$now=self::now();
		self::$timeAnchor=0;
	}


	static function milliSeconds()
	{
		$microtime=\microtime(true)."";
		return (int)\substr($microtime,\strpos(".",$microtime));
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


	static function date($_format,$_now)
	{
		$now=$_now;
		if (!$_now) $now=self::now();
		return \date($_format,$now);
	}

	static function strtotime($_time,$_now=null)
	{
		$now=$_now;
		if (!$_now) $now=self::now();
		return \strtotime($_time,$now);

	}

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
				echo "infected microtime() called: $mt\n";
				return $mt;
			}
		}
		else
		{ //we have to return the microtime as string
			$mt=self::microtime(true);
			return implode(" ",array_reverse(explode(".",$mt)));
		}

	}
}

//now infect our own namespace so that time(), date() and strotime() behave properly
TimeMachine::infectNamespace("TimeDilation");


