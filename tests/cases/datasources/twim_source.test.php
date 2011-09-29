<?php

App::import('Lib', 'Twim.TwimConnectionTestCase');

/**
 *
 * @property TwimSource $TwimSource
 * @property stdClass $Model
 */
class TwimSourceTestCase extends TwimConnectionTestCase {

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
    public function testRequest() {
        $this->Model->request = array(
            'uri' => array(
                'host' => 'search.twitter.com',
                'path' => 'search',
                'query' => array('q' => 'twitter'),
            ),
        );
        $results = $this->TwimSource->request($this->Model);
        $this->assertIdentical(200, $this->TwimSource->Http->response['status']['code']);
        $this->assertTrue(isset($results['results']));
    }

    // =========================================================================
    public function testGetRatelimit() {
        $this->Model->request = array(
            'uri' => array(
                'host' => 'api.twitter.com',
                'path' => '1/statuses/public_timeline',
            ),
        );
        $results = $this->TwimSource->request($this->Model);
        $this->assertIdentical(200, $this->TwimSource->Http->response['status']['code']);
        $limit = $this->TwimSource->getRatelimit();
        $this->assertIdentical('api', $limit['X-Ratelimit-Class']);
        $this->assertIdentical('150', $limit['X-Ratelimit-Limit']);
        $this->assertTrue($limit['X-Ratelimit-Remaining'] > 0);
        $this->assertTrue($limit['X-Ratelimit-Reset'] > time());
    }

}