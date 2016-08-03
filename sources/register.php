<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	global $confMail;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$time = time()+86400;
	$exp_time = time()-86400;
	
	$TMPL_old = $TMPL; $TMPL = array();
	$skin = new skin('register/form'); $form = '';
	$TMPL['url'] = $confUrl;
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		header("Location: ".$confUrl."/stream");
	}
	require_once('./includes/recaptchalib.php');
	
	if($resultSettings[5] == 1) {
		$TMPL['captcha'] = recaptcha_get_html($resultSettings[6]);
	}
	if(isset($_POST['register'])) {
		if(!empty($_POST['regName']) && !empty($_POST['regPass']) && !empty($_POST['regEmail'])) {
			if(strlen($_POST['regName']) >= 3 && strlen($_POST['regName']) <= 16) {
				if(ctype_alnum($_POST['regName'])) {

					
					if (filter_var($_POST['regEmail'], FILTER_VALIDATE_EMAIL)) {
						$querySearch = sprintf("SELECT username from users where username = '%s'",
						mysql_real_escape_string($_POST['regName']));
						$resultSearch = mysql_fetch_row(mysql_query($querySearch));
						if(strtolower($_POST['regName']) == $resultSearch[0] || strtolower($_POST['regName']) == 'anonymous') {
							header("Location: /index.php?a=register&m=ue");
						} else {
							if($resultSettings[5] == 1) {
								if ($_POST["recaptcha_response_field"]) {
								$resp = recaptcha_check_answer ($resultSettings[7],
															$_SERVER["REMOTE_ADDR"],
															$_POST["recaptcha_challenge_field"],
															$_POST["recaptcha_response_field"]);
															
									if ($resp->is_valid) {
											$createQuery = sprintf("INSERT into `users` (`username`, `password`, `email`) VALUES ('%s', '%s', '%s');",
															mysql_real_escape_string(strtolower($_POST['regName'])),
															md5(mysql_real_escape_string($_POST['regPass'])),
															mysql_real_escape_string($_POST['regEmail']));
															mysql_query($createQuery);
											
											$username = $_POST['regName'];
											$password = md5($_POST['regPass']);
											
											setcookie("username", str_replace(' ', '', strtolower($username)), $time);
											setcookie("password", $password, $time);
											if($resultSettings[13] == '1') {
												@sendMail($_POST['regEmail'], $resultSettings[0], $confUrl, $confMail, $_POST['regName'], $_POST['regPass']);
											}
											
											header("Location: ".$confUrl."/stream");
									}
								}
							} else {
										$createQuery = sprintf("INSERT into `users` (`username`, `password`, `email`, `date`) VALUES ('%s', '%s', '%s', '%s');",
														mysql_real_escape_string(strtolower($_POST['regName'])),
														md5(mysql_real_escape_string($_POST['regPass'])),
														mysql_real_escape_string($_POST['regEmail']),
														date("Y-m-d H:i:s"));
														mysql_query($createQuery);
										
										$query = sprintf("SELECT * FROM users WHERE username = '%s'",
														mysql_real_escape_string($_POST['regName']));
										$result = mysql_fetch_row(mysql_query($query));
										
										$insert = sprintf("INSERT INTO relations (`id`, `leader`, `follower`) VALUES ('', '4', '$result[0]')"); 
										$insert = sprintf("INSERT INTO relations (`id`, `leader`, `follower`) VALUES ('', '$result[0]', '1')"); 
																					
										mysql_query($insert);
										$username = $_POST['regName'];
										$password = md5($_POST['regPass']);
							
										
										setcookie("username", str_replace(' ', '', strtolower($username)), $time);
										setcookie("password", $password, $time);
										
										if($resultSettings[13] == '1') {
											@sendMail($_POST['regEmail'], $resultSettings[0], $confUrl, $confMail, $_POST['regName'], $_POST['regPass']);
										}
										
										header("Location: ".$confUrl."/welcomefriend");
							}
						}
					} else {
						header("Location: /index.php?a=register&m=e");
					}
				} else {
					header("Location: /index.php?a=register&m=lu");
				}
			} else {
				header("Location: /index.php?a=register&m=cu");
			}
		} else {
			header("Location: /index.php?a=register&m=af");
		}
	}
	
	if($_GET['m'] == 'out') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Logout Successful</h5>
										<p>You have successfully logged out of your account. Come back soon!</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
	if($_GET['m'] == 'den') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Access Denied</h5>
										<p>You need to be logged in to access that.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				}
				
	
	
	$form .= $skin->make();
	
	$skin = new skin('register/latest'); $latest = '';
		
	$queryLatest = "SELECT * FROM users WHERE image <> '' ORDER BY rand() DESC LIMIT 10";
	$resultLatest = mysql_query($queryLatest);
	
	while($TMPL = mysql_fetch_assoc($resultLatest)) {
		
		$TMPL['url'] = $confUrl;
		$TMPL['image'] = (!empty($TMPL['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$TMPL['image'].'" width="64" height="64" />' : '<img src="http://www.gravatar.com/avatar/'.md5($TMPL['email']).'?s=64&d=mm" />';md5($result[3]);
		
		$latest .= $skin->make();
	}
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['form'] = $form;
	$TMPL['latest'] = $latest;
	
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = 'Create an Account - '.$resultSettings[0];
	
	$TMPL['ad1'] = $resultSettings[2];
	$TMPL['ad2'] = $resultSettings[3];
	
	$skin = new skin('register/content');
	
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		header("Location: ".$confUrl."/index.php?a=me");
	}
	require_once('./includes/recaptchalib.php');
	
	if($resultSettings[5] == 1) {
		$TMPL['captcha'] = recaptcha_get_html($resultSettings[6]);
	}
	if(isset($_POST['register'])) {
		if(!empty($_POST['regName']) && !empty($_POST['regPass']) && !empty($_POST['regEmail'])) {
			if(strlen($_POST['regName']) >= 3 && strlen($_POST['regName']) <= 16) {
				if(ctype_alnum($_POST['regName'])) {

					
					if (filter_var($_POST['regEmail'], FILTER_VALIDATE_EMAIL)) {
						$querySearch = sprintf("SELECT username from users where username = '%s'",
						mysql_real_escape_string($_POST['regName']));
						$resultSearch = mysql_fetch_row(mysql_query($querySearch));
						if(strtolower($_POST['regName']) == $resultSearch[0] || strtolower($_POST['regName']) == 'anonymous') {
							header("Location: /index.php?a=register&m=ue");
						} else {
							if($resultSettings[5] == 1) {
								if ($_POST["recaptcha_response_field"]) {
								$resp = recaptcha_check_answer ($resultSettings[7],
															$_SERVER["REMOTE_ADDR"],
															$_POST["recaptcha_challenge_field"],
															$_POST["recaptcha_response_field"]);
															
									if ($resp->is_valid) {
											$createQuery = sprintf("INSERT into `users` (`username`, `password`, `email`) VALUES ('%s', '%s', '%s');",
															mysql_real_escape_string(strtolower($_POST['regName'])),
															md5(mysql_real_escape_string($_POST['regPass'])),
															mysql_real_escape_string($_POST['regEmail']));
															mysql_query($createQuery);
											
											$username = $_POST['regName'];
											$password = md5($_POST['regPass']);
											
											setcookie("username", str_replace(' ', '', strtolower($username)), $time);
											setcookie("password", $password, $time);
											if($resultSettings[13] == '1') {
												@sendMail($_POST['regEmail'], $resultSettings[0], $confUrl, $confMail, $_POST['regName'], $_POST['regPass']);
											}
											
											header("Location: ".$confUrl."/welcomefriend");
									}
								}
							} else {
										$createQuery = sprintf("INSERT into `users` (`username`, `password`, `email`, `date`) VALUES ('%s', '%s', '%s', '%s');",
														mysql_real_escape_string(strtolower($_POST['regName'])),
														md5(mysql_real_escape_string($_POST['regPass'])),
														mysql_real_escape_string($_POST['regEmail']),
														date("F d, Y H:i:s"));
														mysql_query($createQuery);
										
										$username = $_POST['regName'];
										$password = md5($_POST['regPass']);
										
										setcookie("username", str_replace(' ', '', strtolower($username)), $time);
										setcookie("password", $password, $time);
										
										if($resultSettings[13] == '1') {
											@sendMail($_POST['regEmail'], $resultSettings[0], $confUrl, $confMail, $_POST['regName'], $_POST['regPass']);
										}
										
										header("Location: ".$confUrl."/welcomefriend");
							}
						}
					} else {
						header("Location: /index.php?a=register&m=e");
					}
				} else {
					header("Location: /index.php?a=register&m=lu");
				}
			} else {
				header("Location: /index.php?a=register&m=cu");
			}
		} else {
			header("Location: /index.php?a=register&m=af");
		}
	}
	
	if($_GET['m'] == 'out') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Logout Successful</h5>
										<p>You have successfully logged out of your account. Come back soon!</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
	if($_GET['m'] == 'den') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-error">
										<h5>Access Denied</h5>
										<p>You need to be logged in to access that.</p>
										<a href="#" class="notification-close notification-close-error">x</a>
										</div>';
				}
	
	
	$queryMessages = sprintf("SELECT * FROM messages WHERE uid = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$queryFollowers = sprintf("SELECT follower FROM relations WHERE leader = '%s'",
									mysql_real_escape_string($resultId[0]));
			
			$queryFollowing = sprintf("SELECT follower FROM relations WHERE follower = '%s'",
									mysql_real_escape_string($resultId[0]));
									
			$resultMessages = mysql_num_rows(mysql_query($queryMessages));
			$resultFollowers = mysql_num_rows(mysql_query($queryFollowers));
			$resultFollowing = mysql_num_rows(mysql_query($queryFollowing));
			
			$TMPL['messages'] = $resultMessages;
			$TMPL['followers'] = $resultFollowers;
			$TMPL['following'] = $resultFollowing;
			
	

	$result = mysql_query("SELECT * FROM users");
	$num_users = mysql_num_rows($result);

		
	$TMPL['userCount'] = $num_users;
	
	
	$result = mysql_query("SELECT * FROM messages");
	$num_messages = mysql_num_rows($result);

		
	$TMPL['messageCount'] = $num_messages;
	
	
	return $skin->make();
}
?>