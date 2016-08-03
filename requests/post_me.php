<?php
include("../includes/config.php");
include("../includes/functions.php");
mysql_connect($conf['host'], $conf['user'], $conf['pass']);
mysql_query('SET NAMES utf8');
mysql_select_db($conf['name']);
$confUrl = $conf['url'];

$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));

if(loginCheck($_COOKIE['username'], $_COOKIE['password'])) {
	if(isset($_POST['postmsg'])) {
	
		$data = loginCheck($_COOKIE['username'], $_COOKIE['password']);
		if(empty($_POST['postmsg'])) { $result = '<div class="popup-body-posted">The message is too short.</div>'; }
		elseif(strlen($_POST['postmsg']) <= $resultSettings[10]) {
			
			if(substr($_POST['video'], 0, 20) == "https://youtube.com/" || substr($_POST['video'], 0, 24) == "https://www.youtube.com/" || substr($_POST['video'], 0, 16) == "www.youtube.com/" || substr($_POST['video'], 0, 12) == "youtube.com/" || substr($_POST['video'], 0, 19) == "http://youtube.com/" || substr($_POST['video'], 0, 23) == "http://www.youtube.com/") {
				parse_str(parse_url($_POST['video'], PHP_URL_QUERY), $my_array_of_vars);
				$video = 'yt:'.$my_array_of_vars['v'];
			} elseif(substr($_POST['video'], 0, 17) == "http://vimeo.com/" || substr($_POST['video'], 0, 21) == "http://www.vimeo.com/" || substr($_POST['video'], 0, 14) == "www.vimeo.com/" || substr($_POST['video'], 0, 10) == "vimeo.com/") {
				$video = 'vm:'.(int)substr(parse_url($_POST['video'], PHP_URL_PATH), 1);
			}
			
			$maxsize = $resultSettings[14];	
			$size = $_FILES['my_image']['size'];
			$extArray = explode(',', $resultSettings[15]);
			$ext = pathinfo($_FILES['my_image']['name'], PATHINFO_EXTENSION);
			if(!empty($size) && $size > $maxsize) { 
				
				$result = '<div class="popup-body-posted">The file size is too big.</div>';
			} elseif(!empty($ext) && !in_array($ext, $extArray)) {
				
				$result = '<div class="popup-body-posted">The file format is not supported.</div>';
			} else {
				$searchMentions = mysql_real_escape_string($_POST['postmsg']);
				preg_match_all('/(^|[^a-z0-9_])@([a-z0-9_]+)/i', $searchMentions, $matchedMentions);
				$searchHastags = mysql_real_escape_string($_POST['postmsg']);
				preg_match_all('/(^|[^a-z0-9_])#([a-z0-9_]+)/i', $searchHastags, $matchedHastags);
				
				$mentionsImplode = implode(',', $matchedMentions[0]);
				$hashtagImplode = implode(',', $matchedHastags[0]);
				
				if(!empty($matchedMentions[0])) {
					$mentions = $mentionsImplode.',';
				} else {
					$mentions = '';
				}
				if(!empty($matchedHastags[0])) {
					$hashtag = $hashtagImplode.',';
				} else {
					$hashtag = '';
				}

				if(isset($_FILES['my_image']['name']) && $_FILES['fileselect']['name'] !== '' && $size > 0) {
					$rand = mt_rand();
					$tmp_name = $_FILES['my_image']['tmp_name'];
					$name = pathinfo($_FILES['my_image']['name'], PATHINFO_FILENAME);
					$fullname = $_FILES['my_image']['name'];
					$size = $_FILES['my_image']['size'];
					$type = pathinfo($_FILES['my_image']['name'], PATHINFO_EXTENSION);
					$finalName = $rand.'.'.mysql_real_escape_string($name).'.'.mysql_real_escape_string($type);
					move_uploaded_file($tmp_name, '../uploads/media/'.$rand.'.'.mysql_real_escape_string($name).'.'.mysql_real_escape_string($type));

					$insertMessage = sprintf("INSERT INTO `messages` (`id`, `uid`, `message`, `mentions`, `tag`, `time`, `media`, `video`) VALUES ('', '%s', '%s', '%s', '%s', NOW(), '%s', '%s')", $data['id'], mysql_real_escape_string($_POST['postmsg']), $mentions, $hashtag, $finalName, $video);
				} else {
					$insertMessage = sprintf("INSERT INTO `messages` (`id`, `uid`, `message`, `mentions`, `tag`, `time`, `video`) VALUES ('', '%s', '%s', '%s', '%s', NOW(), '%s')", $data['id'], mysql_real_escape_string($_POST['postmsg']), $mentions, $hashtag, $video);
				}
				
				mysql_query($insertMessage);
				
				$postmsg = $_POST['postmsg'];
				$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid = '%s' AND messages.uid = users.idu ORDER BY id DESC LIMIT 0, 1", $data['id']);
				$newArr = array();
				
				$result = mysql_query($queryMsg);
				while($row = mysql_fetch_array($result)) {
					
					$parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/profile/$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $row['message']); 
					$author = $row['username']; 
					$time = $row['time'];
					
					$censArray = explode(',', $resultSettings[4]);
					$parsedMessage = strip_tags(str_replace($censArray, '', $parsedMessage), '<a>');
					
					if($resultSettings[9] == '0') {
						$time = date("c", strtotime($time));
					} elseif($resultSettings[9] == '2') {
						$time = ago(strtotime($time));
					} elseif($resultSettings[9] == '3') {
						$date = strtotime($time);
						$time = date('Y-m-d', $date);
						$b = '-standard';
					}
					if(substr($row['video'], 0, 3) == 'yt:') {
						$embedVideo = '<iframe width="100%" height="315" src="http://www.youtube.com/embed/'.str_replace('yt:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
						$mediaButton = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
					} else if(substr($row['video'], 0, 3) == 'vm:') {
						$embedVideo = '<iframe width="100%" height="315" src="http://player.vimeo.com/video/'.str_replace('vm:', '', $row['video']).'" frameborder="0" allowfullscreen></iframe>';
						$mediaButton = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
					}
					if(!empty($row['media'])) {
						$embedImage = '<a href="'.$confUrl.'/uploads/media/'.$row['media'].'" target="_blank"><img src="'.$confUrl.'/uploads/media/'.$row['media'].'" /></a>';
						$mediaButton = '<div class="media-button"><img src="'.$confUrl.'/images/icons/attachment.png" />View Media</div>';
					}
					$report = '<div class="report-button sud" title="Report this message" id="'.$row['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
					$getImg = (!empty($row['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$row['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($row['email']).'?s=50&d=mm" />';
					$delete = ($row['username'] == $_COOKIE['username']) 
					? 
					'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=me&d='.$row['id'].'">Delete</a></div>'
					: 
					'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
					
					$result = mysql_real_escape_string('
					<div class="message">
						<div class="message-picture">
							<a href="'.$confUrl.'/profile/'.$author.'">'.$getImg.'</a>
						</div>
						<div class="message-top-container">
							<div class="message-top">
								<div class="message-author">
									<a href="'.$confUrl.'/profile/'.$author.'">'.$author.' '.$result[20].'</a>
								</div>
								<a href="'.$confUrl.'/index.php?a=message&m='.$row['id'].'" target="_blank">
									<div class="timeago'.$b.'" title="'.$time.'">
										'.$time.'
									</div>
								</a>
							</div>
						</div>
						<div class="message-container">
							<div class="message-message">
								'.$parsedMessage.'
							</div>
						</div>
						<div class="message-bottom-container">
							'.$report.'
							'.$delete.'
							'.$mediaButton.'
							<div class="reply-container"></div>
							<div class="media-container">
								'.$embedVideo.'
								'.$embedImage.'
							</div>
						</div>
					</div>
					<div class="popup-body-posted">Message posted. </div>');
				}
			}
		} else {
			$result = '<div class="popup-body">The message is too long.</div>';
		}
	}
} else {
	echo 'Invalid login credentials.';
}
mysql_close();
?>
<script language="javascript" type="text/javascript">window.top.window.stopUpload('<?php echo $result; ?>');</script>