<?php
/*
v6.79	22/9/15	Added outlaw_key to Tweets widget.

v6.78	9/4/15	Changing Tweets widget to use either script - $params['method'] = approved|outlaw
				(Outlaw version currently hardwired for Carplus!) Need to give it another parameter for the key.

v6.76	27/2/15	Added param 'omit_self' to list widget.

v6.69 	16/9/14	Add speed and interval params to slideshow

v6.64	 3/3/14	Added 'w' to format param to allow display of author in list widget.	

v6.63	 3/3/14	List and Vscroller widgets now have $params: 
				'orderby' - order posts by date, title or randomly.
				'date_field' - specify a custom-field to fetch the mySql date from. (And fixes future and order.)
				'dateformat' - specify a format for the date - including a lead-in if you escape all the characters.
				Extensive changes to get_catposts(), List, Vscroller and widget_default_specs.php. Should all
				be backwards compatible.
				
v6.62 26/2/14	Introduce $params['orderby'] and pic_size to vscroller

v6.61 24/2/14	Introduce $params['logged_in'] - bail-out dependent on whether user is logged in

v6.60  7/2/14	Anon function doesn't work with PHP <5.3

v6.29	28/7/13	Slideshow: After a lot of faffing with SOP and WCTS versions have removed 'buttons' option as this is now 
				identical to 'tabs'.
				
v6.28	27/7/13	Slideshow now has text_type param

v6.27	26/7/13	Slideshow can now have layout=buttons [REMOVED v6.29]

v6.2	18/5/13	Slideshow: height and width now neither required nor used

v5.9	25/4/13	Added 'filter' param to list widget

v4.11	16/3/13	Added "inserts" to specs to control whether or not to show available items box

v4.1	26/1/13	Added selflinks and showtitles to slideshow
*/
$default_specs = array();

$region_html = array(); // v4.4 

$default_specs['post'] = array( 

	'display_name' => 'Post widget',
	
	'class_name' => 'Post_widget',
	
	'inserts' => true,
	
	'description' => "Lets you insert one or more posts or images, with the option of making the the whole thing into a link",

	'params' => array(
		'ids' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'ids',
			'paramdesc' => 'Comma-separated list of IDs of post(s) or image(s) to insert. See &lsquo;Available items&rsquo; below.'
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
		'link' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'link',
			'paramdesc' => 'Enter a url, and the widget becomes a link. For internal links use (eg) &lsquo;/about&rsquo;. Normally used with a single ID.'
			),
		'show_title' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'Whether or not to display the title of the post. Not recommended for images.'
			),
		'pic_size' => array(
			'default' => 'full',
			'reqd' => false,
			'validation' => '=thumbnail|small|medium|large|full',
			'paramdesc' => '(Images only): Which of the pre-defined image sizes to use.'
			),
		'type' => array(
			'default' => 'full',
			'reqd' => false,
			'validation' => '=full|excerpts',
			'paramdesc' => 'Whether to display the whole post, or just an excerpt with a &lsquo;Read more >&rsquo; link.'
			),
		'caption' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => '(Images only): Enter some text to use as a caption - or &lsquo;default&rsquo; if you want to use the caption specified in the media library.'
			), // 3.26  'default' or the literal string
		'logged_in' => array(
			'default' => false,
			'reqd' => false,
			'validation' => '=show|hide',
			'paramdesc' => 'Make display of post(s) dependent on whether user is logged in.'
			)
	)
);




