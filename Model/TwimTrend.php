<?php

/**
 * for Trend API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/trends/place
 * @link      https://dev.twitter.com/docs/api/1.1/get/trends/available
 * @link      https://dev.twitter.com/docs/api/1.1/get/trends/closest
 *
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimTrend extends TwimAppModel {

	public $apiUrlBase = '1.1/trends/';

/**
 * Custom find type name
 */
	const FINDTYPE_PLACE = 'place';

	const FINDTYPE_AVAILABLE = 'available';

	const FINDTYPE_CLOSEST = 'closest';

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'place' => true,
		'available' => true,
		'closest' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'place' => array('id', 'exclude'),
		'available' => array('lat', 'long'),
		'closest' => array('lat', 'long'),
	);

/**
 *
 * @param mixed $type
 * @param array $options
 * @return array|false
 */
	public function find($type = 'first', $options = array()) {
		if (method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		return parent::find('all', $options);
	}

}
