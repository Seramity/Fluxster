<?php
require_once('./includes/config.php');
require_once('./includes/skins.php');
require_once('./includes/functions.php');

session_start();

mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);

if(isset($_GET['cookie_notice'])) {
	setcookie('cookie_notice', 'agreed', time() + 31556925);
	header('Location: '.$confUrl.'/');
}

$currentUrl = $_SERVER['PATH_INFO'];

if(!isset($_COOKIE['cookie_notice'])) {
	$TMPL['cookieNotice'] = '

	<div class="cookie-banner">
		 	<div class="container">
		 		<p>Fluxster uses cookies to ensure the best experience possible. Fluxster stores user data and third party cookies. <a href="'.$confUrl.'/cookies"><font color="#adb333">Read More</font></a>
		 		<br />
		 		In order to use this site, you will need to agree with our policy.</p>
		 		<a href="'.$currentUrl.'/?cookie_notice" class="agree-button">Agree</a>
		 	</div>
		 </div>
		<script src="'.$confUrl.'/js/cookies.js"></script>';
}

if(isset($_GET['a']) && isset($action[$_GET['a']])) {
	$page_name = $action[$_GET['a']];
} else {
	$page_name = 'welcome';
}

//check unread messages for notification

$checkMessages = sprintf("SELECT to, read FROM private WHERE read = 0, to = '%s'", mysql_real_escape_string($data['id']));
$countMessages= mysql_num_rows($checkMessages);


if (mysql_num_rows($checkMessages) > 0) {
	$msgNotification = '<span class="msg-notification">'.$countMessages.'</span>';
}


require_once("./sources/{$page_name}.php");

$confUrl = $conf['url'];
$confMail = $conf['mail'];

$TMPL['content'] = PageMain();

$query = sprintf("SELECT * FROM users WHERE username = '%s'", mysql_real_escape_string($_COOKIE['username']));
$result = mysql_fetch_row(mysql_query($query));

// DISPLAY FLUXSTER PLUS LINK
if($result[47] == 1) {
	$plusLink = '<li id="user_dropdown-list"><a href="'.$confUrl.'/index.php?a=settings&b=privacy">Privacy Settings</a></li>
		     <li id="user_dropdown-list"><a href="'.$confUrl.'/plus">Fluxster Plus <div class="linebreak"></div></a></li>';
} else {
	$plusLink = '<li id="user_dropdown-list"><a href="'.$confUrl.'/index.php?a=settings&b=privacy">Privacy Settings <div class="linebreak"></div></a></li>';
}

if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
	if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {

	$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
	$getImg = (!empty($data['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$data['image'].'" width="80" height="80" />' : '<img src="http://www.gravatar.com/avatar/'.md5($data['mail']).'?s=80&d=mm" />';
		$TMPL['userStatus'] =  'My Account';
		$TMPL['topbar'] = '<div class="topbar"><div class="header">
		<a href="'.$confUrl.'"><div class="logo"></div></a>

		<nav>

		<ul>
			<li id="menu" class="stream-link"><a href="'.$confUrl.'/stream">Stream</a></li>
			<li id="menu"><a href="'.$confUrl.'/profile/'.$data['user'].'">Profile</a></li>
			<li id="menu"><a href="'.$confUrl.'/mentions">Mentions</a></li>
			<li id="menu" class="messages-link"><a href="'.$confUrl.'/index.php?a=messages">Messages '.$msgNotification.'</a></li>
			<li id="menu"><a href="'.$confUrl.'/pixels">Pixels</a></li>
			<div class="search-input"><input type="text" id="search" value="Search for people" ></div>
			<li id="menu" class="user_dropdown-button"><img src="'.$confUrl.'/images/user_dropdown-icon.gif" />
			<div class="user_dropdown">
				<ul>
					<li id="user_dropdown-list"><a href="'.$confUrl.'/settings">Profile Settings</a></li>
					'.$plusLink.'
					<li id="user_dropdown-list"><a href="'.$confUrl.'/passport">Passport</a></li>
					<li id="user_dropdown-list"><a href="'.$confUrl.'/index.php?a=stream&logout=1">Logout</a></li>
				</ul>
			</div>
			</li>
		</ul>
		</nav>


		</div></div>';
	} else {
		$TMPL['userStatus'] = 'Log In / Register';
		$TMPL['topbar'] = '<div class="topbar"><div class="header"><a href="'.$confUrl.'"><div class="logo"></div><div class="logo-small"></div></a>
			<a href="'.$confUrl.'"><div class="menu_btn nord" title="Login"><img src="'.$confUrl.'/images/register.png" /></div></a>
			<div class="menu_visitor">Hello <strong>Visitor</strong></div>

							  </div></div>';
	}
} else {
	$TMPL['userStatus'] = 'Log In / Register';
	$TMPL['topbar'] = '<div class="topbar"><div class="header"><a href="'.$confUrl.'"><div class="logo"></div><div class="logo-small"></div></a>




	<a href="'.$confUrl.'"><div class="menu_btn nord" title="Register"><img src="'.$confUrl.'/images/register.png" /></div></a>
								  <div class="menu_visitor">Hello <strong>Visitor</strong></div>

							  </div></div>';

}
$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_COOKIE['username']));
		$result = mysql_fetch_row(mysql_query($query));

