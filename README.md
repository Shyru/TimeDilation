TimeDilation/TimeMachine - mocking with time() for PHPUnit
==========================================================

TimeDilation/TimeMachine helps you when you need to test time()-constrained code in PHPUnit.


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
		$now="2351-08-29 17:28:49";
		TimeMachine::setNow($now);
		$this->asssertEquals(date("Y-m-d H:i:s"),$now);
		sleep(1);
		$this->asssertEquals(date("Y-m-d H:i:s"),"2351-08-29 17:28:50");
		TimeMachine::freeze();
		sleep(1);
		$this->asssertEquals(date("Y-m-d H:i:s"),"2351-08-29 17:28:50");
		TimeMachine::fastForward(10);
		$this->asssertEquals(date("Y-m-d H:i:s"),"2351-08-29 17:29:00");
	}


}


```



Caveats
-------
Does not work with DateTime class of php.