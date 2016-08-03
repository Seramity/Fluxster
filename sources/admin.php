<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	global $confMail;
	$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

	$time = time()+604800;
	$exp_time = time()-604800;

	$TMPL['loginForm'] = '
	<div class="twelve columns">
	<h3>Admin Panel</h3>
	<div class="divider"></div>
	<div class="inner-page">
		<form action="'.$confUrl.'/index.php?a=admin" method="post">
		<div class="input-container">Username</div> <input type="text" name="username" /><br />
		<div class="input-container">Password</div> <input type="password" name="password" /><br />
	</div>
	<div class="divider"></div>
	<div class="inner-page">
		<div class="input-container"></div><input type="submit" value="Log In" name="login"/>
		</form>
	</div>
	</div>
	';

	$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));


	// Require user to be admin to access admin panel
	/*
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
				if ($result[28] == 0) {
					header("Location: /");
				}
			} else {
				header("Location: /");
			}
		*/

	if(isset($_POST['login'])) { // Set cookies for Log-in.
		header("Location: ".$confUrl."/index.php?a=admin&b=users");
		$username = $_POST['username'];
		$password = md5($_POST['password']);

		setcookie("adminUser", $username, $time);
		setcookie("adminPass", $password, $time);

		$query = sprintf('SELECT * from admin where username = "%s" and password ="%s"',
		mysql_real_escape_string($_COOKIE['adminUser']),
		mysql_real_escape_string($_COOKIE['adminPass'])
		);
	} elseif(isset($_COOKIE['adminUser']) && isset($_COOKIE['adminPass'])) { // If cookie admin & pass is set, check for credentials
		$query = sprintf('SELECT * from admin where username = "%s" and password ="%s"', mysql_real_escape_string($_COOKIE['adminUser']), mysql_real_escape_string($_COOKIE['adminPass']));
		if(mysql_fetch_row(mysql_query($query))) { // If true - Logged-in

			$TMPL['loginForm'] = '';

			$TMPL_old = $TMPL; $TMPL = array();
			$TMPL['url'] = $confUrl;
			$menu = 'Welcome <a href="'.$confUrl.'/index.php?a=admin"><strong>'.$_COOKIE['adminUser'].'</strong></a> - <a href="'.$confUrl.'/index.php?a=admin&b=users">Manage Users</a> - <a href="'.$confUrl.'/index.php?a=admin&b=reported">Reported Messages</a> - <a href="'.$confUrl.'/index.php?a=admin&b=plus">Plus Requests</a> - <a href="'.$confUrl.'/index.php?a=admin&b=security">Security</a>';

			if($_GET['b'] == 'security') { // Security Admin Tab
				$skin = new skin('admin/security'); $settings = '';

				$TMPL['adminMenu'] = $menu;

				if(isset($_POST['pwd']) && !empty($_POST['pwd'])) { // If is set post && password is not empty then save the password
					$pwd = md5($_POST['pwd']);
					$query = 'UPDATE `admin` SET password = \''.$pwd.'\' WHERE username = \''.$_COOKIE['adminUser'].'\'';
					mysql_query($query);

					setcookie("adminPass", md5($_POST['pwd']), $time);

					header("Location: ".$confUrl."/index.php?a=admin&b=security&m=s");
				}

				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>Password successfully changed, you can use your new password now.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}

				$TMPL['url1'] = $confUrl;
				$settings .= $skin->make();
			} else if($_GET['b'] == 'reported') {
				$skin = new skin('admin/reports'); $settings = '';

				$TMPL['adminMenu'] = $menu;

				$query = 'SELECT * from reports order by id desc limit 20';
				$result = mysql_query($query);
				for($i = 0; ($row = mysql_fetch_row($result)) !== false; $i++) {
					$TMPL['users'] .= "<div class=\"admin-rows\"><div class=\"one columns\">{$row[0]}</div><div class=\"four columns\"><a href=\"".$confUrl."/index.php?a=profile&u={$row[2]}\" target=\"_blank\">{$row[2]}</a></div><div class=\"four columns\"><a href=\"".$confUrl."/index.php?a=message&m={$row[1]}\" target=\"_blank\">View Message</a></div><div class=\"two columns\"><a href=\"".$confUrl."/index.php?a=admin&b=reported&delete={$row[0]}\"><img src=\"".$confUrl."/images/icons/ignore.png\" /></a></div><div class=\"one columns\"><a href=\"".$confUrl."/index.php?a=admin&b=reported&delete={$row[0]}&idm={$row[1]}\"><img src=\"".$confUrl."/images/icons/delete.png\" /></a></div></div>";
				}

				if($_GET['m'] == 'md') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Message deleted!</h5>
										<p>Message and report has been deleted.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				} elseif ($_GET['m'] == 'rd') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Report deleted!</h5>
										<p>Report has been deleted.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				$result = mysql_query("select * from reports order by id desc limit 20");
				while($row=mysql_fetch_array($result))
				{
					$TMPL['idn']=$row['id'];
				}

				$settings .= $skin->make();
				if(isset($_GET['delete']) && isset($_GET['idm'])) {
					$delQuery = sprintf("DELETE from reports where id = '%s'", mysql_real_escape_string($_GET['delete']));
					mysql_query($delQuery);
					$delMsg = sprintf("DELETE from messages where id = '%s'" , mysql_real_escape_string($_GET['idm']));
					mysql_query($delMsg);
					header("Location: ".$confUrl."/index.php?a=admin&b=reported&m=md");
				} elseif(isset($_GET['delete'])) {
					$delQuery = sprintf("DELETE from reports where id = '%s'", mysql_real_escape_string($_GET['delete']));
					mysql_query($delQuery);
					header("Location: ".$confUrl."/index.php?a=admin&b=reported&m=rd");
				}
			} else if($_GET['b'] == 'users') {
				$skin = new skin('admin/users'); $settings = '';

				$TMPL['adminMenu'] = $menu;

				$query = 'SELECT * from users order by idu desc limit 20';
				$result = mysql_query($query);
				for($i = 0; ($row = mysql_fetch_row($result)) !== false; $i++) {
					$TMPL['users'] .= "<div class=\"admin-rows\"><div class=\"one columns\">{$row[0]}</div><div class=\"three columns\"><a href=\"".$confUrl."/index.php?a=profile&u={$row[1]}\" target=\"_blank\">{$row[1]}</a></div><div class=\"seven columns\">{$row[3]}</div><div class=\"one columns\"><a href=\"".$confUrl."/index.php?a=admin&b=users&delete={$row[0]}\"><img src=\"".$confUrl."/images/icons/delete.png\" /></a></div></div>";
				}

				if($_GET['m'] == 'd') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>User deleted!</h5>
										<p>User with the ID: <strong>'.$_GET['u'].'</strong> has been deleted.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				$result = mysql_query("select * from users order by idu desc limit 20");
				while($row=mysql_fetch_array($result))
				{
					$TMPL['idn']=$row['idu'];
				}

				$settings .= $skin->make();
				if(isset($_GET['delete'])) {
					$delQuery = sprintf("DELETE from users where idu = '%s'", mysql_real_escape_string($_GET['delete']));
					mysql_query($delQuery);
					$delMsg = sprintf("DELETE from messages where uid =  '%s'" , mysql_real_escape_string($_GET['delete']));
					mysql_query($delMsg);
					$delRelations = sprintf("DELETE from relations WHERE follower = '%s'", mysql_real_escape_string($_GET['delete']));
					mysql_query($delRelations);
					header("Location: ".$confUrl."/index.php?a=admin&b=users&m=d&u=".$_GET['delete']);
				}
			} else if($_GET['b'] == 'plus') {
				$skin = new skin('admin/plus'); $settings = '';

				$TMPL['adminMenu'] = $menu;

				$query = 'SELECT * from users where requestedPlus = 1 order by idu desc limit 20';
				$result = mysql_query($query);
				for($i = 0; ($row = mysql_fetch_row($result)) !== false; $i++) {
					$TMPL['users'] .= "<div class=\"admin-rows\"><div class=\"one columns\">{$row[0]}</div><div class=\"three columns\"><a href=\"".$confUrl."/index.php?a=profile&u={$row[1]}\" target=\"_blank\">{$row[1]}</a></div><div class=\"six columns\">{$row[3]}</div><div class=\"one columns\"><a href=\"".$confUrl."/index.php?a=admin&b=plus&accept={$row[0]}\"><img src=\"".$confUrl."/images/icons/check.png\" /></a></div><div class=\"one columns\"><a href=\"".$confUrl."/index.php?a=admin&b=plus&decline={$row[0]}\"><img src=\"".$confUrl."/images/icons/delete.png\" /></a></div></div>";
				}

				if($_GET['m'] == 'a') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>User accepted!</h5>
										<p>User with the ID: <strong>'.$_GET['u'].'</strong> has been accepted.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				if($_GET['m'] == 'd') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>User accepted!</h5>
										<p>User with the ID: <strong>'.$_GET['u'].'</strong> has been declined.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}
				$result = mysql_query("select * from users order by idu desc limit 20");
				while($row=mysql_fetch_array($result))
				{
					$TMPL['idn']=$row['idu'];
				}

				$settings .= $skin->make();
				if(isset($_GET['accept'])) {
					$updateQuery = sprintf("UPDATE users SET plusMember = 1, requestedPlus = 0 where idu = '%s'", mysql_real_escape_string($_GET['accept']));
					mysql_query($updateQuery);
					$getUserData = sprintf("SELECT * FROM users where idu = '%s'", mysql_real_escape_string($_GET['accept']));
					$result = mysql_fetch_row(mysql_query($getUserData));
					@sendAcceptedPlus($result[3], $resultSettings[0], $confUrl, $confMail, $result[1]);
					header("Location: ".$confUrl."/index.php?a=admin&b=plus&m=a&u=".$_GET['accept']);
				}
				if(isset($_GET['decline'])) {
					$updateQuery = sprintf("UPDATE users SET requestedPlus = 0 where idu = '%s'", mysql_real_escape_string($_GET['decline']));
					mysql_query($updateQuery);
					$getUserData = sprintf("SELECT * FROM users where idu = '%s'", mysql_real_escape_string($_GET['decline']));
					$result = mysql_fetch_row(mysql_query($getUserData));
					@sendDeclinedPlus($result[3], $resultSettings[0], $confUrl, $confMail, $result[1]);
					header("Location: ".$confUrl."/index.php?a=admin&b=plus&m=d&u=".$_GET['decline']);
				}
			} else {
				$skin = new skin('admin/general'); $settings = '';

				$TMPL['adminMenu'] = $menu;

				// Current Values
				$TMPL['currentTitle'] = $resultSettings[0]; $TMPL['ad1'] = $resultSettings[2]; $TMPL['ad2'] = $resultSettings[3]; $TMPL['ad3'] = $resultSettings[4]; $TMPL['currentPublic'] = $resultSettings[6]; $TMPL['currentPrivate'] = $resultSettings[7]; $TMPL['currentFormat'] = $resultSettings[12]; $TMPL['currentCensor'] = $resultSettings[4]; $TMPL['currentFormatMsg'] = $resultSettings[15];
				if($resultSettings[5] == '1') {
					$TMPL['on'] = 'selected="selected"';
				} else {
					$TMPL['off'] = 'selected="selected"';
				}

				if($resultSettings[9] == '0') {
					$TMPL['one'] = 'selected="selected"';
				} elseif($resultSettings[9] == '1') {
					$TMPL['two'] = 'selected="selected"';
				} elseif($resultSettings[9] == '2') {
					$TMPL['three'] = 'selected="selected"';
				} else {
					$TMPL['four'] = 'selected="selected"';
				}

				if($resultSettings[1] == '10') {
					$TMPL['ten'] = 'selected="selected"';
				} elseif($resultSettings[1] == '20') {
					$TMPL['twenty'] = 'selected="selected"';
				} elseif($resultSettings[1] == '25') {
					$TMPL['twentyfive'] = 'selected="selected"';
				} else {
					$TMPL['fifty'] = 'selected="selected"';
				}

				if($resultSettings[10] == '140') {
					$TMPL['unu'] = 'selected="selected"';
				} elseif($resultSettings[10] == '160') {
					$TMPL['doi'] = 'selected="selected"';
				} elseif($resultSettings[10] == '200') {
					$TMPL['trei'] = 'selected="selected"';
				} else {
					$TMPL['patru'] = 'selected="selected"';
				}

				if($resultSettings[11] == '1048576') {
					$TMPL['onemb'] = 'selected="selected"';
				} elseif($resultSettings[11] == '2097152') {
					$TMPL['twomb'] = 'selected="selected"';
				} elseif($resultSettings[11] == '3145728') {
					$TMPL['threemb'] = 'selected="selected"';
				} else {
					$TMPL['tenmb'] = 'selected="selected"';
				}

				if($resultSettings[13] == '1') {
					$TMPL['mailon'] = 'selected="selected"';
				} else {
					$TMPL['mailoff'] = 'selected="selected"';
				}

				if($resultSettings[8] == '10000') {
					$TMPL['intone'] = 'selected="selected"';
				} elseif($resultSettings[8] == '30000') {
					$TMPL['inttwo'] = 'selected="selected"';
				} elseif($resultSettings[8] == '60000') {
					$TMPL['intthree'] = 'selected="selected"';
				}  elseif($resultSettings[8] == '120000') {
					$TMPL['intfour'] = 'selected="selected"';
				} elseif($resultSettings[8] == '300000') {
					$TMPL['intfive'] = 'selected="selected"';
				} elseif($resultSettings[8] == '600000') {
					$TMPL['intsix'] = 'selected="selected"';
				} else {
					$TMPL['intseven'] = 'selected="selected"';
				}

				if($resultSettings[14] == '1048576') {
					$TMPL['onembMsg'] = 'selected="selected"';
				} elseif($resultSettings[14] == '2097152') {
					$TMPL['twombMsg'] = 'selected="selected"';
				} elseif($resultSettings[14] == '3145728') {
					$TMPL['threembMsg'] = 'selected="selected"';
				} else {
					$TMPL['tenmbMsg'] = 'selected="selected"';
				}

				// Updating the Values
				if(isset($_POST['title']) || isset($_POST['perpage']) || isset($_POST['dropdown']) || isset($_POST['private']) || isset($_POST['public']) || isset($_POST['ads1']) || isset($_POST['ads2']) || isset($_POST['dropdown']) || isset($_POST['twitter']) || isset($_POST['facebook']) || isset($_POST['message']) || isset($_POST['size']) || isset($_POST['format']) || isset($_POST['mail']) || isset($_POST['interval']) || isset($_POST['sizeMsg']) || isset($_POST['formatMsg'])) {
					$query = sprintf("UPDATE `settings` SET title = '%s', perpage = '%s', ad1 = '%s', ad2 = '%s', captcha = '%s', time = '%s', public = '%s', private = '%s', message = '%s', size = '%s', format = '%s', mail = '%s', inter = '%s', censor = '%s', sizemsg = '%s', formatmsg = '%s'",
					mysql_real_escape_string($_POST['title']),
					mysql_real_escape_string($_POST['perpage']),
					$_POST['ads1'],
					$_POST['ads2'],
					mysql_real_escape_string($_POST['dropdown']),
					mysql_real_escape_string($_POST['time']),
					mysql_real_escape_string($_POST['public']),
					mysql_real_escape_string($_POST['private']),
					mysql_real_escape_string($_POST['message']),
					mysql_real_escape_string($_POST['size']),
					mysql_real_escape_string($_POST['format']),
					mysql_real_escape_string($_POST['mail']),
					mysql_real_escape_string($_POST['interval']),
					mysql_real_escape_string($_POST['censor']),
					mysql_real_escape_string($_POST['sizeMsg']),
					mysql_real_escape_string($_POST['formatMsg']));
					mysql_query($query);
					header("Location: ".$confUrl."/index.php?a=admin&m=s");
				}

				if($_GET['m'] == 's') {
					$TMPL['message'] = '<div class="divider"></div>
										<div class="notification-box notification-box-success">
										<h5>Settings Saved!</h5>
										<p>General settings successfully saved.</p>
										<a href="#" class="notification-close notification-close-success">x</a>
										</div>';
				}

				$settings .= $skin->make();
			}
			$TMPL = $TMPL_old; unset($TMPL_old);
			$TMPL['settings'] = $settings;

			if(isset($_GET['logout']) == 1) { // Log-out (unset cookies)
				setcookie('adminUser', '', $exp_time);
				setcookie('adminPass', '', $exp_time);
				header("Location: ".$confUrl."/index.php?a=admin");
			}
		} else { // Not Logged-in
			$TMPL['error'] = '<div class="error">Invalid username or password. Remember that the password is case-sensitive.</div>';
			unset($_COOKIE['adminUser']);
			unset($_COOKIE['adminPass']);
		}
	}
	$TMPL['localurl'] = $confUrl;
	$TMPL['titleh'] = $resultSettings[0];
	$TMPL['title'] = 'Admin - '.$resultSettings[0];

	$skin = new skin('admin/content');
	return $skin->make();
}
?>
