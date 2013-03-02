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

	function testMicrotime()
	{
		$now="2028-08-29 17:28:49";
		$unix=\strtotime($now);
		TimeMachine::setNow($now,200);
		$this->assertEquals($unix+0.2,round(microtime(true),3));

		usleep(200*1000+200);
		$this->assertEquals($unix+0.4,round(microtime(true),2));
	}

	function testFastMilliseconds()
	{
		$now="2028-08-29 17:28:49";
		TimeMachine::setNow($now,200);
		TimeMachine::fastForward(0.2);
		$mt=microtime(true);
		$ms=$mt-(int)$mt;
		$this->assertEquals(0.4,round($ms,3));
	}


}
