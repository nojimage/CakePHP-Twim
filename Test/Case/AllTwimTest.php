<?php

/**
 * run all tests
 */
class AllTwimTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Twim Plugin tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__));
		return $suite;
	}

}
