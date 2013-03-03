<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace TimeDilation;

require_once(__DIR__."/../../src/TimeDilation/TimeMachine.php");




class InfectNamespaceTest extends \PHPUnit_Framework_TestCase
{
	function tearDown()
	{
		TimeMachine::reset();
	}

	function testInfectedClass()
	{
		TimeMachine::infectNamespace("TestNamespace");
		TimeMachine::freeze();
		TimeMachine::setNow("2009-03-07 14:00:00");
		require_once(__DIR__."/SUT.php");
		$sut=new \TestNamespace\SUT();
		$this->assertEquals("2009-03-07 14:00:00",\date("Y-m-d H:i:s",$sut->constructedAt()));
	}
}
