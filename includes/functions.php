<?php
function getSettings($querySettings) {
	$querySettings = "SELECT * from settings";
	return $querySettings;
}

function sendMail($to, $title, $url, $from, $username, $password) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'Welcome to '.$title; 
	$message = '
	<html> 
	<head>
	
	<style>
	html {

}

body {
color:#333;
font-family: Arial, sans-serif;
font-size:13px;
margin:0;
padding: 20px 0 0 0;
background: #ececec;
}

h1 { font-size: 32px; margin: 0; }
h2 { font-size: 28px; margin: 0; }
h3 { font-size: 28px; margin: 0; }
h4 { font-size: 24px; margin: 0; }
h5 { font-size: 18px; margin:0;  }
h6 { font-size: 18px; margin-left: 160px; }
h7 { font-size: 20px; margin-left: 0px; }
h8 { font-size: 18px; margin-left: 380px; }
h9 { font-size: 12px; margin: 0px; }



h1, h2, h3, h4, h5, h6, h7, h8, h9 {
font-family: Arial, sans-serif;
font-weight: 300;
}

.topbar {
position: absolute;
top: 0;
right: 0;
left: 0;
z-index: 1000;
height: 50px;
background: #45C491; 
margin-bottom: 40px;
-webkit-box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
border-bottom: 0.25em solid #549086;
}
.topbar_margin {
width: 100%;
height: 45px;
}

.logo {
background: url(\''.$url.'/images/logo.png\') no-repeat;
display: block;
height: 29px;
width: 129px;
float: left;
margin-top: 8px;
}
.logo:active {
margin-top: 8px;
}

.header {
width: 100%;
margin: 0 auto;
max-width: 900px;
}
.body-outline {
box-shadow: 0px 1px 1px #CCC;
border: 1px solid #BFBFBF;
-moz-border-radius:3px;
-webkit-border-radius:3px;
border-radius:3px;
width: 100%;
margin: 0 auto;
max-width: 900px;
}

.row { 
width: 100%;
margin: 0 auto;
max-width: 900px;
background: #fff;
color: #6B6B6B;
}
.sidebar {
padding: 20px;
overflow: auto;
overflow-x: hidden;
word-wrap: break-word;
overflow-wrap: break-word;
}
.divider {
height: 1px;
width: 100%;
background: #eee;
}

a {
color: #45C491;
text-decoration: none;
}
	</style>
	<div class="topbar"><div class="header">
		<a href="'.$url.'"><div class="logo"></div></a>
	</div></div>
	</head>

	<body>
	<div class="body-outline">
		<div class="row">
		<div class="sidebar">
			<h3>Welcome to Fluxster</h3>
			
			<br />
			<div class="divider"></div>
			<br />
			
			<p>We hope you find Fluxster an easy and useful tool to share your content.
			<br />If you are new to Fluxster, we suggest checking out the <a href="'.$url.'/guide" target="_blank">tutorial page</a> 
			to learn all the basics to get you started quickly on Fluxster.</p>
			<p>Since you got your account made, why not get your profile set up.</p>
			<br /><strong>Your account info:</strong>
			
			<br />
			Your username: <strong>'.$username.'</strong>
			
			<br /><br />
	
			See your new profile: <a href="'.$url.'/profile/'.$username.'" target="_blank">'.$username.'</a><br />
			
			Start adding infomation and profile settings <a href="'.$url.'/settings" target="_blank">here</a>!
			<br />And of course, you can start posting and following once you get on Fluxster!
			
			<br /><br />
			Thanks for joining, '.$username.'
			<br />
			Have a nice day!
			
			<br /><br />
			- The Fluxster Team
		
		<br /><br />
		
	<div class="divider"></div>
	<br />
	&copy;2013 Fluxster - <a href="'.$url.'/about" target="_blank">About</a> | <a href="'.$url.'/contact" target="_blank">Contact</a> |
	<a href="http://fluxster.net/blog" target="_blank">Blog</a>
		</div>
		</div>
	</div>
	</body>
	</html> 
	';
	return @mail($to, $subject, $message, $headers);
}

