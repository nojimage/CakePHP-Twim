<?php

/**
 * for Application API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/application/rate_limit_status
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 *
 */
class TwimApplication extends TwimAppModel {

	public $apiUrlBase = '1.1/application/';

/**
 * Custom find type name
 */
	const FINDTYPE_RATE_LIMIT_STATUS = 'rateLimitStatus';

/**
 * Custom find types available on this model
 *
 * @var array
 */
	public $findMethods = array(
		'rateLimitStatus' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'rateLimitStatus' => array('resources'),
	);

/**
 * The vast majority of the custom find types actually follow the same format
 * so there was little point explicitly writing them all out. Instead, if the
 * method corresponding to the custom find type doesn't exist, the options are
 * applied to the model's request property here and then we just call
 * parent::find('all') to actually trigger the request and return the response
 * from the API.
 *
 * In addition, if you try to fetch a timeline that supports paging, but you
 * don't specify paging params, you really want all tweets in that timeline
 * since time imemoriam. But twitter will only return a maximum of 200 per
 * request. So, we make multiple calls to the API for 200 tweets at a go, for
 * subsequent pages, then merge the results together before returning them.
 *
 * Twitter's API uses a count parameter where in CakePHP we'd normally use
 * limit, so we also copy the limit value to count so we can use our familiar
 * params.
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
 * get rate limit
 *
 * @param mixed $resources
 * @return mixed
 * @throws RuntimeException
 */
	public function getRateLimit($resources = null) {
		$options = array();
		if (!empty($resources)) {
			$options = array('resources' => $this->_buildRequestResources($resources));
		}

		$statuses = $this->find(self::FINDTYPE_RATE_LIMIT_STATUS, $options);

		if (empty($statuses['resources'])) {
			throw new RuntimeException(__d('twim', 'Can\'t get rate limit status response data'));
		}

		// format
		$statuses = $statuses['resources'];
		$result = array();
		foreach ($statuses as $group => $groupStatus) {
			foreach ($groupStatus as $name => $stats) {
				$result[$name] = $stats['remaining'];
			}
		}

		// return single value
		if (is_string($resources) && isset($result['/' . trim($resources, '/')])) {
			return $result['/' . trim($resources, '/')];
		}

		return $result;
	}

/**
 * 
 * @param mixed $resources
 * @return string
 */
	protected function _buildRequestResources($resources) {
		if (is_string($resources)) {
			$resources = array_map('trim', explode(',', $resources));
		}
		$resources = preg_replace('!^[/]*([^/]+).*$!', '$1', $resources);
		$resources = array_unique($resources);
		return join(',', $resources);
	}

}
