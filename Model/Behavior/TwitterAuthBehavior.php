<?php

/**
 * TwitterAuthBehavior
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
App::uses('ModelBehavior', 'Model');
App::uses('Security', 'Utility');

/**
 *
 */
class TwitterAuthBehavior extends ModelBehavior {

	public $name = 'TwitterAuth';

	public $default = array(
		'user_id' => 'id',
		'screen_name' => 'username',
		'password' => 'password',
		'oauth_token' => 'oauth_token',
		'oauth_token_secret' => 'oauth_token_secret',
	);

/**
 *
 * @param AppModel $model
 * @param array    $config
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = Set::merge($this->default, $config);
	}

/**
 * create save data
 *
 * @param  AppModel $model
 * @param  array    $token
 * @return array
 */
	public function createSaveDataByToken(Model $model, $token) {
		$data = array(
			$model->alias => array(
				$this->settings[$model->alias]['user_id'] => $token['user_id'],
				$this->settings[$model->alias]['screen_name'] => $token['screen_name'],
				$this->settings[$model->alias]['oauth_token'] => $token['oauth_token'],
				$this->settings[$model->alias]['oauth_token_secret'] => $token['oauth_token_secret'],
			),
		);

		if ($model->hasField($this->settings[$model->alias]['password'])) {
			$data[$model->alias][$this->settings[$model->alias]['password']] = Security::hash($token['oauth_token']);
		}

		return $data;
	}

}
