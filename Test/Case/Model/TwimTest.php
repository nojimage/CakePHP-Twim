<?php

App::import('Lib', 'Twim.TwimConnectionTestCase');
App::import('Model', array('Twim.Twim'));

/**
 *
 * @property Twim $Twim
 */
class TwimTestCase extends TwimConnectionTestCase {

    public function startTest($method) {
        $this->Twim = ClassRegistry::init('Twim');
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