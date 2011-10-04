<?php

/**
 * ExpandTweetEntityBehavior
 *
 * @version   1.0
 * @author    nojimage <nojimage at gmail.com>
 * @copyright 2011 nojimage (http://php-tips.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link      http://php-tips.com/
 * @package   twim
 * @since     File available since Release 1.0
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
    public function setup($model, $config = array()) {
        $this->settings[$model->alias] = Set::merge($this->defaults, $config);
    }

    /**
     * set expand hashtag flag
     *
     * @param TwimAppModel $model
     * @param bool $flag
     * @return TwimAppModel
     */
    public function setExpandHashtag($model, $flag = true) {
        $this->settings[$model->alias]['expandHashtag'] = $flag;
        return $model;
    }

    /**
     * set expand url flag
     *
     * @param TwimAppModel $model
     * @param mixed $flag true|false|'string'
     * @return TwimAppModel
     */
    public function setExpandUrl($model, $flag = true) {
        $this->settings[$model->alias]['expandUrl'] = $flag;
        return $model;
    }

    /**
     * set override text flag
     *
     * @param TwimAppModel $model
     * @param mixed $flag true|false
     * @return TwimAppModel
     */
    public function setOverrideText($model, $flag = true) {
        $this->settings[$model->alias]['overrideText'] = $flag;
        return $model;
    }

    /**
     * set include_entities flag
     *
     * @param TwimAppModel $model
     * @param array $query
     * @return array
     */
    public function beforeFind($model, $query) {

        if (!isset($query['include_entities'])
                && ($this->settings[$model->alias]['expandHashtag'] || $this->settings[$model->alias]['expandUrl'])) {
            $model->request['uri']['query']['include_entities'] = true;
        }

        return $query;
    }

    /**
     * Expand hashtags
     *
     * @param TwimAppModel $model
     * @param mixed $tweet
     * @param bool $override
     * @return array
     */
    public function expandHashtag($model, $tweet = null, $override = false) {

        if (is_array($model) && (empty($tweet) || is_bool($tweet))) {
            $override = $tweet;
            $tweet = $model;
        }

        if (empty($tweet) || empty($tweet['entities']['hashtags'])) {
            return $tweet;
        }

        $filedName = 'expanded_text';
        if ($override) {
            $filedName = 'text';
        }
        $tweet[$filedName] = isset($tweet[$filedName]) ? $tweet[$filedName] : $tweet['text'];

        foreach ($tweet['entities']['hashtags'] as $hashtag) {
            $hash = h("#{$hashtag['text']}");
            $data = array(
                "http://twitter.com/#!/search?q=" . urlencode($hash),
                h($hash),
                'twitter-hashtag',
                'external nofollow',
                h($hash),
            );
            $hashtagLink = vsprintf('<a href="%s" title="%s" class="%s" rel="%s">%s</a>', $data);
            $tweet[$filedName] = preg_replace('/' . preg_quote("#{$hashtag['text']}", '/') . '/', $hashtagLink, $tweet[$filedName], 1);
        }

        return $tweet;
    }

    /**
     * Expand urls
     *
     * @param TwimAppModel $model
     * @param mixed $tweet
     * @param bool $override
     * @return array
     */
    public function expandUrl($model, $tweet = null, $override = false) {

        if (is_array($model) && (empty($tweet) || is_bool($tweet))) {
            $override = $tweet;
            $tweet = $model;
        }

        if (empty($tweet) || empty($tweet['entities']['urls'])) {
            return $tweet;
        }

        $filedName = 'expanded_text';
        if ($override) {
            $filedName = 'text';
        }
        $tweet[$filedName] = isset($tweet[$filedName]) ? $tweet[$filedName] : $tweet['text'];


        foreach ($tweet['entities']['urls'] as $url) {

            if (empty($url['expanded_url'])) {
                $url['expanded_url'] = $url['url'];
            }

            $data = array(
                h($url['url']),
                h($url['expanded_url']),
                'twitter-timeline-link',
                'external nofollow',
                h($url['display_url']),
            );
            $urlLink = vsprintf('<a href="%s" title="%s" class="%s" rel="%s">%s</a>', $data);
            $tweet[$filedName] = preg_replace('/' . preg_quote($url['url'], '/') . '/', $urlLink, $tweet[$filedName], 1);
        }

        return $tweet;
    }

    /**
     * Expand urls (string)
     *
     * @param TwimAppModel $model
     * @param mixed $tweet
     * @param bool $override
     * @return array
     */
    public function expandUrlString($model, $tweet = null, $override = false) {

        if (is_array($model) && (empty($tweet) || is_bool($tweet))) {
            $override = $tweet;
            $tweet = $model;
        }

        if (empty($tweet) || empty($tweet['entities']['urls'])) {
            return $tweet;
        }

        $filedName = 'expanded_text';
        if ($override) {
            $filedName = 'text';
        }
        $tweet[$filedName] = isset($tweet[$filedName]) ? $tweet[$filedName] : $tweet['text'];


        foreach ($tweet['entities']['urls'] as $url) {

            if (empty($url['expanded_url'])) {
                $url['expanded_url'] = $url['url'];
            }

            $tweet[$filedName] = preg_replace('/' . preg_quote($url['url'], '/') . '/', h($url['expanded_url']), $tweet[$filedName], 1);
        }

        return $tweet;
    }

    /**
     * expand tweet
     *
     * @param TwimAppModel $model
     * @param array $results
     * @param bool $primary
     * @return array
     */
    public function afterFind($model, $results, $primary) {

        if (empty($results)) {
            return $results;
        }

        $override = $this->settings[$model->alias]['overrideText'];

        if ($this->settings[$model->alias]['expandHashtag']) {
            if (isset($results['results'])) {
                $results['results'] = array_map(array($this, 'expandHashtag'), $results['results'], array($override));
            } else if (Set::numeric(array_keys($results))) {
                $results = array_map(array($this, 'expandHashtag'), $results, array($override));
            } else {
                $results = $this->expandHashtag($results, $override);
            }
        }

        if ($this->settings[$model->alias]['expandUrl'] === 'string') {
            if (isset($results['results'])) {
                $results['results'] = array_map(array($this, 'expandUrlString'), $results['results'], array($override));
            } else if (Set::numeric(array_keys($results))) {
                $results = array_map(array($this, 'expandUrlString'), $results, array($override));
            } else {
                $results = $this->expandUrlString($results, $override);
            }
        } else {
            if (isset($results['results'])) {
                $results['results'] = array_map(array($this, 'expandUrl'), $results['results'], array($override));
            } else if (Set::numeric(array_keys($results))) {
                $results = array_map(array($this, 'expandUrl'), $results, array($override));
            } else {
                $results = $this->expandUrl($results, $override);
            }
        }

        return $results;
    }

}
