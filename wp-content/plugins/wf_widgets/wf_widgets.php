<?php
/*
Plugin Name: Wingfinger Widgets plugin  
Plugin URI: http://www.wingfinger.co.uk
Description: A plugin for handling Wingfinger widgets
Version: 6.80
Author: Wingfinger
Author URI: http://www.wingfinger.co.uk
License: It's copyright! 
*/

/*
This program is NOT free software; if you want to use it, please contact
info[at]wingfinger.co.uk for details of pricing. Thankyou.

v6.80	3/10/15	Added bug trap in function get_regional_inheritance($generations,$region) because 1,000 + daily warnings in error log!
				
				wf_widgets.php: function get_catposts($args,$params) moved from a function to a method in abstract class Wf_Widget because 
				(a) it's used by both List and Vscroller widgets and (b) overloading a function (as opposed to a method) doesn't 
				allow as much control over what gets overwritten. (Eg: extending Lists widget to ToDo widget causes all lists to 
				have ToDo junk added!

v6.79	22/9/15	Added outlaw_key to Tweets widget.

v6.78	9/4/15	Changing Tweets widget to use either script - $params['method'] = approved|outlaw
				(Outlaw version currently hardwired for Carplus!) Need to give it another parameter for the key.

v6.77	30/3/15	Used remove_square_brackets() in get_catposts()

v6.76	27/2/15	Added param 'omit_self' to list widget. Tried unsuccessfully to add $raw_params['host_post'] 
				to __construct.

v6.75	9/1/15	Now using get_insertable_posttypes() in slideshow widget (see v6.52). Carplus slideshow was failing
				because of bbPress post-types.

v6.74	3/1/15	WP 4.1 update problem. 
				Latest jQuery UI 1.11 Sortable Widget breaks the interface because it changes <tr class="vscroller">
				to <tr class="vscroller ui-sortable-handle"> - meaning that we can't extract the wigetType so easily.
				Oddly, the first tab never(?) adds "ui-sortable-handle" to the rows - even though they sort nicely.

v6.73  18/10/14	SITE-BREAKER: $catfather is now called $catfather_array and is defined in widget_config.php
				Major changes in wf_library v6.65 to get_godfather() which now uses get_generations() to do all the work.
				Other changes in wf_widgets.php, widgety_functions.php, index.php
				
				Not obvious if it's better to turn inheritance on for posts with a catfather, or turn it off. 
				Probably means that we need another parameter in the catfather_array. Or could we do it on a 
				per-region basis in wf_admin.js, where nearly all the cancel inheritance stuff happens?

v6.72  10/10/14	Added 'wf_pagination_summary' filter to static function wf_paginate_list() in frontend.php for Amy.
				Params: $summary,$countposts,$current_page,$max. 
				See example in action at www.graphicdesignleeds.info/wingtheme/widgets/lists-widget

v6.71	25/9/14	Added delay for validation in wf_admin.js when OK pressed.

v6.70 	17/9/14	Slideshow widget: init_slideshow.js - exit from slideshow if just one slide. Allows us to set up
				static pic with caption as in slideshow. See frontpage of L4C.

v6.69 	16/9/14	Add speed and interval params to slideshow

v6.68 	20/8/14	List widget post_status corrected to 'publish from 'published'! Also, 'type'=>'DATETIME' added to
				$args['meta_query'].

v6.67 	12/8/14	Was getting errors "should not be called statically", so made various methods static in 
				wf_widgets.php and frontend.php

v6.66 	23/4/14	Added 'jquery-ui-dialog' as dependency to wf_admin.js
				Streamlined get_regional_inheritance() and changed so it sets widget_type == 'cancel'
				admin.php: Streamlined wf_widget_box_contents(). Added fn empty_or_cancelled() and changed 
				conditions to reflect use of left_cancel etc

v6.65	9/4/14	Added extra_markup($params) to slideshow, vscroller, tweets, popup and random

v6.64	 3/3/14	Added 'w' to format param to allow display of author in list widget.	

v6.63	 3/3/14	List and Vscroller widgets now have $params: 
				'orderby' - order posts by date, title or randomly.
				'date_field' - specify a custom-field to fetch the mySql date from. (And fixes future and order.)
				'dateformat' - specify a format for the date - including a lead-in if you escape all the characters.
				Extensive changes to get_catposts(), List, Vscroller and widget_default_specs.php. Should all
				be backwards compatible.

v6.62 26/2/14	Introduce $params['orderby'] and pic_size to vscroller. Various other changes to frontend.php and
				widget_default_specs.php to bring vscroller more in line with list widget. 
				Added autopause: true to init_vscroller.js - to no avail.

v6.61 24/2/14	Introduce $params['logged_in'] - bail-out dependent on whether user is logged in

v6.60  7/2/14	Anon function in widget_default_specs.php and admin.php doesn't work with PHP <5.3
				Replaced now with function wf_get_active_plugin_dirs($value) in wf_widgets.php
		6/2/14	Adding "Break inheritance?" checkbox.

v6.59	5/2/14	Check for class before instantiating update checker - in case wf_lib is not activated.

v6.58	4/2/14	Added update checker.

v6.57	2/2/14	Moved wf_admin.js to scripts folder.

v6.56	30/1/14	Tweaks to widget_base.css to allow better presentation of editlinks in Carplus.

v6.55  9/12/13	Constants now defined in widget_common() because "The plugins_url() function should not be called in the 
				global context of plugins, but rather in a hook like 'init' or 'admin_init'."
				
				Also, WF_WIDGETS_PLUGIN_URL was giving stupid url in Reiki site. Eventually tracked down to problem with 
				symlinks and __FILE__. See core.trac.wordpress.org/ticket/16953#comment:40
				Fixed by adding realpath() in wp-config.php
				
				Added "Selecting doesn't do anything" line to Available items box in admin.php

v6.54  5/12/13	Added do_shortcode to "more" excerpts for 365leedsstories

v6.53  30/11/13	Changed function list_wf_widgets($regions) and function get_regional_inheritance($generations,$region)
				so that $generations only gets calculated once.
				
				Added #twitter_fetcher to tweets widget

v6.52  30/10/13	Now using get_insertable_posttypes() in post widget. Epilepsy site kept losing widgets because of 
				tu post types. Changes to functions.php (if override required), frontend.php and wf_widgets.php. 
				Function posttype_accepts_widgets($postType) and default get_insertable_posttypes() both start
				from new function get_widget_accepting_posttypes(). Also posttype_accepts_debugbox(). 
				All except posttype_accepts_widgets() can be overridden in functions.php.

v6.51 25/10/13	Fixed get_excerpt_or_full() so that it doesn't complain when inheritance is cancelled

v6.50	6/10/13	Disabled param_delete is now disabled! Also adjusted z-index of tooltips.

v6.49	2/10/13	Random widget: replaced single return with <br/>
				Changed get_godfather() so that $catfather gets treated as an array  

v6.48	24/9/13	Moved setting of $raw_params['widget'] from function wf_widgets() to function __construct()
				so that it gets set for hardwired widgets.

v6.47	23/9/13	Found a couple of instances in wf_widgets.php of class endlink (deprecated). Added in coverlink as well.

				Specifying appropriate regions list for a post/page: Removed Wf_Widget::$regions and replaced with 
				function get_regions_list($post) defined in widget_config.php. Files affected: wf_widgets.php and
				admin.php.

v6.46	8/9/13	Added div#credit_wrap to function output_a_post() and tweaked widget_base.css. Solves
				problem of camera getting dropped on top of a caption.

v6.45	5/9/13	In admin.php: loading wf_widget_admin.css later (100) so it comes after the default wp styles.
				In wf_admin.js: added dialogClass: 'wp-dialog' to confirm dialog to fix z-index issue.
				Various changes to wf_widget_admin.css to fix WP3.6 problems.

v6.44	4/9/13	Changed function wf_widgets() in attempt to resolve WCTS  errors "Cannot use string offset as 
				an array in ... wf_widgets.php on line 175" (= the 'disinherit' line)

v6.43	2/9/13	Added .slideshow_widget to all jQuery selectors (to prevent clashes with custom slideshows).

v6.40	15/8/13	Changed add_wf_widget_box($postType) in admin.php so that it now checks pluggable function 
				posttype_accepts_widgets($postType). Same with save_wf_widget_metadata(). Default version of
				posttype_accepts_widgets() is now in wf_widgets.php
				
				Changed function get_regional_inheritance() so that it deviates from normal hierarchical behaviour
				only when $catfather is set. Because most tu posttypes give true for is_single().

v6.38	10/8/13	Disabled widgets for archive pages - because there doesn't seem to be an easy way of specifying them
				and they cause a fatal error.

v6.30 	29/7/13	Slideshow: changed .slidethumb and .darkner divs to spans. IE7/8 doesn't like inline-block on divs.

v6.29	28/7/13	Slideshow: After a lot of faffing with SOP and WCTS versions have removed 'buttons' option as this is now 
				identical to 'tabs'.

v6.28	27/7/13	Slideshow and Post: Added 'page' as a possible posttype - and 'page' to the 'Available items' box. 

v6.27	26/7/13	Moved old wf_lib.js to new files in plugins folder

v6.26	17/7/13	Check that current user can edit this post in function wf_editlink($post_id)

v6.25	13/7/13	Added function add_wf_debug_dashboard_box() to admin.php so we can see debugging info in dashboard

v6.21	1/7/13	Added do_shortcode() to function get_excerpt_or_full - so that we can put reveals into Post widgets.

v6.17	26/6/13	 Defined WF_PLUGINS_DIR_PATH

v6.12	14/6/13	Now checking for is_search() in function setup_region_html().

v6.5	24/5/13	Implemented $active_plugins for json_validate.php. Confirmed that it's OK to drop validation functions into
				default_specs.php for (eg) wf_widget_users. Comments widget now working with new interface.

v6.3	20/5/13	File admin.php now creates div#active_plugin_dirs which contains comma-separated list of active plugin directories.
				Ajax sends this to json_specs.php. This allows function get_plugin_specs($default_specs, $active_plugins) in 
				widget_default_specs.php to filter out the specs for non-active widgets.

v6.2	18/5/13	Changed function tidy_params(). No longer makes post_type into an array.
				Removed call to Wf_Widget::wf_widgets() from index.php. Now hooks onto template_redirect

v5.12	30/4/13	Added $list to output_a_post() so we can turn off <!--more--> behaviour with format 
				Vscroller: Added call to get_excerpt_or_full() so we can use excerpts.

v5.9	25/4/13	Added static function wf_editlink($post_id) to wf_widgets.php to allow editing of widget inserted items
				when user has relevant capability. Now using it in posts and lists.

v5.7	23/4/13	Added function get_endlink($params) to wf_widgets.php and we're now using it in lists and tweets.

v5.3	14/4/13	Added static function get_region_html($region). This allows us to use previously undeclared regions on special pages.
				Makes page templates easier to create. (See Wingtheme "versions" page.)

v4.12	9/4/13	function get_regional_inheritance() changed to return current and inherited widgets, for use
				in new widget interface. Changed function wf_widgets($regions) to match

v4.4	15/2/13	"$regions_html" changed to "$region_html" to be consistent with established usage in index.phps
				
				$region_html is now set in widget_default_specs.php and widget_config.php


v4.3	12/2/13	Dealt with undefined linktext warning (?)



*/



