<?php
/**
 * TimeDilation/TimeMachine - mocking time() for PHPUnit
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 *
 * This is an example class that uses time() for its functionality and serves as example
 * on how TimeDilation/TimeMachine can be useful.
 */

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