function sendRecover($to, $title, $url, $from, $username, $salt) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'Password Recovery - '.$title;
	$message = '
	<html> 
	<head>
	
	<style>
	html {

}

body {
color:#333;
font-family: Arial, sans-serif;
font-size:13px;
margin:0;
padding: 20px 0 0 0;
background: #ececec;
}

h1 { font-size: 32px; margin: 0; }
h2 { font-size: 28px; margin: 0; }
h3 { font-size: 28px; margin: 0; }
h4 { font-size: 24px; margin: 0; }
h5 { font-size: 18px; margin:0;  }
h6 { font-size: 18px; margin-left: 160px; }
h7 { font-size: 20px; margin-left: 0px; }
h8 { font-size: 18px; margin-left: 380px; }
h9 { font-size: 12px; margin: 0px; }



h1, h2, h3, h4, h5, h6, h7, h8, h9 {
font-family: Arial, sans-serif;
font-weight: 300;
}

.topbar {
position: absolute;
top: 0;
right: 0;
left: 0;
z-index: 1000;
height: 50px;
background: #45C491; 
margin-bottom: 40px;
-webkit-box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
border-bottom: 0.25em solid #549086;
}
.topbar_margin {
width: 100%;
height: 45px;
}

.logo {
background: url(\''.$url.'/images/logo.png\') no-repeat;
display: block;
height: 29px;
width: 129px;
float: left;
margin-top: 8px;
}
.logo:active {
margin-top: 8px;
}

.header {
width: 100%;
margin: 0 auto;
max-width: 900px;
}
.body-outline {
box-shadow: 0px 1px 1px #CCC;
border: 1px solid #BFBFBF;
-moz-border-radius:3px;
-webkit-border-radius:3px;
border-radius:3px;
width: 100%;
margin: 0 auto;
max-width: 900px;
}

.row { 
width: 100%;
margin: 0 auto;
max-width: 900px;
background: #fff;
color: #6B6B6B;
}
.sidebar {
padding: 20px;
overflow: auto;
overflow-x: hidden;
word-wrap: break-word;
overflow-wrap: break-word;
}
.divider {
height: 1px;
width: 100%;
background: #eee;
}

a {
color: #45C491;
text-decoration: none;
}
	</style>
	<div class="topbar"><div class="header">
		<a href="'.$url.'"><div class="logo"></div></a>
	</div></div>
	</head>

	<body>
	<div class="body-outline">
		<div class="row">
		<div class="sidebar">
			<h3>Password Recovery</h3>
			
			<br />
			<div class="divider"></div>
			<br />
			
			<p>
			A password recovery has been sent for <strong>'.$username.'</strong>
			</p>
			<br /><strong>Your account info:</strong>
			
			<br /> 
			Your username: <strong>'.$username.'</strong>
			<br />
			Your Reset Key: <strong>'.$salt.'</strong> 
			
			<br /><br />
			
			You can reset your password by accessing the following link: 
			<a href="'.$url.'/index.php?a=recover&r=1" target="_blank">'.$url.'/index.php?a=recover&r=1</a>
			
			<br /><br />
			<font color="#b64949"><strong>Note: If you did not request this password recovery, please ignore this.</strong></font>
			
			
			<br /><br />
			- The Fluxster Team
		
		<br /><br />
		
	<div class="divider"></div>
	<br />
	&copy;2013 Fluxster - <a href="'.$url.'/about" target="_blank">About</a> | <a href="'.$url.'/contact" target="_blank">Contact</a> |
	<a href="http://fluxster.net/blog" target="_blank">Blog</a>
		</div>
		</div>
	</div>
	</body>
	</html> 
	';
	return @mail($to, $subject, $message, $headers);
}