$default_specs['list'] = array( 

	'display_name' => 'List widget',
	
	'class_name' => 'List_widget',
	
	'inserts' => true,
	
	'description' => "Lets you insert listings of posts by category in a variety of formats",

	'params' => array(
		'cat' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'cat',
			'paramdesc' => 'Comma-separated list of category IDs from &lsquo;Available items&rsquo; below. If IDs are not from &lsquo;Post categories&rsquo; list, appropriate post_type should be selected.'
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
		'type' => array(
			'default' => 'titles',
			'reqd' => false,
			'validation' => '=full|excerpts|titles',
			'paramdesc' => 'Pick whether you want the posts displayed in full, as excerpts or just the titles.'
			),
		'order' => array(
			'default' => 'desc',
			'reqd' => false,
			'validation' => '=desc|asc',
			'paramdesc' => 'Pick whether you want the posts displayed in ascending or descending (ie: latest first) order.'
			),
		'orderby' => array( // v6.63
			'default' => 'date',
			'reqd' => false,
			'validation' => '=date|title|rand', 
			'paramdesc' => 'Pick whether you want the posts ordered by date, title or randomly.'
			),
		/*
		'meta_key' => array( // v6.63
			'default' => null,
			'reqd' => false,
			'validation' => 'style', // '/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/'
			'paramdesc' => 'If orderby=&lsquo;meta_value&rsquo;, allows you to order the posts by the named meta_key.'
			),
		*/
		'date_field' => array( // v6.63
			'default' => false,
			'reqd' => false,
			'validation' => 'style', // '/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/'
			'paramdesc' => 'Allows you to specify a custom-field to fetch the date from.'
			),
		'date_format' => array( // v6.63
			'default' => false,
			'reqd' => false,
			'validation' => 'anything', // 'validate_date_format' needs adding to wf lib. NB: escape all lead-in text characters.
			'paramdesc' => 'Allows you to specify a format for the date - including a lead-in if you can be bothered to escape all the characters.'
			),
		'show_posts' => array(
			'default' => 5,
			'reqd' => false,
			'validation' => 'show_posts',
			'paramdesc' => 'Maximum number of posts to display. Default=5. To show all, use &lsquo;-1&rsquo;.'
			),
		'status' => array(
			'default' => 'publish',
			'reqd' => false,
			'validation' => '=publish|future|private',
			'paramdesc' => 'Select the status of posts to display. Default=publish. Private posts are only visible to logged-in users. Use &lsquo;future&rsquo; with events etc where the WordPress &lsquo;publication date&rsquo; has been changed to match the date of the event.'
			),
		'format' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'format',// slight variations from vscroller version - so validation is over-permissive
			'paramdesc' => "Include any of these codes: d=&nbsp;suppress&nbsp;the&nbsp;date. t=&nbsp;include&nbsp;the&nbsp;time. r=&nbsp;revealer&nbsp;headings. l=&nbsp;don&rsquo;t&nbsp;truncate&nbsp;excerpts. a=&nbsp;suppress&nbsp;title&nbsp;links. w=&nbsp;show&nbsp;author." // v6.64
			),
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
			),
		'paginate' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'When true, the number of posts per page is determined by the &lsquo;show_posts&rsquo; parameter.'
			),
		/*
		'taxonomy' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'taxonomy',
			'paramdesc' => 'Pick one of the available taxonomies (category groupings).'
			),
		*/
		'post_type' => array(
			'default' => 'post',
			'reqd' => false,
			'validation' => 'post_type',
			'paramdesc' => 'Pick one of the available post_types. Default=Post. Affects which categories can be displayed.'
		),
		'link' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'link',
			'paramdesc' => 'Enter a url. For internal links use (eg) &lsquo;/about&rsquo;. Requires &lsquo;linktext&rsquo; parameter to be set.'
		),
		'linktext' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'Enter &lsquo;heading&rsquo; to apply the link to the heading. Otherwise whatever you enter becomes the link text - which normally appears following the list of posts. Requires &lsquo;link&rsquo; parameter to be set (and possibly &lsquo;heading&rsquo; too).'
		),
		'pic_size' => array(
			'default' => 0,
			'reqd' => false,
			'validation' => '=thumbnail|small|medium|large|full',
			'paramdesc' => 'Which of the pre-defined image sizes to use when a Featured Image is specified. (To omit them, simply delete this parameter.)'
		),
		'filter' => array( // v5.9
			'default' => false,
			'reqd' => false,
			'validation' => 'filter',
			'paramdesc' => '(Developer option): Name of a custom function in widget_config.php to filter the initial list of posts returned.'
		),
		'omit_self' => array( // v6.76
			'default' => false,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'When true, the current post is omitted from the list.'
		),

	)
);




$default_specs['random'] = array( 

	'display_name' => 'Random text widget',
	
	'class_name' => 'Random_widget',

	'inserts' => true,

	'description' => "Lets you insert a short block of text that changes randomly each time you visit the page. Often used for facts or quotes. NB: The various versions of text are all entered on separate lines in the one post (as specified by the &lsquo;id&rsquo; parameter), with all lines separated by a single blank line.",
	
	'params' => array(	
		'id' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'id',
			'paramdesc' => 'Enter the ID of the post containing the set of text blocks. See &lsquo;Available items&rsquo; below.'
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
			)
	)
);


			
			
