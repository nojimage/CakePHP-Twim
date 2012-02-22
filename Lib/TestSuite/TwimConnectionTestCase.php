<?php

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
