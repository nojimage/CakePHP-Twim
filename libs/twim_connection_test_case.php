<?php

App::import('Datasource', 'Twim.TwimSource');
Mock::generatePartial('TwimSource', 'MockTwimSource', array('request'));

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

        if ($this->_should_skip || !$this->needAuth) {
            return;
        }

        $ds = ConnectionManager::getDataSource('twitter');
        if (!$this->_should_skip && empty($ds->config['oauth_token'])) {
            $this->skipIf(true, 'access token is empty.');
        }
    }

    public function startCase() {
        ClassRegistry::flush();
        $this->createTestDatasource();
        $this->createMockDatasource();
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