$default_specs['slideshow'] = array( 

	'display_name' => 'Slideshow widget',
	
	'class_name' => 'Slideshow_widget',
	
	'inserts' => true,

	'description' => "Lets you insert a set of posts or images as a slideshow. Often used on the home page, with each slide being a link to another part of the site",
	
	'params' => array(
		'ids' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'ids',
			'paramdesc' => 'Comma-separated list of IDs of post(s) or image(s) to insert. See &lsquo;Available items&rsquo; below.'
			),
		/* v6.2 height and width now neither required nor used
		'height' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'posint',
			'paramdesc' => ''
		),
		'width' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'posint',
			'paramdesc' => ''
		),
		*/
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
		
		'layout' => array( // NB: was 'format' ///////////
			'default' => false,
			'reqd' => false,
			'validation' => '=default|tabs|thumbnails', // buttons
			'paramdesc' => 'Pick one of the pre-defined layouts for this widget.'
			),
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
			),
		
		'pic_size' => array(
			'default' => 'full',
			'reqd' => false,
			'validation' => '=thumbnail|small|medium|large|full',
			'paramdesc' => 'Which of the pre-defined image sizes to use when a Featured Image is specified. Default=full.'
		),
		
		'selflinks' => array( // added v4.1
			'default' => false,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'When true, overrides any link specified in the slide post, and instead links back to the self-same post.'
		),
		
		'text_type' => array( // v6.28
			'default' => 'full',
			'reqd' => false,
			'validation' => '=full|excerpts|titles|none',
			'paramdesc' => 'How much of the post text to display.'
			),

		'showtitles' => array( // added v4.1
			'default' => false,
			'reqd' => false,
			'validation' => 'bool',
			'paramdesc' => 'When true, includes post title with slide text as an &lt;h4> element.'
		),
		
		
		
		
		'speed' => array( // added v6.69
			'default' => 500,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Transition time in milliseconds for the slide to change. Default=500 (half a second).'
		),		
		'interval' => array( // added v6.69
			'default' => 5000,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'The pause (ie: reading time) in milliseconds between slide changes. Default=5000 (5 seconds).'
		)		

		/* // NB: This hasn't been implemented yet - although function exists
		'wordmax' => array(
			'default' => 'full',
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => '.'
		)
		*/
	)
);

	
	
				
$default_specs['vscroller'] = array( 

	'display_name' => 'Vscroller widget',
	
	'class_name' => 'Vscroller_widget',
	
	'inserts' => true,

	'description' => "Lets you insert a set of posts as vertically sliding panels. Often used as links to other parts of the site",
	
	'params' => array(
		'ids' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'ids',
			'paramdesc' => 'Comma-separated list of IDs of post(s) or image(s) to insert. See &lsquo;Available items&rsquo; below.'
			),
		'ppv' => array(
			'default' => 1,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Stands for &lsquo;posts per view&rsquo; and specifies the number of posts that are visible at each step. Default=1.'
		),
		'speed' => array(
			'default' => 500,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Transition time in milliseconds for the slide to change. Default=500 (half a second).'
		),		
		'interval' => array(
			'default' => 5000,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'The pause (ie: reading time) in milliseconds between slide changes. Default=5000 (5 seconds).'
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
		
		'img_size' => array(
			'default' => 100,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Resizes the existing square thumbnails for use in the posts. Default=100 (ie: 100px square).'
		),
		
		'cat' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'cat',
			'paramdesc' => 'Comma-separated list of category IDs from &lsquo;Available items&rsquo; below. If IDs are not from &lsquo;Post categories&rsquo; list, appropriate post_type should be selected.'
			),
		'post_type' => array(
			'default' => 'post',
			'reqd' => false,
			'validation' => 'post_type',
			'paramdesc' => 'Pick one of the available post_types. Default=Post. Affects which categories can be displayed.'
		),
		'type' => array(
			'default' => 'titles',
			'reqd' => false,
			'validation' => '=full|excerpts|titles',
			'paramdesc' => 'Pick whether you want the posts displayed in full, as excerpts or just the titles.'
			),
		'order' => array(
			'default' => 'desc',
			'reqd' => false,
			'validation' => '=desc|asc',
			'paramdesc' => 'Pick whether you want the posts displayed in ascending or descending (ie: latest first) order.'
			),
		'orderby' => array( // v6.62
			'default' => 'date',
			'reqd' => false,
			'validation' => '=date|title|rand',
			'paramdesc' => 'Pick whether you want the posts ordered by date, title or randomly.'
			),
		'date_field' => array( // v6.63
			'default' => false,
			'reqd' => false,
			'validation' => 'style', // '/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/'
			'paramdesc' => 'Allows you to specify a custom-field to fetch the date from.'
			),
		'date_format' => array( // v6.63
			'default' => false,
			'reqd' => false,
			'validation' => 'anything', // 'validate_date_format' needs adding to wf lib. NB: escape all lead-in text characters.
			'paramdesc' => 'Allows you to specify a format for the date - including a lead-in if you can be bothered to escape all the characters.'
			),
		'show_posts' => array(
			'default' => 5,
			'reqd' => false,
			'validation' => 'show_posts',
			'paramdesc' => 'Maximum number of posts to display. Default=5. To show all, use &lsquo;-1&rsquo;.'
			),
		'status' => array(
			'default' => 'publish',
			'reqd' => false,
			'validation' => '=publish|future|private',
			'paramdesc' => 'Select the status of posts to display. Default=publish. Private posts are only visible to logged-in users. Use &lsquo;future&rsquo; with events etc where the WordPress &lsquo;publication date&rsquo; has been changed to match the date of the event.'
			),
		'format' => array( 
			'default' => false,
			'reqd' => false,
			'validation' => 'format',// slight variations from get_catposts versions - so validation is over-permissive
			'paramdesc' => "Include any of these codes: d=&nbsp;suppress&nbsp;the&nbsp;date. t=&nbsp;include&nbsp;the&nbsp;time. r=&nbsp;revealer&nbsp;headings. l=&nbsp;don&rsquo;t&nbsp;truncate&nbsp;excerpts. a=&nbsp;suppress&nbsp;title&nbsp;links. h=&nbsp;suppress&nbsp;titles."
			),
		'pic_size' => array( // v6.62
			'default' => 0,
			'reqd' => false,
			'validation' => '=thumbnail|small|medium|large|full',
			'paramdesc' => 'Which of the pre-defined image sizes to use when a Featured Image is specified. (To omit them, simply delete this parameter.)'
		),
		'filter' => array( // v5.9
			'default' => false,
			'reqd' => false,
			'validation' => 'filter',
			'paramdesc' => '(Developer option): Name of a custom function in widget_config.php to filter the initial list of posts returned.'
		)
	)
);			
			

