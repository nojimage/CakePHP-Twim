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
?>
<?php if (!$this->Session->check('Auth.User')) : /* 未ログインの場合 */ ?>
    <?php echo $this->Twitter->oauthLink($linkOptions); ?>
<?php else: ?>
    <div id="logout-wrap">
        <p><?php echo $html->link(__d('twim', 'Logout', true), '/users/logout') ?></p>
    </div>
<?php endif; ?>
<?php echo $this->Js->writeBuffer(); ?>
