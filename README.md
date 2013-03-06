TimeDilation/TimeMachine - mocking time() for PHPUnit
=====================================================

TimeDilation/TimeMachine helps you when you need to test time()-constrained code in PHPUnit.
It only works for PHP 5.3 namespaced classes/code.

[![Build Status](https://travis-ci.org/Shyru/TimeDilation.png)](https://travis-ci.org/Shyru/TimeDilation)

Usage
-----
Write your unit tests in the namespace TimeDilation and use the methods of TimeMachine to mock time.
Basic Example:
```php

namespace TimeDilation;

class TestTime extends PHPUnit_Framework_TestCase
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
}

```

Testing a class that uses time():
```php

namespace IO\Util;

/**
 * Provides buffering of arbitrary data and flushing to registered consumers
 * in an interval that is bigger than the bufferTime.
 */
class Buffer
{
    private $bufferTime;
    private $buffer;
    private $callbacks;
    private $lastFlush;

    /**
     * Constructs a new buffer.
     *
     * @param int $_bufferTime The buffer time in seconds.
     */
    function __construct($_bufferTime=10)
    {
        $this->bufferTime=$_bufferTime;
        $this->lastFlush=time();
    }

    /**
     * Registers a new consumer callback that should be called
     * whenever at least 10 seconds passed since the last time the data was flushed.
     *
     * @param callable $_callback The callback that should be called to receive the flushed data
     */
    function registerConsumer($_callback)
    {
        $this->callbacks[]=$_callback;
    }

    /**
     * Append some data to the buffer.
     * If the last flush of data was more then bufferTime seconds ago, the buffer will be flushed to all registered
     * consumers.
     *
     * @param mixed $_data The data that should be buffered.
     */
    function append($_data)
    {
        $this->buffer[]=$_data;
        if ($this->lastFlush+$this->bufferTime<time())
        { //at least 10 seconds passed since the last flush, now flush our buffer
            foreach ($this->callbacks as $callback)
            {
                call_user_func($callback,$this->buffer);
            }
            $this->buffer=array();
            $this->lastFlush=time();
        }
    }

}


```
If you wanted to write a Unit-Test for the above Buffer class the Unit-Test would at least take a second to complete because
that would be the minimum buffer time. If you wanted to test with the default buffer time the test would take at least 10 seconds
essentially just waiting. With TimeDilation/TimeMachine the class could be tested as follows:
```php
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

```
Thanks to TimeMachine this test will execute in a fraction of a second allthough the complete class is tested the class does
not need to be modified to support fancy time-mocking. I think you can easily come up with more/better examples on where this could be useful.

Since this is still very young code there may very well be some bugs hidden.
If you find something not working correctly, add an issue or propose a fix through a fork and pull-request!

Caveats
-------
 - It only works for PHP 5.3 namespaced classes/code.
 - Does not work with DateTime class of php.


Roadmap
-------
 - Implement time-warping. (Override sleep() and usleep() so that they return immediatly but time is forwarded as much as the sleep should have lasted)
 - Research ways of mocking DateTime class.

