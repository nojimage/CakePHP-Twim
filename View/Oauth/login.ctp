<?php
/**
 * Twim users/login view
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
 * */
$this->set('title_for_layout', __d('twim', 'Login'));
$connectUrl = array('action' => 'connect');
if (!empty($linkOptions)) {
	$connectUrl = am($connectUrl, $linkOptions);
}
?>
<h1><?php echo __d('twim', 'Login'); ?></h1>
<div class="login-wrap">
	<?php if (!$this->Session->check('Auth.User')) : ?>
		<p class="login"><?php echo $this->Html->image('/twim/img/sign-in-with-twitter-d.png', array('alt' => __d('twim', 'Sign in with Twitter'), 'url' => array('action' => 'connect'))); ?></p>
	<?php else: ?>
		<p class="logout"><?php echo $this->Html->link(__d('twim', 'Logout'), array('action' => 'logout')) ?></p>
	<?php endif; ?>
</div>
