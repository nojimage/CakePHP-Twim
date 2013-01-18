<?php

/**
 * test TwimSearch
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
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimSearch $Search
 */
class TwimSearchTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Search = ClassRegistry::init('Twim.TwimSearch');
		$this->Search->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Search);
		parent::tearDown();
		ob_flush();
	}

	// =========================================================================

	public function testSerach() {
		$q = 'test';
		$page = 1;
		$limit = 50;
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('statuses' => array())));

		$this->Search->find('tweets', compact('q', 'limit', 'page'));

		$this->assertSame('1.1/search/tweets', $this->Search->request['uri']['path']);
		$this->assertEquals(array('q' => 'test', 'count' => 50), $this->Search->request['uri']['query']);
	}

	public function testSerach_call2() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('statuses' => array())));
		$this->Search->find('search', 'test');
		$this->assertEquals(array('q' => 'test', 'count' => 100), $this->Search->request['uri']['query']);
	}

	public function testSerach_call3() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('statuses' => array())));
		$this->Search->find('test');
		$this->assertEquals(array('q' => 'test', 'count' => 100), $this->Search->request['uri']['query']);
	}

	public function testSerach_get_empty_results() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('statuses' => array())));
		$result = $this->Search->find('nwoghwiot20gflanvowigiwoagnla;424ty9agfjpoafacdj4#eqpwkp');
		$this->assertSame(array(), $result);
	}

}
