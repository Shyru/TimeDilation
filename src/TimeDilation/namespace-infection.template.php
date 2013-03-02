<?php

//{{namespace}};

function time()
{
	return \TimeDilation\TimeMachine::now();
}

function date($_format,$_now=null)
{
	return \TimeDilation\TimeMachine::date($_format,$_now);
}

function strtotime($_time,$_now=null)
{
	return \TimeDilation\TimeMachine::strtotime($_time,$_now);
}

function microtime($_asFloat=false)
{
	return \TimeDilation\TimeMachine::microtime($_asFloat);
}