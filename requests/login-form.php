if(isset($_POST['login'])) {
	
		// TEST STUFF
		//$salt = generateSalt();
		//setcookie("PHPSESSID", $salt, $time);
		
		$query = sprintf("SELECT * FROM users WHERE username = '%s'", mysql_real_escape_string($_POST['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
		$token = $_SESSION['token'] = generateToken();
		$generateKey = generateKey();
		
		if($result[37] == 0) {
			$makeKey = sprintf("UPDATE users SET key = '%s' WHERE username = '%s'", $generateKey, $_POST['username']);
			mysql_query($makeKey);
		}
		
		$key = $result[37];
	
		$query = sprintf("SELECT * FROM users WHERE username = '%s'",
						mysql_real_escape_string($_POST['username']));
		$result = mysql_fetch_row(mysql_query($query));
		
		$username = $_POST['username']; 
		$password = md5($_POST['password']);
		
		
		//CURRENT PASSPORT
		$IPAddress = $_SERVER["REMOTE_ADDR"];
		
		$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

function getOS() { 

    global $user_agent;

    $os    =   "Unknown OS Platform";

    $os_array       =   array(
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );

    foreach ($os_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $os    =   $value;
        }

    }   

    return $os;

}

		
		// GET BROWSER //
		$HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT']; 
		$browser=""; $browserh=""; 
		if (eregi ("(netscape|mozilla)", $HTTP_USER_AGENT)==true) $browser="Netscape"; 
		if (eregi ("(mozilla|firefox|gecko/)", $HTTP_USER_AGENT)==true) $browser="Mozilla Firefox";
		if (eregi("(chrome)", $_SERVER['HTTP_USER_AGENT'])); $browser="Google Chrome";
		if (eregi ("msie", $HTTP_USER_AGENT)==true) $browser="Internet Explorer"; 
		if (eregi ("opera", $HTTP_USER_AGENT)==true) $browser="Opera"; 
		// END GET BROWSER //
		
		
		//LAST PASSPORT
		$lastIPAddress = $result[38];
		$lastLogin = $result[39];
		$lastOS = $result[40];
		$lastBrowser = $result[41];
				
		setcookie("username", str_replace(' ', '', strtolower($username)), $time);
		setcookie("password", $password, $time);
		//setcookie("key", $key, $time);
		
		if(loginCheck($username, $password)) { 
			
			// SAVE LAST PASSPORT
			$lastPassportQuery = sprintf("UPDATE users SET lastIP = '%s', lastLogin = '%s', lastOS = '%s', lastBrowser = '%s' WHERE username = '%s'", 
					mysql_real_escape_string($lastIPAddress), mysql_real_escape_string($lastLogin),  mysql_real_escape_string($lastOS), mysql_real_escape_string($lastBrowser),
					mysql_real_escape_string($_POST['username']));
			mysql_query($lastPassportQuery);
			// MAKE CURRENT PASSPORT
			$currentPassportQuery = sprintf("UPDATE users SET currentIP = '%s', currentLogin = '%s', currentOS = '%s', currentBrowser = '%s' WHERE username = '%s'", 
					mysql_real_escape_string($IPAddress), date("F j, Y, g:i a"), mysql_real_escape_string($os), mysql_real_escape_string($browser), mysql_real_escape_string($_POST['username']));
			mysql_query($currentPassportQuery);	
			
			header("Location: ".$confUrl."/stream"); 			

		} else { 
			header("Location: ".$confUrl."/index.php?a=welcome?m=le");
		}
	}