$default_specs['tweets'] = array( 

	'display_name' => 'Tweets widget',
	
	'class_name' => 'Tweets_widget',

	'inserts' => false,
	
	'description' => "Lets you insert the latest tweets from a Twitter account",

	'params' => array(	
		'username' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'tweets_username',
			'paramdesc' => 'Insert a valid Twitter username.'
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
		'count' => array(
			'default' => 3,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'How many tweets to display (latest first).'
			),
		'link' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'link',
			'paramdesc' => 'Enter a url, and a link is created. Usually links to the relevant Twitter page.'
		),
		'linktext' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'Enter &lsquo;heading&rsquo; to apply the link to the heading. Otherwise whatever you enter becomes the link text - which normally appears following the list of posts. Requires &lsquo;link&rsquo; parameter to be set (and possibly &lsquo;heading&rsquo; too).'
		),
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'text',
			'paramdesc' => 'A heading for the widget. Usually presented as an &lt;h2> element.'
		),	
		'method' => array( // v6.78
			'default' => 'approved',
			'reqd' => false,
			'validation' => '=approved|outlaw',
			'paramdesc' => 'The method used to fetch the tweets - either a Twitter-approved one, or a third-party hack that allows more options for layout.'
		),	
		'outlaw_key' => array( // v6.79
			'default' => '',
			'reqd' => false,
			'validation' => 'text', // '349478108283408385'
			'paramdesc' => 'The twitter API key - only needed for the outlaw version. All digits?'
		),	
	)
);			
			
			
$default_specs['popup'] = array(
 
	'display_name' => 'Popup widget',
	
	'class_name' => 'Popup_widget',
	
	'inserts' => true,

	'description' => "Lets you insert an image with a panel of (graphic) text that slides into view when you mouse over it. Often used as a graphic link to another page",
	
	'params' => array(
		'back_id' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'id',
			'paramdesc' => 'Enter the ID of the background image. See &lsquo;Available items&rsquo; below.'
		),		
		'front_id' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'id',
			'paramdesc' => 'Enter the ID of the sliding text image. See &lsquo;Available items&rsquo; below.'
		),		
		'start_height' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'posint',
			'paramdesc' => 'This is the number of vertical pixels of the sliding image that you want visible at the start. Usually used for a heading for the rest of the text.'
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
		'link' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'link',
			'paramdesc' => 'Enter a url, and the widget becomes a link. For internal links use (eg) &lsquo;/about&rsquo;.'
		),
		'width' => array( // v5.12
			'default' => false,
			'reqd' => false,
			'validation' => 'posint',
			'paramdesc' => 'Specify a pixel-width for the widget. Enter a positive integer.'
		)
	)
);




