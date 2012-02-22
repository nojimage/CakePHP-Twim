<?php

/**
 * test TwimSearch
 *
 * PHP versions 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
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
	}

	// =========================================================================
	public function testSerach() {
		$q = 'test';
		$page = 1;
		$limit = 50;
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));

		$this->Search->find('search', compact('q', 'limit', 'page'));

		$this->assertIdentical($this->Search->request['uri']['host'], 'search.twitter.com');
		$this->assertIdentical($this->Search->request['uri']['path'], 'search');
		$this->assertEqual($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 50));
	}

	public function testSerach_call2() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));
		$this->Search->find('search', 'test');
		$this->assertEqual($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 100));
	}

	public function testSerach_call3() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));
		$this->Search->find('test');
		$this->assertEqual($this->Search->request['uri']['query'], array('q' => 'test', 'page' => 1, 'rpp' => 100));
	}

	public function testSerach_noquery() {
		$this->Search->setDataSource($this->testDatasourceName);
		try {
			$this->Search->find('');
		} catch (RuntimeException $e) {
			$this->assertIdentical($e->getMessage(), 'You must enter a query.');
		}
		$this->assertEqual($this->Search->request['uri']['query'], array('q' => '', 'page' => 1, 'rpp' => 100));
	}

	public function testSerach_get_all_results() {
		$this->Search->setDataSource($this->testDatasourceName);
		$this->assertTrue(count($this->Search->find('twitter')) > 100);
		$this->assertTrue(empty($this->Search->response['next_page']));
	}

	public function testSerach_limitation_results() {
		$this->Search->setDataSource($this->testDatasourceName);
		$this->assertIdentical(255, count($this->Search->find('search', array('q' => 'twitter', 'limit' => 255))));
		$this->assertFalse(empty($this->Search->response['next_page']));
	}

	public function testSerach_get_empty_results() {
		$this->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('results' => array())));
		$result = $this->Search->find('nwoghwiot20gflanvowigiwoagnla;424ty9agfjpoafacdj4#eqpwkp');
		$this->assertIdentical(array(), $result);
	}

	public function testSerach_with_users_lookup() {
		$this->Search->setDataSource($this->testDatasourceName)->User->setDataSource($this->testDatasourceName);
		$results = $this->Search->find('search', array('q' => 'twitter', 'limit' => 255, 'users_lookup' => true));
		$this->assertIdentical(255, count($results));
		$this->assertTrue(isset($results[0]['user']['id_str']));
	}

	public function testSerach_with_users_lookup_specific_fields() {
		$this->Search->setDataSource($this->testDatasourceName)->User->setDataSource($this->testDatasourceName);
		$results = $this->Search->find('search', array('q' => 'twitter', 'limit' => 1, 'users_lookup' => array('name', 'statuses_count')));
		$this->assertFalse(isset($results[0]['user']['id_str']));
		$this->assertTrue(isset($results[0]['user']['name']));
		$this->assertTrue(isset($results[0]['user']['statuses_count']));
	}

}
