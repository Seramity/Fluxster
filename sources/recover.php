<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	global $confMail;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
	
	$TMPL_old = $TMPL; $TMPL = array();
	$skin = new skin('recover/username'); $rows = '';
	$TMPL['url'] = $confUrl;
	$rows .= $skin->make();
	
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
		header("Location: ".$confUrl."/stream");
	}
	
	if(isset($_POST['username']) && empty($_POST['username'])) {
		header("Location: ".$confUrl."/index.php?a=recover&m=e");
	} 
	elseif(isset($_POST['username']) && !empty($_POST['username'])) {
		$queryUser = sprintf("SELECT * FROM users WHERE username = '%s'", mysql_real_escape_string($_POST['username']));
		$resultUser = mysql_fetch_row(mysql_query($queryUser));
		
		if($_POST['username'] == $resultUser[1]) {
			$salt = md5(mt_rand());
			$makeSalt = sprintf("UPDATE users SET salted = '%s' WHERE idu = '%s'", $salt, $resultUser[0]);
			mysql_query($makeSalt);
			
			@sendRecover($resultUser[3], $resultSettings[0], $confUrl, $confMail, $resultUser[1], $salt);
			
			header("Location: ".$confUrl."/index.php?a=recover&m=s");
		} else {
			header("Location: ".$confUrl."/index.php?a=recover&m=e");
		}
	}
	$key = str_replace(' ', '1', $_POST['k']);

	if(isset($_GET['r'])) {
		if(empty($_POST['n']) || empty($key) || (empty($_POST['u']) && empty($key))) {
			$skin = new skin('recover/error'); $rows = '';
			$TMPL['url'] = $confUrl;
			$rows .= $skin->make();
		} elseif(isset($_POST['n']) && isset($key) && isset($_POST['password'])) {
			$verifySalt = sprintf("SELECT * from users WHERE username = '%s' and salted = '%s'", mysql_real_escape_string($_POST['n']), mysql_real_escape_string($key));
			$resultSalt = mysql_fetch_row(mysql_query($verifySalt));
			if($resultSalt[1] == $_POST['n']) {
				$changePassword = sprintf("UPDATE users SET password = '%s' WHERE salted = '%s' AND idu = '%s'", md5(mysql_real_escape_string($_POST['password'])), $resultSalt[14], $resultSalt[0]);
				mysql_query($changePassword);
				$changeSalt = sprintf("UPDATE users SET salted = '' WHERE idu = '%s'", $resultSalt[0]);
				mysql_query($changeSalt);
				header("Location: ".$confUrl."/index.php?a=recover&r=1&m=ps");
			} else {
				header("Location: ".$confUrl."/index.php?a=recover&r=1&m=wk");
			}
		}
	}
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $rows;
	$TMPL['error'] = $error;
	
	if($_GET['m'] == 's') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-info">
							<h5>Email send!</h5>
							<p>An email containing password reset intructions has been send. Please allow us up to 24 hours to deliver the message.</p>
							<a href="#" class="notification-close notification-close-info">x</a>
							</div>';
	} elseif($_GET['m'] == 'e') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-error">
							<h5>Error!</h5>
							<p>We couldn\'t find selected username.</p>
							<a href="#" class="notification-close notification-close-error">x</a>
							</div>';
	} elseif($_GET['m'] == 'wk') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-error">
							<h5>Error!</h5>
							<p>The username or the reset key are wrong, make sure you\'ve entered the correct credentials.</p>
							<a href="#" class="notification-close notification-close-error">x</a>
							</div>';
	} elseif($_GET['m'] == 'ps') {
		$TMPL['message'] = '<div class="divider"></div>
							<div class="notification-box notification-box-success">
							<h5>Password changed!</h5>
							<p>You have succcessfully reseted your passsword, you can now log-in using the new credentials.</p>
							<a href="#" class="notification-close notification-close-success">x</a>
							</div>';
	}
	
	if(isset($_GET['logout']) == 1) {
		setcookie('username', '', $exp_time);
		setcookie('password', '', $exp_time);
		header("Location: ".$confUrl."/index.php?a=welcome");
	}
	$TMPL['query'] = htmlentities($_GET['u'], ENT_QUOTES, "UTF-8");
	$TMPL['people'] = $errormsg;
	$TMPL['url'] = $confUrl;
	$TMPL['title'] = 'Recover - '.$resultSettings[0];

	$skin = new skin('recover/content');
	return $skin->make();
}
?>