$TMPL['suspended'] = (!empty($result[19])) ? '<div class="notification-box notification-box-suspended">
							<p>Your account has been suspended. For more infomation, please visit the
							<a href="/suspendedaccount">Suspended Account</a> page.</p>
							</div>' : '';

if ($result[27] == 1) {
	$TMPL['disabled'] = '<div class="notification-box notification-box-disabled">
		<p>Your account is disabled. Fill in your account infomation and press "Reactivate"
		to activate your account</p>
		</div>';
}

// GET MESSGAGES

// STREAM MESSAGES
if($_GET['m'] == 're') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Your account has been reactivated.</div>';
}

if($_GET['m'] == 'ms') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Your post was successfully deleted.</div>';
}

if($_GET['m'] == 'me') {
	$TMPL['message'] = '<div class="notification-bar notification-error">We couldn\'t delete the message you\'ve selected.</div>';
}

// PRIVATE MESSAGES



// GENERAL SETTINGS MESSAGES
if($_GET['m'] == 's') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Settings successfully saved.</div>';
}

if($_GET['m'] == 'b') {
	$TMPL['message'] = '<div class="notification-bar notification-error">The Bio description can only be 260 characters or less.</div>';
}

if($_GET['m'] == 'ne') {
	$TMPL['message'] = '<div class="notification-bar notification-error">The email field cannot be empty.</div>';
}

if($_GET['m'] == 'e') {
	$TMPL['message'] = '<div class="notification-bar notification-error">Please enter a valid email.</div>';
}

if($_GET['m'] == 'w') {
	$TMPL['message'] = '<div class="notification-bar notification-error">Please enter a valid URL format.</div>';
}

// DELETE/DISABLE SETTINGS MESSAGES

if($_GET['m'] == 'eb') {
	$TMPL['message'] = '<div class="notification-bar notification-error">Please enter both fields.</div>';
}

if($_GET['m'] == 'iv') {
	$TMPL['message'] = '<div class="notification-bar notification-error">Invalid username or password.</div>';
}

if($_GET['m'] == 'sd') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Your account is now disabled.</div>';
}

// PROFILE PICTURE SETTINGS MESSAGES
if($_GET['m'] == 'sp') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Your profile picture have been changed.</div>';
}

if($_GET['m'] == 'nf') {
	$TMPL['message'] = '<div class="notification-bar notification-error">You did not selected any files to be uploaded, or the selected file(s) are empty.</div>';
}

if($_GET['m'] == 'fs') {
	$TMPL['message'] = '<div class="notification-bar notification-error">The selected file size must not exceed 2 MB.</div>';
}

if($_GET['m'] == 'wf') {
	$TMPL['message'] = '<div class="notification-bar notification-error">The selected file format is not supported. Upload a jpg, png, or gif file format.</div>';
}

if($_GET['m'] == 'de') {
	$TMPL['message'] = '<div class="notification-bar notification-success">Your profile picture has been removed.</div>';
}

// CHANGE USERNAME
if($_GET['m'] == 'su') {
	$TMPL['message'] = '<div class="notification-bar notification-error">Please enter a username other than your current one.</div>';
}

if($_GET['m'] == 'wp') {
	$TMPL['message'] = '<div class="notification-bar notification-error">Incorrect password.</div>';
}

// PLUS
if($_GET['m'] == 'lfp') {
	$TMPL['message'] = '<div class="notification-bar notification-success">You have left the Fluxster Plus program.</div>';
}

// FOLLOW MESSAGES
if($_GET['m'] == 'ff') {
	$TMPL['message'] = '<div class="notification-bar notification-success">You are now following this user.</div>';
}

if($_GET['m'] == 'fu') {
	$TMPL['message'] = '<div class="notification-bar notification-success">You are now no longer following this user.</div>';
}

// REGISTER
if($_GET['m'] == 'ue') {
	$TMPL['message'] = '<div class="notification-bar notification-error">This username already exist, please choose another one.</div>';
}

if($_GET['m'] == 'lu') {
	$TMPL['message'] = '<div class="notification-bar notification-error">The username must contain only letters and numbers.</div>';
}

if($_GET['m'] == 'cu') {
	$TMPL['message'] = '<div class="notification-bar notification-error">The username must be between 3 and 16 characters.</div>';
}

if($_GET['m'] == 'af') {
	$TMPL['message'] = '<div class="notification-bar notification-error">All fields must be completed.</div>';
}
//*******************************************************************************************************************

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
$TMPL['footer'] = $resultSettings[0];
$TMPL['url'] = $conf['url'];
$TMPL['ad1'] = $resultSettings[2];
$TMPL['ad2'] = $resultSettings[3];
$TMPL['msgLimit'] =  $resultSettings[10];

$skin = new skin('wrapper');
echo $skin->make();

mysql_close();
?>
