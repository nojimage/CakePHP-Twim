<?php

/**
 * test TwimAppModel
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
App::uses('TwimAppModel', 'Twim.Model');
App::uses('TwimSource', 'Twim.Model/Datasource');

class TestTwimAppModel extends TwimAppModel {

	public $alias = 'TwimAppModel';

	public $useDbConfig = 'test_twitter_app';

}

class TwimTestOauth extends TestTwimAppModel {

	public $useDbConfig = 'test_twitter2';

}

App::uses('TestTwimAppModelTwimSource', 'Twim.Model/Datasource');
class TestTwimAppModelTwimSource extends TwimSource {
	//
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
		ClassRegistry::config(array('ds' => null));
		$this->Twim = ClassRegistry::init('TestTwimAppModel');
	}

	public function tearDown() {
		unset($this->Twim);
		parent::tearDown();
		ob_flush();
	}

	public function testConstruct() {
		$this->assertSame('cvEPr1xe1dxqZZd1UaifFA', $this->Twim->getDataSource()->config['oauth_consumer_key']);
	}

	public function testConstruct_with_ds() {
		ClassRegistry::flush();
		$this->Twim = ClassRegistry::init(array('class' => 'TestTwimAppModel', 'ds' => 'test_twitter2', 'testing' => false));
		$this->assertInstanceOf('TestTwimAppModelTwimSource', $this->Twim->getDataSource());
		$this->assertSame('testConsumerKey', $this->Twim->getDataSource()->config['oauth_consumer_key']);
	}

	public function testGetDataSource() {
		$this->assertInstanceOf('TwimSource', $this->Twim->getDataSource());
		$this->assertSame('cvEPr1xe1dxqZZd1UaifFA', $this->Twim->getDataSource()->config['oauth_consumer_key']);
	}

	public function testSetDataSource() {
		$this->assertSame('cvEPr1xe1dxqZZd1UaifFA', $this->Twim->getDataSource()->config['oauth_consumer_key']);
		$this->assertSame('testConsumerKey', $this->Twim->setDataSource('test_twitter2')->getDataSource()->config['oauth_consumer_key']);
	}

	public function testLoadTwimModel() {
		$this->assertInstanceOf('TwimTestOauth', $this->Twim->TestOauth);
		$this->assertSame('testConsumerKey', $this->Twim->TestOauth->getDataSource()->config['oauth_consumer_key']);
	}

}