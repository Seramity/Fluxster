<?php
include("../includes/config.php");
include("../includes/functions.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
$confUrl = $conf['url'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
	if(isset($_POST['loadmore'])) {
	
		$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
		
		$loadmore=$_POST['loadmore'];
		$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.tag LIKE '%s' AND messages.uid = users.idu AND id < '%s' ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string('%'.$_POST['u'].'%,'), mysql_real_escape_string($loadmore), $resultSettings[1]);
		$newArr = array();
		
		$result = mysql_query($queryMsg);
		while($row = mysql_fetch_array($result)) {
			
			$parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $row['message']);
			$author = $row['username'];
			$time = $row['time'];
			
			$censArray = explode(',', $resultSettings[4]);
			$parsedMessage = strip_tags(str_replace($censArray, '', $parsedMessage), '<a>');
			
			if($resultSettings[9] == '0') {
				$time = date("c", strtotime($time));
			} elseif($resultSettings[9] == '2') {
				$time = ago(strtotime($time));
			} elseif($resultSettings[9] == '3') {
				$date = strtotime($time);
				$time = date('Y-m-d', $date);
				$b = '-standard';
			}
			if(substr($row['video'], 0, 3) == 'yt:' || substr($row['video'], 0, 3) == 'vm:' || !empty($row['media'])) {
				$mediaButton = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
			}
			if(substr($row['video'], 0, 3) == 'yt:') {
				$embedVideo = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
			} else if(substr($row['video'], 0, 3) == 'vm:') {
				$embedVideo = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
			} else {
				$embedVideo = ' ';
			}
			if(!empty($row['media'])) {
				$embedImage = '<a href="'.$confUrl.'/uploads/media/'.$row['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$row['media'].'" /></a>';
			} else {
				$mediaImage = ' ';
			}
			$report = '<div class="report-button sud" title="Report this message" id="'.$row['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
			$getImg = (!empty($row['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$row['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($row['email']).'?s=50&d=mm" />';
			$delete = ($row['username'] == $_COOKIE['username']) 
			? 
			'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=me&d='.$row['id'].'">Delete</a></div>'
			: 
			'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			
			echo '
			<div class="message">
				<div class="message-picture">
					<a href="'.$confUrl.'/profile/'.$author.'">'.$getImg.'</a>
				</div>
				<div class="message-top-container">
					<div class="message-top">
						<div class="message-author">
							<a href="'.$confUrl.'/profile/'.$author.'">'.$author.'</a>
						</div>
						<a href="'.$confUrl.'/index.php?a=message&m='.$row['id'].'" target="_blank">
							<div class="timeago'.$b.'" title="'.$time.'">
								'.$time.'
							</div>
						</a>
					</div>
				</div>
				<div class="message-container">
					<div class="message-message">
						'.$parsedMessage.'
					</div>
				</div>
				<div class="message-bottom-container">
					'.$report.'
					'.$delete.'
					'.$mediaButton.'
					<div class="reply-container">
						<div class="reply-container-form"><textarea id="post'.$row['id'].'" class="message-form">@'.$author.' </textarea></div>
						<div class="post-button" id="'.$row['id'].'">Post</div>
						<div class="post-loader" id="post-loader'.$row['id'].'"></div>
					</div>
					<div class="media-container">
						'.$embedVideo.'
						'.$embedImage.'
					</div>
				</div>
			</div>
			';
			$newArr[] = $row['id'];
		}
		
		while($min = mysql_fetch_assoc($result)) {
			$newArr[] = $min['id'];
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
} else {
	echo '<a href="'.$confUrl.'/"><div class="new-message">Authentication cookies have expired. Please login again to continue.</div></a>';
}
mysql_close();
?>