function sendPM($to, $title, $url, $from, $username) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'You have a new private message';
	$message = '
	<html> 
	<head>
	
	<style>
	html {

}

body {
color:#333;
font-family: Arial, sans-serif;
font-size:13px;
margin:0;
padding: 20px 0 0 0;
background: #ececec;
}

h1 { font-size: 32px; margin: 0; }
h2 { font-size: 28px; margin: 0; }
h3 { font-size: 28px; margin: 0; }
h4 { font-size: 24px; margin: 0; }
h5 { font-size: 18px; margin:0;  }
h6 { font-size: 18px; margin-left: 160px; }
h7 { font-size: 20px; margin-left: 0px; }
h8 { font-size: 18px; margin-left: 380px; }
h9 { font-size: 12px; margin: 0px; }



h1, h2, h3, h4, h5, h6, h7, h8, h9 {
font-family: Arial, sans-serif;
font-weight: 300;
}

.topbar {
position: absolute;
top: 0;
right: 0;
left: 0;
z-index: 1000;
height: 50px;
background: #45C491; 
margin-bottom: 40px;
-webkit-box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
border-bottom: 0.25em solid #549086;
}
.topbar_margin {
width: 100%;
height: 45px;
}

.logo {
background: url(\''.$url.'/images/logo.png\') no-repeat;
display: block;
height: 29px;
width: 129px;
float: left;
margin-top: 8px;
}
.logo:active {
margin-top: 8px;
}

.header {
width: 100%;
margin: 0 auto;
max-width: 900px;
}
.body-outline {
box-shadow: 0px 1px 1px #CCC;
border: 1px solid #BFBFBF;
-moz-border-radius:3px;
-webkit-border-radius:3px;
border-radius:3px;
width: 100%;
margin: 0 auto;
max-width: 900px;
}

.row { 
width: 100%;
margin: 0 auto;
max-width: 900px;
background: #fff;
color: #6B6B6B;
}
.sidebar {
padding: 20px;
overflow: auto;
overflow-x: hidden;
word-wrap: break-word;
overflow-wrap: break-word;
}
.divider {
height: 1px;
width: 100%;
background: #eee;
}

a {
color: #45C491;
text-decoration: none;
}
	</style>
	<div class="topbar"><div class="header">
		<a href="'.$url.'"><div class="logo"></div></a>
	</div></div>
	</head>

	<body>
	<div class="body-outline">
		<div class="row">
		<div class="sidebar">
			<h3>New Private Message</h3>
			
			<br />
			<div class="divider"></div>
			<br />
			
			<p>
			You have recieved a new private message from <strong>'.$username.'</strong>
			<br />
			You can view it <a href="'.$url.'/index.php?a=messages" target="_blank">here</a>
			</p>
			
			<br /><br />
			- The Fluxster Team
		
		<br /><br />
		
	<div class="divider"></div>
	<br />
	&copy;2013 Fluxster - <a href="'.$url.'/about" target="_blank">About</a> | <a href="'.$url.'/contact" target="_blank">Contact</a> |
	<a href="http://fluxster.net/blog" target="_blank">Blog</a>
		</div>
		</div>
	</div>
	</body>
	</html> 
	';
	return @mail($to, $subject, $message, $headers);
}

function sendAcceptedPlus($to, $title, $url, $from, $username) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'You have been accepted for Fluxster Plus';
	$message = '
	<html> 
	<head>
	
	<style>
	html {

}

body {
color:#333;
font-family: Arial, sans-serif;
font-size:13px;
margin:0;
padding: 20px 0 0 0;
background: #ececec;
}

h1 { font-size: 32px; margin: 0; }
h2 { font-size: 28px; margin: 0; }
h3 { font-size: 28px; margin: 0; }
h4 { font-size: 24px; margin: 0; }
h5 { font-size: 18px; margin:0;  }
h6 { font-size: 18px; margin-left: 160px; }
h7 { font-size: 20px; margin-left: 0px; }
h8 { font-size: 18px; margin-left: 380px; }
h9 { font-size: 12px; margin: 0px; }