function default_posttype_functions() { // v6.52
	if(!function_exists('get_widget_accepting_posttypes')) { // hoping we can override this in functions.php YES
		function get_widget_accepting_posttypes() { // 6.40
			$post_types = array(
				'post' => 'Posts',
				'page' => 'Pages'
			);
			$pt_objects = get_post_types( array('_builtin'=> false),'objects'); // v6.2 now returns objects
			foreach($pt_objects as $pt => $pt_object) {
				$post_types[$pt] =  $pt_object->label;
			}
			return $post_types; // more or less everything except attachment
		}
	}
	
	if(!function_exists('get_insertable_posttypes')) { // hoping we can override this in functions.php YES
		function get_insertable_posttypes() { // 6.40
			$post_types = get_widget_accepting_posttypes(); // v6.52
			$post_types['attachment'] =  'Images';
			return $post_types; // in a fairly sensible order. Items can be subsequently unset or edited // v6.2
		}
	}
	
	function posttype_accepts_widgets($postType) {  // v6.52
		return in_array($postType, array_keys(get_widget_accepting_posttypes()));
	}
	
	if(!function_exists('posttype_accepts_debugbox')) {  // v6.52
		function posttype_accepts_debugbox($postType) {  // v6.52
			return posttype_accepts_widgets($postType);
		}
	}
}
add_action( 'init', 'default_posttype_functions', 12 ); // v6.52



