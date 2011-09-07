<?php

App::import('Core', 'Security');

/**
 * TwitterAuthBehavior
 *
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link      http://php-tips.com/
 * @package   twim
 * @since     File available since Release 1.0
 */
class TwitterAuthBehavior extends ModelBehavior {

    public $name = 'TwitterAuth';
    public $default = array(
        'user_id' => 'id',
        'screen_name' => 'username',
        'password' => 'password',
        'oauth_token' => 'oauth_token',
        'oauth_token_secret' => 'oauth_token_secret',
    );

    /**
     *
     * @param AppModel $model
     * @param array    $config
     */
    public function setup($model, $config = array()) {
        $this->settings[$model->alias] = Set::merge($this->default, $config);
    }

    /**
     * create save data
     *
     * @param  AppModel $model
     * @param  array    $token
     * @return array
     */
    public function createSaveDataByToken($model, $token) {

        $data = array(
            $model->alias => array(
                $this->settings[$model->name]['user_id'] => $token['user_id'],
                $this->settings[$model->name]['screen_name'] => $token['screen_name'],
                $this->settings[$model->name]['oauth_token'] => $token['oauth_token'],
                $this->settings[$model->name]['oauth_token_secret'] => $token['oauth_token_secret'],
            ),
        );

        if ($model->hasField($this->settings[$model->name]['password'])) {
            $data[$model->alias][$this->settings[$model->name]['password']] = Security::hash($token['oauth_token']);
        }

        return $data;
    }

}