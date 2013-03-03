TimeDilation/TimeMachine - mocking time() for PHPUnit
=====================================================

TimeDilation/TimeMachine helps you when you need to test time()-constrained code in PHPUnit.
It only works for PHP 5.3 namespaced classes/code.

[![Build Status](https://travis-ci.org/Shyru/TimeDilation.png)](https://travis-ci.org/Shyru/TimeDilation)

Usage
-----
Write your unit tests in the namespace TimeDilation and use the methods of TimeMachine to mock time.
Example:
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



Caveats
-------
 - It only works for PHP 5.3 namespaced classes/code.
 - Does not work with DateTime class of php.
