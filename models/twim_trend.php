<?php

/**
 * for Trend API
 *
 * PHP versions 5
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package   twim
 * @since   　File available since Release 1.0
 * @see       http://dev.twitter.com/doc/get/trends
 * @see       http://dev.twitter.com/doc/get/trends/current
 * @see       http://dev.twitter.com/doc/get/trends/daily
 * @see       http://dev.twitter.com/doc/get/trends/weekly
 * @see       http://dev.twitter.com/doc/get/trends/available
 * @see       http://dev.twitter.com/doc/get/trends/1
 *
 */
class TwimTrend extends TwimAppModel {

    public $apiUrlBase = '1/trends/';

    /**
     * Custom find types available on this model
     *
     * @var array
     */
    public $_findMethods = array(
        'trends' => true,
        'current' => true,
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
        'trends' => array(),
        'current' => array('exclude'),
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
            return!empty($results[0]) ? $results[0] : $results;
        } else {
            return $results;
        }
    }

}