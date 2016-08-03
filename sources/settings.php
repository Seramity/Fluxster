<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	$time = time()+86400;
	$exp_time = time()-86400;

	if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) { 
		$query = sprintf('SELECT * from users where username = "%s" and password ="%s"', mysql_real_escape_string($_COOKIE['username']), mysql_real_escape_string($_COOKIE['password']));
		if(mysql_fetch_row(mysql_query($query))) {
			
			$TMPL_old = $TMPL; $TMPL = array();
			$TMPL['url'] = $confUrl;
			$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
			if($_GET['b'] == 'security') {
				$skin = new skin('settings/security'); $settings = '';
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';		
				$TMPL['username'] = $data['user'];
			
			if(isset($_POST['password'])) {
				if(isset($_POST['pwd']) && isset($_POST['current']) && isset($_POST['pwd2']) && !empty($_POST['pwd']) && !empty($_POST['current']) && !empty($_POST['pwd2'])) {
					$pwd = md5($_POST['pwd']);
					$pwd2 = md5($_POST['pwd2']);
					$current = md5($_POST['current']);
					
					if($current !== $request[2]) {
						header("Location: ".$confUrl."/index.php?a=settings&b=security&m=wp");
					} else if($_POST['pwd2'] !== $_POST['pwd']) {
						header("Location: ".$confUrl."/index.php?a=settings&b=security&m=dm");
					} else if($pwd == $request[2]) {
						header("Location: ".$confUrl."/index.php?a=settings&b=security&m=spm");
					} else {
						$query = 'UPDATE `users` SET password = \''.$pwd.'\' WHERE username = \''.$_COOKIE['username'].'\'';
						mysql_query($query);
						setcookie('password', md5($_POST['pwd']), $time);
						header("Location: ".$confUrl."/index.php?a=settings&b=security&m=spwd");
					}
					
				} else {
					header("Location: ".$confUrl."/index.php?a=settings&b=security&m=ea");
				}
			}
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="notification-bar notification-success">Settings Saved!</div>';
				}
				
				if($_GET['m'] == 'ea') {  
					$TMPL['message'] = '<div class="notification-bar notification-error">Please enter all fields.</div>';
				}
				
				if($_GET['m'] == 'wp') {
					$TMPL['message'] = '<div class="notification-bar notification-error">You current password was not entered correctly.</div>'; 
				}
				
				if($_GET['m'] == 'dm') {
					$TMPL['message'] = '<div class="notification-bar notification-error">The new passwords don\'t match.</div>';
				}
				
				if($_GET['m'] == 'spm') {
					$TMPL['message'] = '<div class="notification-bar notification-error">The new password cannot be the same as your current password.</div>';
				}
				
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li-active"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>		
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
			} elseif($_GET['b'] == 'avatar') {
				$skin = new skin('settings/avatar'); $settings = '';
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
				$TMPL['username'] = $data['user'];
				$maxsize = $resultSettings[11];
				
				if(isset($_FILES['fileselect']['name'])) {
					foreach ($_FILES['fileselect']['error'] as $key => $error) {
					$ext = pathinfo($_FILES['fileselect']['name'][$key], PATHINFO_EXTENSION);
					$size = $_FILES['fileselect']['size'][$key];
					$extArray = explode(',', $resultSettings[12]);
					
						if (in_array($ext, $extArray) && $size < $maxsize && $size > 0) {
							$rand = mt_rand();
							$tmp_name = $_FILES['fileselect']['tmp_name'][$key];
							$name = pathinfo($_FILES['fileselect']['name'][$key], PATHINFO_FILENAME);
							$fullname = $_FILES['fileselect']['name'][$key];
							$size = $_FILES['fileselect']['size'][$key];
							$type = pathinfo($_FILES['fileselect']['name'][$key], PATHINFO_EXTENSION);
							$convertedName = str_replace(' ', '_', $name);
							move_uploaded_file($tmp_name, 'uploads/avatars/'.$rand.'.'.mysql_real_escape_string($convertedName).'.'.mysql_real_escape_string($type));
										
							
							$query = sprintf("UPDATE users SET image = '%s' WHERE idu = '%s'",
								$rand.'.'.mysql_real_escape_string($convertedName).'.'.mysql_real_escape_string($type),
								mysql_real_escape_string($data['id']));
								mysql_query($query);
							
							$queryLastRow = "SELECT * FROM `files` ORDER by `id` DESC LIMIT 1";
							$execLastRow = mysql_fetch_row(mysql_query($queryLastRow));
							
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=sp");
						} elseif($_FILES['fileselect']['name'][$key] == '') { 
							
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=nf");
						} elseif($size > $maxsize || $size == 0) { 
							
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=fs");
						} else { 
							
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=wf");
						}
					}
				}
				if(isset($_POST['deleteimg'])) {
					$query = sprintf("UPDATE users SET image = '' WHERE idu = '%s'",
							mysql_real_escape_string($data['id']));
							mysql_query($query);
							unlink('uploads/avatars/'.$data['image']);
							header("Location: ".$confUrl."/index.php?a=settings&b=avatar&m=de");
				}
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Image Saved!</h5> 
										<p>Your profile picture have been changed.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif($_GET['m'] == 'nf') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>You did not selected any files to be uploaded, or the selected file(s) are empty.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'fs') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p><strong>The selected file</strong> size must not exceed <strong>'.round($maxsize / 1048576, 2).'</strong> MB.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'wf') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The selected file format is not supported. Upload  <strong>'.$resultSettings[12].'</strong> file format.</p>
										<a href="#" class="notification-close notification-close-error">x</a> 
										</div>';
				} elseif($_GET['m'] == 'de') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Image Removed!</h5>
										<p>Your profile picture has been removed.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li-active"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
	
		} elseif($_GET['b'] == 'plus') {
				$skin = new skin('settings/plus'); $settings = '';
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
				$TMPL['username'] = $data['user'];
				$maxsize = $resultSettings[11];
	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
	$result = mysql_fetch_row(mysql_query($query));
				
	// Check if requested
	if($result[47] == 1) {
		// USER IS A PLUS MEMBER
		$TMPL['plusPage'] = '
		<div class="meTitle"> 
			<center><img src="'.$confUrl.'/images/fluxster-plus_banner.png" width="200px"></center>
		</div>
		<div class="sidebar">
			<center>
			<h4>You are a Plus member!</h4>
			
			<p>
			Manage your subscription, badges, and more. 
			</p>
			
			<div class="follow-container"><a href="'.$confUrl.'/plus"><div class="follow-button">Manage</div></a></div>
			</center>
		</div> 
		<div class="divider"></div>
		<div class="sidebar">
			Want to leave the Fluxster Plus Program?
			<br><br>
			
			<form action="'.$confUrl.'/index.php?a=settings&b=plus" method="post" autocomplete="off">
			
			<input type="text" name="username" placeholder="Account Username"/>
			<br />
			<input type="password" name="password" placeholder="Account password"/>
			<br />		
			<input type="submit" value="Leave" name="leave"/> 
			
			</form>
		</div>
		';	
		
	} else if($result[48] == 1){
		// USER HAS SENT A REQUEST
		$TMPL['plusPage'] = '
		<div class="meTitle"> 
<center><img src="'.$confUrl.'/images/fluxster-plus_banner.png"></center>
</div>

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

	<center>
		<h2>Your request is pending.</h2> <p>Please be patient, we are still looking over your account for approval.</p>
	</center>
		';
	} else {
		// USER HAS NOT SENT A REQUEST
		$TMPL['plusPage'] = ' 
		<div class="meTitle">
<center><img src="'.$confUrl.'/images/fluxster-plus_banner.png"></center>
</div>

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

	<center>
		<div class="follow-container"><a href="'.$confUrl.'/plus"><div class="follow-button">Learn More</div></a></div>  
	</center>
		';
	}			
		
		if(isset($_POST['leave'])) {
				$password = md5($result[2]);
				
				if($_POST['username'] == $_COOKIE['username'] && md5($_POST['password']) == $_COOKIE['password']) {
						
					$updateQuery = sprintf("UPDATE users SET plusMember = 0 WHERE username = '%s'", mysql_real_escape_string($_POST['username']));
					mysql_query($updateQuery);	
					header("Location: /index.php?a=settings&b=plus&m=lfp");		
					
				} elseif (empty($_POST['username']) OR empty($_POST['password'])) {
					header("Location: /index.php?a=settings&b=plus&m=eb"); 
				} else { 
					header("Location: /index.php?a=settings&b=plus&m=iv"); 
				}
		}
	
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=plus"><li class="settings_li-active"><img src="https://cdn1.iconfinder.com/data/icons/tiny-icons/add.png"/> Fluxster Plus</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
	
		} elseif($_GET['b'] == 'username') {
				$skin = new skin('settings/username'); $settings = '';
				
				$time = time()+604800;
				$exp_time = time()-604800;
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
		
				if (isset($_POST['username'])) {
				
					$username = strtolower($_POST['newName']);
					
					if(isset($_POST['newName']) && isset($_POST['password'])) {
						if($_POST['newName'] !== $request[1]) {
							if(md5($_POST['password']) == $_COOKIE['password']) {
								if(strlen($_POST['newName']) >= 3 && strlen($_POST['newName']) <= 16) {
									if(ctype_alnum($_POST['newName'])) {
										$querySearch = sprintf("SELECT username FROM users WHERE username = '%s'",
										mysql_real_escape_string($_POST['newName']));
										$resultSearch = mysql_fetch_row(mysql_query($querySearch));
										if(strtolower($_POST['newName']) == $resultSearch[0]) {
											header("Location: /index.php?a=settings&b=username&m=ue");
										} else {
											$query = sprintf("UPDATE users SET username = '%s', changedUsername = 1 WHERE username = '%s'",									 			
							
											mysql_real_escape_string($username),
											mysql_real_escape_string($request[1]));
											mysql_query($query);
											
											setcookie('username', '', $exp_time);
											setcookie("username", str_replace(' ', '', $username), $time);
											header("Location: ".$confUrl."/index.php?a=settings&m=s");
										}
									} else {
										header("Location: /index.php?a=settings&b=username&m=lu");						
									}
								} else {
									header("Location: /index.php?a=settings&b=username&m=cu");
								}
							} else {
								header("Location: /index.php?a=settings&b=username&m=wp");
							}
						} else {
							header("Location: /index.php?a=settings&b=username&m=su");
						}

					} else {
						header("Location: /index.php?a=settings&b=username&m=eb");
					}
				}
				
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
	
	if($request[46] == 1) {
		header("Location: /stream");
	}

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li-active"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
				
		} elseif ($_GET['b'] == 'delete') {
				$skin = new skin('settings/delete'); $settings = '';
				
				$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
				$result = mysql_fetch_row(mysql_query($query));
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
				$TMPL['username'] = $data['user'];
				
				if(isset($_POST['disable'])) {
				$password = md5($result[2]);
				
					if($_POST['username'] == $_COOKIE['username'] && md5($_POST['password']) == $_COOKIE['password']) {
						
						$query = sprintf("UPDATE `users` SET disabled = 1 WHERE username = '%s'", 
										mysql_real_escape_string($_COOKIE['username']));
						mysql_query($query);
					if($_GET['m'] == 'sd') {
						$TMPL['message'] = '
							<div class="notification-box notification-box-success">
							<h5>Success!</h5> 
							<p>Your account is now disabled.</p>
							<a href="#" class="notification-close notification-close-error">x</a>
							</div>';
					}
						header("Location: /index.php?a=settings&b=delete&m=sd");
							
					} elseif (empty($_POST['username']) OR empty($_POST['password'])) {
						$TMPL['message'] = '
							<div class="notification-box notification-box-error">
							<h5>Error!</h5> 
							<p>Please enter both fields.</p>
							<a href="#" class="notification-close notification-close-error">x</a>
							</div>';
						header("Location: /index.php?a=settings&b=delete&m=eb"); 
					} else { 
						$TMPL['message'] = '
								<div class="notification-box notification-box-error">
								<h5>Error!</h5> 
								<p>Invalid username or password.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';  
						header("Location: /index.php?a=settings&b=delete&m=iv"); 
					}
				}
				
				if(isset($_POST['delete'])) {
				$password = md5($result[2]);
				
					if($_POST['username'] == $_COOKIE['username'] && md5($_POST['password']) == $_COOKIE['password']) {
						
					$delQuery = sprintf("DELETE from users where idu = '%s'", mysql_real_escape_string($result[0]));
					mysql_query($delQuery);
					$delMsg = sprintf("DELETE from messages where uid =  '%s'" , mysql_real_escape_string($result[0]));
					mysql_query($delMsg);
					$delRelations = sprintf("DELETE from relations WHERE follower = '%s'", mysql_real_escape_string($result[0]));
					mysql_query($delRelations);
						
						header("Location: /index.php?a=welcome&m=del");
								
					} elseif (empty($_POST['username']) OR empty($_POST['password'])) {
				 		if($_GET['m'] == 'eb') {
							$TMPL['message'] = '
								<div class="notification-box notification-box-error">
								<h5>Error!</h5> 
								<p>Please enter both fields.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
						}
						header("Location: /index.php?a=settings&b=delete&m=eb"); 
					} else { 
					if($_GET['m'] == 'iv') {
						$TMPL['message'] = '
								<div class="notification-box notification-box-error">
								<h5>Error!</h5> 
								<p>Invalid username or password.</p>
								<a href="#" class="notification-close notification-close-error">x</a>
								</div>';
					} 
					header("Location: /index.php?a=settings&b=delete&m=iv"); 
					}
				}
				
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li-active"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
		}
		 elseif($_GET['b'] == 'design') { 
				$skin = new skin('settings/design'); $settings = '';
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
				$TMPL['username'] = $data['user'];
				
				$TMPL['currentBackground'] = $request[15]; $TMPL['currentBanner'] = $request[21]; 
				
				if($request[35] == 1) {
					$TMPL['fixedBGChecked'] = 'checked="checked"';
				} else {
					$TMPL['fixedBGChecked'] = '';
				}
				
				if($request[36] == 1) {
					$TMPL['repeatBGChecked'] = 'checked="checked"';
				} else {
					$TMPL['repeatBGChecked'] = '';
				}
								
				if($request[25] == '1') {
					$TMPL['hide'] = 'selected="selected"';
				} else {
					$TMPL['display'] = 'selected="selected"';
				}
				
				if (isset($_POST['design'])) {				
					if(isset($_POST['banner']) || isset($_POST['badges']) || isset($_POST['repeatBG']) || isset($_POST['fixedBG'])) {
						if(filter_var($_POST['banner'], FILTER_VALIDATE_URL) || empty($_POST['banner'])) {
							if(filter_var($_POST['background'], FILTER_VALIDATE_URL) || empty($_POST['background'])) {
								$query = sprintf("UPDATE users SET banner = '%s', disableBadges = '%s', background = '%s', repeatBG = '%s', fixedBG = '%s' WHERE username = '%s'",								
												mysql_real_escape_string(strip_tags($_POST['banner'])),
 
												mysql_real_escape_string($_POST['badges']),
													
												mysql_real_escape_string(strip_tags($_POST['background'])),
												
												mysql_real_escape_string($_POST['repeatBG']),
												
												mysql_real_escape_string($_POST['fixedBG']),
												
												mysql_real_escape_string($_COOKIE['username']));
								mysql_query($query);
								header("Location: ".$confUrl."/index.php?a=settings&b=design&m=s"); 
							
							} else {
								header("Location: ".$confUrl."/index.php?a=settings&b=design&m=w"); 
							}
							
							} else {
								header("Location: ".$confUrl."/index.php?a=settings&b=design&m=w"); 
							}
						}
				} 
			
				if($_GET['m'] == 'bu') { 
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error"> 
										<h5>Error</h5>
										<p>Please enter a url for the background.</p> 
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} 
				
				if($_GET['m'] == 's') { 
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success"> 
										<h5>Settings Saved!</h5>
										<p>Settings successfully saved.</p> 
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} 
				
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li-active"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
			} elseif($_GET['b'] == 'privacy') { 
				$skin = new skin('settings/privacy'); $settings = '';
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
				$TMPL['username'] = $data['user'];
				
				if($request[29] == '1') {
					$TMPL['hideEmailChecked'] = 'checked="checked"';
				} else {
					$TMPL['hideEmailChecked'] = '';
				}
				
				if($request[30] == '1') {
					$TMPL['searchResultsChecked'] = 'checked="checked"';
				} else {
					$TMPL['searchResultsChecked'] = '';
				}
				
				if($request[31] == '1') {
					$TMPL['hideInfoChecked'] = 'checked="checked"';
				} else {
					$TMPL['hideInfoChecked'] = '';
				}
				
				if($request[32] == '1') {
					$TMPL['hidePostsChecked'] = 'checked="checked"';
				} else {
					$TMPL['hidePostsChecked'] = '';
				}	
							
				if (isset($_POST['privacy'])) {
					if(isset($_POST['hideInfo']) || isset($_POST['hideEmail']) || isset($_POST['hidePosts']) || isset($_POST['searchResults'])) {
						$query = sprintf("UPDATE `users` SET hideInfo = '%s', hideEmail = '%s', hidePosts = '%s', searchResults = '%s' WHERE username = '%s'",									 			
							
							mysql_real_escape_string($_POST['hideInfo']),
							mysql_real_escape_string($_POST['hideEmail']),
							mysql_real_escape_string($_POST['hidePosts']),
							mysql_real_escape_string($_POST['searchResults']),																	
							
							mysql_real_escape_string($_COOKIE['username']));
							
							mysql_query($query);
							header("Location: ".$confUrl."/index.php?a=settings&b=privacy&m=s");
					}
				}
				
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>Settings successfully saved.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li-active"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				$settings .= $skin->make();
			} else { 
				$skin = new skin('settings/general'); $settings = '';
				
				$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
				$request = mysql_fetch_row(mysql_query($query));
				
				
				
				$TMPL['currentName'] = $request[4]; $TMPL['currentEmail'] = $request[3]; $TMPL['currentLocation'] = $request[5]; $TMPL['currentWebsite'] = $request[6]; $TMPL['currentBio'] = $request[7]; $TMPL['currentFacebook'] = $request[9]; $TMPL['currentTwitter'] = $request[10]; $TMPL['currentYoutube'] = $request[11]; $TMPL['currentWs'] = $request[18]; 
				
				if($request[34] == '1') {
					$TMPL['male'] = 'selected="selected"';
				} elseif($request[34] == '2') {
					$TMPL['female'] = 'selected="selected"';
				} else {
					$TMPL['none'] = 'selected="selected"';
				}	
				
				
				$TMPL['image'] = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
				$TMPL['username'] = $data['user'];
				
				if (isset($_POST['general'])) {
					
					if(filter_var($_POST['website'], FILTER_VALIDATE_URL) || empty($_POST['website'])) {
						if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || empty($_POST['email'])) {
							if(!empty($_POST['email'])) {
								if(strlen($_POST['bio']) <= 260) {
								// Updating the Values
								if(isset($_POST['name']) || isset($_POST['email']) || isset($_POST['location']) || isset($_POST['gender']) || isset($_POST['website']) || isset($_POST['bio']) || isset($_POST['ws']) ) {
									//$query = 'UPDATE `users` SET name = \''.$_POST['name'].'\', email = \''.$_POST['email'].'\', location = \''.$_POST['location'].'\', facebook = \''.$_POST['facebook'].'\', twitter = \''.$_POST['twitter'].'\', gplus = \''.$_POST['gplus'].'\', website = \''.$_POST['website'].'\', bio = \''.strip_tags($_POST['bio']).'\' WHERE username = \''.$_COOKIE['username'].'\'';
									
									$bio = '<pre>'.$_POST['bio'].'</pre>';
									
									$query = sprintf("UPDATE `users` SET name = '%s', email = '%s', location = '%s', gender = '%s', facebook = '%s', twitter = '%s', youtube = '%s', ws = '%s', bio = '%s' WHERE username = '%s'",
									 				mysql_real_escape_string($_POST['name']),
													mysql_real_escape_string($_POST['email']),
													mysql_real_escape_string(strip_tags($_POST['location'])),
													mysql_real_escape_string($_POST['gender']),
													mysql_real_escape_string(strip_tags($_POST['facebook'])),
													mysql_real_escape_string(strip_tags($_POST['twitter'])),
													mysql_real_escape_string(strip_tags($_POST['youtube'])),
													mysql_real_escape_string(strip_tags($_POST['ws'])),
													mysql_real_escape_string(strip_tags($bio)),                                                                                                                                                 													
													
													mysql_real_escape_string($_COOKIE['username']));
									mysql_query($query);
									header("Location: ".$confUrl."/index.php?a=settings&m=s");
								}
							} else {
								header("Location: ".$confUrl."/index.php?a=settings&m=b");
							}
						} else {
							header("Location: ".$confUrl."/index.php?a=settings&m=ne"); 
						}
						} else { 
							header("Location: ".$confUrl."/index.php?a=settings&m=e");
						}
					} else {
						header("Location: ".$confUrl."/index.php?a=settings&m=w");
					}
				}
				$backgrounds = array('0', '1', '2', '3', '4','5','6','7','8','9');
				if(isset($_GET['bg'])) {
					if(in_array($_GET['bg'], $backgrounds)) {
						$queryBg = sprintf("UPDATE `users` SET background = '%s' WHERE username = '%s'", mysql_real_escape_string($_GET['bg']), mysql_real_escape_string($_COOKIE['username']));
						mysql_query($queryBg);
						header("Location: ".$confUrl."/index.php?a=settings&m=bs");
					} else {
						header("Location: ".$confUrl."/index.php?a=settings&m=be");
					}
				}
				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>Settings successfully saved.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif($_GET['m'] == 'b') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The Bio description should be 260 characters or less.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'ne') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The Email field cannot be empty.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';							
				} elseif($_GET['m'] == 'e') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>Please enter a valid email.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'w') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>Please enter a valid URL format.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				} elseif($_GET['m'] == 'bs') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>The background has been successfully changed.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif($_GET['m'] == 'be') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Error!</h5>
										<p>The background could not be changed.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				}
		
	// GET SIDEBAR		
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';

	// HIDE CHANGE USERNAME LINK
	if($request[46] == 0) {
		$changeUsername = '<a href="'.$confUrl.'/index.php?a=settings&b=username"><li class="settings_li"><img src="'.$confUrl.'/images/pencil.png"/> Change Username <div style="font-weight: 100; display: inline;">- One use only</div></li></a>';
	} else {
		$changeUsername = '';		
	}
	
	$TMPL['sidebar'] = '
	<div class="four columns">
		<div class="sidebar">
			<div class="settings-image">'.$image.'</div>
			<div class="settings-name"><div style="text-transform:capitalize; display: inline-block;">'.$name.'</div></div>
			<div class="settings-change">
			<br /><strong>Profile</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings"><li class="settings_li-active"><img src="'.$confUrl.'/images/Person.png"/> General Settings</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=design"><li class="settings_li"><img src="'.$confUrl.'/images/Design.png"/> Profile Design</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=avatar"><li class="settings_li"><img src="'.$confUrl.'/images/Photo.png"/> Change Profile Picture</li></a>
					
				</ul>
			</div>
		</div>
		<div class="divider"></div>
		<div class="sidebar">
			<div class="settings-change">
				<strong>Account</strong>
				<ul class="settings_nav">
					<a href="'.$confUrl.'/index.php?a=settings&b=privacy"><li class="settings_li"><img src="'.$confUrl.'/images/shield.png"/> Privacy Settings</li></a>
					'.$changeUsername.'
					<a href="'.$confUrl.'/index.php?a=settings&b=security"><li class="settings_li"><img src="'.$confUrl.'/images/Lock.png"/> Change Password</li></a>
					<a href="'.$confUrl.'/index.php?a=settings&b=delete"><li class="settings_li"><img src="'.$confUrl.'/images/delete.png"/> Delete or Disable Your Account</li></a>
				</ul>
			</div>
		</div>
	</div>
	';
				
				
				$settings .= $skin->make();
			}	
		
			$TMPL = $TMPL_old; unset($TMPL_old);
			$TMPL['settings'] = $settings;
			
			if(isset($_GET['logout']) == 1) {
				setcookie('username', '', $exp_time);
				setcookie('password', '', $exp_time);
				header("Location: ".$confUrl."/index.php?a=welcome");
				}
			}
		} else {
		header("Location: ".$confUrl."/index.php?a=welcome&m=den");
	}			
	
	$TMPL['title'] = 'Settings - '.$resultSettings[0];

	$skin = new skin('settings/content');
	
	$query = 'SELECT * FROM users WHERE username = \''.$_COOKIE['username'].'\'';
	$request = mysql_fetch_row(mysql_query($query));
	
	$name = $request[1];
	
	$image = (!empty($request['12'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$request['12'].'" width="40" height="40" />' : '<img src="http://www.gravatar.com/avatar/'.md5($request[3]).'?s=40&d=mm" />';
	
	
	

	return $skin->make();
}
?>