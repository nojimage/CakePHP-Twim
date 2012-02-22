<?php

/**
 * test TwimUser (need Auth)
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
App::import('Model', 'Twim.TwimUser');

/**
 *
 * @property TwimUser $User
 */
class TwimUserNeedAuthTestCase extends TwimConnectionTestCase {

    public $needAuth = true;

    public function startTest() {
        $this->User = ClassRegistry::init('Twim.TwimUser');
    }

    public function endTest() {
        unset($this->User);
        ClassRegistry::flush();
    }

    // =========================================================================
    public function testSearch() {
        $results = $this->User->find('search', array('q' => 'cake'));
        $this->assertTrue(isset($results[0]['screen_name']));
    }

}
