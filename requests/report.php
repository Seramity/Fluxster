<?php
include("../includes/config.php");
include("../includes/functions.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
$confUrl = $conf['url'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
	if(isset($_POST['report'])) {
		
		$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
		$checkDuplicate = sprintf("SELECT * FROM `reports` WHERE `idm` = '%s'", mysql_real_escape_string($_POST['report']));
		$resultDuplicate = mysql_fetch_row(mysql_query($checkDuplicate));

		if(empty($resultDuplicate[0])) {
			if(empty($_POST['report']) || !ctype_digit($_POST['report'])) {
				echo '<div class="popup-body-posted">The reported message doens\'t exist. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>'; 
			} elseif(strlen($_POST['postmsg']) <= $resultSettings[10]) {
				$insertMessage = sprintf("INSERT INTO `reports` (`id`, `idm`, `user`) VALUES ('', '%s', '%s')", mysql_real_escape_string($_POST['report']), $data['user']);
				mysql_query($insertMessage);
			
				echo '<div class="popup-body-posted">Message reported. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>';
			}
		} else {
			echo '<div class="popup-body-posted">You already reported this message. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>';
		}
	}
} else {
	echo 'Invalid login credentials.';
}
mysql_close();
?>