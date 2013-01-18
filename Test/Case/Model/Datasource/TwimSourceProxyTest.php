<?php

/**
 * test TwimSource using proxy
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
class TwimSourceProxyTestCase extends TwimConnectionTestCase {

	public function skip() {
		parent::skip();
		$sock = @fsockopen('localhost', 3128, $errno, $errstr, 15);
		$this->skipIf($sock === FALSE, 'unable to connect to localhost:3128 ' . $errstr);
		if ($sock) {
			fclose($sock);
		}
	}

	public function setUp() {
		parent::setUp();
		$this->TwimSource = ConnectionManager::getDataSource($this->testDatasourceName);
		$this->Model = new stdClass();
	}

	public function tearDown() {
		unset($this->Model);
		unset($this->TwimSource);
		parent::tearDown();
	}

	// =========================================================================

	public function testConfigProxy() {
		$this->Model->request = array(
			'uri' => array(
				'host' => 'search.twitter.com',
				'path' => 'search',
				'query' => array('q' => 'twitter'),
			),
		);
		$this->TwimSource->configProxy('localhost');
		$results = $this->TwimSource->request($this->Model);
		$this->assertEquals(200, $this->TwimSource->Http->response['status']['code']);
		$this->assertNotEmpty($results['results']);
	}

}