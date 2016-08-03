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
		
		$queryFollowers = sprintf("SELECT leader FROM relations WHERE follower = '%s'",
								mysql_real_escape_string($data['id']));
		
		$array = array();
		
		$resFol = mysql_query($queryFollowers);
		while($row = mysql_fetch_assoc($resFol)) {
			$array[] = $row['leader'];
		}
		$followers_separated = implode(",", $array);
		
		if($followers_separated) {
			$op = ',';
		} else {
			$op = '';
		}
		
		$loadmore=$_POST['loadmore'];
		$queryMsg = sprintf("SELECT * FROM messages, users WHERE uid IN (%s%s%s) AND messages.uid = users.idu AND messages.id < '%s' ORDER BY messages.id DESC LIMIT %s", $data['id'], $op, $followers_separated, mysql_real_escape_string($loadmore), $resultSettings[1]);

		$newArr = array();
		
		$result = mysql_query($queryMsg);
		while($row = mysql_fetch_array($result)) {
			
			$parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $row['message']); 
  
			$verified = $row['verified'];
			
			$censArray = explode(',', $resultSettings[4]);
			$parsedMessage = strip_tags(str_replace($censArray, '', $parsedMessage), '<a>');
			
			
			if (!empty($row['suspended'])) { 
				$author = 'SUSPENDED';
				$parsedMessage = '<font color="#999">The user\'s content is hidden</font>';
				$report = ''; 
				$delete = '';
				$time = '';
					if($resultSettings[9] == '0') {
						$time = '';
					} elseif($resultSettings[9] == '2') {
						$time = '';
					} elseif($resultSettings[9] == '3') {
						$date = strtotime($time);
						$time = '';
						$b = '-standard';
				}
			} else { 
				
				
				$author = '<a href="'.$confUrl.'/profile/'.$row['username'].'">'.$row['username'].'</a>';
				$report = '<div class="report-button sud" title="Report this message" id="'.$row['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
			$delete = ($row['username'] == $_COOKIE['username']) ? '<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=stream&d='.$row['id'].'">Delete</a></div>' : '<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			$time = $row['time']; 
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
			} else {
				$mediaButton = ''; 
			}
			if(substr($row['video'], 0, 3) == 'yt:') {
				$embedVideo = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
			} else if(substr($row['video'], 0, 3) == 'vm:') {
				$embedVideo = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
			} else {
				$embedVideo = '';
			}
			if(!empty($row['media'])) {
				$embedImage = '<a href="'.$confUrl.'/uploads/media/'.$row['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$row['media'].'" /></a>';
			} else {
				
				$embedImage = '';
			}
				
			}
			
			if (!empty($row['suspended'])) {
				$getImg = '<img src="'.$confUrl.'/images/suspended.png" class="message-profile-pic" />';	 
			} else {
				$getImg = (!empty($row['image'])) ? '<a href="'.$confUrl.'/profile/'.$row['username'].'"><img src="'.$confUrl.'/uploads/avatars/'.$row['image'].'" class="message-profile-pic" /></a>' : '<a href="'.$confUrl.'/profile/'.$row['username'].'"><img src="http://www.gravatar.com/avatar/'.md5($row['email']).'?s=70&d=mm" class="message-profile-pic"/></a>';
			}
			
			if ($row['disabled'] == 1) { 
				$author = '<a href="'.$confUrl.'/profile/'.$row['username'].'">'.$row['username'].'</a>';
				$parsedMessage = '<font color="#999">The user\'s content is hidden</font>';
				$report = ''; 
				$delete = '';
				$time = '';
					if($resultSettings[9] == '0') {
						$time = '';
					} elseif($resultSettings[9] == '2') {
						$time = '';
					} elseif($resultSettings[9] == '3') {
						$date = strtotime($time);
						$time = '';
						$b = '-standard';
					}
					if(substr($row['video'], 0, 3) == 'yt:' || substr($row['video'], 0, 3) == 'vm:' || !empty($row['media'])) { 
				$mediaButton = '';
			} else {
				$mediaButton = ''; 
			}
			if(substr($row['video'], 0, 3) == 'yt:') {
				$embedVideo = '';
			} else if(substr($row['video'], 0, 3) == 'vm:') {
				$embedVideo = '';
			} else {
				$embedVideo = '';
			}
			if(!empty($row['media'])) {
				$embedImage = '';
			} else {
				
				$embedImage = '';
			}
				
			} else {
				if(substr($row['video'], 0, 3) == 'yt:' || substr($row['video'], 0, 3) == 'vm:' || !empty($row['media'])) { 
				$mediaButton = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
			} else {
				$mediaButton = ''; 
			}
			if(substr($row['video'], 0, 3) == 'yt:') {
				$embedVideo = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
			} else if(substr($row['video'], 0, 3) == 'vm:') {
				$embedVideo = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
			} else {
				$embedVideo = '';
			}
			if(!empty($row['media'])) {
				$embedImage = '<a href="'.$confUrl.'/uploads/media/'.$row['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$row['media'].'" /></a>';
			} else {
				
				$embedImage = '';
			}
			}
			
			if ($row['disabled'] == 1) {
				$getImg = '<a href="'.$confUrl.'/profile/'.$row['username'].'">
				<img src="http://gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y" class="message-profile-pic" /></a>';	 
			} 
			
			
			if($row['repliedTo'] !== 0) {
				$conversation = '<div class="conversation-button"><a href="'.$confUrl.'/index.php?a=message&m='.$row['repliedTo'].'">View conversation</a></div>';
			} else {
				$conversation = '';
			}
			
			
			echo '
			<div class="message">
				<div class="message-picture">
					'.$getImg.'
				</div>
				<div class="message-top-container">
					<div class="message-top">
						<div class="message-author">
							'.$author.'
						</div>
						<a href="'.$confUrl.'/index.php?a=message&m='.$row['id'].'">
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
					'.$conversation.'
					'.$delete.'
					'.$mediaButton.'
					<div class="reply-container">
						<div class="reply-container-form"><textarea id="post'.$row['id'].'" class="message-form">@'.$row['username'].' </textarea></div>
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

		echo '';
		}
	}
} else {
	echo '<a href="'.$confUrl.'/"><div class="new-message">Authentication cookies have expired. Please login again to continue.</div></a>';
}
mysql_close();
?>