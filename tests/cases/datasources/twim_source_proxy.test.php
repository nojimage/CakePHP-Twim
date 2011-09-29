<?php

App::import('Lib', 'Twim.TwimConnectionTestCase');

/**
 *
 * @property TwimSource $TwimSource
 * @property stdClass $Model
 */
class TwimSourceProxyTestCase extends TwimConnectionTestCase {

    public function skip() {

        parent::skip();

        if ($this->_should_skip) {
            return;
        }

        $sock = @fsockopen('localhost', 3128, $errno, $errstr, 15);
        $this->skipIf($sock === FALSE, 'unable to connect to localhost:3128 ' . $errstr);
        if ($sock) {
            fclose($sock);
        }
    }

    public function startTest($method) {
        $this->TwimSource = ConnectionManager::getDataSource($this->testDatasourceName);
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