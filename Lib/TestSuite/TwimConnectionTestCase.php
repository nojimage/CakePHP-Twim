<?php

/**
 * Custom TestCase
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
App::uses('TwimSource', 'Twim.Model/Datasource');

/**
 * for plugin test
 */
class TwimConnectionTestCase extends CakeTestCase {

	public $connectionCheck = true;

	public $needAuth = false;

	public $testDatasourceName = 'test_twim_source';

	public $mockDatasourceName = null;

	public function skip() {
		if (!$this->connectionCheck) {
			return;
		}
		$sock = @fsockopen('twitter.com', 80, $errno, $errstr, 15);
		$this->skipIf($sock === FALSE, 'unable to connect to twitter.com ' . $errstr);
		if ($sock) {
			fclose($sock);
		}

		if ($this->needAuth) {
			try {
				$ds = ConnectionManager::getDataSource('twitter');
				if (empty($ds->config['oauth_token'])) {
					$this->markTestSkipped('access token is empty.');
				}
			} catch (MissingDatasourceConfigException $e) {
				$this->markTestSkipped($e->getMessage());
			}
		}
	}

	public function setUp() {
		parent::setUp();
		ClassRegistry::flush();
		$this->skip();
		if (!class_exists('MockTwimSource')) {
			$this->getMock('TwimSource', array('request'), array(), 'MockTwimSource');
		}
		$this->createTestDatasource();
		$this->createMockDatasource();
	}

	public function tearDown() {
		parent::tearDown();
		ConnectionManager::drop($this->testDatasourceName);
		ConnectionManager::drop($this->mockDatasourceName);
	}

	protected function createTestDatasource($dataSourceName = '') {
		if (empty($dataSourceName)) {
			$dataSourceName = $this->testDatasourceName;
		}

		if (!in_array($dataSourceName, ConnectionManager::sourceList())) {
			ConnectionManager::create($dataSourceName, array(
				'datasource' => 'Twim.TwimSource',
				'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
				'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
				'cache' => false,
			));
		}
	}

	protected function createMockDatasource($dataSourceName = '') {
		if (empty($dataSourceName)) {
			$dataSourceName = 'mock_twim_source_' . Inflector::underscore(get_class($this));
			$this->mockDatasourceName = $dataSourceName;
		}

		if (!in_array($dataSourceName, ConnectionManager::sourceList())) {
			ConnectionManager::create($dataSourceName, array(
				'datasource' => 'MockTwimSource',
			));
		}
	}

}
