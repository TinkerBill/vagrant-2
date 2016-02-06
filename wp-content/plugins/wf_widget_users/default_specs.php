<?php
/*
default_specs for wf_widget_users

Comments widget
Comment Form widget
YouTube widget
User Photo widget

*/


	
	
$default_specs['comments'] = array( 

	'display_name' => 'Comments widget',
	
	'class_name' => 'Comments_widget',
	
	'inserts' => true, // the post being commented on
	
	'description' => "Lets you insert the latest comments relating to a particular post and/or user.",
	
	'params' => array(
		'comment' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'Appears in the &lsquo;Widgets on this page&rsquo; list - a brief note to remind you what this widget is showing.'
			),	
		'style' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'style',
			'paramdesc' => 'Pick one of the pre-defined styles for this widget.'
			),
		
		
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
			),
		'number' => array(
			'default' => 3,
			'reqd' => false,
			'validation' => 'show_posts',
			'paramdesc' => 'Maximum number of comments to display. Default=3. To show all, use &lsquo;all&rsquo;.'
			),
			
		'img_size' => array(
			'default' => 100,
			'reqd' => false,
			'validation' => 'int', // allow 0 to suppress image
			'paramdesc' => 'Resizes the avatar images. Default=100 (ie: 100px square). To suppress, use 0.'
			),
		'date_format' => array(
			'default' => 'j.n.y',
			'reqd' => false,
			'validation' => 'anything',/////////////
			'paramdesc' => "Format of the date and time (see www.php.net/manual/en/function.date.php)."
			),
		'post_id' => array(
			'default' => '',
			'reqd' => false,
			'validation' => 'comments_post_id',// post being commented on - which could be anything. No easy way to specify more than one
			'paramdesc' => 'ID of the post or attachment being commented on. Use &lsquo;current&rsquo; for comments relating to this post.'
			),
		'user_id' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'comments_user_id',// don't really want to make all user ids public
			'paramdesc' => 'ID of the comment author. Use &lsquo;current&rsquo; to show the logged-in user&rsquo;s comments.'
			),
		'show_source' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'Whether to display the title of the post (with link) that the comment is attached to.'
			)
		)
	);
	

	function validate_comments_user_id($val) {
		$feedback = 'ok';
		if(!((intminmax('1|', $val) == '') || ($val == 'current'))) { 
			$feedback = "positive integer or 'current'";
		}
		return $feedback;
	}
	function validate_comments_post_id($val) {
		return validate_comments_user_id($val);
	}
	

/// DISPLAY FORM FOR COMMENTS ////////////////////////////////////////////////
	
	
$default_specs['comment_form'] = array( 

	'display_name' => 'Comment Form widget',
	
	'class_name' => 'Comment_form_widget',
	
	'inserts' => false, 
	
	'description' => "Displays a form for posting comments.",
	
	'params' => array(
		'comment' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'Appears in the &lsquo;Widgets on this page&rsquo; list - a brief note to remind you what this widget is showing.'
			),	
		'style' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'style',
			'paramdesc' => 'Pick one of the pre-defined styles for this widget.'
			),
		
		
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
			),
		/*
		'role' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'posint'
			),
		*/
		'roles' => array( // list of roles that can post comments = comma-separated
			'default' => false,
			'reqd' => false,
			'validation' => 'roles',/////////////
			'paramdesc' => 'A comma-separated list of roles that can post comments.'
			)
		
		)
	);
	
function validate_roles($val) {
	global $user_roles;
	$feedback = 'ok';
	$roles_array = explode(',',$user_roles);
	//if(strpos($user_roles,$val) === false) {
	if(!in_array($val,$roles_array)) {
		$feedback = "Available roles: ".str_replace(',',', ',$user_roles);
	}
	return $feedback;
}
	
/// YOUTUBE VIDEO WIDGET //////////////////////////////////////////
	
	
$default_specs['youtube'] = array( 

	'display_name' => 'YouTube widget',
	
	'class_name' => 'YouTube_widget',
	
	'inserts' => false, 
	
	'description' => "Lets you insert a YouTube video.",
	
	'params' => array(
		
		'code' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'youtube_code',
			'paramdesc' => 'Enter the code for any existing video on YouTube.'
			),
		'comment' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'Appears in the &lsquo;Widgets on this page&rsquo; list - a brief note to remind you what this widget is showing.'
			),	
		'style' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'style',
			'paramdesc' => 'Pick one of the pre-defined styles for this widget.'
			),
		
		
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
			),
		
		'width' => array(
			'default' => 600,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Specifies the width of the containing iframe. Default=600.'
			),
		'height' => array(
			'default' => 336,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Specifies the height of the containing iframe. Default=336.'
			)
	)
);


function validate_youtube_code($val) {
	$checklink = 'http://gdata.youtube.com/feeds/api/videos/'.$val;
	
	//** curl the check link ***//
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$checklink);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0); // indefinite
	$result = curl_exec($ch);
	curl_close($ch);
	if(trim($result)=="Invalid id") {
		$feedback = 'This video doesn\'t appear to exist';
	} else {
		$feedback = 'ok';
	}
	return $feedback;
}



	
	

/// USER PHOTO WIDGET //////////////////////////////////////////
	
	
$default_specs['user_photo'] = array( 

	'display_name' => 'User Photo widget',
	
	'class_name' => 'UserPhoto_widget',
	
	'inserts' => true, // the post the photo is attached to
	
	'description' => 'Lets you insert a gallery of thumbnails and/or a user-photo uploader.',
	
	'params' => array(
		
		'comment' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'Appears in the &lsquo;Widgets on this page&rsquo; list - a brief note to remind you what this widget is showing.'
			),	
		'style' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'style',
			'paramdesc' => 'Pick one of the pre-defined styles for this widget.'
			),		
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
			),
		
		'columns' => array(
			'default' => 4,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Number of columns.'
			),
		'gallery' => array(
			'default' => true,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'Whether or not to display any images.'
			),
		'upload' => array(
			'default' => true,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'Whether or not to display a user-photo uploader.'
			),
		'owner_id' => array(
			'default' => null, // author, user, quser
			'reqd' => false,
			'validation' => 'user_photo_owner_id',/////////////
			'paramdesc' => 'Whose photos do you want to display? Specify &lsquo;author&rsquo;, &lsquo;user&rsquo;, &lsquo;all&rsquo; - or a user_ID.'
			),
		'attached_to' => array(
			'default' => 'all', // a post ID, 'current', 'all'
			'reqd' => false,
			'validation' => 'user_photo_attached_to',
			'paramdesc' => 'The post or page that the images are attached to. Default=&lsquo;all&rsquo;.'
			),
		'type' => array( // Jetpack tiled options
			'default' => false, 
			'reqd' => false,
			'validation' => '=rectangular|square|circle|slideshow',
			'paramdesc' => 'Pick one of the pre-defined formats for this widget.'
			)
	)
);
	
function validate_user_photo_attached_to($val) {
	$feedback = 'ok';
	if(!( intminmax('1|', $val) == '' || in_array($val,array('current','all')) )) { 
		$feedback = "'current', 'all' or positive integer";
	}
	return $feedback;
}


function validate_user_photo_owner_id($val) {
	$feedback = 'ok';
	if(!( intminmax('1|', $val) == '' || in_array($val,array('author','user','all')) )) { 
		$feedback = "'author', 'user', 'all' or positive integer";
	}
	return $feedback;
}
