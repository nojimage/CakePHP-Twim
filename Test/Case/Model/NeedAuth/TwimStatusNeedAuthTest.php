<?php

/**
 * test TwimStatus (need Auth)
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
App::import('Lib', 'Twim.TwimConnectionTestCase');
App::import('Model', 'Twim.TwimStatus');

/**
 *
 * @property TwimStatus $Status
 */
class TwimStatusNeedAuthTestCase extends TwimConnectionTestCase {

    public $needAuth = true;

    public function startTest() {
        $this->Status = ClassRegistry::init('Twim.TwimStatus');
    }

    public function endTest() {
        unset($this->Status);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function testTweet_and_delete() {

        $data = array(
            'TwimStatus' => array(
                'text' => 'test tweet ' . time(),
            ),
        );
        $this->assertTrue($this->Status->tweet($data), 'can\'t tweet: %s');
        $result = $this->Status->find('show', array('id' => $this->Status->getLastInsertID()));
        $this->assertIdentical($result['text'], $data['TwimStatus']['text']);
        $this->assertTrue($this->Status->delete($this->Status->getLastInsertID()), 'can\'t remove tweet: %s');
    }

}
