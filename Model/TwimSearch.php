<?php

/**
 * for Search API
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
 * @link      https://dev.twitter.com/docs/api/1.1/get/search/tweets
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

	public $apiUrlBase = '1.1/search/';

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
		'tweets' => true,
	);

/**
 * The options allowed by each of the custom find types
 *
 * @var array
 */
	public $allowedFindOptions = array(
		'tweets' => array('q', 'geocode', 'lang', 'locale', 'result_type', 'count', 'until', 'since_id', 'max_id', 'include_entities', 'callback'),
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
	public $maxCount = 100;

/**
 *
 * @param string $type
 * @param array $options
 * @return array
 * @throws InvalidArgumentException
 */
	public function find($type = 'first', $options = array()) {
		if (is_string($type) && empty($options)) {
			$options = $type;
			$type = 'tweets';
		}
		if ($type === 'search') {
			$type = 'tweets';
		}

		if (is_string($options)) {
			$options = array('q' => $options);
		}

		if (empty($options['q'])) {
			throw new InvalidArgumentException(__d('twim', 'You must enter a query.'));
		}

		$defaults = array('count' => $this->maxCount, 'limit' => $this->resultLimit, 'strict' => false);

		$options = array_merge($defaults, $options);

		if (!empty($options['limit']) && $options['limit'] <= $this->maxCount) {
			$options['count'] = $options['limit'];
		}

		if (empty($options['page'])) {
			$options['page'] = 1;
			$results = array();
			try {
				while (($page = $this->find($type, $options)) != false) {
					$results = array_merge($results, $page);
					if (count($page) < $options['count']) {
						break;
					}
					if (!empty($options['limit']) && count($results) >= $options['limit']) {
						$results = array_slice($results, 0, $options['limit']);
						break;
					}
					// get next page
					if (isset($this->response['search_metadata']['next_results'])) {
						parse_str(parse_url($this->response['search_metadata']['next_results'], PHP_URL_QUERY), $nextPage);
						$options = am($options, $nextPage);
					} elseif (in_array('since_id', $this->allowedFindOptions[$type]) && !empty($options['since_id'])) {
						if (PHP_INT_SIZE === 4 && extension_loaded('bcmath')) {
							$options['since_id'] = bcadd($page[0]['id_str'], '1'); // for 32bit
						} else {
							$options['since_id'] = $page[0]['id'] + 1;
						}
					} elseif (in_array('max_id', $this->allowedFindOptions[$type])) {
						if (PHP_INT_SIZE === 4 && extension_loaded('bcmath')) {
							$options['max_id'] = bcsub($page[count($page) - 1]['id_str'], '1'); // for 32bit
						} else {
							$options['max_id'] = $page[count($page) - 1]['id'] - 1;
						}
					}
					// adjust count
					if (!empty($options['limit']) && $options['limit'] < count($results) + $options['count']) {
						$options['count'] = $options['limit'] - count($results);
					}
				}
			} catch (RuntimeException $e) {
				if ($options['strict']) {
					throw $e;
				}
				$this->log($e->getMessage(), LOG_DEBUG);
			}
			return $results;
		}

		$this->_setupRequest($type, $options);

		$results = parent::find('all', $options);

		$results = isset($results['statuses']) ? $results['statuses'] : $results;

		return $results;
	}

}
