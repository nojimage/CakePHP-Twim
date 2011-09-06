<?php

App::import('Core', array('Cache'));
App::import('Datasource', 'Rest.RestSource');

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
 * @package   twim
 * @see       http://dev.twitter.com/doc
 *
 */
class TwimSource extends RestSource {

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
     * Overrides RestSource constructor to use the OAuth extension to CakePHP's
     * default HttpSocket class to issue the requests.
     *
     * If no config is passed into the constructor, i.e. the config is not in
     * app/config/database.php check if any config is in the config directory of
     * the plugin, or in the configure class and use that instead.
     *
     * @param array $config
     */
    public function __construct($config = null) {

        if (!is_array($config)) {
            $config = array();
        }

        // Default config
        $defaults = array(
            'datasource' => 'Twim.TwimSource',
        );

        // Try and import the plugins/twim/config/twitter_config.php file and
        // merge the config with the defaults above
        if (App::import(array('type' => 'File', 'name' => 'Twim.TWITTER_CONFIG', 'file' => 'config' . DS . 'twitter_config.php'))) {
            $TWITTER_CONFIG = new TWITTER_CONFIG();
            if (isset($TWITTER_CONFIG->twitter)) {
                $defaults = array_merge($defaults, $TWITTER_CONFIG->twitter);
            }
        }

        // Add any config from Configure class that you might have added at any
        // point before the model is instantiated.
        if (($configureConfig = Configure::read('Twitter.config')) != false) {
            $defaults = array_merge($defaults, $configureConfig);
        }

        $config = array_merge($defaults, $config);

        App::import('Vendor', 'Twim.HttpSocketOauth');
        parent::__construct($config, new HttpSocketOauth());
    }

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
   * Adds in common elements to the request such as the host and extension and
   * OAuth params from config if not set in the request already
   *
   * @param AppModel $model The model the operation is called on. Should have a
   *  request property in the format described in HttpSocket::request
   * @return mixed Depending on what is returned from RestSource::request()
   */
  protected function _request(&$model) {

    // If auth key is set and not false, fill the request with auth params from
    // config if not already present in the request and set the method to OAuth
    // to trigger HttpSocketOauth to sign the request
    if (array_key_exists('auth', $model->request)
    && $model->request['auth'] !== false) {

      if (!is_array($model->request['auth'])) {
        $model->request['auth'] = array();
      }
      if (!isset($model->request['auth']['method'])) {
        $model->request['auth']['method'] = 'OAuth';
      }
      $oAuthParams = array(
        'oauth_consumer_key',
        'oauth_consumer_secret',
        'oauth_token',
        'oauth_token_secret',
      );
      foreach ($oAuthParams as $oAuthParam) {
        if (!isset($model->request['auth'][$oAuthParam])) {
          $model->request['auth'][$oAuthParam] = $this->config[$oAuthParam];
        }
      }
    }

    // Set default host, N.B. some API calls use api.twitter.com, in which case
    // they should be set in the individual model call
    if (!isset($model->request['uri']['host'])) {
      $model->request['uri']['host'] = 'api.twitter.com';
    }

    // Append '.json' to path if not already got an extension
    if (strpos($model->request['uri']['path'], '.') === false && !preg_match('!oauth/!i', $model->request['uri']['path'])) {
      $model->request['uri']['path'] .= '.json';
    }

    // Get the response from calling request on the Rest Source (it's parent)
    $response = parent::request($model);

    return $response;

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

            $response = $this->_request($model);

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