h1, h2, h3, h4, h5, h6, h7, h8, h9 {
font-family: Arial, sans-serif;
font-weight: 300;
}

.topbar {
position: absolute;
top: 0;
right: 0;
left: 0;
z-index: 1000;
height: 50px;
background: #45C491; 
margin-bottom: 40px;
-webkit-box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
border-bottom: 0.25em solid #549086;
}
.topbar_margin {
width: 100%;
height: 45px;
}

.logo {
background: url(\''.$url.'/images/logo.png\') no-repeat;
display: block;
height: 29px;
width: 129px;
float: left;
margin-top: 8px;
}
.logo:active {
margin-top: 8px;
}

.header {
width: 100%;
margin: 0 auto;
max-width: 900px;
}
.body-outline {
box-shadow: 0px 1px 1px #CCC;
border: 1px solid #BFBFBF;
-moz-border-radius:3px;
-webkit-border-radius:3px;
border-radius:3px;
width: 100%;
margin: 0 auto;
max-width: 900px;
}

.row { 
width: 100%;
margin: 0 auto;
max-width: 900px;
background: #fff;
color: #6B6B6B;
}
.sidebar {
padding: 20px;
overflow: auto;
overflow-x: hidden;
word-wrap: break-word;
overflow-wrap: break-word;
}
.divider {
height: 1px;
width: 100%;
background: #eee;
}

a {
color: #45C491;
text-decoration: none;
}
	</style>
	<div class="topbar"><div class="header">
		<a href="'.$url.'"><div class="logo"></div></a>
	</div></div>
	</head>

	<body>
	<div class="body-outline">
		<div class="row">
		<div class="sidebar">
			<h3>You have been accepted for Fluxster Plus!</h3>
			
			<br />
			<div class="divider"></div>
			<br />
			
			<p>
			Hello '.$username.', 
			<br>
			you have been accepted for Fluxster Plus.
			
			<br><br>
			With Fluxster Plus, you have many opportunities such as early access to unreleased features and much more!
			<br>
			You can manage your Plus membership <a href="'.$url.'/plus">here</a> and if you ever feel you that you want to leave the program
			you can go <a href="'.$url.'/index.php?a=settings&b=plus">here</a>.
			
			<br><br>
			Thanks for joining the Fluxster Plus program.
			
			<br><br>
			Have a nice day!
			</p>
			
			- The Fluxster Team
		
		<br /><br />
		
	<div class="divider"></div>
	<br />
	&copy;2013 Fluxster - <a href="'.$url.'/about" target="_blank">About</a> | <a href="'.$url.'/contact" target="_blank">Contact</a> |
	<a href="http://fluxster.net/blog" target="_blank">Blog</a>
		</div>
		</div>
	</div>
	</body>
	</html> 
	';
	return @mail($to, $subject, $message, $headers);
}

