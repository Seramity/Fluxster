<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('pixels/rows'); $rows = '';
		
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
		
		$skin = new skin('pixels/profile'); $profile = '';
		
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
		
		$skin = new skin('pixels/top'); $top = '';
		
		$TMPL['image'] = (!empty($result[12])) ? '<div class="profile-picture"><img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="50" height="50" /></div>' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=50&d=mm" />';md5($result[3]);
		$TMPL['username'] = $result[1];
		$TMPL['url'] = $confUrl;
				
		$top .= $skin->make();
		
	} else {
		header("Location: ".$confUrl."/index.php?a=welcome&m=den");
	}
	
	$skin = new skin('pixels/userlist'); $followerList = '';
		
	$queryFollowerList = "SELECT * FROM users WHERE image <> '' ORDER BY rand() DESC LIMIT 10";
	$resultFollowerList = mysql_query($queryFollowerList);
	
	while($TMPL = mysql_fetch_assoc($resultFollowerList)) {
		
		$TMPL['url'] = $confUrl;
		$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="64" height="64" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=64&d=mm" />';md5($result[3]);
		
		$latest .= $skin->make();
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
	$TMPL['title'] = 'My Pixels- '.$resultSettings[0];

	$skin = new skin('pixels/content');
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
		$TMPL['image'] = (!empty($result[12])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" width="80" height="80" />' : '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=80&d=mm" />';md5($result[3]);
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
									
			$resultMessagePixels = mysql_num_rows(mysql_query($queryMessages)) * 2;
                        $resultFollowerPixels = mysql_num_rows(mysql_query($queryFollowers)) * 2;
                        $resultFollowingPixels = mysql_num_rows(mysql_query($queryFollowing)) * 2;
                        $resultMessages = mysql_num_rows(mysql_query($queryMessages));
			$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
			$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));

                        $totalPixels = $resultMessagePixels + $resultFollowerPixels + $resultFollowingPixels;
			
			$TMPL['totalMessagePixels'] = $resultMessagePixels;
			$TMPL['totalFollowerPixels'] = $resultFollowerPixels;
			$TMPL['totalFollowingPixels'] = $resultFollowingPixels;
			$TMPL['totalPixels'] = $totalPoints;

			$TMPL['messages'] = $resultMessages;
			$TMPL['followers'] = $resultFollowers;
			$TMPL['following'] = $resultFollowing;
		        $TMPL['pixels'] = 'Pixels: <font color="#67a59b">'.$totalPoints.'</font>';
	
	
			if($totalPixels >= 0) {                
                             	$TMPL['newGuyBadge'] = '<div class="pixel-badge nord" title="The new guy: 0 Pixels and up"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>'; 
                        } 
                        if($totalPixels >= 10) {
                        	$TMPL['newGuyBadge'] = '<div class="pixel-badge nord" title="The new guy: 0 Pixels and up"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>';
                             	$TMPL['newbieBadge'] = '<div class="pixel-badge nord" title="Newbie: 10 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_bronze_1.png" /></div>'; 
                        }                        

                        if($totalPixels >= 100) {
                        	$TMPL['newGuyBadge'] = '<div class="pixel-badge nord" title="The new guy: 0 Pixels and up"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>';
                        	$TMPL['newbieBadge'] = '<div class="pixel-badge nord" title="Newbie: 10 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_bronze_1.png" /></div>'; 
                             	$TMPL['averageBadge'] = '<div class="pixel-badge nord" title="Average Joe: 100 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_silver_1.png" /></div>';   
                        }
                        if($totalPixels >= 500) {
                        	$TMPL['newGuyBadge'] = '<div class="pixel-badge nord" title="The new guy: 0 Pixels and up"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>';
                        	$TMPL['newbieBadge'] = '<div class="pixel-badge nord" title="Newbie: 10 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_bronze_1.png" /></div>'; 
                             	$TMPL['averageBadge'] = '<div class="pixel-badge nord" title="Average Joe: 100 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_silver_1.png" /></div>';
                            	$TMPL['nobleBadge'] = '<div class="pixel-badge nord" title="Noble User: Over 500 Pixels"><img src="'.$confUrl.'/images/badges/medal_gold_1.png" /></div>'; 
                        }
                        if($totalPixels >= 1000) {
                        	$$TMPL['newGuyBadge'] = '<div class="pixel-badge nord" title="The new guy: 0 Pixels and up"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>';
                        	$TMPL['newbieBadge'] = '<div class="pixel-badge nord" title="Newbie: 10 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_bronze_1.png" /></div>'; 
                             	$TMPL['averageBadge'] = '<div class="pixel-badge nord" title="Average Joe: 100 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_silver_1.png" /></div>'; 
                             	$TMPL['nobleBadge'] = '<div class="pixel-badge nord" title="Noble User: Over 500 Pixels"><img src="'.$confUrl.'/images/badges/medal_gold_1.png" /></div>'; 
                            	$TMPL['dedicatedBadge'] = '<div class="pixel-badge nord" title="Dedicated User: Over 1000 Pixels"><img src="'.$confUrl.'/images/badges/dedicated_trophy.png" /></div>'; 
                            	$TMPL['points'] = 'Pixels: <font color="#EAC117">'.$totalPoints.'</font>';
                        }
                        
                        $TMPL['betaTesterBadge'] = ($result[23] !== '') ? '<div class="pixel-badge nord" title="Beta Tester!"><img src="'.$confUrl.'/images/badges/beta_tester.gif" /></div>' : '';
	
	
	
			//BADGE LIST
			
			if($totalPixels >= 0) { 
				$TMPL['listNewGuy'] = '<div class="pixel-badge nord" title="The new guy: 0 Pixels and up (Unlocked)"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>';
			}
			
			if($totalPixels >= 10) {
				$TMPL['listNewbie'] = '<div class="pixel-badge nord" title="Newbie: 10 Pixels and up (Unlocked)"><img src="'.$confUrl.'/images/badges/medal_bronze_1.png" /></div>';
			} else {
				$TMPL['listNewbie'] = '<div class="pixel-badge nord" title="Newbie: 10 Pixels and up (Locked)"><img src="'.$confUrl.'/images/badges/medal_grey.png" /></div>';
			}
			
			if($totalPixels >= 100) {
				$TMPL['listAverage'] = '<div class="pixel-badge nord" title="Average Joe: 100 Pixels and up (Unlocked)"><img src="'.$confUrl.'/images/badges/medal_silver_1.png" /></div>'; 
			} else {
				$TMPL['listAverage'] = '<div class="pixel-badge nord" title="Average Joe: 100 Pixels and up (Locked)"><img src="'.$confUrl.'/images/badges/medal_grey.png" /></div>'; 
			}	
			
			if($totalPixels >= 500) {
				$TMPL['listNoble'] = '<div class="pixel-badge nord" title="Noble User: Over 500 Pixels (Unlocked)"><img src="'.$confUrl.'/images/badges/medal_gold_1.png" /></div>'; 
			} else {
				$TMPL['listNoble'] = '<div class="pixel-badge nord" title="Noble User: Over 500 Pixels (Locked)"><img src="'.$confUrl.'/images/badges/medal_grey.png" /></div>'; 
			}
	
			if($totalPixels >= 1000) {
				$TMPL['listDedicated'] = '<div class="pixel-badge nord" title="Dedicated User: Over 1000 Pixels (Unlocked)"><img src="'.$confUrl.'/images/badges/dedicated_trophy.png" /></div>'; 
			} else {
				$TMPL['listDedicated'] = '<div class="pixel-badge nord" title="Secret (Locked)"><img src="'.$confUrl.'/images/badges/trophy_grey.png" /></div>';
			}
			
			$TMPL['listBetaTester'] = ($result[23] !== '') ? '<div class="pixel-badge nord" title="Beta Tester! (Unlocked)"><img src="'.$confUrl.'/images/badges/beta_tester.gif" /></div>' : '';
			
			if($resultMessages <= 0) {
				$TMPL['listQuiet'] = '<div class="pixel-badge nord" title="Quiet: No messages posted (Unlocked)"><img src="'.$confUrl.'/images/badges/quiet.png" /></div> &nbsp;';
			} 
			
			if($totalPixels <= 10) {
				$TMPL['noPixels'] = '<font color="#b64949">Oh no! It looks like you have low pixels! :( </font>';
				$TMPL['noPixelsNotice'] = '<br /><br /><font color="#b64949">Don\'t worry, weither you are a new user or you just
				haven\'t done squat on Fluxster yet, you can still easily earn some Pixels! By just simply getting involved!</font>';
			}
			
			if($resultMessages <= 0 AND $resultFollowing >= 10) {
			 	$TMPL['listStalker'] = '<div class="pixel-badge nord" title="Stalker: No messages posted and is following 10 or more users (Unlocked)"><img src="'.$confUrl.'/images/badges/stalker.png" /></div> &nbsp;';
			} 
			
			$TMPL['listStaff'] = ($result[24] !== '') ? '<div class="pixel-badge nord" title="Staff (Unlocked)"><img src="'.$confUrl.'/images/badges/staff.png" /></div>' : '';
			
			$TMPL['listFacebook'] = ($result[9] !== '') ? '<div class="pixel-badge nord" title="Facebook: Add your Facebook link (Unlocked)"><img src="'.$confUrl.'/images/badges/facebook.png" /></div>' : '<div class="pixel-badge nord" title="Facebook: Add your Facebook link (Locked)"><img src="'.$confUrl.'/images/badges/facebook_grey.png" /></div>';
			
			$TMPL['listTwitter'] = ($result[10] !== '') ? '<div class="pixel-badge nord" title="Twitter: Add your Twiiter link (Unlocked)"><img src="'.$confUrl.'/images/badges/twitter.png" /></div>' : '<div class="pixel-badge nord" title="Twitter: Add your Twitter link (Locked)"><img src="'.$confUrl.'/images/badges/twitter_grey.png" /></div>';
			
			$TMPL['listYoutube'] = ($result[11] !== '') ? '<div class="pixel-badge nord" title="Youtube: Add your Channel link (Unlocked)"><img src="'.$confUrl.'/images/badges/youtube.png" /></div>' : '<div class="pixel-badge nord" title="Youtube: Add your Channel link (Locked)"><img src="'.$confUrl.'/images/badges/youtube_grey.png" /></div>';
			
			$TMPL['listPicture'] = ($result[12] !== '') ? '<div class="pixel-badge nord" title="Add a profile picture (Unlocked)"><img src="'.$confUrl.'/images/badges/picture.png" /></div>' : '<div class="pixel-badge nord" title="Add a profile picture (Locked)"><img src="'.$confUrl.'/images/badges/picture_grey.png" /></div>';
			
			$TMPL['listBanner'] = ($result[21] !== '') ? '<div class="pixel-badge nord" title="Add a profile banner (Unlocked)"><img src="'.$confUrl.'/images/badges/banner.png" /></div>' : '<div class="pixel-badge nord" title="Add a profile banner (Locked)"><img src="'.$confUrl.'/images/badges/banner_grey.png" /></div>';
			
			$TMPL['listName'] = ($result[4] !== '') ? '<div class="pixel-badge nord" title="Add your name (Unlocked)"><img src="'.$confUrl.'/images/badges/name.png" /></div>' : '<div class="pixel-badge nord" title="Add your name (Locked)"><img src="'.$confUrl.'/images/badges/name_grey.png" /></div>';
			
			$TMPL['listBio'] = ($result[7] !== '') ? '<div class="pixel-badge nord" title="Add a bio (Unlocked)"><img src="'.$confUrl.'/images/badges/bio.png" /></div>' : '<div class="pixel-badge nord" title="Add a bio (Locked)"><img src="'.$confUrl.'/images/badges/bio_grey.png" /></div>';
			
			$TMPL['listLocation'] = ($result[5] !== '') ? '<div class="pixel-badge nord" title="Add your location (Unlocked)"><img src="'.$confUrl.'/images/badges/location.png" /></div>' : '<div class="pixel-badge nord" title="Add your location (Locked)"><img src="'.$confUrl.'/images/badges/location_grey.png" /></div>';
			
			if($resultMessages >= 50) {
				$TMPL['list50Messages'] = '<div class="pixel-badge nord" title="Post 50 or more messages (Unlocked)"><img src="'.$confUrl.'/images/badges/messages.png" /></div>';
			} else {
				$TMPL['list50Messages'] = '<div class="pixel-badge nord" title="Post 50 or more messages (Unlocked)"><img src="'.$confUrl.'/images/badges/messages_grey.png" /></div>';
			}
			
			if($resultFollowers >= 50) {
				$TMPL['list50Followers'] = '<div class="pixel-badge nord" title="50 or more followers (Unlocked)"><img src="'.$confUrl.'/images/badges/follower.png" /></div>';
			} else {
				$TMPL['list50Followers'] = '<div class="pixel-badge nord" title="50 or more followers (Locked)"><img src="'.$confUrl.'/images/badges/follower_grey.png" /></div>';
			}
			
			if($resultFollowing >= 50) {
				$TMPL['list50Following'] = '<div class="pixel-badge nord" title="50 or more followings (Unlocked)"><img src="'.$confUrl.'/images/badges/following.png" /></div>';
			} else {
				$TMPL['list50Following'] = '<div class="pixel-badge nord" title="50 or more followings (Locked)"><img src="'.$confUrl.'/images/badges/following_grey.png" /></div>';
			}
			
			if($resultMessages >= 50 AND $resultFollowing >= 50 AND $resultFollowers >= 50) {
				$TMPL['list50All'] = '<div class="pixel-badge nord" title="50 or more messages, followers, and followings (Unlocked)"><img src="'.$confUrl.'/images/badges/bronze_crown.png" /></div>';
			} else {
				$TMPL['list50All'] = '<div class="pixel-badge nord" title="50 or more messages, followers, and followings (Locked)"><img src="'.$confUrl.'/images/badges/crown_grey.png" /></div>';
			}
			
			if($resultMessages >= 100 AND $resultFollowing >= 100 AND $resultFollowers >= 100) {
				$TMPL['list100All'] = '<div class="pixel-badge nord" title="100 or more messages, followers, and followings (Unlocked)"><img src="'.$confUrl.'/images/badges/silver_crown.png" /></div>';
			} else {
				$TMPL['list100All'] = '<div class="pixel-badge nord" title="100 or more messages, followers, and followings (Locked)"><img src="'.$confUrl.'/images/badges/crown_grey.png" /></div>';
			}
			
			if($resultMessages >= 500 AND $resultFollowing >= 500 AND $resultFollowers >= 500) {
				$TMPL['list500All'] = '<div class="pixel-badge nord" title="500 or more messages, followers, and followings (Unlocked)"><img src="'.$confUrl.'/images/badges/gold_crown.png" /></div>';
			} else {
				$TMPL['list500All'] = '<div class="pixel-badge nord" title="Secret (Locked)"><img src="'.$confUrl.'/images/badges/crown_grey.png" /></div>'; 
			}
			
			if(!empty($result[4]) AND !empty($result[7]) AND !empty($result[5]) AND !empty($result[12]) AND !empty($result[21]) AND !empty($result[9])
			AND !empty($result[10]) AND !empty($result[11])) {
				$TMPL['profileComplete'] = '<font color="#adb333">Completed!</font>'; 
			}
			
			if($result[47] == 1) {
				$TMPL['plusMember'] = '<div class="pixel-badge nord" title="Joined Fluxster Plus"><div style="color: #67a59b; font-size: 16px; font-style: italic; font-weight: bold; margin-top: 1px; display: inline;">P</div></div>';
				if($result[49] == 1) {
					$TMPL['plusBetaTester'] = '<div class="pixel-badge nord" title="Fluxster Plus Beta Tester"><img src="'.$confUrl.'/images/badges/plusBetaTester.png" /></div>';
				} else {
					$TMPL['plusBetaTester'] = '<div class="pixel-badge nord" title="Fluxster Plus Beta Tester (Locked)"><img src="'.$confUrl.'/images/badges/plusBetaTester_grey.png" /></div>';
				}
				
				$TMPL['plusBadges'] = '
					<div class="columns three">	
						<div class="sidebar-description-head" style="text-align: left;"><h7>Fluxster Plus</h7> </div>
						<div class="sidebar-description-text" style="text-align: left;">
						'.$TMPL['plusMember'].' &nbsp; '.$TMPL['plusBetaTester'].'
						</div>
					</div>
					';
			} else {
				$TMPL['plusBadges'] = '
					<div class="columns three">	
						<div class="sidebar-description-head" style="text-align: left;"><h7>Fluxster Plus</h7> </div>
						<div class="sidebar-description-text" style="text-align: left;">
							Become a Fluxster Plus member to gain access to these badges.
						</div>
					</div>
					<br>
					';
			}
		
	if(!empty($result[19])) {
		header("Location: /suspendedaccount");
	}
	
	if($result[27] == 1) {
		header("Location: /disabledaccount");
	}
	
	
	return $skin->make();
}
?>