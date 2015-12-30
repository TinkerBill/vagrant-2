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

 
function override_get_widget_accepting_posttypes() { 

	function get_widget_accepting_posttypes() {
		$post_types = array(
			'post' => 'Posts',
			'page' => 'Pages',
			'wf_snippet' => 'Snippets',
			'wf_sitenote' => 'Sitenotes',
		);
		return $post_types; 
	}
	
	function get_insertable_posttypes() {
		$post_types = array(
			'post' => 'Posts',
			'page' => 'Pages',
			'wf_snippet' => 'Snippets',
			'wf_sitenote' => 'Sitenotes',
			'attachment' => 'Images'
		);
		return $post_types; 
	}
	
	function posttype_accepts_debugbox($postType) { 
		return in_array($postType,array('post','page','wf_snippet','wf_sitenote',));
	}
}
add_action( 'after_setup_theme', 'override_get_widget_accepting_posttypes', 8);


//www.inmotionhosting.com/support/website/wordpress/heartbeat-heavy-admin-ajax-php-usage#disable-everywhere
//add_action( 'init', 'stop_heartbeat', 1 );

function stop_heartbeat() {
  wp_deregister_script('heartbeat');
  wp_register_script('heartbeat', false);
}
add_action( 'admin_enqueue_scripts', 'stop_heartbeat');

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
	
	
	function get_server_details() { 
		return array(
			"server" => 'localhost',
			"username"   => DB_USER,
			"password"   => DB_PASSWORD,
			"database"   => DB_NAME,
		);
	}

	
	
	
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
	
	function extra_markup($params) { // used to add extra divs etc to start of widgets
		
		if(strpos($params['style'],'featured_group') !== false) {
			$html = "<div class='fader'></div>
					<div id='featured_group'>
						<a href='/about/how-it-works/how-it-works-for-groups/' class='link'>Feature your Group here!</a>
					</div>";
		} elseif($params['region'] == 'home2_left') {
			$html = "<div id='join_groups'>
						<a class='link' href='/groups/'>Join the family</a>
					</div>
					<a class='coverlink' href='/groups/'>&nbsp;</a>";
		} else {
			$html ='';
		}
		return $html;
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



		
	if(function_exists('identify_dev_site_css')) { // v6.67
		add_action('wp_head','identify_dev_site_css');
		add_action('admin_head','identify_dev_site_css');
	}
	
	
		
	// restrict posts returned on category pages
	function search_filter($query) {
	  if ( !is_admin() && $query->is_main_query() ) {
		if ($query->is_category) {
		  $query->set('post_type', array('post'));
		}
	  }
	}
	add_action('pre_get_posts','search_filter');
	
		
	//remove auto loading rel=next post link in header
	//www.ebrueggeman.com/blog/wordpress-relnext-and-firefox-prefetching
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
	
	
	function better_button($formname) { // v3.73
		return "<input type='submit' class='submit' name='do_formsend' title='Submit my details' id='do_formsend_".sluggify($formname)."' value='Submit my details' />";
	}
	
	
	// Adds classes for post types and singles
	function l4c_page_class($post) {
		if(!isset($post)) {
			return;
		}
		$class = $post->post_name;
		if(in_array(get_post_type($post),array('l4c_location','l4c_event','l4c_group','l4c_action','l4c_skill'))) {
			$class .= ' single-l4c';							   
		}
		if(in_category('blog', $post)) {
			$class .= ' blog';							   
		}
		return $class;
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

		
		
	// ADJUST TITLES //////////////////////////////////////////////////////////////////////////////////

	
	// output pre-title and date/author lines for blog post
	function get_blog_pre_title_bits($post) {
		$author_id = $post->post_author;
		$author = "<span class='author'>by <strong>".get_userdata($author_id)->display_name."</strong></span>";
		$date = $post->post_date;
		$date_format = '\P\u\b\l\i\s\h\e\d j/n/y  g:ia';
		$date = date($date_format, strtotime($date));
		$date = str_replace(" ","&nbsp;",$date);
		return "<p class='date'>".$date.$author."</p>\n
		<p class='pre-title'>Blog</p>";	
	}
	
	
	
	function l4c_the_title($post) {
		$title = ($post->post_title);
		if(function_exists('remove_square_brackets')) {
			$title = remove_square_brackets($post->post_title); 
		}
		if($post->post_status == 'private') {
			$title = '<span>Private: </span>'.$title;
		}
		//d('$post->post_type',$post->post_type);
		if($post->post_type =='page' && function_exists('get_godfather')) {
			$godfather = get_godfather($post->post_id,'');
			//d('godfather',$godfather);
			$toplevel = array(
							1758 => 'About',
							1760 => 'Groups',
							1799 => 'Events',
							1762 => 'Actions',
							1764 => 'Resources'
						);
			if(in_array($godfather,array_keys($toplevel)) && $godfather != $post->post_id) {
				$title = '<span>'.$toplevel[$godfather].'</span>'.$title;
			}
		}
		return $title;
	}
	
	
		
		
	
	// USER FEEDBACK /////////////////////////////////////////////////////////////////////////////////////
	
	
	function get_navbar_user() { // the 'logged in as' bit to the right of the main menu
		global $wp_roles;
		global $post;
		$html = "
		<div id='navbar_user' class='l4c_green'>\n";
		
		if(is_user_logged_in()) {
			$current_user = wp_get_current_user(); // coz may just have been logged in as public
			$role_slug = $current_user->roles[0];
			//WFB($role_slug,'$role_slug from get_navbar_user()');
			$html .= "<p>Logged in as <strong>".$current_user->user_login."</strong>&nbsp; &nbsp;<a href='".wp_logout_url(home_url())."'>Logout</a></p>";	  
			/*
			<ul>
			  <li><a href='".get_edit_user_link()."'>Edit my details</a> |</li> 
			  <li><a href='".wp_logout_url(home_url())."'>Logout</a></li>
			</ul>";
			*/
		} else {
			$permalink = (is_front_page()) ? get_bloginfo('url').'/summary-page/' : get_permalink();
			$html .= "
			<p>
				<a href='".wp_login_url($permalink)."'>Log in</a> or 
				<a href='".wp_registration_url()."' title='Register'>Register</a>
			</p>";
		}
		$html .= "</div>";
		return $html;
	}
	
	
		
	// LOGIN FORM ////////////////////////////////////////////////////////////////////////////////////
	
	
	function wf_login_stylesheet() { 
		echo "<link rel='stylesheet' id='custom_wp_admin_css'  href='".THEME_FOLDER_URL. '/css/style-login.css'."' type='text/css' media='all' />";
	} 
	add_action( 'login_enqueue_scripts', 'wf_login_stylesheet' );
	
	
	
	function login_headertitle($login_headertitle) {
		return "Leeds for Change";
	}
	add_filter('login_headertitle', 'login_headertitle');

	
			
	
		
	
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

	
	// reduce max size of uploaded images to 432x202 (full size)
	//wordpress.stackexchange.com/a/76613/8397
	function l4c_handle_upload($params) {
		$arg = array(
			'mime_type' => 'image/png',
			'methods' => array(
				'resize',
				'save'
			)
		);
		$img_editor_test = wp_image_editor_supports($arg);
		
		if ($img_editor_test !== false) {
			//FB::info('mime_type and method(s) supported!');
		
			$filePath = $params['file'];
		
			if((!is_wp_error($params)) && file_exists($filePath) && in_array($params['type'], array('image/png','image/gif','image/jpeg'))) {
				$quality                        = 90;
				//list($largeWidth, $largeHeight) = array( get_option( 'large_size_w' ), get_option( 'large_size_h' ) );
				list($largeWidth, $largeHeight) = array(432, 202);
				list($oldWidth, $oldHeight)     = getimagesize( $filePath );
				list($newWidth, $newHeight)     = wp_constrain_dimensions( $oldWidth, $oldHeight, $largeWidth, $largeHeight );
				
				if($largeWidth > $oldWidth || $largeHeight > $oldHeight) {
					return $params;
				}
		
				$img = wp_get_image_editor( $filePath );
				unlink( $filePath );
				
				if(!is_wp_error($img)){
					$resizeImageResult = $img->resize( $largeWidth, $largeHeight, true ); // won't touch if smaller than this
										
					if ( !is_wp_error( $resizeImageResult ) ) {
							if(!$resizeImageResult) {
							return $params; // the image is smaller
						}
						$saved = $img->save($filePath);
					} else {
						$params = wp_handle_upload_error($filePath, $resizeImageResult->get_error_message());
					}
								
				} else {
					$params = wp_handle_upload_error($filePath, $img->get_error_message());
				} // if ( !is_wp_error( $img ) )
			
			} // if ( (!is_wp_error($params)) etc
		} // if ($img_editor_test !== false) 
		return $params;
	}
	//add_filter( 'wp_handle_upload', 'l4c_handle_upload' );
	
	
	function setup_image_sizes() {
		if( function_exists('add_theme_support') ) {
			add_theme_support('post-thumbnails');
		}
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'small', 210, 100, true ); 
		}
	
		function l4c_image_sizes($sizes){
			$custom_sizes = array(
				'small' => 'Small'
			);
			return array_merge( $sizes, $custom_sizes );
		}
	
		add_filter('image_size_names_choose', 'l4c_image_sizes');
	}
	//add_action( 'after_setup_theme', 'setup_image_sizes' );
	
	
	
}




