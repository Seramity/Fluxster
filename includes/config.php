<?php
error_reporting(0);
#error_reporting(E_ALL ^ E_NOTICE);

$conf = $TMPL = array();
$conf['host'] = 'localhost';
$conf['user'] = 'root';
$conf['pass'] = '';
$conf['name'] = '';
$conf['url'] = '';
$conf['mail'] = '';

$action = array(		'admin'			=> 'admin',
				'register'		=> 'register',
				'message'		=> 'message',
				'stream'		=> 'stream',
				'settings'		=> 'settings',
				'mentions'		=> 'mentions',
				'messages'		=> 'messages',
				'profile'		=> 'profile',
				'discover'		=> 'discover',
				'search'		=> 'search',
				'recover'		=> 'recover',
				'explore'		=> 'explore',
				'pixels'		=> 'pixels',
				'suspendedaccount'	=> 'suspendedaccount',
				'disabledaccount'	=> 'disabledaccount',
				'contact'		=> 'contact',
				'welcomefriend'		=> 'welcomefriend',
				'bundle'		=> 'bundle',
				'passport'		=> 'passport',
				'plus'			=> 'plus',

				// Start the secondary pages
				'privacy'      		=> 'page',
				'thanks'       		=> 'page',
				'about'      		=> 'page',
				'changelog'      	=> 'page',
				'notfound'      	=> 'page',
				'accessdenied'       	=> 'page',
				'terms'			=> 'page',
				'safety'		=> 'page',
				'wut'			=> 'page',
				'aboutbundle'		=> 'page',
				'verified'		=> 'page',
				'brand'			=> 'page',
				'guide'			=> 'page',
				'faqs'			=> 'page',
				'cookies'		=> 'page',
				'private'		=> 'page',
				'suspended'		=> 'page',
				'disabled'		=> 'page',

				);

/* if(get_magic_quotes_gpc()) {
	function strips($v) {return is_array($v)?array_map('strips',$v):stripslashes($v);}
	$_GET = strips($_GET);
} */
?>
