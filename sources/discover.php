<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$getId = sprintf("SELECT * FROM messages WHERE tag LIKE '%s' LIMIT 0,1", mysql_real_escape_string('%'.$_GET['u'].'%,'));
	$resultId = mysql_fetch_row(mysql_query($getId));
	if(!empty($resultId[1]) && !empty($_GET['u'])) {
	
		if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
			$TMPL_old = $TMPL; $TMPL = array();
			$skin = new skin('discover/rows'); $rows = '';

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.tag LIKE '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string('%'.$_GET['u'].',%'), $resultSettings[1]);
			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
				$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
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
				'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=me&d='.$TMPL['id'].'">Delete</a></div>'
				: 
				'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
				$TMPL['url'] = $confUrl;
				$newArr[] = $TMPL['id'];
				$rows .= $skin->make();
			}
			
			$skin = new skin('discover/top'); $top = '';
			
			$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($data['mail']).'?s=50&d=mm" />';
			$TMPL['username'] = $result[1];
			$TMPL['currentUser'] = $_GET['u'];
			$TMPL['url'] = $confUrl;
			
			$top .= $skin->make();
			
		} else {
			$TMPL_old = $TMPL; $TMPL = array();
			$skin = new skin('discover/rows2'); $rows = '';

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.tag LIKE '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string('%'.$_GET['u'].'%,'), $resultSettings[1]);

			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
				$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
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
				$TMPL['url'] = $confUrl;
				$newArr[] = $TMPL['id'];
				$rows .= $skin->make();
			}
			
			$skin = new skin('discover/top2'); $top = '';
			
			$TMPL['url'] = $confUrl;
			
			$top .= $skin->make();
			$public = '_public';
		}
		
		$skin = new skin('discover/latest'); $latest = '';
	
		$queryLatest = sprintf("SELECT DISTINCT username, email, image FROM messages, users WHERE messages.tag LIKE '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT 16", mysql_real_escape_string('%'.$_GET['u'].'%,'));
		$resultLatest = mysql_query($queryLatest);
		
		while($TMPL = mysql_fetch_assoc($resultLatest)) {
			
			$TMPL['url'] = $confUrl;
			$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=50&d=mm" />';md5($result[3]);
			
			$latest .= $skin->make();
		}
		
	} else {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('discover/error'); $error = '';
		
		$error .= $skin->make();
	}

	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $rows;
	$TMPL['top'] = $top;
	$TMPL['public'] = $public; // selecteaza js-ul pt. public, fara auth
	$TMPL['error'] = $error;
	$TMPL['latest'] = $latest;
	
	$hideResult = mysql_num_rows($resultMsg);
	$TMPL['hide'] = ($hideResult < $resultSettings[1]) ? 'style="display: none;"' : '';
	
	$TMPL['idn'] = @min($newArr);
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
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome");
	}
	
	
	
	$TMPL['userBackground'] = (!empty($data['background'])) ? ' style="background-image: url('.$confUrl.'/images/backgrounds/'.$data['background'].'.png)"' : '';
	$TMPL['topic'] = '#'.$_GET['u'];
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = $_GET['u'].' - '.$resultSettings[0];

	$skin = new skin('discover/content');
	
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
	
	if(!empty($result[19])) {
		header("Location: /suspendedaccount");
		if(isset($_GET['logout']) == 1) {
			setcookie('username', '', $exp_time);
			setcookie('password', '', $exp_time);
			header("Location: ".$confUrl."/index.php?a=welcome&m=out");
		}
	}
	
	if($result[27] == 1) {
		header("Location: /disabledaccount");
		if(isset($_GET['logout']) == 1) {
			setcookie('username', '', $exp_time);
			setcookie('password', '', $exp_time); 
			header("Location: ".$confUrl."/index.php?a=welcome&m=out");
		}
	}
	
	
	
	return $skin->make();
}
?>