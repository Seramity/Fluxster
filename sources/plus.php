<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
	$request = mysql_fetch_row(mysql_query($query));
	
	if($request[28] == 1) {
	
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('plus/rows'); $rows = '';
		
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
		
		$skin = new skin('plus/profile'); $profile = '';
		
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
	
	} else {
		header("Location: ".$confUrl."/notfound");
	}	
	
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
	$TMPL['title'] = 'Fluxster Plus - '.$resultSettings[0];

	;

	$skin = new skin('plus/content');
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
	if(!empty($result[19])) { 
		header("Location: /stream");

	}
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
	
	// Check if requested
	if($result[47] == 1) {
		// USER IS A PLUS MEMBER
		$TMPL['plusPage'] = '
<div class="content">
<div class="four columns">
<div class="sidebar">
	<div class="sidebar-image">
		<a href="'.$confUrl.'/profile/'.$TMPL['username'].'">'.$TMPL['image'].'</a>
	</div>
	<div class="sidebar-details">
		<div class="sidebar-username">
			<a href="'.$confUrl.'/profile/'.$TMPL['username'].'"><h7><div style="text-transform:capitalize; display: inline-block;">'.$TMPL['name'].'</div></h7></a> '.$TMPL['verify'].'
		<br />
		'.$TMPL['points'].' | <a href="'.$confUrl.'/settings">Edit Profile</a>
		</div>
		
	<br />
	
	</div>
	
	<div class="discover-input">
	
				<input type="text" id="hashtag" value="Search a topic/hashtag" />
				
						
			</div>
			
	<br />
	
	<table width="100%">
		<tr>
			<td style="width: 70px;"><h4>'.$TMPL['messages'].'</h4></td>
			<td style="width: 80px;"><h4>'.$TMPL['followers'].'</h4></td>
			<td><h4>'.$TMPL['following'].'</h4></td>
		</tr>
		
		<tr>
			<td><strong>posts</strong></td>
			<td><strong>followers</strong></td>
			<td><strong>following</strong></td>
		</tr>
	</table>
	
</div>						
</div>
<div class="eight columns">
	<div class="meTitle"> 
			<center><img src="'.$confUrl.'/images/fluxster-plus_banner.png" width="200px"></center>
	</div>
	<div class="sidebar">
		<center><h4>Subscription Control Panel</h4></center>
		
		<p>
			Hello '.$TMPL['name'].', and welcome to your Fluxster Plus control panel.
			<br>
			Here you can manage and see all your perks and other features.
		</p>
		
		<div class="row_welcome">
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>Early Access</h4></div>
					<div class="sidebar-description-text">
						There are currently no betas happening. <br>You will be notified via email if so.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>Free stuff</h4></div>
					<div class="sidebar-description-text">
						New premium badges.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>Other</h4></div>
					<div class="sidebar-description-text">
						None
					</div>
				</div>
			</div>
	</div>

	<div class="divider"></div>
	
	<div class="sidebar">
		<center>
			<p>Want to leave the Fluxster Plus program?</p>
			<div class="follow-container"><a href="'.$confUrl.'/index.php?a=settings&b=plus"><div class="follow-button">Leave</div></a></div>
		</center>
	</div>
</div>
</div>

		';	         
	} else if($result[48] == 1){
		// USER HAS SENT A REQUEST
		$TMPL['plusPage'] = '
		<div class="twelve columns">
		<div class="content">
		
		<br> 
		<center><img src="'.$confUrl.'/images/fluxster-plus_banner.png"/></center>
		
		<center><h2>Features and Perks</h2></center>
		
		<div class="sidebar">			
			<div class="row_welcome">
				<div class="columns four-list">
					<div class="sidebar-description"><img src="'.$confUrl.'/images/flat_icons/browser.png" /></div>
					<div class="sidebar-description-head"><h4>Early Access</h4></div>
					<div class="sidebar-description-text">
						With Fluxster Plus, you gain early access to new features and apps.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description"><img src="'.$confUrl.'/images/flat_icons/diamond.png" /></div>
					<div class="sidebar-description-head"><h4>Free stuff</h4></div>
					<div class="sidebar-description-text">
						Fluxster Plus rewards you for helping and being a part of our community. Rewards vary through time.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description"><img src="'.$confUrl.'/images/flat_icons/open-box.png" /></div>
					<div class="sidebar-description-head"><h4>And much more!</h4></div>
					<div class="sidebar-description-text">
						There are many more perks and features that come with Fluxster Plus. And more to come in the future!
					</div>
				</div>
			</div>
		</div>
		
		<div class="divider"></div>
		
		<div class="sidebar">	
			<div class="row_welcome">
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>What is Fluxster Plus?</h4></div>
					<div class="sidebar-description-text">
						Fluxster Plus is an optional premium membership that gives your special perks and permissions on Fluxster. 
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>Requirements</h4></div>
					<div class="sidebar-description-text">
						The only requirements for Fluxster Plus are being a mature and active user without any record of suspensions.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>How do I sign up?</h4></div>
					<div class="sidebar-description-text">
						Just click on the "Request Upgrade" button at the bottom of the page to send your request for membership.
					</div>
				</div>
			</div>
		</div>


	<div class="sidebar">
		<center>
			<h2>Your request is pending.</h2> <p>Please be patient, we are still looking over your account for approval.</p>
		</center>
	</div>
	</div>
	</div>
		';
	} else {
		// USER HAS NOT SENT A REQUEST
		$TMPL['plusPage'] = '
		<div class="twelve columns">
		<div class="content">
		
		<br> 
		<center><img src="'.$confUrl.'/images/fluxster-plus_banner.png"/></center>
		
		<center><h2>Features and Perks</h2></center>
		
		<div class="sidebar">			
			<div class="row_welcome">
				<div class="columns four-list">
					<div class="sidebar-description"><img src="'.$confUrl.'/images/flat_icons/browser.png" /></div>
					<div class="sidebar-description-head"><h4>Early Access</h4></div>
					<div class="sidebar-description-text">
						With Fluxster Plus, you gain early access to new features and apps.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description"><img src="'.$confUrl.'/images/flat_icons/diamond.png" /></div>
					<div class="sidebar-description-head"><h4>Free stuff</h4></div>
					<div class="sidebar-description-text">
						Fluxster Plus rewards you for helping and being a part of our community. Rewards vary through time.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description"><img src="'.$confUrl.'/images/flat_icons/open-box.png" /></div>
					<div class="sidebar-description-head"><h4>And much more!</h4></div>
					<div class="sidebar-description-text">
						There are many more perks and features that come with Fluxster Plus. And more to come in the future!
					</div>
				</div>
			</div>
		</div>
		
		<div class="divider"></div>
		
		<div class="sidebar">	
			<div class="row_welcome">
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>What is Fluxster Plus?</h4></div>
					<div class="sidebar-description-text">
						Fluxster Plus is an optional premium membership that gives your special perks and permissions on Fluxster. 
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>Requirements</h4></div>
					<div class="sidebar-description-text">
						The only requirements for Fluxster Plus are being a mature and active user without any record of suspensions.
					</div>
				</div>
				<div class="columns four-list">
					<div class="sidebar-description-head"><h4>How do I sign up?</h4></div>
					<div class="sidebar-description-text">
						Just click on the "Request Upgrade" button at the bottom of the page to send your request for membership.
					</div>
				</div>
			</div>
		</div>


	<div class="sidebar">
		<center>
			<h2>Fluxster Plus is <i>free</i>! Why not try it out?</h2><br>
			<form action="'.$confUrl.'/index.php?a=plus" method="post"><input type="submit" name="request" value="Request Upgrade" /></form>
		</center>
	</div>
	</div>
	</div>
		';

	}
	
	
	
	
	if(isset($_POST['request'])) {
		$query = sprintf("UPDATE users SET requestedPlus = 1 WHERE username = '%s'", mysql_real_escape_string($_COOKIE['username']));
		mysql_query($query);
		header("Location: /index.php?a=plus&m=plm");
	}	

	if($_GET['m'] == 'plm') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Your request has been submitted. You will be notified about your membership soon via email.</div>'; 
	}
	
	return $skin->make();
	
	
	
}
?>