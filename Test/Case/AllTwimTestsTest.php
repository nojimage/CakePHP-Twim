<?php

/**
 * run all tests
 *
 * CakePHP 2.x
 * PHP version 5
 *
 * Copyright 2013, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.1
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2013 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Twim
 * @since     File available since Release 1.0
 *
 */
class AllTwimTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Twim Plugin tests');
		self::addTestDirectoryRecursive($suite, dirname(__FILE__));
		return $suite;
	}

	public static function addTestDirectoryRecursive(PHPUnit_Framework_TestSuite $suite, $directory) {
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

		while ($it->valid()) {
			if (!$it->isDot()) {
				$file = $it->key();
				if (preg_match('|Test\.php$|', $file) && $file !== __FILE__) {
					$suite->addTestFile($file);
				}
			}
			$it->next();
		}
	}

}
