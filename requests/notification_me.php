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
			
			$queryFollowers = sprintf("SELECT leader FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($data['id']));
			
			$array = array();
			
			$resFol = mysql_query($queryFollowers);
			while($row = mysql_fetch_assoc($resFol)) {
				$array[] = $row['leader'];
			}
			$followers_separated = implode(",", $array);
			$lastId = $_POST['lastid'];
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid IN (%s) AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT 1", $followers_separated);
			$resultMsg = mysql_fetch_row(mysql_query($queryMsg));
			
			if($resultMsg[0] > $lastId) {
				echo '<a href="'.$confUrl.'/stream"><div class="new-message">New posts. Click here to load.</div></a>';
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