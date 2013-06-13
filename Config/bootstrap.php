<?php

/**
 * plugin bootstrap
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
CakePlugin::load('Rest');

// read composer autoload file
$autoloadFile = dirname(dirname(__FILE__)) . DS . 'Vendor' . DS . 'autoload.php';
if (is_file($autoloadFile)) {
	require $autoloadFile;
}
