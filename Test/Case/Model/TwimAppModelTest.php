<?php

/**
 * test TwimAppModel
 *
 * PHP versions 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
 *
 */
App::uses('TwimConnectionTestCase', 'Twim.TestSuite');
App::uses('TwimAppModel', 'Twim.Model');
App::uses('TwimSource', 'Twim.Model/Datasource');

class TestTwimAppModel extends TwimAppModel {

	public $alias = 'TwimAppModel';

	public $useDbConfig = 'test_twitter_app';

}

class TwimTestOauth extends TestTwimAppModel {

	public $useDbConfig = 'test_twitter2';

}

class TestTwimAppModelTwimSource extends TwimSource {
	
}

/**
 *
 * @property TwimAppModel $Twim
 */
class TwimAppModelTestCase extends TwimConnectionTestCase {

	public $connectionCheck = false;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		ConnectionManager::create('test_twitter_app', array(
			'datasource' => 'Twim.TwimSource',
			'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
			'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
		));

		ConnectionManager::create('test_twitter2', array(
			'datasource' => 'TestTwimAppModelTwimSource',
			'oauth_consumer_key' => 'testConsumerKey',
			'oauth_consumer_secret' => 'testConsumerSecret',
		));
	}

	public static function tearDownAfterClass() {
		ConnectionManager::drop('test_twitter_app');
		ConnectionManager::drop('test_twitter2');
		parent::tearDownAfterClass();
	}

	public function setUp() {
		parent::setUp();
		$this->Twim = ClassRegistry::init('TestTwimAppModel');
	}

	public function tearDown() {
		unset($this->Twim);
		parent::tearDown();
	}

	public function testConstruct() {
		$this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'cvEPr1xe1dxqZZd1UaifFA');
	}

	public function testConstruct_with_ds() {
		$this->Twim = ClassRegistry::init(array('class' => 'TestTwimAppModel', 'ds' => 'test_twitter2'));
		$this->assertIsA($this->Twim->getDataSource(), 'TestTwimAppModelTwimSource');
		$this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'testConsumerKey');
	}

	public function testGetDataSource() {
		$this->assertIsA($this->Twim->getDataSource(), 'TwimSource');
		$this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'cvEPr1xe1dxqZZd1UaifFA');
	}

	public function testSetDataSource() {
		$this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'cvEPr1xe1dxqZZd1UaifFA');
		$this->assertIdentical($this->Twim->setDataSource('test_twitter2')->getDataSource()->config['oauth_consumer_key'], 'testConsumerKey');
	}

	public function testLoadTwimModel() {
		$this->assertIsA($this->Twim->TestOauth, 'TwimTestOauth');
		$this->assertIdentical($this->Twim->TestOauth->getDataSource()->config['oauth_consumer_key'], 'testConsumerKey');
	}

}