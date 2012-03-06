<?php

/**
 * for Trend API
 *
 * CakePHP 2.0
 * PHP version 5
 *
 * Copyright 2012, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   2.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2012 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   Twim
 * @since     File available since Release 1.0
 *
 * @link      https://dev.twitter.com/docs/api/1/get/trends/daily
 * @link      https://dev.twitter.com/docs/api/1/get/trends/weekly
 * @link      https://dev.twitter.com/docs/api/1/get/trends/available
 * @link      https://dev.twitter.com/docs/api/1/get/trends/%3Awoeid
 *
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimTrend extends TwimAppModel {

	public $apiUrlBase = '1/trends/';

	/**
	 * Custom find types available on this model
	 *
	 * @var array
	 */
	public $findMethods = array(
		'daily' => true,
		'weekly' => true,
		'available' => true,
		'woeid' => true,
	);

	/**
	 * The options allowed by each of the custom find types
	 *
	 * @var array
	 */
	public $allowedFindOptions = array(
		'daily' => array('exclude', 'date'),
		'weekly' => array('exclude', 'date'),
		'available' => array('lat', 'long'),
		'woeid' => array('woeid'),
	);

	/**
	 *
	 * @param mixed $type
	 * @param array $options
	 * @return array|false
	 */
	public function find($type, $options = array()) {
		if (method_exists($this, '_find' . Inflector::camelize($type))) {
			return parent::find($type, $options);
		}

		$this->_setupRequest($type, $options);

		if ($type === 'trends') {
			$this->request['uri']['path'] = Inflector::underscore($type);
		}

		return parent::find('all', $options);
	}

	/**
	 *
	 * @param string $state
	 * @param array $query
	 * @param array $results
	 * @return mixed
	 */
	protected function _findWoeid($state, $query = array(), $results = array()) {
		if ($state === 'before') {
			if (empty($query['woeid'])) {
				return $query;
			}

			$this->_setupRequest('woeid', $query);

			$this->request['uri']['path'] = $this->apiUrlBase . $query['woeid'];
			unset($query['woeid']);
			unset($this->request['uri']['query']['woeid']);

			return $query;
		} else if ($state === 'after') {
			return !empty($results[0]) ? $results[0] : $results;
		} else {
			return $results;
		}
	}

}
