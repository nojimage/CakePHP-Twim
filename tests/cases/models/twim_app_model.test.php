<?php

/**
 * test TwimAppModel
 *
 * PHP versions 5
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link    　http://php-tips.com/
 * @since   　File available since Release 1.0
 *
 */
App::import('Model', array('Twim.TwimAppModel'));
App::import('Datasource', array('Twitter.TwitterSource'));

class TestTwimAppModel extends TwimAppModel {

    public $alias = 'TwimAppModel';
    public $useDbConfig = 'test_twitter';

}

class TwimTestOauth extends TestTwimAppModel {

}

class TestTwimAppModelTwitterSource extends TwitterSource {
    
}

ConnectionManager::create('test_twitter',
                array(
                    'datasource' => 'Twitter.TwitterSource',
                    'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
                    'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
        ));

ConnectionManager::create('test_twitter2',
                array(
                    'datasource' => 'TestTwimAppModelTwitterSource',
                    'oauth_consumer_key' => 'testConsumerKey',
                    'oauth_consumer_secret' => 'testConsumerSecret',
        ));

/**
 *
 * @property TwimAppModel $Twim
 */
class TwimAppModelTestCase extends CakeTestCase {

    function startTest($method) {
        $this->Twim = ClassRegistry::init('TestTwimAppModel');
    }

    function endTest($method) {
        unset($this->Twim);
        ClassRegistry::flush();
    }

    function test_construct() {
        $this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'cvEPr1xe1dxqZZd1UaifFA');
    }

    function test_construct_with_ds() {
        $this->Twim = ClassRegistry::init(array('class' => 'TestTwimAppModel', 'ds' => 'test_twitter2'));
        $this->assertIsA($this->Twim->getDataSource(), 'TestTwimAppModelTwitterSource');
        $this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'testConsumerKey');
    }

    function test_getDataSource() {
        $this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'cvEPr1xe1dxqZZd1UaifFA');
    }

    function test_setDataSource() {
        $this->assertIdentical($this->Twim->getDataSource()->config['oauth_consumer_key'], 'cvEPr1xe1dxqZZd1UaifFA');
        $this->assertIdentical($this->Twim->setDataSource('test_twitter2')->getDataSource()->config['oauth_consumer_key'], 'testConsumerKey');
    }

    function test_loadTwimModel() {
        $this->assertIsA($this->Twim->TestOauth, 'TwimTestOauth');
    }

}