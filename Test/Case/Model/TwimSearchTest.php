<?php

/**
 * test TwimSearch
 *
 * CakePHP 2.0
 * PHP version 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
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
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));

		$this->Search->find('search', compact('q', 'limit', 'page'));

		$this->assertSame('search.twitter.com', $this->Search->request['uri']['host']);
		$this->assertSame('search', $this->Search->request['uri']['path']);
		$this->assertEquals(array('q' => 'test', 'page' => 1, 'rpp' => 50), $this->Search->request['uri']['query']);
	}

	public function testSerach_call2() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));
		$this->Search->find('search', 'test');
		$this->assertEquals(array('q' => 'test', 'page' => 1, 'rpp' => 100), $this->Search->request['uri']['query']);
	}

	public function testSerach_call3() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));
		$this->Search->find('test');
		$this->assertEquals(array('q' => 'test', 'page' => 1, 'rpp' => 100), $this->Search->request['uri']['query']);
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage You must enter a query.
	 */
	public function testSerach_noquery() {
		$this->Search->setDataSource($this->testDatasourceName);
		$this->Search->find('');
	}

	public function testSerach_get_all_results() {
		$this->Search->setDataSource($this->testDatasourceName);
		$results = $this->Search->find('test');
		$this->assertGreaterThan(100, count($results));
	}

	/**
	 * @depends testSerach_get_all_results
	 */
	public function testSerach_limitation_results() {
		$this->Search->setDataSource($this->testDatasourceName);
		$this->assertSame(255, count($this->Search->find('search', array('q' => 'test', 'limit' => 255))));
	}

	public function testSerach_get_empty_results() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));
		$result = $this->Search->find('nwoghwiot20gflanvowigiwoagnla;424ty9agfjpoafacdj4#eqpwkp');
		$this->assertSame(array(), $result);
	}

	public function testSerach_with_users_lookup() {
		$this->Search->setDataSource($this->testDatasourceName)->User->setDataSource($this->testDatasourceName);
		$results = $this->Search->find('search', array('q' => 'twitter', 'limit' => 255, 'users_lookup' => true));
		$this->assertNotEmpty($results[0]['user']['id_str']);
		$this->assertSame(255, count($results));
	}

	/**
	 * @depends testSerach_with_users_lookup
	 */
	public function testSerach_with_users_lookup_specific_fields() {
		$this->Search->setDataSource($this->testDatasourceName)->User->setDataSource($this->testDatasourceName);
		$results = $this->Search->find('search', array('q' => 'twitter', 'limit' => 1, 'users_lookup' => array('name', 'statuses_count')));
		$this->assertArrayNotHasKey('id_str', $results[0]['user']);
		$this->assertArrayHasKey('name', $results[0]['user']);
		$this->assertArrayHasKey('statuses_count', $results[0]['user']);
	}

}
