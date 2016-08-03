<?php
include("../includes/config.php");
include("../includes/functions.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
$confUrl = $conf['url'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if(isset($_POST['loadmore'])) {

	$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
	
	$loadmore=$_POST['loadmore'];
	$queryMsg = sprintf("SELECT * FROM users WHERE username RLIKE '%s' AND idu < %s OR name RLIKE '%s' AND idu < %s ORDER BY idu DESC LIMIT %s", mysql_real_escape_string(str_replace(' ', '|', $_POST['u'])),  mysql_real_escape_string($loadmore), mysql_real_escape_string(str_replace(' ', '|', $_POST['u'])), mysql_real_escape_string($loadmore), $resultSettings[1]);
	$newArr = array();
	
	$result = mysql_query($queryMsg);
	while($row = mysql_fetch_array($result)) {
		$email = md5($row['email']);
		$author = $row['username']; 
		$location = $row['location'];
		$verify = $row['verify'];
		$bio = $row['bio'];
		$verified = $row['verified']; 
		
		$view = '<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'"><div class="follow-container-follow"><div class="view-button">view Profile</div></div></a>';
		$getImg = (!empty($row['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$row['image'].'" class="message-profile-pic" />' : '<img src="http://www.gravatar.com/avatar/'.md5($row['email']).'?s=70 &d=mm" class="message-profile-pic"/>';
		echo '
		<div class="message">
			<div class="message-picture">
				<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'">'.$getImg.'</a>
			</div>
			<div class="message-top-container">
				<div class="message-top">
					<div class="message-author">
						<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'">'.$author.'</a> '.$verified.'
						
					</div>
					<div class="timeago'.$b.'" title="'.$location.'">
						'.$location.'
					</div>
				</div>
			</div>
			<div class="message-container">
				<div class="message-message">
					'.$bio.$view.'
				</div>
			</div>
		</div>
		';
		$newArr[] = $row['idu'];
	}
	
	while($min = mysql_fetch_assoc($result)) {
		$newArr[] = $min['idu'];
	}

	if(array_key_exists($resultSettings[1] - 1, $newArr)) {


	echo '<div id="more'.min($newArr).'" class="morebox">
			<div id="'.min($newArr).'" class="more"><div class="more-button">More results</div></div>
		  </div>';


	} else {

	echo '<div class="morebox">
			<div class="more"><div class="more-button">No more results</div></div>
		  </div>';
	}
}
mysql_close();
?>