$default_specs['reveal'] = array(
 
	'display_name' => 'Reveal widget',
	
	'class_name' => 'Reveal_widget',
	
	'inserts' => false,

	'description' => "Lets you create a collapsible chunk of text that&rsquo;s revealed/hidden by clicking its heading",
	
	'params' => array() // no params!
);

//Wf_Debug::stash(array("default_specs"=>$default_specs));


//if(!isset($active_plugins)) { // v6.3 gets set as string by Ajax
if (class_exists('Wf_Widget')) { // ie: AJAX Keep Out!
	/* v6.60  doesn't work with PHP <5.3
	$active_plugin_dirs = array_map(function($value) { // v6.3
		$path_array = explode('/',$value);
		return $path_array[0];
	}, get_option('active_plugins'));
	*/	
	// v6.60
	/* //v6.60 replaced now with function wf_get_active_plugin_dirs($value) in wf_widgets.php
	function wf_get_active_plugin_dirs($value) {
		$path_array = explode('/',$value);
		return $path_array[0];
	}
	*/
	$active_plugin_dirs = array_map('wf_get_active_plugin_dirs', get_option('active_plugins')); //v6.60
	$active_plugins = implode(',',$active_plugin_dirs); // should now both be in the same format eg: eg: syntaxhighlighter,wf_library,wf_widget_forms,wf_widgets
}

$default_specs = get_plugin_specs($default_specs,$active_plugins); // v6.3

/*	This function checks the plugins folder for any other folders starting with "wf_" and includes any file named default_specs.php.
	It shouldn't matter too much if the plugin is not activated? 
	Well actually it does because these widgets then appear on the list of available widgets! // v6.2

*/
function get_plugin_specs($default_specs, $active_plugins) { // v6.3
	if(!defined('WF_WIDGETS_PLUGIN_PATH')) { // v4.3
		define( 'WF_WIDGETS_PLUGIN_PATH', dirname( __FILE__ ) );
	}
	//define( 'PLUGIN_PATH', dirname(dirname( __FILE__ ) ));
	define( 'PLUGIN_PATH', dirname(WF_WIDGETS_PLUGIN_PATH)); // v5.4
	//Wf_Debug::stash(array("PLUGIN_PATH"=>PLUGIN_PATH));
	
	//$active_plugins = get_option('active_plugins'); // typical array item: "wf_widget_forms/wf_forms.php" // v6.2	
	
	if ($handle = opendir(PLUGIN_PATH)) {
		/* This is the correct way to loop over the directory. */
		while (false !== ($entry = readdir($handle))) {
			if(substr($entry,0,10) == "wf_widget_" && strpos($active_plugins,$entry) !== false){ // v6.3
			//if(substr($entry,0,10) == "wf_widget_") {
				require(PLUGIN_PATH."/".$entry."/default_specs.php"); 
			}
		}	
		closedir($handle);
		
		return $default_specs;
	}
}


function set_styles($specs, $vstring) {
	foreach($specs as $widget => $widget_spec) {
		if(isset($widget_spec['params']['style'])) {
			$specs[$widget]['params']['style']['validation'] = $vstring; // eg: '=scruffy|neat'
		}
	}
	return $specs;
}

