<?php
/**
 * TimeDilation/TimeMachine - mocking time() for PHPUnit
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 *
 * This shows how the example BufferTest class could be tested with the
 * powers of TimeMachine.
 */

namespace TimeDilation;

TimeMachine::infectNamespace("IO\\Util"); //infect the namespace so that we can control time
require_once(__DIR__."/Buffer.php");

class BufferTest extends \PHPUnit_Framework_TestCase
{

    function testAppendAndFlushing()
    {
        $flushContainer=new \stdClass;
        $flushContainer->data=array();
        $buffer=new \IO\Util\Buffer();
        $buffer->registerConsumer(function($_data) use ($flushContainer) {
            $flushContainer->data=$_data;
        });

        $buffer->append("TimeMachine");
        TimeMachine::fastForward(4); //fast forward time by 4 seconds
        $buffer->append("rocks");
        TimeMachine::fastForward(7); //fast forward time by 7 seconds
        $buffer->append("the world!");
        //since we forwarded time more than 10 seconds our consumer should now have been called,
        //lets check this
        $this->assertEquals(3,count($flushContainer->data));
        $this->assertEquals("TimeMachine",$flushContainer->data[0]);
        $this->assertEquals("rocks",$flushContainer->data[1]);
        $this->assertEquals("the world!",$flushContainer->data[2]);
    }
}