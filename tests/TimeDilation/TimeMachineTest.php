<?php
/**
 *
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace TimeDilation;



require_once(__DIR__."/../../src/TimeDilation/TimeMachine.php");


class TimeMachineTest extends \PHPUnit_Framework_TestCase
{

	function testSetNow()
	{
		$realNow=\time();
		$now=time();
		$this->assertEquals($realNow,$now);

		TimeMachine::setNow(10);
		$this->assertEquals(10,time());

		TimeMachine::setNow(10,200);
		$this->assertEquals(10.200,round(microtime(true),3));

		TimeMachine::freeze();
		TimeMachine::setNow(2938444.29383);
		$this->assertEquals(2938444.29383,round(microtime(true),5));
		TimeMachine::unfreeze();
	}



	function testMicrotime()
	{
		$now="2028-08-29 17:28:49";
		$unix=\strtotime($now);
		TimeMachine::setNow($now,200);
		$this->assertEquals($unix+0.2,round(microtime(true),3));

		usleep(200*1000+200);
		$this->assertEquals($unix+0.4,round(microtime(true),2));
		TimeMachine::freeze();
		$now=microtime(true);
		usleep(200*1000);
		$this->assertEquals($now,microtime(true));

		$realNow=\microtime();
		$realNowParts=explode(" ",$realNow);
		TimeMachine::setNow(doubleval(doubleval($realNowParts[1])+doubleval($realNowParts[0])));

		$now=microtime();
		$nowParts=explode(" ",$now);
		$this->assertEquals($realNowParts[1],$nowParts[1]);
		//we have to round the first part because double is not precise enough :-(
		$this->assertEquals(round($realNowParts[0],4),round($nowParts[0],4));


		TimeMachine::unfreeze();
	}

	function testFastForward()
	{
		$now="2028-08-29 17:28:49";
		TimeMachine::setNow($now);
		TimeMachine::fastForward(5);
		$this->assertEquals("2028-08-29 17:28:54",date("Y-m-d H:i:s"));
		//sleep an additonal second and check if the time is correct
		sleep(1);
		$this->assertEquals("2028-08-29 17:28:55",date("Y-m-d H:i:s"));
	}

	function testRewind()
	{
		$now="2028-08-29 17:28:49";
		TimeMachine::setNow($now);
		TimeMachine::rewind(2);
		$this->assertEquals("2028-08-29 17:28:47",date("Y-m-d H:i:s"));
		//sleep an additonal second and check if the time is correct
		sleep(1);
		$this->assertEquals("2028-08-29 17:28:48",date("Y-m-d H:i:s"));
	}

	function testFastForwardMilliseconds()
	{
		$now="2028-08-29 17:28:49";
		TimeMachine::setNow($now,200);
		TimeMachine::fastForward(0.2);
		$mt=microtime(true);
		$ms=$mt-(int)$mt;
		$this->assertEquals(0.4,round($ms,3));
	}

	function testStrtotime()
	{
		$now="2028-08-29 17:28:49";
		TimeMachine::setNow($now,200);
		$nextDay=strtotime("+1 day");
		$this->assertEquals("2028-08-30 17:28:49",date("Y-m-d H:i:s",$nextDay));

	}


	function testBasics()
	{
		$now="2028-08-29 17:28:49";
		TimeMachine::setNow($now);
		$this->assertEquals($now,date("Y-m-d H:i:s"));
		sleep(1);
		$this->assertEquals("2028-08-29 17:28:50",date("Y-m-d H:i:s"));
		TimeMachine::freeze();
		sleep(1);
		$this->assertEquals("2028-08-29 17:28:50",date("Y-m-d H:i:s"));
		TimeMachine::fastForward(10);
		$this->assertEquals("2028-08-29 17:29:00",date("Y-m-d H:i:s"));
	}


}
