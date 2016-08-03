 <?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		$TMPL_old = $TMPL; $TMPL = array();
		
		
		$skin = new skin('passport/profile'); $profile = '';
		
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

		// GRAB IP
		$TMPL['currentIP'] = $_SERVER["REMOTE_ADDR"];	

		$profile .= $skin->make(); 
		
		$skin = new skin('passport/top'); $top = ''; 
		
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

	$TMPL['interval'] = $resultSettings[8];
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome&m=out");
	}

	$TMPL['url'] = $confUrl;
	$TMPL['title'] = 'Passport - '.$resultSettings[0]; 

	$skin = new skin('passport/content');
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
	// CURRENT PASSPORT
	$TMPL['myIP'] = (!empty($result[38])) ? ''.$result[38].'' : 'Not recorded'; 
	$TMPL['myTime'] = (!empty($result[39])) ? ''.$result[39].' (Central Daylight Time)' : 'Not recorded';
	$TMPL['myOS'] = (!empty($result[40])) ? ''.$result[40].'' : 'Not recorded';
	$TMPL['myBrowser'] = (!empty($result[41])) ? ''.$result[41].'' : 'Not recorded'; 
	
	// LAST PASSPORT
	$TMPL['lastIP'] = (!empty($result[42])) ? ''.$result[42].'' : 'Not recorded'; 
	$TMPL['lastTime'] = (!empty($result[43])) ? ''.$result[43].' (Central Daylight Time)' : 'Not recorded';
	$TMPL['lastOS'] = (!empty($result[44])) ? ''.$result[44].'' : 'Not recorded';
	$TMPL['lastBrowser'] = (!empty($result[45])) ? ''.$result[45].'' : 'Not recorded'; 
	
	// MESSAGES
	if($_GET['m'] == 're') {	
		$TMPL['message'] = '
								<div class="notification-box notification-box-success">
								<h5>Success!</h5> 
								<p>Your account has been reactivated.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>'; 
	}
	
	if($_GET['m'] == 'new') {	
		$TMPL['message'] = '
								<div class="notification-box notification-box-success">
								<h5>Welcome to Fluxster!</h5> 
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>'; 
	}
	
	// REDIRECTS
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
	
	
	//ADMIN ACCESS
	if($result[28] == 0) {
		header("Location: /stream");
	}
	
	return $skin->make();
}
?>