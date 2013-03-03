<?php
/**
 * This file is part of Phutler.
 * Please check the file LICENSE for information about the license.
 *
 * @copyright Daniel Haas 2013
 * @author Daniel Haas <daniel@file-factory.de>
 */

namespace TestNamespace;


/**
 * Sample class in a TestNamepsace.
 * This class records the creation timestamp in a private member variable.
 * This allows us to test if we can mock the call to time() in the constructor.
 */
class SUT
{
	private $constructedAt;

	function __construct()
	{
		$this->constructedAt=time();
	}

	function constructedAt()
	{
		return $this->constructedAt;
	}

}