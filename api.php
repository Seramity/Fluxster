<?php
require_once('./includes/config.php');
require_once('./includes/functions.php');
error_reporting(0);
header('Content-Type: text/plain; charset=utf-8;');

mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);

$confUrl = $conf['url'];

$user = $_GET['user'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if(isset($user) && !empty($user)) {
	
	$username = urlencode($user);
	$query = sprintf("SELECT * FROM users WHERE username = '%s'", mysql_real_escape_string($user));
	$result = mysql_fetch_row(mysql_query($query));
		
	if(!empty($result[0])) {
		
		$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
								mysql_real_escape_string($result[0]));
		
		$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
								mysql_real_escape_string($result[0]));
								
		$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
		$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));
		
		 echo '{"apiVersion":"1.0 pre-beta", "data":{ "Username":"'.$result[1].'", "Name":"'.$result[4].'", "Location":"'.$result[5].'", "Bio":"'.$result[7].'", "Image":"'.$result[12].'", "Followers":"'.$resultFollowers.'", "Following":"'.$resultFollowing.'" } }';

		
	} else {
		echo '{"apiVersion":"1.0", "data":{ "error":"The user requested is does not exist." } }';
	}
} else {
	echo '{"apiVersion":"1.0", "data":{ "error":"You need to specify the user parameter" } }';
}
mysql_close();
?>