<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('welcomefriend/rows'); $rows = '';
		
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
		
		$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid IN (%s%s%s) AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", $data['id'], $op, $followers_separated, $resultSettings[1]);

		$newArr = array();
		
		$resultMsg = mysql_query($queryMsg);
		while($TMPL = mysql_fetch_assoc($resultMsg)) {
			$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?������]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
			$TMPL['verify'] = $result[20];	
			$censArray = explode(',', $resultSettings[4]);
			$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
		
			
			$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=50&d=mm" />';md5($result[3]);
			
			
			if($resultSettings[9] == '0') {
				$TMPL['time'] = date("c", strtotime($TMPL['time']));
			} elseif($resultSettings[9] == '2') {
				$TMPL['time'] = ago(strtotime($TMPL['time']));
			} elseif($resultSettings[9] == '3') {
				$date = strtotime($TMPL['time']);
				$TMPL['time'] = date('Y-m-d', $date);
				$TMPL['b'] = '-standard';
			
			}
			if(substr($TMPL['video'], 0, 3) == 'yt:') {
				$TMPL['video'] = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $TMPL['video']).'" frameborder="0" allowfullscreen></iframe>';
				$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
			} else if(substr($TMPL['video'], 0, 3) == 'vm:') {
				$TMPL['video'] = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $TMPL['video']).'" frameborder="0" allowfullscreen></iframe>';
				$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
			}
			if(!empty($TMPL['media'])) {
				$TMPL['media'] = '<a href="'.$confUrl.'/uploads/media/'.$TMPL['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$TMPL['media'].'" /></a>';
				$TMPL['mediaButton'] = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
			}
			$TMPL['reportButton'] = '<div class="report-button sud" title="Report this message" id="'.$TMPL['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
			$TMPL['delReply'] = ($TMPL['username'] == $_COOKIE['username']) 
			? 
			'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=stream&d='.$TMPL['id'].'">Delete</a></div>'
			: 
			'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			
			$TMPL['url'] = $confUrl;
			$newArr[] = $TMPL['id'];
			$rows .= $skin->make();
		}
		
		$skin = new skin('welcomefriend/profile'); $profile = '';
		
		// Get Profile Data
		$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
		$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=50&d=mm" />';md5($result[3]);
                $TMPL['username'] = $result[1];
                $TMPL['name'] = (!empty($result[4])) ? ''.$result[4].'' : ''.$result[1].'';
		$TMPL['url'] = $confUrl;
		$TMPL['badge'] = (!empty($result[6])) ? '<div class="profile-description">'.$result[6].'</div>' : '';
		$TMPL['location'] = (!empty($result[5])) ? '<div class="profile-description">'.$result[5].'</div>' : '';
		$TMPL['bio'] = (!empty($result[7])) ? ''.$result[7].'' : '';
		$TMPL['verify'] = $result[20];
		
		if($result[9] !== '' || $result[10] !== '' || $result[11]) {
			$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">facebook profile</a></div>' : '';
			$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">twitter profile</a></div>' : '';
			$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">google+ profile</a></div>' : '';
			$TMPL['social'] = '<div class="divider"></div>
							   <div class="sidebar">
							   '.$facebook.'
							   '.$twitter.'
							   '.$google.'
							   </div>';
		}
		
		// Get posts number
		$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
								mysql_real_escape_string($data['id']));
								
		$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
								mysql_real_escape_string($data['id']));
		
		$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
								mysql_real_escape_string($data['id']));
									
			$resultMessagePoints = mysql_num_rows(mysql_query($queryMessages)) * 2;
                        $resultFollowerPoints = mysql_num_rows(mysql_query($queryFollowers)) * 2;
                        $resultFollowingPoints = mysql_num_rows(mysql_query($queryFollowing)) * 2;
                        $resultMessages = mysql_num_rows(mysql_query($queryMessages));
			$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
			$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));

                        $totalPoints = $resultMessagePoints + $resultFollowerPoints + $resultFollowingPoints;

			$TMPL['messages'] = $resultMessages;
			$TMPL['followers'] = $resultFollowers;
			$TMPL['following'] = $resultFollowing;
		        $TMPL['points'] = 'Pixels: <font color="#67a59b">'.$totalPoints.'</font>';
		
		
		
		$profile .= $skin->make();
		
		$skin = new skin('welcomefriend/top'); $top = '';
		
		$TMPL['image'] = (!empty($result[12])) ? '<div class="profile-picture"><img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="50" height="50" /></div>' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=50&d=mm" />';md5($result[3]);
		$TMPL['username'] = $result[1];
		$TMPL['url'] = $confUrl;
				
		$top .= $skin->make();
		
	
	} else {
		header("Location: ".$confUrl."/index.php?a=welcome&m=den");
	}

	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['followerList'] = $followerList;
	$TMPL['rows'] = $rows;
	$TMPL['top'] = $top;
	$TMPL['profile'] = $profile;
	
	
	
	$hideResult = mysql_num_rows($resultMsg);
	$TMPL['hide'] = ($hideResult < $resultSettings[1]) ? 'style="display: none;"' : '';
	
	$TMPL['idn'] = @min($newArr);
	$TMPL['idx'] = @max($newArr);
	$TMPL['interval'] = $resultSettings[8];
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		if(isset($_GET['d'])) {
			$queryDel = sprintf("DELETE FROM messages WHERE id = '%s' AND uid = '%s'", mysql_real_escape_string($_GET['d']), mysql_real_escape_string($data['id']));
			$resultDel = mysql_query($queryDel);
			if($resultDel) {
				header("Location: ".$confUrl."/index.php?a=stream&m=ms");
			
			} else {
				header("Location: ".$confUrl."/index.php?a=stream&m=me");
			
			}
		}
	}
	if($_GET['m'] == 'ms') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-info">
							<h5>Message deleted!</h5> 
							<p>The message was successfully deleted.</p>
							<a href="#" class="notification-close notification-close-info">x</a>
							</div>';
	} elseif($_GET['m'] == 'me') {
		$TMPL['message'] = '<div class="divider"></div> 
							<div class="notification-box notification-box-error">
							<h5>Something went wrong!</h5>
							<p>We couldn\'t delete the message you\'ve selected.</p>
							<a href="#" class="notification-close notification-close-error">x</a>
							</div>';
	}
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome&m=out"); 
	}

	$TMPL['userBackground'] = (!empty($data['background'])) ? ' style="background-image: url('.$confUrl.'/images/backgrounds/'.$data['background'].'.png)"' : '';
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = 'Welcome Friend! - '.$resultSettings[0];

	$skin = new skin('welcomefriend/content');
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
	if(!empty($result[19])) { 
		header("Location: /stream");

	}
	
	
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
		$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=50&d=mm" />';md5($result[3]);
                $TMPL['username'] = $result[1];
                $TMPL['name'] = (!empty($result[4])) ? ''.$result[4].'' : ''.$result[1].'';
		$TMPL['url'] = $confUrl;
		$TMPL['badge'] = (!empty($result[6])) ? '<div class="profile-description">'.$result[6].'</div>' : ''; 
		$TMPL['location'] = (!empty($result[5])) ? '<div class="profile-description">'.$result[5].'</div>' : '';
		$TMPL['bio'] = (!empty($result[7])) ? ''.$result[7].'' : '';
		$TMPL['verify'] = $result[20]; 
		$TMPL['suspendDetails'] = $result[26];
		$TMPL['email'] = $result[3];  
	
	
	
	if(isset($_POST['welcome'])) {
		$query = sprintf("UPDATE `users` SET welcomePage = 1 WHERE username = '%s'", 
					mysql_real_escape_string($_COOKIE['username']));
		mysql_query($query); 
						
		header("Location: /index.php?a=stream&m=new");
	}
	
	
	return $skin->make();
}
?>