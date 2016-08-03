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
		if(empty($_POST['postmsg'])) { echo '<div class="popup-body-posted">The message is too short.</div>'; }
		elseif(strlen($_POST['postmsg']) <= $resultSettings[10]) {
		
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
			
			$insertMessage = sprintf("INSERT INTO `messages` (`id`, `uid`, `message`, `mentions`, `tag`, `time`, `repliedTo`) VALUES ('', '%s', '%s', '%s', '%s', NOW(), '%s')", $data['id'], mysql_real_escape_string($_POST['postmsg']), $mentions, $hashtag, $row['id']);
			mysql_query($insertMessage);
			
			$postmsg = $_POST['postmsg'];
			$queryMsg = sprintf("SELECT * FROM messages, users WHERE messages.uid = '%s' AND messages.uid = users.idu ORDER BY id DESC LIMIT 0, 1", $data['id']);
			$newArr = array();
			
			$result = mysql_query($queryMsg);
			while($row = mysql_fetch_array($result)) {
				
				$parsedMessage = preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '/(^|[^a-z0-9_])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_])#([a-z0-9_]+)/i'), array('<a href="$1" target="_blank" rel="nofollow">$1</a>', '$1<a href="'.$confUrl.'/index.php?a=profile&u=$2">@$2</a>', '$1<a href="'.$confUrl.'/index.php?a=discover&u=$2">#$2</a>'), $row['message']);
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
				
				$report = '<div class="report-button sud" title="Report this message" id="'.$row['id'].'"><img src="'.$confUrl.'/images/report.png" /></div>';
				$getImg = (!empty($row['image'])) ? '<img src="'.$confUrl.'/uploads/avatars/'.$row['image'].'" width="50" height="50" />' : '<img src="http://www.gravatar.com/avatar/'.md5($row['email']).'?s=50&d=mm" />';
				$delete = ($row['username'] == $_COOKIE['username']) 
				? 
				'<div class="delete-button"><img src="'.$confUrl.'/images/icons/delete_message.png" /><a href="'.$confUrl.'/index.php?a=me&d='.$row['id'].'">Delete</a></div>'
				: 
				'<div class="reply-button"><img src="'.$confUrl.'/images/icons/reply.png" />Reply</div>';
				
				echo '
				<div class="message">
					<div class="message-picture">
						<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'">'.$getImg.'</a>
					</div>
					<div class="message-top-container">
						<div class="message-top">
							<div class="message-author">
								<a href="'.$confUrl.'/index.php?a=profile&u='.$author.'">'.$author.'</a>
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
						<div class="reply-container"></div>
					</div>
				</div>
				';
				echo '<div class="popup-body-posted">Message posted.</div>';
			}
		} else {
			echo '<div class="popup-body">The message is too long.</div>';
		}
	}
} else {
	echo '<a href="'.$confUrl.'/"><div class="new-message">Authentication cookies have expired. Please login again to continue.</div></a>';
}
mysql_close();
?>