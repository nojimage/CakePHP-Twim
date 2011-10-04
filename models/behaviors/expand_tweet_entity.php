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
        'expand_hashtag' => true,
        'expand_url' => true,
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
        $this->settings[$model->alias]['expand_hashtag'] = $flag;
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
        $this->settings[$model->alias]['expand_url'] = $flag;
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
                && ($this->settings[$model->alias]['expand_hashtag'] || $this->settings[$model->alias]['expand_url'])) {
            $model->request['uri']['query']['include_entities'] = true;
        }

        return $query;
    }

    /**
     * Expand hashtags
     *
     * @param TwimAppModel $model
     * @param array $tweet
     * @return array
     */
    public function expandHashtag($model, array $tweet = null) {

        if (is_array($model) && empty($tweet)) {
            $tweet = $model;
        }

        if (empty($tweet) || empty($tweet['entities']['hashtags'])) {
            return $tweet;
        }

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
            $tweet['text'] = preg_replace('/' . preg_quote("#{$hashtag['text']}", '/') . '/', $hashtagLink, $tweet['text'], 1);
        }

        return $tweet;
    }

    /**
     * Expand urls
     *
     * @param TwimAppModel $model
     * @param array $tweet
     * @return array
     */
    public function expandUrl($model, array $tweet = null) {

        if (is_array($model) && empty($tweet)) {
            $tweet = $model;
        }

        if (empty($tweet) || empty($tweet['entities']['urls'])) {
            return $tweet;
        }

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
            $tweet['text'] = preg_replace('/' . preg_quote($url['url'], '/') . '/', $urlLink, $tweet['text'], 1);
        }

        return $tweet;
    }

    /**
     * Expand urls (string)
     *
     * @param TwimAppModel $model
     * @param array $tweet
     * @return array
     */
    public function expandUrlString($model, array $tweet = null) {

        if (is_array($model) && empty($tweet)) {
            $tweet = $model;
        }

        if (empty($tweet) || empty($tweet['entities']['urls'])) {
            return $tweet;
        }

        foreach ($tweet['entities']['urls'] as $url) {

            if (empty($url['expanded_url'])) {
                $url['expanded_url'] = $url['url'];
            }

            $tweet['text'] = preg_replace('/' . preg_quote($url['url'], '/') . '/', h($url['expanded_url']), $tweet['text'], 1);
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

        if ($this->settings[$model->alias]['expand_hashtag']) {
            if (isset($results['results'])) {
                $results['results'] = array_map(array($this, 'expandHashtag'), $results['results']);
            } else if (Set::numeric(array_keys($results))) {
                $results = array_map(array($this, 'expandHashtag'), $results);
            } else {
                $results = $this->expandHashtag($results);
            }
        }

        if ($this->settings[$model->alias]['expand_url'] === 'string') {
            if (isset($results['results'])) {
                $results['results'] = array_map(array($this, 'expandUrlString'), $results['results']);
            } else if (Set::numeric(array_keys($results))) {
                $results = array_map(array($this, 'expandUrlString'), $results);
            } else {
                $results = $this->expandUrlString($results);
            }
        } else {
            if (isset($results['results'])) {
                $results['results'] = array_map(array($this, 'expandUrl'), $results['results']);
            } else if (Set::numeric(array_keys($results))) {
                $results = array_map(array($this, 'expandUrl'), $results);
            } else {
                $results = $this->expandUrl($results);
            }
        }

        return $results;
    }

}
