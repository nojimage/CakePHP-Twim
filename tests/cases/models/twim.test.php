<?php

App::import('Model', array('Twim.Twim'));

class TestTwim extends Twim {

    public $alias = 'Twim';
    public $useDbConfig = 'test_twitter';

}

ConnectionManager::create('test_twitter',
                array(
                    'datasource' => 'Twim.TwimSource',
                    'oauth_consumer_key' => 'cvEPr1xe1dxqZZd1UaifFA',
                    'oauth_consumer_secret' => 'gOBMTs7Rw4Z3p5EhzqBey8ousRTwNDvreJskN8Z60',
        ));

/**
 *
 * @property Twim $Twim
 */
class TwimTestCase extends CakeTestCase {

    public function startTest($method) {
        $this->Twim = ClassRegistry::init('TestTwim');
    }

    public function endTest($method) {
        unset($this->Twim);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function test_serach() {
        $q = 'test';
        $limit = 100;
        $datas = $this->Twim->Search->find('search', compact('q', 'limit'));
        $this->assertTrue(!empty($datas));
        $this->assertIdentical(count($datas), 100);
    }

}