// LOAD SCRIPTS AND STYLES  /////////////////////////////////////////////////////////////////////////////////////


  
// add a custom admin stylesheet - this will be global for admin and also login
function l4c_admin_assets() { // v6.25
	wp_enqueue_style('l4c_admin_css', get_template_directory_uri().'/css/l4c_admin.css');
	wp_enqueue_script( 'l4c_admin_js', get_template_directory_uri().'/scripts/l4c_admin.js', array('jquery'));
	wp_localize_script( 'l4c_admin_js', 'params', array(  // v2.7
		'ajax_url' => admin_url( 'admin-ajax.php' ), 
		'current_user' => get_current_user_id() )
	);
}
// add_action('admin_print_styles', 'l4c_admin_assets', 100 ); //v6.45
  
function wf_register_javascripts() {    
	wp_register_script( 'l4c_groups_js', THEME_FOLDER_URL."/scripts/l4c_groups.js", array('jquery','jquery-ui-widget','jquery-ui-core')); 
	wp_register_script( 'site_specific_js', get_template_directory_uri().'/scripts/site_specific.js', array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-position','jquery-ui-menu','jquery-ui-autocomplete'));
	wp_register_script( 'bootstrap_js', 'http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', array('jquery'));
}    
// add_action('wp_loaded', 'wf_register_javascripts', 1); 


// new function to load assets from child theme if they exist - else from parent theme
function wf_enqueue_style_hier($handle, $src_tail, $deps = null) { // 
	if(file_exists(get_stylesheet_directory_uri().$src_tail)) {
		wp_enqueue_style($handle, get_stylesheet_directory_uri().$src_tail, $deps); // version in child theme
	} else {
		wp_enqueue_style($handle, get_template_directory_uri().$src_tail, $deps); // version in parent theme
	}
}

// new function to load scripts from child theme if they exist - else from parent theme
function wf_enqueue_script_hier($handle, $src_tail, $deps = null) { // 
	if(file_exists(get_stylesheet_directory_uri().$src_tail)) {
		wp_enqueue_script($handle, get_stylesheet_directory_uri().$src_tail, $deps); // version in child theme
	} else {
		wp_enqueue_script($handle, get_template_directory_uri().$src_tail, $deps); // version in parent theme
	}
}


function wf_enqueue_assets() { 
	wf_enqueue_style_hier('font_exasap_css','/fonts/exasap/exasap.css');
	wf_enqueue_style_hier('awesome_css','/fonts/font-awesome/css/font-awesome.min.css');
	wf_enqueue_style_hier('bootstrap3_css','/bootstrap-3.3.1/dist/css/bootstrap.css'); // v2.26
	wf_enqueue_style_hier('after_bootstrap_css','/css/l4c_after_bootstrap.css'); // v2.26
	wf_enqueue_style_hier('hubweb_css','/css/hubweb5.css'); // v2.26 now contains contents of hometest.css
	wp_enqueue_script('respond_js', get_template_directory_uri().'/scripts/respond.min.js'); // v2.27 IE support
	wp_enqueue_script('html5shiv_js', get_template_directory_uri().'/scripts/html5shiv.min.js'); // v2.27 IE support
	//wp_enqueue_script('jq_watermark'); // v3.73
	//wp_enqueue_script('bootstrap_js'); 
	wp_enqueue_script('bootstrap_js','http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', array('jquery'));
	wf_enqueue_script_hier('site_specific_js','/scripts/site_specific.js', array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-position','jquery-ui-menu','jquery-ui-autocomplete'));
}    
add_action('wp_enqueue_scripts', 'wf_enqueue_assets'); 


