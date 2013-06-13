<?php

/**
 * test TwimSource
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
 * @property TwimSource $TwimSource
 * @property stdClass $Model
 */
class TwimSourceTestCase extends TwimConnectionTestCase {

	public $needAuth = true;

	public function setUp() {
		parent::setUp();
		$this->TwimSource = ConnectionManager::getDataSource('twitter');
		$this->Model = new stdClass();
	}

	public function tearDown() {
		unset($this->Model);
		unset($this->TwimSource);
		parent::tearDown();
	}

	// =========================================================================

	public function testRequest() {
		$this->Model->request = array(
			'method' => 'GET',
			'uri' => array(
				'host' => 'api.twitter.com',
				'path' => '1.1/search/tweets',
				'query' => array('q' => 'twitter'),
			),
			'auth' => true,
		);
		$results = $this->TwimSource->request($this->Model);
		$this->assertEquals('https', $this->TwimSource->Http->request['uri']['scheme']);
		$this->assertEquals(200, $this->TwimSource->Http->response['status']['code']);
		$this->assertNotEmpty($results['statuses']);
	}

	// =========================================================================

	public function testGetRatelimit() {
		$this->Model->request = array(
			'method' => 'GET',
			'uri' => array(
				'host' => 'api.twitter.com',
				'path' => '1.1/statuses/home_timeline',
			),
			'auth' => true,
		);
		$results = $this->TwimSource->request($this->Model);
		$this->assertEquals(200, $this->TwimSource->Http->response['status']['code']);

		$limit = $this->TwimSource->getRatelimit();
		$this->assertEquals('15', $limit['x-rate-limit-limit']);
		$this->assertGreaterThan(0, $limit['x-rate-limit-remaining']);
		$this->assertGreaterThan(time(), $limit['x-rate-limit-reset']);
	}

}
