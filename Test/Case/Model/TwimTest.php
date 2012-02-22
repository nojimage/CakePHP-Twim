<?php

App::uses('TwimConnectionTestCase', 'Twim.TestSuite');

/**
 *
 * @property Twim $Twim
 */
class TwimTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Twim = ClassRegistry::init('Twim.Twim');
	}

	public function tearDown() {
		unset($this->Twim);
		parent::tearDown();
	}

	public function testSerach() {
		$q = 'test';
		$limit = 100;
		$results = $this->Twim->Search->find('search', compact('q', 'limit'));
		$this->assertTrue(!empty($results));
		$this->assertIdentical(count($results), 100);
	}

}
