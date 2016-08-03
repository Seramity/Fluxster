<?php
function PageMain() {
	global $TMPL;
	global $confUrl;
	$title = array( 'privacy'    => 'Privacy Policy',
					'terms'		 => 'Terms of Service',
					'about'		 => 'About',
					'safety' => 'Safety And Guidelines',
					'badges' => 'Badges',
					'contact'    => 'Contact',
					'thanks'    => 'Thanks for Submitting',
					'changelog'    => 'Changelog',
					'updates'    => 'Upcoming Changes',
					'notfound'    => 'Not Found',
					'accessdenied'    => 'Access Denied',
					'advertise'    => 'Advertise',
					'featured'    => 'Featured Content',
					'welcomefxtr'    => 'Welcome!',
					'people'    => 'People',
					'wut'    => 'Um... wut?',
					'careers'    => 'Careers',
					'network'    => 'Flux Network',
					'explore'    => 'Explore',
					'aboutbundle'    => 'Bundle',
					'why'    => 'Why Fluxster?',
					'verified'    => 'Verified Accounts',
					'brand'    => 'Brand',
					'guide'    => 'Guide',
					'faqs'    => 'FAQs',
					'cookies'    => 'Cookies',
					'private'    => 'Private Account',
					'suspended'    => 'Account Suspended',
					'disabled'    => 'Account Disabled',
					
					'example'    => 'Example Website',
					
					'api'		 => 'API Documentation');
	if(!empty($_GET['a']) && isset($title[$_GET['a']])) {
		$a = $_GET['a'];
		
		$resultSettings = mysql_fetch_row(mysql_query(getSettings($querySettings)));
		
		$TMPL['url'] = $confUrl;
		$TMPL['titleh'] = $resultSettings[0];
		$TMPL['title'] = "{$title[$a]} - ".$resultSettings[0]."";
		
		$skin = new skin("page/$a");
		return $skin->make();
	} else {
		local_redirect('/');
	}
	
}
?>