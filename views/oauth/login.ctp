<?php
/**
 * Twim users/login view
 *
 * PHP version 5
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
 * @since     File available since Release 1.0
 * */
$this->set('title_for_layout', __d('twim', 'Login', true));
$connectUrl = array('action' => 'connect');
if (!empty($linkOptions)) {
    $connectUrl = am($connectUrl, $linkOptions);
}
?>
<h1><?php __d('twim', 'Login'); ?></h1>
<div class="login-wrap">
    <?php if (!$this->Session->check('Auth.User')) : ?>
        <p class="login"><?php echo $this->Html->image('/twim/img/sign-in-with-twitter-d.png', array('alt' => __d('twim', 'Sign in with Twitter', true), 'url' => array('action' => 'connect'))); ?></p>
    <?php else: ?>
        <p class="logout"><?php echo $this->Html->link(__d('twim', 'Logout', true), array('action' => 'logout')) ?></p>
    <?php endif; ?>
</div>