<?php

/**
 * ExpandTweetEntityBehavior
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

/**
 *
 */
class ExpandTweetEntityBehavior extends ModelBehavior {

	public $name = 'ExpandTweetEntity';

	public $defaults = array(
		'expandHashtag' => true,
		'expandUrl' => true,
		'overrideText' => false,
	);

/**
 *
 * @param TwimAppModel $model
 * @param array    $config
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->name] = Set::merge($this->defaults, $config);
	}

/**
 * set expand hashtag flag
 *
 * @param TwimAppModel $model
 * @param bool $flag
 * @return TwimAppModel
 */
	public function setExpandHashtag(Model $model, $flag = true) {
		$this->settings[$model->name]['expandHashtag'] = $flag;
		return $model;
	}

/**
 * set expand url flag
 *
 * @param TwimAppModel $model
 * @param mixed $flag true|false|'string'
 * @return TwimAppModel
 */
	public function setExpandUrl(Model $model, $flag = true) {
		$this->settings[$model->name]['expandUrl'] = $flag;
		return $model;
	}

/**
 * set override text flag
 *
 * @param TwimAppModel $model
 * @param mixed $flag true|false
 * @return TwimAppModel
 */
	public function setOverrideText(Model $model, $flag = true) {
		$this->settings[$model->name]['overrideText'] = $flag;
		return $model;
	}

/**
 * set include_entities flag
 *
 * @param TwimAppModel $model
 * @param array $query
 * @return array
 */
	public function beforeFind(Model $model, $query) {
		if (!isset($query['include_entities'])
			&& ($this->settings[$model->name]['expandHashtag'] || $this->settings[$model->name]['expandUrl'])) {
			$model->request['uri']['query']['include_entities'] = true;
		}

		return $query;
	}

/**
 * expand tweet
 *
 * @param TwimAppModel $model
 * @param array $results
 * @param bool $primary
 * @return array
 */
	public function afterFind(Model $model, $results, $primary = false) {
		if (empty($results)) {
			return $results;
		}

		$override = $this->settings[$model->name]['overrideText'];

		if ($this->settings[$model->name]['expandHashtag']) {
			if (!empty($results['statuses'])) {
				$results['statuses'] = array_map(array($model, 'expandHashtag'), $results['statuses'], array_fill(0, count($results['statuses']), $override));
			} else if (!empty($results['results'])) {
				$results['results'] = array_map(array($model, 'expandHashtag'), $results['results'], array_fill(0, count($results['results']), $override));
			} else if (Set::numeric(array_keys($results))) {
				$results = array_map(array($model, 'expandHashtag'), $results, array_fill(0, count($results), $override));
			} else if (!empty($results)) {
				$results = $this->expandHashtag($model, $results, $override);
			}
		}

		if ($this->settings[$model->name]['expandUrl'] === 'string') {
			if (!empty($results['statuses'])) {
				$results['statuses'] = array_map(array($model, 'expandUrlString'), $results['statuses'], array_fill(0, count($results['statuses']), $override));
			} else if (!empty($results['results'])) {
				$results['results'] = array_map(array($model, 'expandUrlString'), $results['results'], array_fill(0, count($results['results']), $override));
			} else if (Set::numeric(array_keys($results))) {
				$results = array_map(array($model, 'expandUrlString'), $results, array_fill(0, count($results), $override));
			} else if (!empty($results)) {
				$results = $this->expandUrlString($model, $results, $override);
			}
		} else {
			if (!empty($results['statuses'])) {
				$results['statuses'] = array_map(array($model, 'expandUrl'), $results['statuses'], array_fill(0, count($results['statuses']), $override));
			} else if (!empty($results['results'])) {
				$results['results'] = array_map(array($model, 'expandUrl'), $results['results'], array_fill(0, count($results['results']), $override));
			} else if (Set::numeric(array_keys($results))) {
				$results = array_map(array($model, 'expandUrl'), $results, array_fill(0, count($results), $override));
			} else if (!empty($results)) {
				$results = $this->expandUrl($model, $results, $override);
			}
		}

		return $results;
	}

/**
 * Expand hashtags
 *
 * @param TwimAppModel $model
 * @param mixed $tweet
 * @param bool $override
 * @return array
 */
	public function expandHashtag(Model $model, $tweet = null, $override = false) {
		return $this->_expand('_expandHashtag', 'hashtags', $model, $tweet, $override);
	}

/**
 * Expand urls
 *
 * @param TwimAppModel $model
 * @param mixed $tweet
 * @param bool $override
 * @return array
 */
	public function expandUrl(Model $model, $tweet = null, $override = false) {
		$tweet = $this->_expand('_expandUrl', 'urls', $model, $tweet, $override);
		return $this->_expand('_expandUrl', 'media', $model, $tweet, $override);
	}

/**
 * Expand urls (string)
 *
 * @param TwimAppModel $model
 * @param mixed $tweet
 * @param bool $override
 * @return array
 */
	public function expandUrlString(Model $model, $tweet = null, $override = false) {
		$tweet = $this->_expand('_expandUrlString', 'urls', $model, $tweet, $override);
		return $this->_expand('_expandUrlString', 'media', $model, $tweet, $override);
	}

/**
 * epand text
 *
 * @param string $func
 * @param string $entityField
 * @param mixed $model
 * @param mixed $tweet
 * @param bool $override
 * @return array
 */
	protected function _expand($func, $entityField, $model, $tweet = null, $override = false) {
		if (is_array($model) && (empty($tweet) || is_bool($tweet))) {
			$override = $tweet;
			$tweet = $model;
		}

		if (empty($tweet) || empty($tweet['text'])) {
			return $tweet;
		}

		$filedName = 'expanded_text';
		if ($override) {
			$filedName = 'text';
		}
		$tweet[$filedName] = isset($tweet[$filedName]) ? $tweet[$filedName] : $tweet['text'];

		if (empty($tweet['entities'][$entityField])) {
			return $tweet;
		}

		foreach ($tweet['entities'][$entityField] as $entity) {
			$tweet[$filedName] = $this->{$func}($tweet[$filedName], $entity);
		}

		return $tweet;
	}

/**
 *
 * @param string $text
 * @param array $entity
 * @return string
 */
	protected function _expandHashtag($text, array $entity) {
		$hash = h("#{$entity['text']}");
		$data = array(
			"http://twitter.com/#!/search?q=" . urlencode($hash),
			h($hash),
			'twitter-hashtag',
			'external nofollow',
			h($hash),
		);
		$hashtagLink = vsprintf('<a href="%s" title="%s" class="%s" rel="%s">%s</a>', $data);

		return preg_replace('/(^|[^"^>])' . preg_quote($hash, '/') . '([^"^<]|$)/u', "$1{$hashtagLink}$2", $text, 1);
	}

/**
 *
 * @param string $text
 * @param array $entity
 * @return string
 */
	protected function _expandUrl($text, array $entity) {
		if (empty($entity['expanded_url'])) {
			$entity['expanded_url'] = $entity['url'];
		}

		if (empty($entity['display_url'])) {
			$entity['display_url'] = $entity['url'];
		}

		$data = array(
			h($entity['url']),
			h($entity['expanded_url']),
			'twitter-timeline-link',
			'external nofollow',
			h($entity['display_url']),
		);
		$urlLink = vsprintf('<a href="%s" title="%s" class="%s" rel="%s">%s</a>', $data);
		return preg_replace('/(^|[^"])' . preg_quote($entity['url'], '/') . '([^"]|$)/u', "$1{$urlLink}$2", $text, 1);
	}

/**
 *
 * @param string $text
 * @param array $entity
 * @return string
 */
	protected function _expandUrlString($text, array $entity) {
		if (empty($entity['expanded_url'])) {
			$entity['expanded_url'] = $entity['url'];
		}
		return preg_replace('/' . preg_quote($entity['url'], '/') . '/', h($entity['expanded_url']), $text, 1);
	}

}
