<?php

/**
 * Custom TestCase
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
App::uses('TwimSource', 'Twim.Model/Datasource');

/**
 * for plugin test
 */
abstract class TwimConnectionTestCase extends ControllerTestCase {

	public $connectionCheck = true;

	public $needAuth = false;

	public $testDatasourceName = 'test_twim_source';

	public $mockDatasourceName = null;

	public function skip() {
		if (!$this->connectionCheck) {
			return;
		}
		$sock = @fsockopen('twitter.com', 80, $errno, $errstr, 15);
		$this->skipIf($sock === false, 'unable to connect to twitter.com ' . $errstr);
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
			App::uses('MockTwimSource', 'Twim.Model/Datasource');
		}
		$this->_createTestDatasource();
		$this->_createMockDatasource();
	}

	public function tearDown() {
		parent::tearDown();
		ConnectionManager::drop($this->testDatasourceName);
		ConnectionManager::drop($this->mockDatasourceName);
	}

	protected function _createTestDatasource($dataSourceName = '') {
		if (empty($dataSourceName)) {
			$dataSourceName = $this->testDatasourceName;
		}

		$sources = ConnectionManager::enumConnectionObjects();
		if (!isset($sources[$dataSourceName])) {
			ConnectionManager::create($dataSourceName, array(
				'datasource' => 'Twim.TwimSource',
				'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
				'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
				'cache' => false,
			));
		}
	}

	protected function _createMockDatasource($dataSourceName = '') {
		if (empty($dataSourceName)) {
			$dataSourceName = 'mock_twim_source_' . Inflector::underscore(get_class($this));
			$this->mockDatasourceName = $dataSourceName;
		}

		$sources = ConnectionManager::enumConnectionObjects();
		if (!isset($sources[$dataSourceName])) {
			ConnectionManager::create($dataSourceName, array(
				'datasource' => 'Twim.MockTwimSource',
			));
		}
	}

}