function sendDeclinedPlus($to, $title, $url, $from, $username) {
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$title.' <'.$from.'>' . "\r\n";
	$subject = 'You have been declined for Fluxster Plus';
	$message = '
	<html> 
	<head>
	
	<style>
	html {

}

body {
color:#333;
font-family: Arial, sans-serif;
font-size:13px;
margin:0;
padding: 20px 0 0 0;
background: #ececec;
}

h1 { font-size: 32px; margin: 0; }
h2 { font-size: 28px; margin: 0; }
h3 { font-size: 28px; margin: 0; }
h4 { font-size: 24px; margin: 0; }
h5 { font-size: 18px; margin:0;  }
h6 { font-size: 18px; margin-left: 160px; }
h7 { font-size: 20px; margin-left: 0px; }
h8 { font-size: 18px; margin-left: 380px; }
h9 { font-size: 12px; margin: 0px; }



h1, h2, h3, h4, h5, h6, h7, h8, h9 {
font-family: Arial, sans-serif;
font-weight: 300;
}

.topbar {
position: absolute;
top: 0;
right: 0;
left: 0;
z-index: 1000;
height: 50px;
background: #45C491; 
margin-bottom: 40px;
-webkit-box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
box-shadow:  0px 1px 5px 1px rgba(0, 0, 0, 0.2);
border-bottom: 0.25em solid #549086;
}
.topbar_margin {
width: 100%;
height: 45px;
}

.logo {
background: url(\''.$url.'/images/logo.png\') no-repeat;
display: block;
height: 29px;
width: 129px;
float: left;
margin-top: 8px;
}
.logo:active {
margin-top: 8px;
}

.header {
width: 100%;
margin: 0 auto;
max-width: 900px;
}
.body-outline {
box-shadow: 0px 1px 1px #CCC;
border: 1px solid #BFBFBF;
-moz-border-radius:3px;
-webkit-border-radius:3px;
border-radius:3px;
width: 100%;
margin: 0 auto;
max-width: 900px;
}

.row { 
width: 100%;
margin: 0 auto;
max-width: 900px;
background: #fff;
color: #6B6B6B;
}
.sidebar {
padding: 20px;
overflow: auto;
overflow-x: hidden;
word-wrap: break-word;
overflow-wrap: break-word;
}
.divider {
height: 1px;
width: 100%;
background: #eee;
}

a {
color: #45C491;
text-decoration: none;
}
	</style>
	<div class="topbar"><div class="header">
		<a href="'.$url.'"><div class="logo"></div></a>
	</div></div>
	</head>

	<body>
	<div class="body-outline">
		<div class="row">
		<div class="sidebar">
			<h3>You have been declined for Fluxster Plus</h3>
			
			<br />
			<div class="divider"></div>
			<br />
			
			<p>
			Hello '.$username.', 
			<br>
			we are sorry to say that you have been declined for Fluxster Plus.
			This is because you did not meet all the requirements for the program.
			<br>
			If you have no knowledge of the requirements for Fluxster Plus, you can read them <a href="">here</a>.
			
			<br><br>
			Sorry for the inconvenience. 
			<br>
			Once you are able to meet all the requirements, you can feel free to resend a request.

			<br><br>
			Have a nice day!
			</p>

			- The Fluxster Team
		
		<br /><br />
		
	<div class="divider"></div>
	<br />
	&copy;2013 Fluxster - <a href="'.$url.'/about" target="_blank">About</a> | <a href="'.$url.'/contact" target="_blank">Contact</a> |
	<a href="http://fluxster.net/blog" target="_blank">Blog</a>
		</div>
		</div>
	</div>
	</body>
	</html> 
	';
	return @mail($to, $subject, $message, $headers);
}

function loginCheck($username, $password) {
	$query = sprintf('SELECT * FROM users WHERE username = "%s" AND password = "%s"', mysql_real_escape_string(strtolower($username)), mysql_real_escape_string($password));
	if(mysql_fetch_row(mysql_query($query))) {
		$result = mysql_fetch_row(mysql_query($query));
		$out['true'] = true;
		$out['id'] = $result[0];
		$out['user'] = $result[1];
		$out['mail'] = $result[3];
		$out['image'] = $result[12];
		$out['background'] = $result[15];
		return $out;
	} else {
		return false;
	}
}

function ago($i){
    $m = time()-$i; $o='just now';
    $t = array('year'=>31556926,'month'=>2629744,'week'=>604800,
'day'=>86400,'hour'=>3600,'minute'=>60,'second'=>1);
    foreach($t as $u=>$s){
        if($s<=$m){$v=floor($m/$s); $o="$v $u".($v==1?'':'s').' ago'; break;}
    }
    return $o;
}

function fsize($bytes) { 
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KiB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MiB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GiB';
   else return round($bytes / 1099511627776, 2).' TiB';
}

function generateToken($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function generateKey($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>