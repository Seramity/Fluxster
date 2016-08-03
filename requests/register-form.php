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
						header("Location: ".$confUrl."/index.php?a=welcome&m=e");
					}
				} else {
					header("Location: ".$confUrl."/index.php?a=welcome&m=lu");
				}
			} else {
				header("Location: ".$confUrl."/index.php?a=welcome&m=cu");
			}
		} else {
			header("Location: ".$confUrl."/index.php?a=welcome&m=af");
		}
	}