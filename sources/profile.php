<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$getId = sprintf("SELECT * FROM users WHERE username = '%s'", mysql_real_escape_string($_GET['u']));
	$resultId = mysql_fetch_row(mysql_query($getId));
	
	$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
	$qPP = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", $resultId[0], $data['id']);
	$rPP = mysql_fetch_row(mysql_query($qPP));	
	
	
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));
	
	if(!empty($result[19])) {
		header("Location:  {$url}/suspended"); 	
	} elseif ($result[27] == 1) {
		header("Location:  {$url}/disabled"); 
	} 
	
	if(($data == false && $resultId[3] == '1') || ($data == true && $resultId[3] == '1' && $_COOKIE['username'] !== $resultId[1])) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('profile/private'); $error = '';
			header("Location: {$url}/private");
		$error .= $skin->make();
	
	} elseif (($data == false && $resultId[3] == '2' && $rPP == '') || ($data == true && $resultId[3] == '2' && $_COOKIE['username'] !== $resultId[1] && $rPP == '')) {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('profile/private'); $error = '';
			header("Location: {$url}/private");
		$public = '_follow';
		$error .= $skin->make();
	} 
	elseif(!empty($resultId[1])) {
	
		$TMPL['subtitle'] = 'profile';
		if($_GET['f'] == 'followers') {
			$TMPL['subtitle'] = 'followers';
			$TMPL['followedvar'] = '&f=followers';
		} elseif($_GET['f'] == 'following') {
			$TMPL['subtitle'] = 'following';	
			$TMPL['followedvar'] = '&f=following';
		}
		
		
		//FOLLOWERS-FOLLOWING PAGES
		
		if($_GET['f'] == 'followers' || $_GET['f'] == 'following') {
			$TMPL_old = $TMPL; 
			$skin = new skin('profile/follow'); $rows = '';

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			if($_GET['f'] == 'followers') {
				$val1 = 'follower';
				$val2 = 'leader';
			} elseif($_GET['f'] == 'following') {
				$val1 = 'leader';
				$val2 = 'follower';
			}
			$queryFollowers = sprintf("SELECT %s FROM relations WHERE %s = '%s'", $val1, $val2,
									mysql_real_escape_string($resultId[0]));
			$array = array();
			
			$resFol = mysql_query($queryFollowers);
			while($row = mysql_fetch_assoc($resFol)) {
				$array[] = $row["$val1"];
			}
			$followers_separated = implode(",", $array);
			
			$queryMsg = sprintf("SELECT * FROM users WHERE idu IN (%s) ORDER BY idu DESC LIMIT %s", $followers_separated, $resultSettings[1]);
			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
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
				
				$TMPL['url'] = $confUrl;
				
				if($_GET['f'] == 'followers') {
					$val3 = ($resultId[0] == $data['id']) ? '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'"><div class="follow-container-follow"><div class="view-button">view profile</div></div></a>' : '';
				} elseif($_GET['f'] == 'following') {
					$val3 = ($resultId[0] == $data['id']) ? '<a href="'.$confUrl.'/profile/'.$TMPL['username'].'"><div class="follow-container-follow"><div class="view-button">view profile</div></div></a>' : '';		
				}
				$TMPL['follow'] = $val3;
				$newArr[] = $TMPL['idu'];
				$rows .= $skin->make();
			}
			
			
		
			// HIDING INFO
			
			$query = sprintf("SELECT * FROM users WHERE username = '%s'", 
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));
			
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", 	mysql_real_escape_string($data['id']), mysql_real_escape_string($result[0])); 
			$resultRelation = mysql_query($queryRelation);
				
			
			if ($result[31] == 1) { 
				 if ($_COOKIE['username'] !== $resultId[1]) {
					if(mysql_num_rows($resultRelation) == 0) { 
						$skin = new skin('profile/profile2'); $profile = ''; 					
					} else {
						$skin = new skin('profile/profile'); $profile = ''; 
					}
					} else {
						$skin = new skin('profile/profile3'); $profile = ''; 
				}	
			} else {
				$skin = new skin('profile/profile'); $profile = ''; 
			}
			
			// Get Profile Data
			$query = sprintf("SELECT * FROM users WHERE username = '%s'",
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));

			$TMPL['image'] = (!empty($result[12])) ? '<div class="profile-picture"><img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" style="width="150" height="150""/></div>' 
			: '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=150&d=mm" />';md5($result[3]);
			
			$TMPL['username'] = $result[1];
			$TMPL['name'] = (!empty($result[4])) ? '<strong>Name</strong>: '.$result[4].' ' : '';
			$TMPL['url'] = $confUrl;
			$TMPL['badge'] = (!empty($result[6])) ? '<div class="profile-description">'.$result[6].'</div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? '<div class="sidebar-about"><strong>Location</strong>: '.$result[5].'</div>' : '';
			$TMPL['bio'] = (!empty($result[7])) ? '<div class="sidebar-about"><strong>Bio</strong>: <br />'.$result[7].'</div>' : '';
			$TMPL['promotion'] = (!empty($result[16])) ? '<div class="sidebar-about">'.$result[16].'</div>' : '';
			$TMPL['date'] = (!empty($result[8])) ? '<strong>Joined</strong>: '.$result[8].'' : '';
                        $TMPL['visitProfile'] = (!empty($result[1])) ? '<div class="follow-container-follow"><a href="'.$confUrl.'/profile/'.$result[1].'"><div class="follow-button">View Profile</div></a></div>' : '';
			$TMPL['reportUser'] = (!empty($result[1])) ? '<img src="'.$confUrl.'/images/report.png" width="10px" height="10px" /> Report User' : '';
			$TMPL['website'] = (!empty($result[18])) ? '<div class="sidebar-about">
						<strong>Website</strong>: <a href="'.$result[18].'" target="_blank" rel="nofollow">'.$result[18].'</a></div>' : '';
			
			if($result[34] == '1') {
			        $TMPL['gender'] = '<div class="sidebar-about"><strong>Gender</strong>: Male</div>';
			} else if($result[34] == '2') {
                                $TMPL['gender'] = '<div class="sidebar-about"><strong>Gender</strong>: Female</div>';
                        } else {
                                $TMPL['gender'] = '';
                        }	
			
			if (empty($result[5]) AND empty($result[7]) AND empty($result[16])) {
				$TMPL['noInfo'] = '<div class="sidebar-about"><strong>No infomation given</strong></div>';  
			}
			
			if ($_COOKIE['username'] == $result[1]) {
				$TMPL['edit'] = '(<a href="/settings">Edit</a>)';
			}

			if($result[18] !== '' || $result[9] !== '' || $result[10] !== '' || $result[11]) {
				$ws = ($result[18] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/website.png" /> <a href="'.$result[18].'" target="_blank" rel="nofollow">Website</a></div>' : '';
				$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">Facebook</a></div>' : '';
				$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">Twitter</a></div>' : '';
				$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">Google+</a></div>' : '';
				$TMPL['social'] = '<div class="divider"></div>
								   <div class="sidebar">
								   '.$ws.'
								   '.$facebook.'
								   '.$twitter.'
								   '.$google.'
								   </div>';
			}

                        if($result[29] == 0) {
                                  $TMPL['showEmail'] = '<div class="sidebar-about"><strong>Email</strong>: '.$result[3].'</div>';
                        } else {
                                  $TMPL['showEmail'] = '';
                        }

			
			// Get posts number
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
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
		        $TMPL['points'] = ''.$totalPoints.'';
			
			$profile .= $skin->make();
			$public = '_follow';
		}
		
	
		else if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
			$TMPL_old = $TMPL; $TMPL = array();
			
			// GET USER INFO
			$query = sprintf("SELECT * FROM users WHERE username = '%s'", 
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));
			
			// GET RELATION
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", 	mysql_real_escape_string($data['id']), mysql_real_escape_string($result[0])); 
			$resultRelation = mysql_query($queryRelation);
			
			if ($result[32] == 1) { 
				 if ($_COOKIE['username'] !== $resultId[1]) {
					if(mysql_num_rows($resultRelation) == 0) { 
						$skin = new skin('profile/rows3'); $rows = ''; 					
					} else { 
						$skin = new skin('profile/rows'); $rows = '';  
					}
				} else { 
					$skin = new skin('profile/rows4'); $rows = '';  
				}	
			} else {
				$skin = new skin('profile/rows'); $rows = '';  
			}

			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);

			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid = '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string($resultId[0]), $resultSettings[1]);
	
			$newArr = array(); 
			$resultMsg = mysql_query($queryMsg);
			
			
			while($TMPL = mysql_fetch_assoc($resultMsg)) { 
				$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?Â«Â»â€œâ€â€˜â€™]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
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
			
			// HIDING INFO
			
			$query = sprintf("SELECT * FROM users WHERE username = '%s'", 
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));
			
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", 	mysql_real_escape_string($data['id']), mysql_real_escape_string($result[0])); 
			$resultRelation = mysql_query($queryRelation);
				
			
			if ($result[31] == 1) { 
				 if ($_COOKIE['username'] !== $resultId[1]) {
					if(mysql_num_rows($resultRelation) == 0) { 
						$skin = new skin('profile/profile2'); $profile = ''; 					
					} else {
						$skin = new skin('profile/profile'); $profile = ''; 
					}
					} else {
						$skin = new skin('profile/profile3'); $profile = ''; 
				}	
			} else {
				$skin = new skin('profile/profile'); $profile = ''; 
			}
			
			// Get Profile Data 
			$query = sprintf("SELECT * FROM users WHERE username = '%s'", 
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));

			$TMPL['image'] = (!empty($result[12])) ? '<div class="profile-picture"><img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" style="width="150" height="150""/></div>' 
			: '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=150&d=mm" />';md5($result[3]);
			
			
			
			$TMPL['username'] = $result[1];
			$TMPL['name'] = (!empty($result[4])) ? '<strong>Name</strong>: '.$result[4].' ' : '';
			$TMPL['url'] = $confUrl;
			$TMPL['badge'] = (!empty($result[6])) ? '<div class="profile-description">'.$result[6].'</div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? '<div class="sidebar-about"><strong>Location</strong>: '.$result[5].'</div>' : '';
			$TMPL['bio'] = (!empty($result[7])) ? '<div class="sidebar-about"><strong>Bio</strong>: <p>'.$result[7].'</p></div>' : '';
			$TMPL['promotion'] = (!empty($result[16])) ? '<div class="sidebar-about">'.$result[16].'</div>' : '';
			$TMPL['date'] = (!empty($result[8])) ? '<strong>Joined</strong>: '.$result[8].'' : '';
			$TMPL['reportUser'] = (!empty($result[1])) ? '<img src="'.$confUrl.'/images/report.png" width="10px" height="10px" /> Report User' : '';
			$TMPL['website'] = (!empty($result[18])) ? '<div class="sidebar-about">
						<strong>Website</strong>: <a href="'.$result[18].'" target="_blank" rel="nofollow">'.$result[18].'</a></div>' : '';
						
			if($result[34] == '1') {
			        $TMPL['gender'] = '<div class="sidebar-about"><strong>Gender</strong>: Male</div>';
			} else if($result[34] == '2') {
                                $TMPL['gender'] = '<div class="sidebar-about"><strong>Gender</strong>: Female</div>';
                        } else {
                                $TMPL['gender'] = '';
                        }				
						
			if (empty($result[5]) AND empty($result[7]) AND empty($result[16])) {
				$TMPL['noInfo'] = '<div class="sidebar-about"><strong>No infomation given</strong></div>';  
			}
			
			if ($_COOKIE['username'] == $result[1]) {
				$TMPL['edit'] = '(<a href="/settings">Edit</a>)';
			}	
				
			if($result[18] !== '' || $result[9] !== '' || $result[10] !== '' || $result[11]) {
				$ws = ($result[18] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/website.png" /> <a href="'.$result[18].'" target="_blank" rel="nofollow">Website</a></div>' : '';
				$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">Facebook</a></div>' : '';
				$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">Twitter</a></div>' : '';
				$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">Google+</a></div>' : '';
				$TMPL['social'] = '<div class="divider"></div>
								   <div class="sidebar">
								   '.$ws.'
								   '.$facebook.'
								   '.$twitter.'
								   '.$google.'
								   </div>';
			}

                        if($result[29] == 0) {
                                  $TMPL['showEmail'] = '<div class="sidebar-about"><strong>Email</strong>: '.$result[3].'</div>';
                        } else {
                                  $TMPL['showEmail'] = '';
                        }
			
			// Get posts number
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
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
		        $TMPL['points'] = ''.$totalPoints.'';
			
			// Follow Unfollow buttons
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0]));
			$resultRelation = mysql_query($queryRelation);
			
			if(mysql_num_rows($resultRelation) == 1) { 
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="unfollow-button">
				Following</div></a></div>' : '';
				if($_GET['r'] == 2) {
					$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">
					<img src="'.$confUrl.'/images/icons/follow.png" /> Follow</div></a></div>' : '';
					$delete = sprintf("DELETE FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0]));
					mysql_query($delete);
					header("Location: ".$confUrl."/index.php?a=profile&u=".mysql_real_escape_string($_GET['u'])."&m=fu");
				}
			} elseif(mysql_num_rows($resultRelation) == 0) {
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">Follow</div></a></div>' : '';
				if($_GET['r'] == 1) {
					if($resultId[0] !== $data['id']) { 
					$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="follow-button">Following</div></a></div>' : '';
					$insert = sprintf("INSERT INTO relations (`id`, `leader`, `follower`) VALUES ('', '%s', '%s')", mysql_real_escape_string($resultId[0]), mysql_real_escape_string($data['id']));
					mysql_query($insert);
					header("Location: ".$confUrl."/index.php?a=profile&u=".mysql_real_escape_string($_GET['u'])."&m=ff");
					}
				}
			}
			
			$profile .= $skin->make();
			
			$skin = new skin('profile/top'); $top = '';
			
			$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($data['mail']).'?s=50&d=mm" />';
			$TMPL['username'] = $result[1];
			$TMPL['currentUser'] = $_GET['u'];
			$TMPL['url'] = $confUrl;
			
			$top .= $skin->make();
			
		} else {
			$TMPL_old = $TMPL; $TMPL = array();
			
			// GET USER INFO
			$query = sprintf("SELECT * FROM users WHERE username = '%s'", 
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));
			
			// GET RELATION
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", 	mysql_real_escape_string($data['id']), mysql_real_escape_string($result[0])); 
			$resultRelation = mysql_query($queryRelation);
			
			if ($result[32] == 1) { 
				$skin = new skin('profile/rows3'); $rows = '';
			} else {
				$skin = new skin('profile/rows2'); $rows = '';
			}
			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid = '%s' AND messages.uid = users.idu ORDER BY messages.id DESC LIMIT %s", mysql_real_escape_string($resultId[0]), $resultSettings[1]);

			$newArr = array();
			
			$resultMsg = mysql_query($queryMsg);
			while($TMPL = mysql_fetch_assoc($resultMsg)) {
				$TMPL['message'] = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?Â«Â»â€œâ€â€˜â€™]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $TMPL['message']);
				
				$censArray = explode(',', $resultSettings[4]);
				$TMPL['message'] = strip_tags(str_replace($censArray, '', $TMPL['message']), '<a>');
				
				$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" class="message-profile-pic />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=70&d=mm"  class="message-profile-pic"/>';md5($result[3]);
				
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
			
			// HIDING INFO
			
			$query = sprintf("SELECT * FROM users WHERE username = '%s'", 
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));
			
			$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", 	mysql_real_escape_string($data['id']), mysql_real_escape_string($result[0])); 
			$resultRelation = mysql_query($queryRelation);
				
			
			if ($result[31] == 1) { 
				 if ($_COOKIE['username'] !== $resultId[1]) {
					if(mysql_num_rows($resultRelation) == 0) { 
						$skin = new skin('profile/profile2'); $profile = ''; 					
					} else {
						$skin = new skin('profile/profile'); $profile = ''; 
					}
					} else {
						$skin = new skin('profile/profile3'); $profile = ''; 
				}	
			} else {
				$skin = new skin('profile/profile'); $profile = ''; 
			}
			
			// Get Profile Data
			$query = sprintf("SELECT * FROM users WHERE username = '%s'",
							mysql_real_escape_string($_GET['u']));
			$result = mysql_fetch_row(mysql_query($query));

			$TMPL['image'] = (!empty($result[12])) ? '<div class="profile-picture"><img src="'.$confUrl.'/uploads/avatars/'.$result[12].'" style="width="150" height="150""/></div>' 
			: '<img src="http://www.gravatar.com/avatar/'.md5($result[3]).'?s=150&d=mm" />';md5($result[3]);
			
			$TMPL['username'] = $result[1];
			$TMPL['name'] = (!empty($result[4])) ? '<strong>Name</strong>: '.$result[4].' ' : '';
			$TMPL['url'] = $confUrl;
			$TMPL['badge'] = (!empty($result[6])) ? '<div class="profile-description">'.$result[6].'</div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? '<div class="sidebar-about"><strong>Location</strong>: '.$result[5].'</div>' : '';
			$TMPL['bio'] = (!empty($result[7])) ? '<div class="sidebar-about"><strong>Bio</strong>: <br />'.$result[7].'</div>' : '';
			$TMPL['promotion'] = (!empty($result[16])) ? '<div class="sidebar-about">'.$result[16].'</div>' : '';
			$TMPL['date'] = (!empty($result[8])) ? '<strong>Joined</strong>: '.$result[8].'' : '';
			$TMPL['loginNote'] = (!empty($result[1])) ? '<div class="sidebar-about"><strong>Want to follow this user? <br /><a href="'.$confUrl.'"><font color="67a59b">Login</font></a> or 
			<a href="'.$confUrl.'/register"><font color="67a59b">Register</font></a></strong></div>' : '';
			$TMPL['website'] = (!empty($result[18])) ? '<div class="sidebar-about">
						<strong>Website</strong>: <a href="'.$result[18].'" target="_blank" rel="nofollow">'.$result[18].'</a></div>' : '';
						
			if($result[34] == '1') {
			        $TMPL['gender'] = '<div class="sidebar-about"><strong>Gender</strong>: Male</div>';
			} else if($result[34] == '2') {
                                $TMPL['gender'] = '<div class="sidebar-about"><strong>Gender</strong>: Female</div>';
                        } else {
                                $TMPL['gender'] = '';
                        }			
					
			if (empty($result[5]) AND empty($result[7]) AND empty($result[16])) {
				$TMPL['noInfo'] = '<div class="sidebar-about"><strong>No infomation given</strong></div>';  
			}
			
			
			if($result[18] !== '' || $result[9] !== '' || $result[10] !== '' || $result[11]) {
				$ws = ($result[18] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/website.png" /> <a href="'.$result[18].'" target="_blank" rel="nofollow">Website</a></div>' : '';
				$facebook = ($result[9] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/facebook.png" /> <a href="'.$result[9].'" target="_blank" rel="nofollow">Facebook</a></div>' : '';
				$twitter = ($result[10] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/twitter.png" /> <a href="'.$result[10].'" target="_blank" rel="nofollow">Twitter</a></div>' : '';
				$google = ($result[11] !== '') ? '<div class="social-url"><img src="'.$confUrl.'/images/icons/google.png" /> <a href="'.$result[11].'" target="_blank" rel="nofollow">Google+</a></div>' : '';
				$TMPL['social'] = '<div class="divider"></div>
								   <div class="sidebar">
								   '.$ws.'
								   '.$facebook.'
								   '.$twitter.'
								   '.$google.'
								   </div>';
			}

                        if($result[29] == 0) {
                                  $TMPL['showEmail'] = '<div class="sidebar-about"><strong>Email</strong>: '.$result[3].'</div>';
                        } else {
                                  $TMPL['showEmail'] = '';
                        }
			
			// Get posts number
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
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
		        $TMPL['points'] = ''.$totalPoints.'';
			
			$profile .= $skin->make();
			
			$skin = new skin('profile/top2'); $top = '';
			
			$TMPL['url'] = $confUrl;
			
			$top .= $skin->make();
			$public = '_public';
		}
	} else {
		$TMPL_old = $TMPL; $TMPL = array();
		$skin = new skin('profile/error'); $error = '';
		
		header("Location: {$url}/notfound");
		
		$error .= $skin->make();
	} 
	
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $rows;
	$TMPL['top'] = $top;
	$TMPL['profile'] = $profile;
	$TMPL['public'] = $public; 
	$TMPL['error'] = $error;
	
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
	} elseif($_GET['m'] == 'ff') {
		$TMPL['follow_popup'] = '<div class="popup-body-posted">You are now following '.$resultId[1].'. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>';
	} elseif($_GET['m'] == 'fu') {
		$TMPL['follow_popup'] = '<div class="popup-body-posted">You have unfollowed '.$resultId[1].'. <div style="font-size: 12px; margin-top:3px;">Tap to close.</div></div>';
	}
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome");
	}
	
	
	
   	$TMPL['userBanner'] = (!empty($result[21])) ? ''.$result[21].'' : ''.$confUrl.'/images/default_banner.png';
	$TMPL['username'] = $resultId[1];
	$TMPL['userid'] = $resultId[0];
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = $_GET['u'].' - '.$resultSettings[0];	
	$TMPL['username'] = $result[1];
 
                        $TMPL['image'] = (!empty($result[12])) ? ''.$confUrl.'/uploads/avatars/'.$result[12].'' : 'http://www.gravatar.com/avatar/'.md5($result[3]).'?s=120&d=mm';md5($result[3]);

			$TMPL['url'] = $confUrl;
			$TMPL['usernamelink'] = $result[1];
			 $TMPL['knownas'] = (!empty($result[4])) ? 'As known as <font color="#67a59b"><div style="text-transform:capitalize; display: inline;">'.$result[1].'</div></font>' : '';
                        $TMPL['username'] = (!empty($result[1])) ? ''.$result[1].'' : '';
                        $TMPL['name'] = (!empty($result[4])) ? '<h4>'.$result[4].'</h4>
                        <a href="'.$confUrl.'/verified">'.$result[20].'</a>' 
                        : '<h4><div style="text-transform:capitalize; display: block;">'.$result[1].'</div></h4> 
                        <a href="'.$confUrl.'/verified">'.$result[20].'</a>';

			$TMPL['badge'] = (!empty($result[6])) ? '<div class="profile-description">'.$result[6].'</div>' : '';
			$TMPL['location'] = (!empty($result[5])) ? ''.$result[5].'' : '&nbsp;';

			

			$TMPL['website'] = ($result[18] !== '') ? '<a href="'.$result[18].'" target="_blank" rel="nofollow"> '.$result[18].'</a>' : '';
			$TMPL['facebook'] = ($result[9] !== '') ? '&nbsp;&nbsp;&nbsp;<a href="'.$result[9].'" target="_blank" rel="nofollow"><div style="background: url('.$confUrl.'/images/icons/profile-sprites.png) no-repeat -9px -98px; display: inline-block; width: 16px; height: 16px;"></div></a>' : '';
			$TMPL['twitter'] = ($result[10] !== '') ? '&nbsp;&nbsp;&nbsp;<a href="'.$result[10].'" target="_blank" rel="nofollow"><div style="background: url('.$confUrl.'/images/icons/profile-sprites.png) no-repeat -9px -128px; display: inline-block; width: 16px; height: 16px;"></div></a>' : '';
			$TMPL['youtube'] = ($result[11] !== '') ? '&nbsp;&nbsp;&nbsp;<a href="'.$result[11].'" target="_blank" rel="nofollow"><div style="background: url('.$confUrl.'/images/icons/profile-sprites.png) no-repeat -9px -153px; display: inline-block; width: 16px; height: 16px;"></div></a>' : '';
			

                   
                        
			$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
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
                        
                        if(($result[25]) == 1) {
                        		$TMPL['disableBadges'] = '.profile_badges { display: none; }';
                        }
                        
                        
                         if($totalPoints >= 0) {                
                             	$TMPL['newGuyBadge'] = '<div class="profile_badges nord" title="The new guy: 0 Pixels and up"><img src="'.$confUrl.'/images/badges/new_badge.png" /></div>'; 
                        } 
                        if($totalPoints >= 10) {
                        	$TMPL['newGuyBadge'] = '';
                             	$TMPL['newbieBadge'] = '<div class="profile_badges nord" title="Newbie: 10 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_bronze_1.png" /></div>'; 
                        }                        

                        if($totalPoints >= 100) {
                        	$TMPL['newGuyBadge'] = '';
                        	$TMPL['newbieBadge'] = '';
                             	$TMPL['averageBadge'] = '<div class="profile_badges nord" title="Average Joe: 100 Pixels and up"><img src="'.$confUrl.'/images/badges/medal_silver_1.png" /></div>';   
                        }
                        if($totalPoints >= 500) {
                        	$TMPL['newGuyBadge'] = '';
                        	$TMPL['newbieBadge'] = '';
                             	$TMPL['averageBadge'] = ''; 
                            	$TMPL['nobleBadge'] = '<div class="profile_badges nord" title="Noble User: Over 500 Pixels"><img src="'.$confUrl.'/images/badges/medal_gold_1.png" /></div>'; 
                        }
                        if($totalPoints >= 1000) {
                        	$TMPL['newGuyBadge'] = '';
                        	$TMPL['newbieBadge'] = '';
                             	$TMPL['averageBadge'] = ''; 
                            	$TMPL['dedicatedBadge'] = '<div class="profile_badges nord" title="Dedicated User: Over 1000 Pixels"><img src="'.$confUrl.'/images/badges/dedicated_trophy.png" /></div>'; 
                            	$TMPL['points'] = 'Pixels: <font color="#EAC117">'.$totalPoints.'</font>';
                        }
                        
                        $TMPL['betaTesterBadge'] = ($result[23] !== '') ? '<div class="profile_badges nord" title="Beta Tester!"><img src="'.$confUrl.'/images/badges/beta_tester.gif" /></div>' : '';
			
			if($resultMessages <= 0 AND $resultFollowing >= 10) {
			 	$TMPL['stalkerBadge'] = '<div class="profile_badges nord" title="Stalker: No messages posted and is following 10 or more users"><img src="'.$confUrl.'/images/badges/stalker.png" /></div>';
			}
			
			$TMPL['staffBadge'] = ($result[24] !== '') ? '<div class="profile_badges nord" title="Fluxster Staff"><img src="'.$confUrl.'/images/badges/staff.png" /></div>' : '';
			
			if($resultMessages <= 0) {
				$TMPL['quietBadge'] = '<div class="profile_badges nord" title="Quiet: No messages posted"><img src="'.$confUrl.'/images/badges/quiet.png" /></div>';
			} 
			
			if($resultMessages >= 50) {
				$TMPL['list50Messages'] = '<div class="profile_badges nord" title="50 or more messages posted"><img src="'.$confUrl.'/images/badges/messages.png" /></div>';
			}
			
			if($resultFollowers >= 50) {
				$TMPL['list50Followers'] = '<div class="profile_badges nord" title="50 or more followers"><img src="'.$confUrl.'/images/badges/follower.png" /></div>';
			} 
			
			if($resultFollowing >= 50) {
				$TMPL['list50Following'] = '<div class="profile_badges nord" title="50 or more followings"><img src="'.$confUrl.'/images/badges/following.png" /></div>';
			} 
			
			if($resultMessages >= 50 AND $resultFollowing >= 50 AND $resultFollowers >= 50) {
				$TMPL['list50All'] = '<div class="profile_badges nord" title="50 or more messages, followers, and followings"><img src="'.$confUrl.'/images/badges/bronze_crown.png" /></div>';
			} 
			
			if($resultMessages >= 100 AND $resultFollowing >= 100 AND $resultFollowers >= 100) {
				$TMPL['list100All'] = '<div class="profile_badges nord" title="100 or more messages, followers, and followings"><img src="'.$confUrl.'/images/badges/silver_crown.png" /></div>';
			}
			
			if($resultMessages >= 500 AND $resultFollowing >= 500 AND $resultFollowers >= 500) {
				$TMPL['list500All'] = '<div class="profile_badges nord" title="500 or more messages, followers, and followings"><img src="'.$confUrl.'/images/badges/gold_crown.png" /></div>';
			}
			
			if($result[47] == 1) {
				$TMPL['plusMember'] = '<div class="profile_badges nord" title="Plus Member"><div style="color: #67a59b; font-size: 16px; font-style: italic; font-weight: bold; margin-top: -1px;">P</div></div>';
			}
			
	$skin = new skin('profile/content');
	
        // Login to Follow

        if (!isset($_COOKIE['username']) AND !isset($_COOKIE['password'])) {
            $TMPL['loginNote'] = '<center style="color: #6B6B6B; margin: 10px; float: right; padding: 0 5px;"><strong>Want to follow this user? <br><a href="http://fluxster.net"><font color="67a59b">Login</font></a> or 
			<a href="http://fluxster.net/register"><font color="67a59b">Register</font></a></strong></center>';
        }

        // Edit Profile Button

        if ($_COOKIE['username'] == $resultId[1]) {
                $TMPL['editProfile'] = '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=settings&b=design"><div class="follow-button">Edit Design</div></a></div>';
        }

	//  This user Follows you
	
	$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($resultId[0]), mysql_real_escape_string($data['id'])); 
	$resultRelation = mysql_query($queryRelation);
	
	if(mysql_num_rows($resultRelation) == 1) { 
		$TMPL['followsYou'] = '<div class="followsYou">(Follows You)</div>';
	} else {
		$TMPL['followsYou'] = ''; 
	}

	// Profile Background
				
	$TMPL['background'] = (!empty($result[15])) ? 'url('.$result[15].')' : 'url(../images/bg.png) repeat !important';
	
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
	
	
	// Follow Unfollow buttons
	
        if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		

		$queryRelation = sprintf("SELECT * FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0])); 
		$resultRelation = mysql_query($queryRelation);
			
			if(mysql_num_rows($resultRelation) == 1) { 
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="unfollow-button">
				Following</div></a></div>' : '';
				if($_GET['r'] == 2) {
					$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">
					<img src="'.$confUrl.'/images/icons/follow.png" /> Follow</div></a></div>' : '';
					$delete = sprintf("DELETE FROM relations WHERE follower = '%s' AND leader = '%s'", mysql_real_escape_string($data['id']), mysql_real_escape_string($resultId[0]));
					mysql_query($delete);
					header("Location: ".$confUrl."/index.php?a=profile&u=".mysql_real_escape_string($_GET['u'])."&m=fu"); 
				}
			} elseif(mysql_num_rows($resultRelation) == 0) { 
				$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=1"><div class="follow-button">Follow</div></a></div>' : '';
				if($_GET['r'] == 1) { 
					if($resultId[0] !== $data['id']) { 
					$TMPL['follow'] = ($resultId[0] !== $data['id']) ? '<div class="follow-container"><a href="'.$confUrl.'/index.php?a=profile&u='.$_GET['u'].'&r=2"><div class="follow-button">Following</div></a></div>' : '';
					$insert = sprintf("INSERT INTO relations (`id`, `leader`, `follower`) VALUES ('', '%s', '%s')", mysql_real_escape_string($resultId[0]), mysql_real_escape_string($data['id']));
					mysql_query($insert);
					header("Location: ".$confUrl."/index.php?a=profile&u=".mysql_real_escape_string($_GET['u'])."&m=ff");
					}
				}
			}
        }

	$age = date_diff(date_create($result[8]), date_create('today'))->y;
        if($age == '1') {
                $TMPL['yearBadge'] = '<img src="../images/1y-badge.png" style="margin-top:3px;"/>';
        }elseif($age == '2') {
        	$TMPL['yearBadge'] = '<img src="../images/2y-badge.png" style="margin-top:3px;"/>';
        }elseif($age == '3') {
        	$TMPL['yearBadge'] = '<img src="../images/3y-badge.png" style="margin-top:3px;"/>';
        }elseif($age == '4') {
        	$TMPL['yearBadge'] = '<img src="../images/4y-badge.png" style="margin-top:3px;"/>';
        }elseif($age == '5') {
        	$TMPL['yearBadge'] = '<img src="../images/5y-badge.png" style="margin-top:3px;"/>';
        }

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