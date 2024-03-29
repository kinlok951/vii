<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if(!defined('MOZG'))
	die("Hacking attempt!");
$user_info = $user_info ?? Registry::get('user_info');
if($user_info['user_group'] != '1'){
    $config = settings_get();
    $tpl->load_template('offline.tpl');
	$config['offline_msg'] = str_replace('&quot;', '"', stripslashes($config['offline_msg']));
	$tpl->set('{reason}', nl2br($config['offline_msg']));
	$tpl->compile('main');
	echo $tpl->result['main'];
}