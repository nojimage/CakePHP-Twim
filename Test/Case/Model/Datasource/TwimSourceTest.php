<?php

App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property TwimSource $TwimSource
 * @property stdClass $Model
 */
class TwimSourceTestCase extends TwimConnectionTestCase {

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

	public function testRequest() {
		$this->Model->request = array(
			'uri' => array(
				'host' => 'search.twitter.com',
				'path' => 'search',
				'query' => array('q' => 'twitter'),
			),
		);
		$results = $this->TwimSource->request($this->Model);
		$this->assertEquals(200, $this->TwimSource->Http->response['status']['code']);
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
		$this->assertEquals(200, $this->TwimSource->Http->response['status']['code']);

		$limit = $this->TwimSource->getRatelimit();
		$this->assertEquals('api', $limit['X-RateLimit-Class']);
		$this->assertEquals('150', $limit['X-RateLimit-Limit']);
		$this->assertTrue($limit['X-RateLimit-Remaining'] > 0);
		$this->assertTrue($limit['X-RateLimit-Reset'] > time());
	}

}