<?php

/**
 * TwitterUserFixture
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
 */
class TwitterUserFixture extends CakeTestFixture {

	public $name = 'TwitterUser';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'oauth_token' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 128),
		'oauth_token_secret' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 128),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
	);

	public $records = array(
		array(
			'id' => '12345678',
			'created' => '2011-09-08 13:02:58',
			'modified' => '2011-09-08 14:43:27',
			'username' => 'dummry user',
			'password' => 'dummry password',
			'oauth_token' => '12345678-token',
			'oauth_token_secret' => 'dummy_token_secret',
		),
	);

}
