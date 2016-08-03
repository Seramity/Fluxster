<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('mentions/rows'); $rows = '';
		
		$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
		
		$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
								mysql_real_escape_string($data['id']));
		
		$array = array();
		
		$resFol = mysql_query($queryFollowers);
		while($row = mysql_fetch_assoc($resFol)) {
			$array[] = $row['follower'];
		}
		$followers_separated = implode(",", $array);
		
		if($followers_separated) {
			$op = ',';
		} else {
			$op = '';
		}
		
		//$queryMsg = sprintf("SELECT * FROM messages WHERE uid IN (%s%s%s) ORDER BY id DESC LIMIT %s", $data['id'], $op, $followers_separated, $resultSettings[1]);
		
		$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.mentions LIKE '%s' AND messages.uid = users.idu ORDER BY messages.time DESC LIMIT %s", 
							mysql_real_escape_string('%'.$_COOKIE['username'].',%'),
							$resultSettings[1]);

		$newArr = array();
		
		$resultMsg = mysql_query($queryMsg);
		while($TMPL = mysql_fetch_assoc($resultMsg)) {
			$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
			
			$censArray = explode(',', $resultSettings[4]);
			$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
			
			
			
			
			if (!empty($TMPL['suspended'])) {
				$TMPL['username'] = 'SUSPENDED';
				$TMPL['profileLink'] = 'SUSPENDED';
				$TMPL['message'] = '<font color="#999">The user\'s content is hidden</font>';
				$TMPL['reportButton'] = '';
				$TMPL['delReply'] = '';
				
					if($resultSettings[9] == '0') {
						$TMPL['time'] = '';
					} elseif($resultSettings[9] == '2') {
						$TMPL['time'] = ''; 
					} elseif($resultSettings[9] == '3') {
						$date = strtotime($TMPL['time']);
						$TMPL['time'] = ''; 
						$TMPL['b'] = '-standard';
					}
				if(substr($TMPL['video'], 0, 3) == 'yt:') {
				$TMPL['video'] = '';
				$TMPL['mediaButton'] = '';
			} else if(substr($TMPL['video'], 0, 3) == 'vm:') {
				$TMPL['video'] = '';
				$TMPL['mediaButton'] = '';
			}
			if(!empty($TMPL['media'])) {
				$TMPL['media'] = '';
				$TMPL['mediaButton'] = '';
			}
			
			} else { 
				$TMPL['username'] = ''.$TMPL['username'].'';
				$TMPL['profileLink'] = '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'">'.$TMPL['username'].'</a>';
				$TMPL['reportButton'] = '<div class="report-button sud" title="Report this message" id="'.$TMPL['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
			$TMPL['delReply'] = ($TMPL['username'] == $_COOKIE['username']) ? '<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=stream&d='.$TMPL['id'].'">Delete</a></div>' : '<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			
				if($resultSettings[9] == '0') {
					$TMPL['time'] = date("c", strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '2') {
					$TMPL['time'] = ago(strtotime($TMPL['time']));
				} elseif($resultSettings[9] == '3') {
					$date = strtotime($TMPL['time']);
					$TMPL['time'] = date('Y-m-d', $date);
					$TMPL['b'] = '-standard';
				}
			
			}
			
			if (!empty($TMPL['suspended'])) {
				$TMPL['image'] = '<img src="'.$confUrl.'/images/suspended.png" class="message-profile-pic"/>';	
			} else {
				$TMPL['image'] = (!empty($TMPL['image'])) ? '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'"><img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" class="message-profile-pic" /></a>' : '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'"><img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=70&d=mm" class="message-profile-pic"/></a>';md5($result[3]);
			}
			
			if ($TMPL['disabled'] == 1) {
				$TMPL['username'] = ''.$TMPL['username'].'';
				$TMPL['profileLink'] = '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'">'.$TMPL['username'].'</a>';
				$TMPL['message'] = '<font color="#999">The user\'s content is hidden</font>';
				$TMPL['reportButton'] = '';
				$TMPL['delReply'] = '';
				
					if($resultSettings[9] == '0') {
						$TMPL['time'] = '';
					} elseif($resultSettings[9] == '2') {
						$TMPL['time'] = ''; 
					} elseif($resultSettings[9] == '3') {
						$date = strtotime($TMPL['time']);
						$TMPL['time'] = ''; 
						$TMPL['b'] = '-standard';
					}
				if(substr($TMPL['video'], 0, 3) == 'yt:') {
				$TMPL['video'] = '';
				$TMPL['mediaButton'] = '';
			} else if(substr($TMPL['video'], 0, 3) == 'vm:') {
				$TMPL['video'] = '';
				$TMPL['mediaButton'] = '';
			}
			if(!empty($TMPL['media'])) {
				$TMPL['media'] = '';
				$TMPL['mediaButton'] = '';
			}
				
			} else {
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
			}
			
			if ($TMPL['disabled'] == 1) {
				$TMPL['image'] = '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'">
				<img src="http://gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y" class="message-profile-pic"/></a>';	
			} 

			
			
			
			
			
			$TMPL['url'] = $confUrl;
			$newArr[] = $TMPL['id'];
			$rows .= $skin->make();
		}
		
		$skin = new skin('mentions/profile'); $profile = '';
		
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
		$TMPL['bio'] = (!empty($result[7])) ? '<div class="profile-bio">'.$result[7].'</div>' : '';
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
		
		$skin = new skin('mentions/top'); $top = '';
		
		$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=50&d=mm" />';md5($result[3]);
		$TMPL['username'] = $result[1];
		
		$top .= $skin->make();
		
	}  else {
		header("Location: ".$confUrl."/index.php?a=welcome&m=den");
	}		
	
	$TMPL = $TMPL_old; unset($TMPL_old);
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
				header("Location: ".$confUrl."/index.php?a=mentions&m=ms");
			
			} else {
				header("Location: ".$confUrl."/index.php?a=mentions&m=me");
			
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
		header("Location: ".$confUrl."/index.php?a=welcome");
	}
	
	
	// Profile Background
				
	$TMPL['background'] = (!empty($result[15])) ? 'url('.$result[15].')' : '#ececec';
	if($result[35] == 1) {
		$TMPL['fixedBG'] = 'fixed';
	} else {
		$TMPL['fixedBG'] = '';
	}
	if($result[36] == 1) {
		$TMPL['repeatBG'] = 'repeat';
	} else {
		$TMPL['repeatBG'] = 'no-repeat';
	}
	
	if($result[36] == 0) {
		$TMPL['coverBG'] = '
			-webkit-background-size: cover;
			-moz-background-size: cover;
       			-o-background-size: cover;
        		background-size: cover;';
        	$TMPL['centerBG'] = 'center center';
	} else {
		$TMPL['coverBG'] = '';
        	$TMPL['centerBG'] = '';
	} 
	
	if($result[35] == 0) {
		$TMPL['coverBG'] = '';
        	$TMPL['centerBG'] = '';
	} else {
		$TMPL['coverBG'] = '
			-webkit-background-size: cover;
			-moz-background-size: cover;
       			-o-background-size: cover;
        		background-size: cover;';
        	$TMPL['centerBG'] = 'center center';
	}
	
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = 'Mentions - '.$resultSettings[0];

	$skin = new skin('mentions/content');
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
	if(!empty($result[19])) {
		header("Location: /suspendedaccount");
	}
	
	if($result[27] == 1) {
		header("Location: /disabledaccount");
	}
	
	
	return $skin->make();
}
?>