<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('message/rows'); $rows = '';
				
		$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.id = '%s' AND messages.uid = users.idu LIMIT 0, 1", 
							mysql_real_escape_string($_GET['m']));
		
		$resultMsg = mysql_query($queryMsg);
		while($TMPL = mysql_fetch_assoc($resultMsg)) {
			$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
			
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
			'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=mentions&d='.$TMPL['id'].'">Delete</a></div>'
			: 
			'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			$TMPL['url'] = $confUrl;
			$rows .= $skin->make();
		}
		
	} else {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('message/rows2'); $rows = '';
		
		$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.id = '%s' AND messages.uid = users.idu LIMIT 0, 1", 
							mysql_real_escape_string($_GET['m']));
		
		$resultMsg = mysql_query($queryMsg);
		while($TMPL = mysql_fetch_assoc($resultMsg)) {
			$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/discover/$2">#$2</a>'), $TMPL['message']);
			
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
			$TMPL['delReply'] = ($TMPL['author'] == $_COOKIE['username']) 
			? 
			'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=mentions&d='.$TMPL['id'].'">Delete</a></div>'
			: 
			'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
			$TMPL['url'] = $confUrl;
			$rows .= $skin->make();
		}
	}
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $rows;
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		if(isset($_GET['d'])) {
			$queryDel = sprintf("DELETE FROM messages WHERE id = '%s' AND author = '%s'", mysql_real_escape_string($_GET['d']), mysql_real_escape_string($_COOKIE['username']));
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
	
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = $resultSettings[0];

	$skin = new skin('message/content');
	
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