<?php

/**
 * for Help API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/help/configuration
 * @link      https://dev.twitter.com/docs/api/1.1/get/help/languages
 * @link      https://dev.twitter.com/docs/api/1.1/get/help/privacy
 * @link      https://dev.twitter.com/docs/api/1.1/get/help/tos
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimHelp extends TwimAppModel {

	public $apiUrlBase = '1.1/help/';

/**
 * Custom find type name
 */
	const FINDTYPE_CONFIGURATION = 'configuration';

	const FINDTYPE_LANGUAGES = 'languages';

	const FINDTYPE_PRIVACY = 'privacy';

	const FINDTYPE_TOS = 'tos';

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'configuration' => true,
		'languages' => true,
		'privacy' => true,
		'tos' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'configuration' => array(),
		'languages' => array(),
		'privacy' => array(),
		'tos' => array(),
	);

/**
 *
 * @param string $type
 * @param array $options
 * @return mixed
 */
	public function find($type = 'first', $options = array()) {
		if (method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		return parent::find('all', $options);
	}

/**
 * privacy
 * -------------
 *
 *     TwimHelp::find('privacy')
 *
 * @param $state string 'before' or 'after'
 * @param $query array
 * @param $results array
 * @return mixed
 * @access protected
 * */
	protected function _findPrivacy($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			$this->_setupRequest(self::FINDTYPE_PRIVACY, $query);
			return $query;
		} else {
			if (isset($results['privacy'])) {
				return $results['privacy'];
			}
			return $results;
		}
	}

/**
 * tos
 * -------------
 *
 *     TwimHelp::find('tos')
 *
 * @param $state string 'before' or 'after'
 * @param $query array
 * @param $results array
 * @return mixed
 * @access protected
 * */
	protected function _findTos($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			$this->_setupRequest(self::FINDTYPE_TOS, $query);
			return $query;
		} else {
			if (isset($results['tos'])) {
				return $results['tos'];
			}
			return $results;
		}
	}

}
