<?php

/**
 * for Search API
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
 * @link      https://dev.twitter.com/docs/api/1/get/search
 *
 */
App::uses('TwimAppModel', 'Twim.Model');

/**
 * @method TwimSearch setExpandHashtag()
 * @method TwimSearch setExpandUrl()
 * @method array expandHashtag()
 * @method array expandUrl()
 */
class TwimSearch extends TwimAppModel {

	/**
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Twim.ExpandTweetEntity' => array('expandHashtag' => false, 'expandUrl' => false),
	);

	/**
	 * Custom find types available on this model
	 *
	 * @var array
	 */
	public $findMethods = array(
		'search' => true,
	);

	/**
	 * The options allowed by each of the custom find types
	 *
	 * @var array
	 */
	public $allowedFindOptions = array(
		'search' => array('lang', 'locale', 'max_id', 'q', 'rpp', 'page', 'since', 'since_id', 'geocode', 'show_user', 'until', 'result_type'),
	);

	/**
	 * Search API result data limit
	 *
	 * @var int
	 */
	public $resultLimit = 1500;

	/**
	 * Search API max number of rpp(result per page)
	 *
	 * @var int
	 */
	public $maxRpp = 100;

	/**
	 * for search API
	 *
	 * @var array
	 */
	public $request = array(
		'uri' => array('host' => 'search.twitter.com')
	);

	/**
	 *
	 * @param string $type
	 * @param array $options
	 * @return  array
	 */
	public function find($type, $options = array()) {
		if (is_string($type) && empty($options)) {
			$options = $type;
			$type = 'search';
		}

		if (is_string($options)) {
			$options = array('q' => $options);
		}

		$defaults = array('rpp' => $this->maxRpp, 'limit' => $this->resultLimit, 'users_lookup' => false);

		$options = array_merge($defaults, $options);

		if (!empty($options['limit']) && $options['limit'] <= $this->maxRpp) {
			$options['rpp'] = $options['limit'];
		}

		if (empty($options['page'])) {
			$options['page'] = 1;
			$results = array();
			while ($pageData = $this->find($type, $options)) {
				$results = array_merge($results, array_slice($pageData, 0, $options['limit'] - count($results)));
				$options['page']++;
				if ($options['rpp'] * $options['page'] > $this->resultLimit || count($results) >= $options['limit'] || empty($this->response['next_page'])) {
					break;
				}
			}
			return $results;
		}

		$this->_setupRequest($type, $options);

		$results = parent::find('all', $options);

		$results = isset($results['results']) ? $results['results'] : $results;

		if ($options['users_lookup']) {
			$results = $this->usersLookup($results, $options['users_lookup']);
		}

		return $results;
	}

	/**
	 * lookup user
	 *
	 * @param array $datas
	 * @param mixed $fields
	 * @return array
	 */
	public function usersLookup(array $datas, $fields = true) {
		if ($fields === true) {
			$fields = array();
		} else if (is_scalar($fields)) {
			$fields = array($fields);
		}
		$fields = array_flip($fields);

		//
		$screenNames = array_unique(Set::extract('/from_user', $datas));
		$sets = array_chunk($screenNames, 100);

		$users = array();

		foreach ($sets as $screenNames) {
			$result = $this->User->find('lookup', array('screen_name' => $screenNames));
			foreach ($result as $user) {
				$users[$user['screen_name']] = !empty($fields) ? array_intersect_key($user, $fields) : $user;
			}
		}

		//
		for ($i = 0; $i < count($datas); $i++) {
			if (isset($users[$datas[$i]['from_user']])) {
				$datas[$i]['user'] = $users[$datas[$i]['from_user']];
			}
		}

		return $datas;
	}

}
