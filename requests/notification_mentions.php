<?php
include("../includes/config.php");
include("../includes/functions.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
$confUrl = $conf['url'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if($_POST['interval'] >= $resultSettings[8]) {
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		if(isset($_POST['lastid'])) {
			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			
			$lastId = $_POST['lastid'];
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.mentions LIKE '%s' AND messages.uid = users.idu ORDER BY messages.time DESC LIMIT 1", mysql_real_escape_string('%'.$_COOKIE['username'].',%'));
			$resultMsg = mysql_fetch_row(mysql_query($queryMsg));
			
			if($resultMsg[0] > $lastId) {
				echo '<a href="'.$confUrl.'/index.php?a=me"><div class="new-message">New messages. Click here to load.</div></a>';
			}
		} else {
			// echo 'Invalid unique id';
		}
	} else {
		echo '<a href="'.$confUrl.'/"><div class="new-message">Authentication cookies have expired. Please login again to continue.</div></a>';
	}
} else {
	echo 'Invalid query values.';
}
mysql_close();
?>