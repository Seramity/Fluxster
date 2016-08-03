<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
		
		if(isset($_GET['s'])) {
			$deleteSent = sprintf("DELETE FROM `private` WHERE `id` = '%s' AND `from` = '%s'", mysql_real_escape_string($_GET['s']), $data['id']);
			mysql_query($deleteSent);
		} elseif(isset($_GET['i'])) {
			$deleteInbox = sprintf("DELETE FROM `private` WHERE `id` = '%s' AND `to` = '%s'", mysql_real_escape_string($_GET['i']), $data['id']);
			mysql_query($deleteInbox);
		} elseif(isset($_GET['r'])) {
			$markRead = sprintf("UPDATE `private` SET `read` = '1' WHERE `id` = '%s' AND `to` = '%s'", mysql_real_escape_string($_GET['r']), $data['id']);
			mysql_query($markRead);
		} elseif(isset($_GET['u'])) {
			$markUnread = sprintf("UPDATE `private` SET `read` = '0' WHERE `id` = '%s' AND `to` = '%s'", mysql_real_escape_string($_GET['u']), $data['id']);
			mysql_query($markUnread);
		}
		
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('messages/sent'); $sent = '';

		// Select the messages sent
		$queryTo = sprintf("SELECT `to` FROM `private` WHERE `from` = '%s'", mysql_real_escape_string($data['id']));
		$array = array();
		
		$resTo = mysql_query($queryTo);
		while($row = mysql_fetch_assoc($resTo)) {
			$array[] = $row['to'];
		}
		$to_separated = implode(",", $array);
							 
		$queryToMsg = sprintf("SELECT * FROM private,users WHERE (private.to IN (%s) AND private.from = '%s') AND private.to = users.idu ORDER by private.id DESC", $to_separated, mysql_real_escape_string($data['id']));
		
		$resultToMsg = mysql_query($queryToMsg);
		while($TMPL = mysql_fetch_assoc($resultToMsg)) {
			$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
			$censArray = explode(',', $resultSettings[4]);
			$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
			
			$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" class="message-profile-pic"/>' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=70&d=mm" class="message-profile-pic"/>';md5($result[3]);
			
			if($resultSettings[9] == '0') {
				$TMPL['time'] = date("c", strtotime($TMPL['time']));
			} elseif($resultSettings[9] == '2') {
				$TMPL['time'] = ago(strtotime($TMPL['time']));
			} elseif($resultSettings[9] == '3') {
				$date = strtotime($TMPL['time']);
				$TMPL['time'] = date('Y-m-d', $date);
				$TMPL['b'] = '-standard';
			}
			
			$TMPL['delete'] = '<div class="report-button sud" title="Delete this message" id="'.$TMPL['id'].'"><a href="'.$confUrl.'/index.php?a=messages&s='.$TMPL['id'].'"><img src="'.$confUrl.'/images/icons/delete_message.png" /></a></div>';
			$TMPL['url'] = $confUrl;
			$newArr[] = $TMPL['id'];
			$sent .= $skin->make();
		}
		
		
		$skin = new skin('messages/from'); $from = '';
		// Select the received messages
		$queryFrom = sprintf("SELECT `from` FROM `private` WHERE `to` = '%s'", mysql_real_escape_string($data['id']));
		$arrayFrom = array();
		
		$resFrom = mysql_query($queryFrom);
		while($row = mysql_fetch_assoc($resFrom)) {
			$arrayFrom[] = $row['from'];
		}
		$from_separated = implode(",", $arrayFrom);
							 
		$queryFromMsg = sprintf("SELECT * FROM private,users WHERE (private.from IN (%s) AND private.to = '%s') AND private.from = users.idu ORDER by private.id DESC", $from_separated, mysql_real_escape_string($data['id']));

		$resultFromMsg = mysql_query($queryFromMsg);
		while($TMPL = mysql_fetch_assoc($resultFromMsg)) {
			$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
			$censArray = explode(',', $resultSettings[4]);
			$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
			
			$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" class="message-profile-pic" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=70&d=mm" class="message-profile-pic"/>';md5($result[3]);
			
			if($resultSettings[9] == '0') {
				$TMPL['time'] = date("c", strtotime($TMPL['time']));
			} elseif($resultSettings[9] == '2') {
				$TMPL['time'] = ago(strtotime($TMPL['time']));
			} elseif($resultSettings[9] == '3') {
				$date = strtotime($TMPL['time']);
				$TMPL['time'] = date('Y-m-d', $date);
				$TMPL['b'] = '-standard';
			}
			$TMPL['style'] = ($TMPL['read'] == 0) ? ' unread"' : '';
			$TMPL['state'] = ($TMPL['read'] == 0) ? '<div class="report-button sud" title="Mark this as read" id="'.$TMPL['id'].'"><a href="'.$confUrl.'/index.php?a=messages&r='.$TMPL['id'].'"><img src="'.$confUrl.'/images/icons/unread.png" /></a></div>' : '<div class="report-button sud" title="Mark this as unread" id="'.$TMPL['id'].'"><a href="'.$confUrl.'/index.php?a=messages&u='.$TMPL['id'].'"><img src="'.$confUrl.'/images/icons/read.png" /></a></div>';
			$TMPL['delete'] = '<div class="report-button sud" title="Delete this message" id="'.$TMPL['id'].'"><a href="'.$confUrl.'/index.php?a=messages&i='.$TMPL['id'].'"><img src="'.$confUrl.'/images/icons/delete_message.png" /></a></div>';
			$TMPL['url'] = $confUrl;
			$newArr[] = $TMPL['id'];
			$from .= $skin->make();
		}
		
		$skin = new skin('messages/profile'); $profile = '';
		
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
		
		$skin = new skin('messages/top'); $top = '';
		
		if(!empty($_GET['pre'])) {
			$TMPL['value'] = 'value="'.$_GET['pre'].'"';
		}
		$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=50&d=mm" />';md5($result[3]);
		$TMPL['username'] = $result[1];
		$TMPL['url'] = $confUrl;
		
		$top .= $skin->make();
		
	} else {
		header("Location: ".$confUrl."/index.php?a=welcome&m=den");
	}
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['sent'] = $sent;
	$TMPL['from'] = $from;
	$TMPL['top'] = $top;
	$TMPL['profile'] = $profile;
	
	
	$hideResult = mysql_num_rows($resultMsg);
	$TMPL['hide'] = ($hideResult < $resultSettings[1]) ? 'style="display: none;"' : '';

	$TMPL['interval'] = $resultSettings[8];
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		if(isset($_GET['d'])) {
			$queryDel = sprintf("DELETE FROM messages WHERE id = '%s' AND uid = '%s'", mysql_real_escape_string($_GET['d']), mysql_real_escape_string($data['id']));
			$resultDel = mysql_query($queryDel);
			if($resultDel) {
				header("Location: ".$confUrl."/index.php?a=me&m=ms");
			
			} else {
				header("Location: ".$confUrl."/index.php?a=me&m=me");
			
			}
		}
	}
	if(isset($_GET['md']) || isset($_GET['i'])) {
		$TMPL['message'] = '<div class="notification-bar notification-success">The message was successfully deleted.</div>';
	} elseif(isset($_GET['r'])) {
		$TMPL['message'] = '<div class="notification-bar notification-success">Marked as read!</h5></div>';
	} elseif(isset($_GET['u'])) {
		$TMPL['message'] = '<div class="notification-bar notification-success">The message was marked as unread.</div>';
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
	$TMPL['title'] = 'Messages - '.$resultSettings[0];

	$skin = new skin('messages/content');
	
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