add_action( 'init', 'widget_common', 1 );

function widget_common() {
	
	// v6.55 moved here from global because:
	// The plugins_url() function should not be called in the global context of plugins, but rather in a hook like "init" or "admin_init" 
	define( 'WF_WIDGETS_PLUGIN_PATH', dirname( __FILE__ ) );
	define( 'WF_WIDGETS_PLUGIN_URL', plugins_url( '', __FILE__ ) ); // v6.55  See note about odd urls that are sometimes generated 
	define( 'WF_WIDGETS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
	define( 'WF_PLUGINS_DIR_PATH', dirname(dirname( __FILE__ )) ); // v 6.17
	//echo WF_WIDGETS_PLUGIN_URL; 
	
	// v6.58 already loaded by wf_lib
	if(class_exists('PluginUpdateChecker')) { // v6.59 - just in case
		$UpdateChecker = new PluginUpdateChecker(
			'http://www.graphicdesignleeds.info/lib/wf_widgets.json',
			__FILE__,
			'wf_widgets'
		);
	}
	
	function wf_get_active_plugin_dirs($value) { //v6.60
		$path_array = explode('/',$value);
		return $path_array[0];
	}
	
	

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
		abstract class Wf_Widget {
		public $region;
		public $params;
		public $widget_type;
		public $shortcode_content = null; // content of enclosing shortcode gets passed to 2nd param of function shortcode_process()
		protected $can_shortcode;
		protected $default_params = array(); // param defaults for current widget
		
		static $current_specs; // complete set of current specs for all widgets
		static $region_html; // v4.4
		static $catfather_array; // v6.73
		
		public static function init() {
			require_once( dirname( __FILE__ ) . '/widget_default_specs.php' );
			require_once( dirname(dirname( __FILE__ )) . '/files/wf_widgets/widget_config.php' );// v5.2
			Wf_Widget::$current_specs = $current_specs;
			Wf_Widget::$region_html = $region_html; // v4.4
			Wf_Widget::$catfather_array = (isset($catfather_array)) ? $catfather_array : array(); // v6.73
			$widget_keys = array_keys($current_specs);
			foreach($widget_keys as $widget) {
				add_shortcode('shortcode_'.$widget, array(__CLASS__,'shortcode_process'));
			}
			return;
		}		
			
		public static function add_specs($widget_type, $specs){
			Wf_Widget::$current_specs[$widget_type] = $specs;
		}
		
		public function __construct($region,$widget_type,$data) { //$region,$qstring
			//global $post; //v6.76
			$this->region = $region;  
			if($region == 'shortcode') {
				$raw_params = $data;// data is atts
			} else {
				$raw_params = trim_parse($data); // data is qstring //v6.4 Was $this->trim_parse
			}
			$raw_params['widget'] = $widget_type; // v6.48  - coz it was getting lost for hardwired widgets
			//$raw_params['host_post'] = $post->ID; //v6.76
			if(empty($this->default_params)) {
				foreach (Wf_Widget::$current_specs[$widget_type]['params'] as $pkey => $pvalue) {
					$this->default_params[$pkey] = $pvalue['default'];
				}
			}
			$this->params = $this->tidy_params($this->default_params, $raw_params, $region);
			$this->widget_type = $widget_type;
		}  
		
		
		abstract public function get_html();
		
		//static function wf_widgets($regions) { // v3.84 split this up so can access widget_list from admin pages
		static function wf_widgets() { // v5.3
			global $post; // v6.44
			//$regions = Wf_Widget::$regions;  // v5.3
			$regions = get_regions_list($post);// v6.47
			$widget_list = self::list_wf_widgets($regions);
			WF_Debug::stash(array('$widget_list' => $widget_list));
			$region_html = Wf_Widget::$region_html; // v4.4
			foreach($regions as $region) {// v3.60
				if(!isset($region_html[$region])) {
					$region_html[$region] = ''; // v4.4
				}
				
				$w_list = array(); // v6.44?  WCTS  errors 
					//Cannot use string offset as an array in ... wf_widgets.php on line 175 (= the 'disinherit' line)
				if(count($widget_list[$region]['current']) > 0) {
					
					$w_list[$region] = $widget_list[$region]['current'];
					if(!is_array($w_list[$region][0])) { // v6.44?
						error_log("WF bug trap: wf_widgets.php, REQUEST_URI = ".$_SERVER['REQUEST_URI'].", REMOTE_ADDR = ".$_SERVER['REMOTE_ADDR']);
					} else {
						//if($w_list[$region][0]['qstring'] == "0") { // disinherit!
						if($w_list[$region][0]['widget_type'] == "cancel") { // disinherit!  // v6.66
							continue; // skip to next region
						}
					}
				} elseif(count($widget_list[$region]['inherited']) > 0 && $widget_list[$region]['inherited'][0]['widget_type'] != "cancel") { // v6.66
					$w_list[$region] = $widget_list[$region]['inherited'];
				} else {
					continue; // skip to next region
				}
				
				foreach($w_list[$region] as $widgnum => $widget) { // widgnum introduced 12.3.12 to allow id for widget						
					$qstring = $widget['qstring']."&widgnum=".$widgnum;// ."&widget=".$widget['widget_type'];  // v6.48
					$widget_class = Wf_Widget::$current_specs[$widget['widget_type']]['class_name']; // eg: Post_widget
					$widget_obj = new $widget_class($region, $widget['widget_type'], $qstring);
					$region_html[$region] .= $widget_obj->get_html(); // ($region, $qstring); // v4.4 
					
					WF_Debug::stash(array('$qstring' => $qstring, '$widget_class' => $widget_class, '$region' => $region, '$region_html["'.$region.'"]' => $region_html[$region]));
				}
				
			}
			Wf_Widget::$region_html = $region_html; // v5.3
			//WF_Debug::stash(array('$region_html' => $region_html));
		}
		
		
		
		static function get_region_html($region) { // v5.3 This allows us to use previously undeclared regions on special pages
			if(!isset(Wf_Widget::$region_html[$region])) {
				return'';
			} else {
				return Wf_Widget::$region_html[$region];
			}
		}
	
	
		
			
		static function list_wf_widgets($regions) { // v3.84  This gets called by admin.php
			global $post;
			
			/* // v6.73
			$godfather = get_godfather($post->ID, Wf_Widget::$catfather_array); // v6.73
			WF_Debug::stash(array('$godfather' => $godfather));
			if (!$godfather) {
				return '';
			}
			$generations = get_post_ancestors($post->ID); // why do this bit more than once??
			
			array_unshift($generations, $post->ID); // $id followed by all ancestors
			
			if(!empty(Wf_Widget::$catfather_array)) {  // v6.73
				$generations = array($godfather); // precalculated from $catfather_array
				WF_Debug::stash(array('is_single' => "Yes"));
			}
			*/
			$generations = get_generations($post->ID, Wf_Widget::$catfather_array); // v6.73
			d('$generations',$generations);
			
			$widget_list = array(); // v6.12
			foreach($regions as $region) {
				$widget_list[$region] = self::get_regional_inheritance($generations,$region); // v6.53 
			}
			return $widget_list; // v4.12 [$region]['current' or 'inherited'] $widget_type,$qstring,$rank,$aboveness
		}


		// v6.67 was getting errors "should not be called statically", so made static
		private static function get_regional_inheritance($generations,$region) { // returns 2 arrays: 'current' and 'inherited' // v6.49 // v6.53
			$custom_array = array( // v4.12
				'current' => array(),
				'inherited' => array()
			);
			foreach ($generations as $aboveness => $generation) { // v3.84
				$c_or_i = ($aboveness == 0) ? 'current' : 'inherited'; // v6.66
				$custom_values = get_post_custom($generation); 
				//v6.80 - because 1,000 + daily warnings in error log!
				if(!is_array($custom_values)) {
					/*
					error_log("WF bug trap: wf_widgets.php, REQUEST_URI = ".$_SERVER['REQUEST_URI'].", REMOTE_ADDR = ".$_SERVER['REMOTE_ADDR'].
						', $generation = '.$generation.', $region = '.$region);
					*/
					// this error occurs with  /car-sharing-clubs/car-clubs/feed/ - or any non-existent feed
					continue; // skips to next generation
				}
				foreach ($custom_values as $cv_key => $cv_value) {
					
					$cv_key_array = explode('-',$cv_key);
					if($cv_key_array[0] == $region) {
						
						$qstring = qscode($cv_value[0]); // qscode makes it into a querystring
						$c = count($cv_key_array);
						if($c == 3) { // an order is specified
							$rank = $cv_key_array[1]; // eg: "2"
						} else {
							$rank = "0";
						}
						$custom_array[$c_or_i][] = array( // v6.66
								'widget_type' => $cv_key_array[$c-1],
								'qstring' => $qstring,
								'rank' => $rank,
								'aboveness' => $aboveness
							);
					// v6.66	
					} elseif($cv_key == $region."_cancel" && $cv_value[0] == '1') {
						// whether current or inherited: this has to be the only entry for this level
						$custom_array[$c_or_i][] = array( // bit of a clunky, line-of-least-resistance solution!
							'widget_type' => 'cancel',
							'qstring' => '',
							'rank' => '0', // we need to be able to sort by rank later - hence the dummy array
							'aboveness' => $aboveness
						);
					}
				} // foreach ($custom_values
				if (count($custom_array['inherited']) > 0){  // v4.12 we only need the nearest inheritance
					break; // jumps out of foreach  ($generations
				}
			} // foreach ($generations
			
			array_sort_by_column($custom_array['current'], 'rank');  
			array_sort_by_column($custom_array['inherited'], 'rank'); 
			return $custom_array;
		}



		protected function set_shortcode_content($content) {
			$this->shortcode_content = $content;
		}
	
		
		public static function shortcode_process($atts,$content=null,$widget_type){ // $atts is an array
			$atts['widget']= substr($widget_type,10); // removes 'shortcode_'
			$widget_class = ucfirst($atts['widget'])."_widget"; // eg: Post_widget
			$scwidgobj = new $widget_class('shortcode', $atts['widget'], $atts);
			$scwidgobj->set_shortcode_content($content);
			$html = $scwidgobj->get_html();
			return $html;
		}
		
		
		protected function add_action( $action, $function = '', $priority = 10, $accepted_args = 1 ) {
			add_action( $action, array($this, $function == '' ? $action : $function ), $priority, $accepted_args );
		}
		
		
		
		protected function tidy_params($default_params, $sc_atts, $region) { // v3.51
			d('$sc_atts',$sc_atts);
			$default_params = array_merge($default_params, array('widgnum'=>false,'widget'=>'','region'=>$region)); // v4.3 added region
		
			extract(shortcode_atts($default_params, $sc_atts)); // v3.50
			
			if(isset($ids)) {
				$ids = str_replace(" ","", $ids); // get rid of any spaces
				$ids = explode(',',$ids); // v3.51 Was $id_array
			}
			if(isset($cat) && $cat) { // v3.77  to allow multiple categories v3.79 added && $cat
				$cat = str_replace(" ","", $cat); // get rid of any spaces
				$cat = explode(',',$cat); 
			}
			if(isset($link)){ // v3.54
				if($link) {
					$link = wf_linkfix($link);
				}
			}
			if(isset($style)){
				$style = str_replace(","," ", $style); // turn commas into spaces
				$style = $widget."_widget wf_widget region_".$region." ".$style; // v3.50 renamed $class to $style // v4.2 added wf_widget
			} else {
				$style = $widget."_widget wf_widget region_".$region." default";  // v4.2 added wf_widget
			}
			if(isset($caption)) { // v3.38
				$caption=stripslashes($caption);
				$caption=str_replace("_", " ", $caption); // because shortcode values can't contain spaces
			}
			if(isset($heading)) {
				$heading=stripslashes($heading);
				$heading=str_replace("_", " ", $heading); // because shortcode values can't contain spaces
				// added with Wf_Widgets
				if(isset($linktext) && $linktext == 'heading') { // 4.3
					$heading = "<a href='".$link."'>".$heading."</a>";
				}
			}
			
			$params=compact(array_keys($default_params)); // v3.50  this turns all those variables back into an array again
			return $params;
		}
		
		
		protected function get_endlink($params) { // v5.7
			$html = '';
			if($params['link'] && ($params['linktext'] != 'heading')){
				$linktext = ($params['linktext']) ? $params['linktext'] : "&nbsp;";
				$html =  "\n<a class='endlink coverlink' href='".$params['link']."'>".$linktext."</a>\n";
			} 
			return $html;
		}

		
		
		protected function output_a_post($rankedpost, $params) { // v3.50
			global $post;
			$post_store = $post; //v6.52
			$post = $rankedpost; // v3.40
			$html = '';
			d('$rankedpost->ID',$rankedpost->ID);
			if($rankedpost->post_type != 'attachment') { // changed from == 'post' 19.3.12  3.18
				$html .= "\n<div class='".$params['style']."'>\n";
				$html .= Wf_Widget::wf_editlink($rankedpost->ID);// v5.9
				$html .= extra_markup($params); // v4.2
				$custom_fields = get_post_custom($rankedpost->ID);  // v3.32
				
				// v3.37 This section now looks for a customfield "extras" applied to the post, and gives priority to this for "when" and "link"
				$when_html =''; // 
				if(isset($custom_fields['when'])) { // an array
					//$html .= "<p class = 'details'>".$when[0]."</p>\n"; // v3.37  DODGY!! Has $when been defined?
					$when_html = "<p class = 'details'>".$custom_fields['when'][0]."</p>\n";
				}
				if(isset($custom_fields['extras'])) { // v3.37 
					$qstring = qscode($custom_fields['extras'][0]); // the QUERYSTRING version
					parse_str($qstring, $q_array); // parses the querystring into an associative array
					//preint_r($q_array);
					if(isset($q_array['link'])) {
						$params['link'] = wf_linkfix($q_array['link']); // NB this over-rides any "link" parameter that's set for leftpost, rightpost etc.
					}
					if(isset($q_array['when'])) {
						$when_html = "<p class = 'details'>".$q_array['when']."</p>\n"; // NB this over-rides any value set in customfield "when".
					}		
				}
				$html .= $when_html;	
				
				if($params['show_title']) {
					$title = remove_square_brackets($rankedpost->post_title); //v3.36
					$html .= "<div class='hwrap'><h2>".$title."</h2></div>\n"; // <div class='hwrap'> added for carplus v3.15
				}
				
				$img_html = '';
				if($params['pic_size']) {
					
					if (has_post_thumbnail($rankedpost->ID ) && $params['pic_size'] != '0') {  // v3.38
						$feature_pic_id = get_post_thumbnail_id($rankedpost->ID);
						$img_html = wp_get_attachment_image($feature_pic_id, $params['pic_size']);
						//$img_html = change_width_and_height($img_html,$img_size,$img_size); // v3.49  Was 100,100
						$img_html .= wf_get_caption($rankedpost,$params['caption']); // 3.26
						$img_html .= wf_get_credits($feature_pic_id); // returns empty if no 'credit=' in description, otherwise a suitable <div> (or <a> if it's a link)
						$img_html = "<div class='picwrap'>".$img_html."</div>\n"; // v3.65
					}
				}		
				
				
				if(isset($params['format']) && strpos($params['format'],'l') !== false) { //v5.12
					$list = true;
				} else {
					$list = false;
				}
				
				//d('$list',$list); // (F3) calls Wf_Debug
				
				
				
				
				$html .= "<div class='postwrap'>".$img_html.Wf_Widget::get_excerpt_or_full($rankedpost,$params['type'],$list)."</div>\n";  //v5.12 $list
							
			} else { // assume it must be 'attachment'
			
				$html .= "\n<div class='".$params['style']." attachment'>\n"; // v4.2
				$html .= Wf_Widget::wf_editlink($rankedpost->ID);// v5.9
				$html .= extra_markup($params); // v4.2
				$html .= "\n<div class='credit_wrap'>\n"; // v6.46
				$html .= wp_get_attachment_image($rankedpost->ID, $size=$params['pic_size'], $icon=false, $attr=array('title' =>''));
				$html .= wf_get_credits($rankedpost->ID); // returns empty if no 'credits=' in description, otherwise a suitable <div> (or <a> if it's a link)
				$html .= "</div>\n"; // .credit_wrap // v6.46
				$html .= wf_get_caption($rankedpost,$params['caption']); // 3.26
			}
			if($params['link']) {
				if(!isset($q_array['linktext'])) { //v3.40
					$linktext = "&nbsp;";
				} else {
					$linktext = $q_array['linktext'];
				}
				$html .= "\n<a class='endlink coverlink' href='".$params['link']."'>".$linktext."</a>\n"; //v3.40  //v3.41
			}
			
			$html .= "</div>\n";
			$post = $post_store; //v6.52
			return $html;
		}
		
		
		static function wf_editlink($post_id) { // v5.9
			if (current_user_can('edit_post', $post_id)) { // v6.26
				return "<a class='editlink' href='".get_edit_post_link($post_id)."'>Edit</a>\n";
			}
		}
		
		
		// v6.67 was getting errors "should not be called statically", so made static
		public static function get_excerpt_or_full($catpost,$type,$list) { // v3.25 pulled out of get_catposts() so we can recycle it in scode_insert_items()
			// 3.31 added $list - so <more> items in a list get treated the same as automatic excerpts
			$html = ''; // v6.51 so that it doesn't complain when inheritance is cancelled
			if($type=='excerpts') { // there's 3 varieties of excerpt...
				if(strpos($catpost->post_content, '<!--more-->') && !$list) { // ie: there's a more tag in the content   3.31
					$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]'); // set this in functions.php
					$get_the_excerpt = explode('<!--more-->', $catpost->post_content);
					$get_the_excerpt = $get_the_excerpt[0] . $excerpt_more;
					$get_the_excerpt = do_shortcode(apply_filters('mimic_content',$get_the_excerpt)); // 3.30 v6.54
				} else { // there is not a more tag in the content (or it's a list)
					if(($catpost->post_excerpt != '')  && !$list) { // there's a real excerpt!  (Yeah, right...)  3.31
						$get_the_excerpt = apply_filters('get_the_excerpt', $catpost->post_excerpt); // this is exactly how WordPress does it
					} else { // we need to do an automatic excerpt - truncate to 55(?) words
						$get_the_excerpt =  Wf_Widget::wf_wp_trim_excerpt($catpost->post_content); // function borrowed from wp-includes/formatting.php 
					}
				}
				$html = apply_filters('the_excerpt', $get_the_excerpt); // this is exactly how WordPress does it	
			} elseif ($type=='full'){
				$html = do_shortcode(apply_filters('mimic_content', $catpost->post_content))."\n"; // v6.21
			}
			return $html;
		}
		
		
		
		/**
		 * Generates an excerpt from the content, if needed.
		 *
		 * The excerpt word amount will be 55 words and if the amount is greater than
		 * that, then the string ' [...]' will be appended to the excerpt. If the string
		 * is less than 55 words, then the content will be returned as is.
		 *
		 * The 55 word limit can be modified by plugins/themes using the excerpt_length filter
		 * The ' [...]' string can be modified by plugins/themes using the excerpt_more filter
		 *
		 * @since 1.5.0
		 *
		 * @param string $text The excerpt. If set to empty an excerpt is generated.
		 * @return string The excerpt.
		 */
		 
		// v6.67 was getting errors "should not be called statically", so made static
		public static function wf_wp_trim_excerpt($text) { // Wingfinger version
			$raw_excerpt = $text;
			$text = strip_shortcodes( $text );
		
			$text = apply_filters('mimic_content', $text); // 3.30
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);
			$excerpt_length = apply_filters('excerpt_length', 55); // set this in functions.php
			$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]'); // set this in functions.php
			$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
			if ( count($words) > $excerpt_length ) {
				array_pop($words);
				$text = implode(' ', $words);
				$text = $text . $excerpt_more;
			} else {
				$text = implode(' ', $words);
			}
			return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
		}
		
		
		//v6.80 function get_catposts($args,$params) moved from a function to a method in abstract class Wf_Widget because 
		// (a) it's used by both List and Vscroller widgets and (b) overloading a function (as opposed to a method) doesn't 
		// allow as much control over what gets overwritten. (Eg: extending Lists widget to ToDo widget causes all lists to 
		// have ToDo junk added!
		public function get_catposts($args,$params) {
			global $post;
			$post_store = $post;
			$widget_query = new WP_Query($args);
			$catposts = $widget_query -> posts;
			d('catposts',$catposts);
			if($params['date_format']) { // v6.63
				$date_format = $params['date_format']; // v6.63
			} else {
				$date_format = (strpos($params['format'],'t') === false) ? '\P\u\b\l\i\s\h\e\d j/n/y   g:iA' : '\P\u\b\l\i\s\h\e\d j/n/y'; // v6.63
			}
			$list = (strpos($params['format'],'l') === false); // v5.18
			// Default behaviour (ie: no 'l' so $list is true) truncates all excerpts as plain text.
			$has_reveal = (strpos($params['format'],'r') !== false); // v3.84
			
			$html = "<div class='posts ".$params['type']."'>\n"; // added for carplus v3.15. $type for v3.20
			foreach($catposts as $catpost) {
				$post = $catpost; // necessary to make 'More' links work
				
				if($params['filter']) { // v5.9  name of function used to filter posts
					$fname = $params['filter'];
					if(function_exists($fname)) {
						if(!$fname($catpost)) { // true=keep, false=discard
							continue;
						}
					}
				}
				
				//$cid = $catpost ->ID; // v6.63
				
				$html .=  "<div class='post wfpid_".$catpost->ID." ".($has_reveal ? 'reveal' : '')."'>\n"; // v6.79 added class wfpid_1234 for easier jQ manipulation
				$html .= Wf_Widget::wf_editlink($catpost->ID);// v5.9
				
				
				if(strpos($params['format'],'w') !== false) { // 'w' shows writer
					$author_id = $catpost->post_author;
					//$html .= "<p class='author'>Author: ".get_userdata($author_id)->display_name."</p>\n"; 
					$author = "<span class='author'>by <strong>".get_userdata($author_id)->display_name."</strong></span>";
				} else {
					$author = '';
				}
				
				// v6.63
				if($params['date_field']) {
					$date = get_post_meta($catpost->ID, $params['date_field'], true); // $single = true
					//echo $date;
				} else {
					$date = $catpost->post_date;// v3.35
				}
				$date = date($date_format, strtotime($date));// v6.63
				$date = str_replace(" ","&nbsp;",$date);
				//$date_intro = $params['date_intro'] ? $params['date_intro'] : 'Published '; // v6.63
				if(strpos($params['format'],'d') === false) { // 'd' suppresses the date
					$html .= "<p class='date'>".$date.$author."</p>\n"; // v6.63 'Published' moved from here to date format
				}
				
				$img_html = ''; // v3.53
				if($params['pic_size']) {
				
					if (has_post_thumbnail($catpost->ID) && $params['pic_size'] != '0') {  // v3.38
						$feature_pic_id = get_post_thumbnail_id($catpost->ID);
						$img_html = wp_get_attachment_image($feature_pic_id, $params['pic_size']);
						//$img_html = change_width_and_height($img_html,$img_size,$img_size); // v3.49  Was 100,100
						if(isset($params['caption'])) {
							$img_html .= wf_get_caption($catpost,$params['caption']); // 3.26
						}
						$img_html .= wf_get_credits($feature_pic_id); // returns empty if no 'credit=' in description, otherwise a suitable <div> (or <a> if it's a link)
					}
				}		
				
				$custom_fields = get_post_custom($catpost->ID);// v3.32
				if(isset($custom_fields['when'])) { // an array
					$html .= "<p class = 'details'>".$custom_fields['when'][0]."</p>\n";
				}
				if(isset($custom_fields['Descriptor'])) { // an array
					$html .=  "<p class='type'>".$custom_fields['Descriptor'][0]."</p>\n";
				}
				$title = (strpos($params['format'],'a') === false && strpos($params['format'],'r') === false) ? "<a href='".get_permalink($catpost->ID)."' rel='bookmark' title='Permanent Link to ".remove_square_brackets($catpost->post_title)."' >".remove_square_brackets($catpost -> post_title)."</a>" : remove_square_brackets($catpost -> post_title); // v3.78 v3.84 v6.77
				
				$html .= "<p class='title ".($has_reveal ? 'reveal_head' : '')."'>".$title."</p>\n"; //v5.18
				if(function_exists('insert_post_extras')) { // v3.53
					$html .= insert_post_extras($custom_fields, $params);  // v3.53 eg: SoP featured videos	 v3.63 added $params
				}
				
				$html .= $img_html; // v3.53
				if($params['type'] !=='titles') {
					$html .= "<div class='entry ".($has_reveal ? 'reveal_tail' : '')."'>\n"; // v5.18
					$html .= Wf_Widget::get_excerpt_or_full($catpost,$params['type'], $list);  //   3.31 $list = true 3.39
					$html .= "</div>\n"; // class = 'entry'
				}
				$html .= "</div>\n"; // class = 'post'
			} //foreach
			$html .= "</div>\n"; // class = 'posts' added for carplus   v3.15
			$html .= List_widget::wf_paginate_list($args, $widget_query->found_posts);
			$post = $post_store;
			return $html;
		}
	

	
	
	
	}//abstract class Wf_Widget
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	//global $post;	
	//d('$post->post_name',$post->post_name);
	Wf_Widget::init(); // default specs and config - sets up statics $regions and $current_specs and shortcodes
	
		
		
	if ( is_admin() ) {
		require_once( WF_WIDGETS_PLUGIN_PATH . '/admin.php' );
	} else {
		require_once( WF_WIDGETS_PLUGIN_PATH . '/frontend.php' );
		add_action( 'template_redirect', 'setup_region_html'); // v6.2
		function setup_region_html() {
			if(!is_404() && !is_search() && !is_archive()) { // v6.3  v6.12 v6.38
				Wf_Widget::wf_widgets();
			}
		}
	}


} // function widget_common()  - on 'init' hook with priority 1
