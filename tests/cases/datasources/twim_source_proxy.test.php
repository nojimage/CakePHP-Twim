<?php

App::import('Datasource', 'Twim.TwimSource');

ConnectionManager::create('test_twitter_twim_source_proxy', array(
    'datasource' => 'Twim.TwimSource',
    'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
    'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
));

/**
 *
 * @property TwimSource $TwimSource
 * @property stdClass $Model
 */
class TwimSourceProxyTestCase extends CakeTestCase {

    public function skip() {
        $sock = @fsockopen('localhost', 3128, $errno, $errstr, 15);
        $this->skipIf($sock === FALSE, 'unable to connect to localhost:3128 ' . $errstr);
        if ($sock) {
            fclose($sock);
        }
    }

    public function startTest($method) {
        $this->TwimSource = ConnectionManager::getDataSource('test_twitter_twim_source_proxy');
        $this->Model = new stdClass();
    }

    public function endTest($method) {
        unset($this->Model);
        unset($this->TwimSource);
        ClassRegistry::flush();
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
        $this->assertIdentical(200, $this->TwimSource->Http->response['status']['code']);
        $this->assertTrue(isset($results['results']));
    }

}