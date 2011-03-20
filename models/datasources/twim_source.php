<?php

App::import('Core', array('Cache'));
App::import('Datasource', 'Twitter.TwitterSource');

/**
 * Twitter API Datasource
 *
 * for CakePHP 1.3+
 * PHP version 5.2+
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
 * @see       http://dev.twitter.com/doc
 *
 */
class TwimSource extends TwitterSource {

    /**
     *
     * @var array
     */
    public $_baseConfig = array(
        'oauth_consumer_key' => '',
        'oauth_consumer_secret' => '',
        'oauth_token' => '',
        'oauth_token_secret' => '',
        'oauth_callback' => '',
        'throw_exception' => true,
        'cache' => false,
        'refresh_cache' => false,
    );

    /**
     * Enable Cache
     *
     * @params mixed $config
     */
    public function enableCache($config = true) {
        $this->setConfig(array('cache' => $config));
    }

    /**
     * Next request force update cache
     */
    public function refreshCache() {
        $this->setConfig(array('refresh_cache' => true));
    }

    /**
     * Request API and process responce
     *
     * @param TwimAppModel $model
     * @return mixed
     */
    public function request(&$model) {

        if ($this->_cacheable($model->request)) {
            $this->_setupCache();
        }

        $response = array();

        if ($this->_cacheable($model->request) && !$this->config['refresh_cache']) {
            // get Cache, only GET method
            $response = Cache::read($this->_getCacheKey($model->request), $this->configKeyName);
        }

        if (empty($response)) {

            $response = parent::request($model);

            if ($this->_cacheable($model->request)) {
                // save Cache, only GET method
                $cache = Cache::write($this->_getCacheKey($model->request), $response, $this->configKeyName);
                $this->config['refresh_cache'] = false;
            }
        }

        return $response;
    }

    /**
     * get Cache key
     *
     * @param array $params
     * @return stirng
     */
    protected function _getCacheKey($params) {
        return sha1($this->config['oauth_token'] . serialize($params));
    }

    /**
     *
     */
    protected function _setupCache() {

        if ($this->config['cache'] && !Cache::isInitialized($this->configKeyName)) {

            if (!is_array($this->config['cache'])) {

                $this->config['cache'] = array(
                    'engine' => 'File',
                    'duration' => '+5 min',
                    'path' => CACHE . 'twitter' . DS,
                    'prefix' => 'cake_' . Inflector::underscore($this->configKeyName) . '_',
                );
            }

            Cache::config($this->configKeyName, $this->config['cache']);
        }
    }

    /**
     * is cacheable
     *
     * @param array $params
     * @return bool
     */
    protected function _cacheable($request) {
        return $this->config['cache'] && strtoupper($request['method']) === 'GET' && !preg_match('!oauth/!i', $request['uri']['path']);
    }

}
