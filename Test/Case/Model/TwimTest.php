<?php

/**
 * test Twim model
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
 * @property Twim $Twim
 */
class TwimTestCase extends TwimConnectionTestCase {

	public function setUp() {
		parent::setUp();
		$this->Twim = ClassRegistry::init('Twim.Twim');
		$this->Twim->Search->setDataSource($this->mockDatasourceName);
	}

	public function tearDown() {
		unset($this->Twim);
		parent::tearDown();
	}

	public function testSerach() {
		$this->Twim->Search->getDataSource()->expects($this->once())->method('request')->will($this->returnValue(array('statuses' => array())));
		$q = 'test';
		$limit = 100;
		$results = $this->Twim->Search->find('search', compact('q', 'limit'));
		$this->assertSame('1.1/search/tweets', $this->Twim->Search->request['uri']['path']);
		$this->assertEquals(array('q' => 'test', 'count' => 100), $this->Twim->Search->request['uri']['query']);
	}

}
