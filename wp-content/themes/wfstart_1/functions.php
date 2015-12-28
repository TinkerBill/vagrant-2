<?php
// THIS IS THE FUNCTIONS.PHP FILE FOR Hubweb1 DEV VERSION ////////////////////////////////////////////   

/*


*/ 
 
 

/* The most notable security risk to using FirePHP is that anyone with the firephp extension enabled can you use it to see any information you are dumping to the client. My advice is to immediately comment out firebug logging calls once you are finished debugging the application, especially if you are dumping out raw SQL syntax to the client.
*/
//$firephp = FirePHP::getInstance(true);


function WFB() {
	if(!class_exists('FB')) {
		return;
	}
	$numargs = func_num_args();
    $arg_list = func_get_args();
	if($numargs == 1) {
		FB::info($arg_list[0]);
	} elseif($numargs == 2) {
		FB::info($arg_list[0],$arg_list[1]);
	}
}

// ALWAYS, ALWAYS, ALWAYS VISIT PERMALINKS PAGE AFTER CREATING A NEW POST-TYPE!!!!! 

date_default_timezone_set('Europe/London');

WFB(ERROR_LOG_PATH,'ERROR_LOG_PATH');

 
if ( !defined('THEME_FOLDER_URL') ) { // called STYLESHEETURL on some of our other sites - but this is more understandable
	define('THEME_FOLDER_URL', dirname( get_bloginfo('stylesheet_url') )); // NB NEEDED BY INCLUDES
}


// INCLUDES CAN GO HERE ////////////////////////////////////////

//include 'cpt_functions.php';




add_action('after_setup_theme', 'load_before_lib', 9);

function load_before_lib() {
	
	// SET UP CONSTANTS AND VARIABLES ---------------------------------------------------------------
	
	$uri_bits = explode("?",$_SERVER['REQUEST_URI']); // splits into url-path from root and query string
	define('SELF_URL', 'http://'.$_SERVER['SERVER_NAME'].$uri_bits[0]);
	//define('BASE', 'http://'.$_SERVER['SERVER_NAME']. '/');
	if(!defined('ROOT_PATH')) {
		define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']."/live"); // /home/wingfing/public_html/leeds4change.graphicdesignleeds.info
	}
	
	if($uri_bits[0] == '/wf_data/') { add_action('init', 'wf_get_versions'); } 
	
	
	
	
	
	// INSERT HERE ANY FUNCTIONS THAT NEED TO OVERWRITE DEFAULT LIBRARY VERSIONS ///////////////////
	
	function wf_linkfix($link) { // adjust so that links to (say) '/about' work OK
		return $link;
	}
	
	function wf_filter_content($content) {   
		$content=str_ireplace("<a name=", "<a class='anchor' name=",$content); // ensures a bit of vertical space before jumped-to heading
		$content = str_replace('<p><!--:en--><br />', '', $content); // removes special comments introduced by language plugin
		$content = str_replace('<!--:-->', '', $content);
		$content = str_replace("<p><img","<img",$content); // 3.33
		$content = str_replace(" /></p>"," />",$content); // 3.33
		$content = str_replace("<p><object","<object",$content); // these 2 added from Tidal to enable video embedding  3.28
		$content = str_replace("</object></p>","</object>",$content);
		$content=str_replace("/".CMS_FOLDER."/wp-content/","/wp-content/",$content); // this nonsense gets created when referring to media // v3.47
		if(SUBDOMAIN_FOLDER != '') {
			$content=str_replace('="/wp-content/','="/'.SUBDOMAIN_FOLDER.'/wp-content/',$content);
		}
		return $content;	
	}	
	add_filter('the_content','wf_filter_content');
	
	
		
	function postProcessSidepost($sidepost) { 
		$text = wpautop($sidepost); // turns 2 linebreaks into a paragraph
		$text = str_replace('<p><!--:en--><br />', '', $text); // removes special comments introduced by language plugin
		$text = str_replace('<!--:-->', '', $text);
		return  $text;
	}
	
	
}


// WF_LIBRARY LOADS HERE




add_action('init', 'lib_move_stuff', 11);

function lib_move_stuff() { // stuff that broke when I moved wf_lib to a plugin

	// MISCELLANEOUS /////////////////////////////////////////////////////////////////////////////
	
	
	// Specify users (by login name) who will see debug info...
	if(class_exists('Wf_Debug')) { //v6.15
		Wf_Debug::$users = array(
			'Wingfinger',
			'Bill',
			'admin',
		);
	}
	if(function_exists('set_up_mimic_content_filter')) { //v6.15
		set_up_mimic_content_filter();
	}
	
	
	if(!function_exists('widget_html')) {
		function widget_html($widget_class, $style, $qstring) {
			return "<p class='not_found'>Class ".$widget_class." not found. <strong>wf_library</strong> plugin not loaded.</p>";
		}
	}
	
	function get_region_html($region) {
		if(class_exists('Wf_Widget')) {
			return Wf_Widget::get_region_html($region);
		} else {
			return "Wingfinger Widgets plugin not loaded.";
		}
	}



	function new_excerpt_length($length) {
		return 25; //to change the number of words in an excerpt
	}
	add_filter('excerpt_length', 'new_excerpt_length');
	
	
	function new_excerpt_more($more) {
		  global $post;
		return ' <a class=more_link href="'. get_permalink($post->ID) . '">&#187; Read more...</a>';// to make the read more into a link to the post
	}
	add_filter('excerpt_more', 'new_excerpt_more');
	
		
	if(function_exists('search_request_filter')) {
		add_filter( 'request', 'search_request_filter' ); // wf_lib.php
	}
	
	if(function_exists('show_future_posts')) {
		add_filter('the_posts', 'show_future_posts');
	}
	
	add_theme_support('post-thumbnails');
	
	add_post_type_support( 'wf_snippet', 'comments' );
	add_post_type_support( 'wf_sitenote', 'comments' );
	
	

	// Specify which pages should be excluded from main menu
	function get_page_excludes() { // v2.19
		$excludes = array(
			// 1909 => 'Locations',
			// 2006 => 'Thank you',
		);
		return implode(',',array_keys($excludes));
	}

				
	
	// IMAGE SIZES /////////////////////////////////////////////////////////////////////////////
	
	
	global $content_width;
	$content_width = 740; // limits width of large size uploads - www.deluxeblogtips.com/2010/05/max-image-size-wordpress-theme.html
	
	if(false === get_option("medium_crop")) { // force WP to crop medium images: wordpress.org/support/topic/force-crop-to-medium-size
		add_option("medium_crop", "1");
	} else {
		update_option("medium_crop", "1");
	}
	if(false === get_option("large_crop")) { // force WP to crop large images: wordpress.org/support/topic/force-crop-to-medium-size
		add_option("large_crop", "1");
	} else {
		update_option("large_crop", "1");
	}

	
}




// LOAD SCRIPTS AND STYLES  /////////////////////////////////////////////////////////////////////////////////////


   
function wf_enqueue_assets() { 
	wp_enqueue_style('font_exasap_css',THEME_FOLDER_URL.'/fonts/exasap/exasap.css');
	wp_enqueue_style('awesome_css', get_template_directory_uri().'/fonts/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('hubweb_css',THEME_FOLDER_URL.'/css/hubweb5.css'); // v2.26 now contains contents of hometest.css
	//wp_enqueue_script('site_specific_js', get_template_directory_uri().'/scripts/site_specific.js', array('jquery'));	
}    
add_action('wp_enqueue_scripts', 'wf_enqueue_assets'); 


