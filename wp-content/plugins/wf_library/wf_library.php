<?php
/*
Plugin Name: Wingfinger Library plugin  
Plugin URI: http://www.wingfinger.co.uk
Description: A plugin for supporting Wingfinger themes
Version: 6.73
Author: Wingfinger
Author URI: http://www.wingfinger.co.uk
License: It's copyright!
*/

/*
This program is NOT free software; if you want to use it, please contact
info[at]wingfinger.co.uk for details of pricing. Thankyou.


 
 		TIP:	To find an edit for (say) version 3.22, try searching for "3.22" - I usually drop the version number into a comment on 
				the line I've changed.
				
v6.73	4/9/15	Added pluggable function get_input_html() to function db_edit_form(). This allows us to add other stuff 
				to the same table cell. Eg: javascript UI for selecting operator icon in Carplus map admin. 
				Now class Form_MCP can work with subset of database fields - eg: when table has columns that are being phased
				out, but we don't want to delete them yet.
				Function output_row() now checks for nothing returned (= null) - so we don't have to return anything for last row.
				Also provides all defaults in 'calculate_values' mode, so only need to provide changed values. 
				Also stripslashes everything. So eponymous functions can be much shorter.
				
				Added radio buttons to function db_edit_form().
				wf_validation.php: Added function radio().
				
v6.72	23/3/15	Changed function wf_change_mce_options() to cope with TinyMCE 4.				
				
v6.71	23/3/15	Moved functions validate_postcode() and validate_function_name() from map widget to wf_validation.php
		7/3/15	Changed method get_validation_MCP() to static, so can use from outside Form_MCP.
				Added lots of csv stuff to class Form_MCP.

v6.70	4/3/15	Switch class Wf_SuperTable() to using PDO. If using query, needs 4th param = $pdo.
				Ditto class Form_MCP.				
				
v6.69	13/2/15	Found error in function get_table(). Single-line tables not getting output when source was array.

v6.68  6/2/15	Added PDO functions. (Affects only Wingtheme, Carplus, Metacarpool.)		

v6.67  22/12/14	Added pluggable function identify_dev_site_css().								
				
v6.66  25/10/14	Was getting Undefined index: display_name, nickname from function duplicate_display_or_nickname_registration_errors()
				because these are usually not required at self-registration. So now wrapped in isset().				
				
v6.65  18/10/14	Moved various general WP functions from l4c functions.php	
				
				SITE-BREAKER: $catfather is now called $catfather_array and is defined in widget_config.php
				Major changes to get_godfather() which now uses get_generations() to do all the work.
				Other changes in wf_widgets.php (v6.73), widgety_functions.php, index.php

v6.64  15/10/14	Changed email validation to allow 4 letter tld. Eg: pete.richardson@phonecoop.coop

v6.63	4/10/14	Added suite of functions to allow checking (and legacy fixing) of display_name and nickname uniqueness

v6.62	22/9/14	Added qs_restore($ignore) in wf_library.php and wf_db_classes.php. Saves the other query string info
				when processing database forms. Params in the $ignore array are omitted - eg: array('qmode').

v6.61	30/8/14	Added $returns['subhead_row'] to function output_row($row_items,$rownum) in class Wf_SuperTable.
				This allows us to add subheads in the output table when appropriate. Used in plugin wf_newsletter_diagnostics.
				
v6.60	14/8/14	Extending wf_get_versions() to include hostname. Most of the domain stuff is done directly from Wingtheme.	

v6.59	12/8/14	Test for function_exists('array_column') because PHP 5.5 has its own (compatible) version				
				
v6.58 	19/4/14	Wf_Debug function dump(): moved $label out of <pre> so that it validates OK.
				
v6.57	28/3/14	Added 'w' to format param in wf_validation.php to allow display of author in list widget.

				Added function widget_html($widget_class, $style, $qstring) to widgety functions. 
				Reduces dependency on wf_widgets in frontend. 
				
v6.56	13/2/14	Changed labels 'add_new_item' and 'edit_item' for wf_snippet and wf_sitenote				

v6.55	11/2/14	Removed cellspacing ='0' from functions get_table() and db_edit_form(). Invalid in HTML5.

v6.54	7/2/14	Changed way we do add_action( 'wp_enqueue_scripts' etc. Original anon function failed in PHP 5.2 (eg: Spark)

v6.53	5/2/14	Now using latest version of plugin-update-checker
				 
v6.52	5/2/14	Added function load_update_checker() that gets called in wf plugins. Ooops - no it doesn't.

v6.51  26/10/13	widgety_functions.php: Changed deprecated split() to explode() in get_godfather()
				
v6.50 	7/10/13	Changed get_godfather() to work with sub-categories - but not if more than 1 box ticked
				
v6.49	2/10/13	Changed get_godfather() so that $catfather gets treated as an array  

v6.45	21/9/13	Changed JSON encoding in wf_get_versions() to allow cron job to use it.			
				
v6.44	20/9/13	Rejigged Development menu in function wf_change_admin_bar_menu()
				
v6.43 	4/9/13	Added error count to wf_get_versions().	

v6.42 	20/8/13	Added error_log_path and latest_errors to wf_get_versions().	

v6.41 	19/8/13	Added php_type and php_version to wf_get_versions().			
				
v6.39	14/8/13	Copied pluggable function add_to_message() to from wf_forms.php. Required by wf_db_classes.php and wf_forms.php
				Was causing fatal error for UTA.

				
v6.37	5/8/13	Added error_reporting to monitored items in wf_get_versions()
				
v6.36	5/8/13	Deleted versions_json.php and original wf_get_versions() and renamed nwf_get_versions2() - the json version - 
				to wf_get_versions(). Requires a 1-line call in functions.php
				
v6.35	3/8/13	Added function wf_get_versions2() - the json version
				
v6.34	3/8/13	Added function with($obj) - allows a one-line instantiation and use of an object without an additional variable.
				Removed single reference to THEME_FOLDER_URL, to allow wf_library to be independent of functions.php
				Moved function d() here from functions.php
				
v6.33	31/7/13	Function wf_get_versions() was requiring a deprecated file.
				Added new SiteData root element and Standards element (which hijacks Description)

v6.32	30/7/13	Rejigged Development menu to include (if they exist) Dev log, Edit Dev Log and To Do.

				Added pluggable function remove_dashboard_widgets(). 
				
v6.31	30/7/13	Added function weblink($arg, $value) to wf_validation.php				

 v6.30	29/7/13 Changed sluggify to use underscores instead of hyphens. Can't have hyphens in function names.
 
 v6.28 	27/7/13	Control error_reporting() now via WP_DEBUG in wp_config.php
 
 v6.27	26/7/13	Moved old wf_lib.js to new files in plugins folder
 
 v6.23	10/7/13	Added $key to function array_column($array, $column). To date, only used in uta_member.php on UTravelActive site
 				and shouldn't be affected.
				
 v6.22	9/7/13	Added PLUGINS_DIR
 				Added function wf_get_versions()
 
 v6.18	27/6/13	Added date button to Text Editor
 
 v6.17	26/6/13	Changed function load_wfdb() to use WF_LIB_PLUGIN_PATH (defined in wf_widgets.php).
 
 v6.16	24/6/13	Added functions wf_load_stylesheet() and wf_load_script().
 
 v6.15	22/6/13	Changed function wf_change_admin_bar_menu() to add in an edit link to '/wf_sitenote/development-log'
 
 v6.7	6/6/13	Added id to maintenance menu item.
 
 v6.6	3/6/13	Culmination of maintenance mode development. See notes on Wingtheme page.
 
 v6.5	24/5/13	Added function get_editable_roles()
 
 v6.4	21/5/13	Functions get_cf_pair_value(), get_ranked_posts($args) and trim_parse($qstring) moved to widgety_functions.php.
 				This is because they're used by the newsletter plugin (which isn't a widget). 
				Changed get_cf_pair_value() so that it returns empty if not found.
 
 v6.2	18/5/13	Changed get_widget_help_posttypes() (which can be overwritten in functions.php). Now returns an array of plural names
				and includes 'page'=>'Pages' and 'attachment'=>'Images'
				
				Added function in_array_partial($needle,$haystack)

v6.1	17/5/13	Took ages to track down Dreamweaver syntax errors. Eventually found that heredoc closing identifiers didn't like 
				being indented!
 
 				Function load_wfdb() can now be used to load wf_db_classes.php on those pages where it's needed.
				


CONTENTS

General purpose PHP utilities
WordPress utilities
Navigation-related
Debugging
Text-munging and post-processing
Forms and validation


Pluggable functions:

Cusomising Admin Bar

*/
  
  
  
if(!defined('PLUGINS_DIR')) {
	define( 'PLUGINS_DIR', dirname(dirname( __FILE__ ))); // v6.22
}


define( 'WF_LIB_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WF_LIB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'WF_LIB_PLUGIN_FILE', plugin_basename( __FILE__ ) );

define( 'WF_REMOTE_ADDR', $_SERVER['REMOTE_ADDR']); // v6.1


// v6.52
//require_once 'plugin-updates/plugin-update-checker.php';
//load_update_checker();

require_once 'plugin-update-checker/plugin-update-checker.php'; // v6.53

/* This fails with PHP  <5.3
add_action( 'wp_enqueue_scripts', function() {
		wp_enqueue_script( 'wf_lib_new_js', WF_LIB_PLUGIN_URL.'/scripts/wf_lib_new.js', array('jquery')); // v6.27								   
});
*/

add_action( 'wp_enqueue_scripts', 'enqueue_lib_js'); // v6.54 now compatible with PHP 5.2

function enqueue_lib_js() { // v6.54
	wp_enqueue_script( 'wf_lib_new_js', WF_LIB_PLUGIN_URL.'/scripts/wf_lib_new.js', array('jquery')); // v6.27								   
}



add_action( 'after_setup_theme', 'wflib_common', 10 );

function wflib_common() {
	
	// v6.52
	$UpdateChecker = new PluginUpdateChecker(
		'http://www.graphicdesignleeds.info/lib/wf_library.json',
		__FILE__,
		'wf_library'
	);


	require_once(dirname( __FILE__ ) . '/wf_validation.php');
	
	 
	class WF_Exception extends Exception { //v4.1
	
	}  
	 /*
	//stackoverflow.com/questions/6982037/php-class-server-variable-a-property
	class WF_Server{ // v6.1
		public function __construct() {
			$this->ui_ip = $_SERVER['REMOTE_ADDR'];
			$this->ui_user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
	}  
	*/
	
	  
	  
	// GENERAL PURPOSE PHP UTILITIES ////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// v6.62 - rstores querystring, ignoring the $ignore array
	function qs_restore($ignore=array()) {
		parse_str($_SERVER['QUERY_STRING'],$qs_array);
		//d('$qs_array',$qs_array);
		$qs_array = array_diff_key($qs_array,array_flip($ignore)); // all entries from array1 whose keys are not present in any other arrays.
		$str_array = array();
		foreach($qs_array as $key => $value) {
			$str_array[] = $key.'='.$value;
		}
		d('$str_array',$str_array);
		return implode('&',$str_array);
	}
	
	
	function in_array_partial($needle,$haystack) { // v6.2
		foreach ($haystack as $piece_of_hay) {
			if (strpos($piece_of_hay, $needle) !== false) return true;
		}
		return false;
	}
	
	// codeaid.net/php/extract-all-keys-from-a-multidimensional-array // v4.7
	/**
	 * Get list of all keys of a multidimentional array
	 *
	 * @param array $array Multidimensional array to extract keys from
	 * @return array
	 */
	function array_keys_multi(array $array) {
		$keys = array();
	
		foreach ($array as $key => $value) {
			$keys[] = $key;
	
			if (is_array($array[$key])) {
				$keys = array_merge($keys, array_keys_multi($array[$key]));
			}
		}
	
		return $keys;
	}
	
	
	//php.net/manual/en/language.operators.array.php
	function array_add($a1, $a2) {  // v4.7
		// adds the values at identical keys together
		$aRes = $a1;
		foreach (array_slice(func_get_args(), 1) as $aRay) {
			foreach (array_intersect_key($aRay, $aRes) as $key => $val) {
				$aRes[$key] += $val;
			}
			$aRes += $aRay; 
		}
		return $aRes; 
	}
	
	
	// www.php.net/manual/en/function.array-intersect.php  comment 23/6/2004
	function key_values_intersect($values,$keys) {  // v3.38
	   foreach($keys AS $key) {
		  $key_val_int[$key] = $values[$key];
		  }
	   return $key_val_int;
	}
	
	
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}
	
	if(!function_exists('array_column')) { // v6.59  PHP introduced its own version of this in PHP 5.5
		function array_column($array, $column) { // v4.5  returns one column of multi array
			$ret = array();
			foreach ($array as $key => $row) { // v6.23 added $key
				 $ret[$key] = $row[$column]; // v6.23 added $key
			}
			return $ret;
		}
	}
	
	function RoundSigDigs($number, $sigdigs) {
		$multiplier = 1;
		while ($number < 0.1) {
			$number *= 10;
			$multiplier /= 10;
		}
		while ($number >= 1) {
			$number /= 10;
			$multiplier *= 10;
		}
		return round($number, $sigdigs) * $multiplier;
	} 
	
	if ( ! function_exists('array_fill_keys') ) { // irritatingly, this function reqires PHP >= 5.2.0
		function array_fill_keys($target, $value = '') {
			if(is_array($target)) {
				foreach($target as $key => $val) {
					$filledArray[$val] = is_array($value) ? $value[$key] : $value;
				}
			}
			return $filledArray;
		}
	}
	
	if ( ! function_exists('readQstring') ) {
		function readQstring($name) {
			if (isset($_GET[$name])) { 
				return $_GET[$name];
			} else {
				return '';
			}
		}
	}
	
	
	function nbsp_if_empty($string) { // v4.1
		return ($string == '') ? '&nbsp' : $string;
	}
	
	//stackoverflow.com/questions/1402505/in-php-can-you-instantiate-an-object-and-call-a-method-on-the-same-line
	function with($obj) { //v6.34 allows a one-line instantiation and use of an object without an additional variable
		return $obj; 
	} 
	
	if ( !defined('__DIR__') ) { // 
			define('__DIR__', dirname(__FILE__) . '/'); 
	}
	
	if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");
	
	
	
	
	/**
	 * Logging class:
	 * - contains lfile, lwrite and lclose public methods
	 * - lfile sets path and name of log file
	 * - lwrite writes message to the log file (and implicitly opens log file)
	 * - lclose closes log file
	 * - first call of lwrite method will open log file implicitly
	 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
	 */
	class Logging {
		// declare log file and file pointer as private properties
		private $log_file, $fp;
		// set log file (path and name)
		public function lfile($path) {
			$this->log_file = $path;
		}
		// write message to the log file
		public function lwrite($message) {
			// if file pointer doesn't exist, then open log file
			if (!$this->fp) {
				$this->lopen();
			}
			// define script name
			$script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
			// define current time and suppress E_WARNING if using the system TZ settings
			// (don't forget to set the INI setting date.timezone)
			$time = @date('[d/M/Y:H:i:s]');
			// write current time, script name and message to the log file
			fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
		}
		// close log file (it's always a good idea to close a file when you're done with it)
		public function lclose() {
			if ($this->fp) { // added by Bill v3.62
				fclose($this->fp);
			}
		}
		// open log file (private method)
		private function lopen() {
			// in case of Windows set default log file
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$log_file_default = 'c:/php/logfile.txt';
			}
			// set default log file for Linux and other systems
			else {
				$log_file_default = '/tmp/logfile.txt';
			}
			// define log file from lfile method or use previously set default
			$lfile = $this->log_file ? $this->log_file : $log_file_default;
			// open log file for writing only and place file pointer at the end of the file
			// (if the file does not exist, try to create it)
			$this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
		}
	}
	
	
	
	if(!function_exists('get_pdo')) {
		function get_pdo($details){ //v6.68
			/*
			function get_server_details() { 
				return array(
					"server" => 'localhost',
					"username"   => 'wingfing_metausr',
					"password"   => <Jeep>,
					"database"   => 'wingfing_metacp'
				);
			}
			*/
			extract($details);
			 
			$dsn        = "mysql:host=$server;dbname=$database";
				
			try {
				$pdo = new PDO($dsn, $username, $password);
				$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				$pdo->exec('SET NAMES "utf8"');
				return $pdo; 
			} catch (Exception $e) {
				echo 'Unable to connect to the database server: '.$e->getMessage();
				exit();
			}
		}
	}
	
	// originally called pdo_select() - but it should also work for CRUD
	if(!function_exists('pdo_process')) {
		function pdo_process($conn, $query, $bindings=null) { //v6.68
			try {
				$stmt = $conn->prepare($query);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$stmt->execute($bindings);
				if(strpos(trim($query), 'SELECT') === 0) { // position 0, not false
					$result = $stmt->fetchAll();
				} else {
					$result = $stmt->rowCount(); // for DELETE, INSERT or UPDATE
				}
				$stmt->closeCursor();
				return $result;
			} catch (Exception $e) {
				echo 'Unable to retrieve from the database';
				error_log('Unable to retrieve from the database: '.$e->getMessage());
			}
		}
	}
	
	
	// v6.73  select_builder() copied from L4C and renamed
	function wf_select_builder($options_array,$handle,$first_label) {
		$html = "<select name='".$handle."_select' id='".$handle."_select'>"; // eg: 'category_select'   
		$itemno = 0; 
		if($first_label !== false) { // skip this line if $first_label set to false - NB: watch out for $itemno
			$html .= 	"<option value='' ".wf_select_prepop($options_array, $itemno, $handle.'_select')." >".$first_label."</option>";
		} 
			foreach($options_array as $key => $option_label) {      
				$itemno++;
				$html .= "<option value='".$key."' ".wf_select_prepop($options_array, $itemno, $handle."_select").">".$option_label."</option>";  
			}    
		$html .= "</select>";	  
		
		return $html;
	}
	
	// v6.73  l4c_select_prepop() copied from L4C and renamed
	function wf_select_prepop($option_array, $itemno, $selectname) {
		if(isset($_GET[$selectname])) { // v3.57
			$value = $_GET[$selectname]; // v3.57
		} else {
			$value = '';
		}
		$option_keys = array_keys($option_array);
		// site_specific function to prepopulate dropdown menu options
		// $option_array shows $value => $label
		if ($value=='') { // not set
			if($itemno==0) {
			// it's the first item on the list
				return  ' selected="selected"';
			}
			else {
				return "";
			}
		}
		else {
			if($itemno==0) {
				return "";
			}
			// there are $_POST values to enter
			if ($value == $option_keys[$itemno-1]) {
				return  " selected='selected'";
			}
			else {
				return "";
			}
		}
	}



	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	// WORDPRESS UTILITIES ////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
	//wordpress.stackexchange.com/questions/1665/getting-a-list-of-currently-available-roles-on-a-wordpress-site
	function get_editable_roles() { // v6.5
		global $wp_roles;
	
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
	
		return $editable_roles;
	}
	*/
	
	
	function wf_load_stylesheet($handle,$url) { // v6.16
		if(!wp_style_is($handle, $list = 'registered')) {
			wp_register_style( $handle, $url );
		}
		wp_enqueue_style($handle);
	}
	
	function wf_load_script($handle,$url,$deps = null) {  // v6.16
		if(!wp_script_is($handle, $list = 'registered')) {
			wp_register_script( $handle, $url, $deps );
		}
		wp_enqueue_script($handle);
	}
	
	// Some sites (eg: Carplus) are picky about allowed environment variable names. This can be overridden in functions.php
	if(!function_exists('get_maint_var_name')) { // v6.6
		function get_maint_var_name() {
			return 'MAINTENANCE';
		}
	}
	
	function showMaintenanceReminder() { // v6.6
		if( wp_script_is( 'jquery', 'done' ) && isset($_SERVER[get_maint_var_name()]) && $_SERVER[get_maint_var_name()] == 'true') { ?>
			<script type="text/javascript">
				var reminder = "<p id='maintenance' style='position: absolute; background-color: #FF0; right: 30px; font-weight: bold; font-size: 20px;'>MAINTENANCE MODE!</p>";
				jQuery('#header').prepend(reminder);
			</script> <?php
		}
	}
	add_action( 'wp_footer', 'showMaintenanceReminder' );	
	
	
	function maintenance_reminder() {  // v6.6 adds a further item to the menu
		global $wp_admin_bar;
		if (is_admin_bar_showing()  && isset($_SERVER[get_maint_var_name()]) && $_SERVER[get_maint_var_name()] == 'true') { 
			$wp_admin_bar->add_menu( array(
			'id' => 'maintenance-mode', // v6.7
			'title' => __('MAINTENANCE MODE!'))); // #wp-admin-bar-maintenance-mode div
			wp_enqueue_style( 'wf_widget_admin_css', WF_WIDGETS_PLUGIN_URL.'/css/wf_widget_admin.css' ); // ensures styles working in dashboard etc 
			
		}
	}
	add_action('admin_bar_menu', 'maintenance_reminder', 1200);

	
	function admin_login_link() {
		if(! is_user_logged_in()) { ?>
			<div id="login_link" title="Admin login"><a href="<?php echo wp_login_url( get_permalink() ); ?>">Admin login</a></div> <?php
		} 
	}
	
	
	// this one is possibly not used currently - but looks potentially useful
	function get_cat_array(){ // creates an associative array using the slugs: 'news' => 1, 'blog' = 5
		$categories = get_categories('hide_empty=0');
		$cat_array = array(); 
		foreach ($categories as $category) {
			$cat_array[$category->slug] = $category->cat_ID;
		}
		return $cat_array;
	}
	
	
	/*
	 * Plugin Name: Show Future Posts on Single Post
	 * Author URI: http://www.pinoyinternetbusiness.com
	 * Plugin URI: http://plugins.svn.wordpress.org/show-future-posts-on-single-post/trunk/
	 * Description: Make all future posts visible in single post templates.
	 * Author: Stanley Dumanig
	 * Version: 1.0
	 */
	
	/*  Copyright 2009  Show Future Posts on Single Post Templates (email : stanley@dumanig.com)
	
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.
	
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
	
	
	*/
	
	
	function show_future_posts($posts)
	{
	   global $wp_query, $wpdb;
	   if(is_single() && $wp_query->post_count == 0)
	   {
		  $posts = $wpdb->get_results($wp_query->request);
	   }
	   return $posts;
	}
	
	// Fixes search for empty string (by returning everything with a space in it!)
	// www.wordpress.org/support/topic/blank-search-sends-you-to-the-homepage
	
	function search_request_filter( $query_vars ) {
		if( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
			$query_vars['s'] = " ";
		}
		return $query_vars;
	}
	// add_filter( 'request', 'search_request_filter' );
	
	
	function wf_search_filter($query){ // v3.54
	  if($query->is_search) {
		$omit_cats = array('1'); // 1 uncategorized does not work with taxonomy
		
		$omit_posts = array();
		$posttypes = array('wf_sitenote');
		
		if(function_exists('omit_from_search')){
			$omit = omit_from_search();
			$omit_posts = $omit['posts'];
			$posttypes = $omit['post_types'];
		}
		//var_dump($omit_posts); 
		$args = array(
		  'post_type' => $posttypes,
		  'numberposts' => -1,
		);
	
		$alltaxposts= array();
		$taxposts = get_posts($args); // which returns them in an unpredictable order (or, possibly, order of ID)
	
		foreach($taxposts as $taxpost) {		
			$alltaxposts[] = $taxpost->ID;
		}
		$omit_posts = array_merge($omit_posts, $alltaxposts);
		  
		// Set the pages/posts and categories to exclude in the WP Query
		$query->set('category__not_in', $omit_cats);
		$query->set('post__not_in', $omit_posts);
	  }
	  return $query;
	}
	// add_filter('pre_get_posts','wf_search_filter');
	
	
	function wf_display_image_size_names( $sizes ) { //Bill add to wf library?!
		$new_sizes = array();
		$added_sizes = get_intermediate_image_sizes();
		// $added_sizes is an indexed array, therefore need to convert it to associative array, using $value for $key and $value
		foreach( $added_sizes as $key => $value) {
			$new_sizes[$value] = $value;
		}
		// This preserves the labels in $sizes, and merges the two arrays
		$new_sizes = array_merge( $new_sizes, $sizes );
		return $new_sizes;
	}
	
	//add_filter('image_size_names_choose', 'wf_display_image_size_names', 11, 1);
	
	
		
	/* WRITE ONE OF THESE IN FUNCTIONS.PHP...
	function omit_from_search(){
		$omit['posts'] = array('171', '88', '90'); // 23 thankyou; 88 & 90 addresses
		$omit['post_types'] = array('wf_sitenote'); // 
		return $omit;
	}
	*/
	
	
	
	
	
	// MAKING DISPLAY NAME AND NICKNAME UNIQUE  v6.63
	
	//www.bappi-d-great.com/unique-display-name-and-nickname-in-wordpress/
	// A dirty script by Ashok Kumar Nath (Bappi D Great)


	//add_action('personal_options_update', 'check_display_name');
	//add_action('edit_user_profile_update', 'check_display_name');
	function check_display_name($user_id) {
		global $wpdb;
		// Getting user data and user meta data
		$err['display'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE display_name = %s AND ID <> %d", $_POST['display_name'], $_POST['user_id']));
		$err['nick'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname' AND meta.meta_value = %s AND users.ID <> %d", $_POST['nickname'], $_POST['user_id']));
		foreach($err as $key => $e) {
			// If display name or nickname already exists
			if($e >= 1) {
				//$err[$key] = $_POST['username']; // DON'T UNDERSTAND THIS LINE!!
				//$err[$key] = $_POST['user_login']; // DON'T UNDERSTAND THIS LINE!!
				// Adding filter to corresponding error
				add_filter('user_profile_update_errors', "check_{$key}_field", 10, 3);
			}
		}
	}
	
	// Filter function for display name error
	function check_display_field($errors, $update, $user) {
		$errors->add('display_name_error',__('Sorry, Display Name is already in use. It needs to be unique.'));
		return false;
	}
	
	// Filter function for nickname error
	function check_nick_field($errors, $update, $user) {
		$errors->add('display_nick_error',__('Sorry, Nickname is already in use. It needs to be unique.'));
		return false;
	}
	
	
	// Function to check for duplicate display name and nickname and replace with username
	function display_name_and_nickname_duplicate_check() {
		global $wpdb;
		$query = $wpdb->get_results("select * from $wpdb->users");
		$query2 = $wpdb->get_results("SELECT * FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname'");
		$c = count($query);
		for($i = 0; $i < $c; $i++) {
			for($j = $i+1; $j < $c; $j++) {
				if($query[$i]->display_name == $query[$j]->display_name){
					
					wp_update_user(
						array(
						'ID' => $query[$i]->ID,
						'display_name' => $query[$i]->user_login
						)
					);
					
					FB::info('repeated display_name',($query[$i]->ID).': '.($query[$i]->display_name));
				}
				if($query2[$i]->meta_value == $query2[$j]->meta_value){
					update_user_meta($query2[$i]->ID, 'nickname', $query2[$i]->user_login, $prev_value);
					FB::info('repeated nickname',($query2[$i]->ID).': '.($query2[$i]->meta_value));
				}
			}
		}
	}
	
		
	// Calling the display_name_and_nickname_duplicate_check() again when a new user is registered
	//add_action( 'user_register', 'check_nickname', 10, 1 );
	function check_nickname($user_id) {
		display_name_and_nickname_duplicate_check();
	}
	
	// JWBP alternative version of registration check
	//add_filter('registration_errors', 'duplicate_display_or_nickname_registration_errors',10,3);
	function duplicate_display_or_nickname_registration_errors( $errors, $sanitized_user_login, $user_email ) {
		global $wpdb;
		
		if(isset($_POST['display_name'])) { // v6.66 because usually not done at self-registration
			$err['display'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE display_name = %s", $_POST['display_name']));
			if($err['display'] >= 1) {
				$errors->add('displayname_duplicate_error',__('Sorry, Display Name is already in use. It needs to be unique.'));
			}
		}
		
		if(isset($_POST['nickname'])) { // v6.66 because usually not done at self-registration
			$err['nick'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname' AND meta.meta_value = %s", $_POST['nickname']));
			if($err['nick'] >= 1) {
				$errors->add('nickname_duplicate_error',__('Sorry, Nickname is already in use. It needs to be unique.'));
			}
		}
		
		return $errors;
	}
	
	//Filter a user's nickname before the user is created or updated.
	//$meta['nickname'] = apply_filters( 'pre_user_nickname', $nickname );
	
	function wf_filter_nickname($nickname) {
		global $wpdb;
		global $user_id; 
		global $current_user;
		if(isset($user_id)) { // not available on add new user
			$duplicates = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname' AND meta.meta_value = %s", $nickname));

			//FB::info($nickname,'$nickname');
			error_log('wf_filter_nickname: '.$nickname.''.$user_id);
			// gets called on submitting Add New User screen
			if($duplicates >= 1) {
  				get_currentuserinfo();
  				$nickname = $current_user->user_login;
			}
		}
		return $nickname;
	}
	
	function wf_enable_checking_unique_display_and_nicknames() {
		//add_filter('registration_errors', 'duplicate_display_or_nickname_registration_errors',10,3); // seems to work - but usually don't do these at regn
		add_action('personal_options_update', 'check_display_name'); // editing own profile - seems to work OK
		add_action('edit_user_profile_update', 'check_display_name'); // editing someone else's profile - seems to work OK
		//add_filter('pre_user_nickname','wf_filter_nickname',10,1);
		add_action('user_register','check_nickname',10,1); // $user_id
	}
	//wf_enable_checking_unique_display_and_nicknames(); // call this early in functions.php
	
	//display_name_and_nickname_duplicate_check(); // call this once to sort out existing users, then disable
	
	
	
	
	// v6.65 gets the current post type in the WordPress Admin
	function get_current_post_type() {
		global $post, $typenow, $current_screen;
		if ( $post && $post->post_type ) //we have a post so we can just get the post type from that
			return $post->post_type;
		elseif( $typenow ) //check the global $typenow - set in admin.php
			return $typenow;
		elseif( $current_screen && $current_screen->post_type ) //check the global $current_screen object - set in screen.php
			return $current_screen->post_type;
		elseif( isset( $_REQUEST['post_type'] ) )
			return sanitize_key( $_REQUEST['post_type'] ); //lastly check the post_type querystring
		return null; //we do not know the post type!
	}
	
	// v6.65
	function get_ID_by_name($post_name, $post_type) {
	   global $wpdb;
	   $post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$post_name."' AND post_type = '".$post_type."'");
	   return $post_id;
	}
	
	// v6.65
	function get_slug_by_id($post_id, $post_type) {
	   global $wpdb;
	   $slug = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE ID = '".$post_id."' AND post_type = '".$post_type."'");
	   return $slug;
	}
	
	// v6.65 wordpress.stackexchange.com/a/56109
	function get_meta_boxes($screen=null,$context='advanced') {
		global $wp_meta_boxes;
		if ( empty( $screen ) ) {
			$screen = get_current_screen();
		} elseif ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		}
		$page = $screen->id;
		return $wp_meta_boxes[$page][$context];
	}
	
	
	if(!function_exists('identify_dev_site_css')) { // v6.67
		function identify_dev_site_css() {
			if(strpos($_SERVER['SERVER_NAME'],'dev.') === 0) {	
				$output="<style> 
					#header:after,
					.wp-admin:after { 
						content: 'DEVELOPMENT SITE';
						display: block;
						padding: 10px;
						background-color: #fc0; 
						position: absolute;
						top: 40px;
						left: 40%;
						font-size: 2em;
						line-height: 1.2;
					} 
					body.wp-admin {
						position: relative;
					}
					</style>";
				echo $output;
			}
		}
		
	}
	
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	   
	
	
	// NAVIGATION RELATED  ////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	 
	// developed by Richard for Medoria Solar site
	function removeParentLinks($querystring) { // replavce call to wp_list_pages with this
		//bavotasan.com/tutorials/how-to-remove-the-links-to-parent-pages-in-the-wordpress-page-list
		$pages = wp_list_pages($querystring);
		$pages = explode("</li>", $pages);
		$count = 0;
		
		foreach($pages as $page) {
			if(strstr($page,"<ul class='children'>")) {
				$page = explode("<ul class='children'>", $page);
				$page[0] = str_replace('</a>','',$page[0]);
				$page[0] = preg_replace('/\<a(.*)\>/','',$page[0]);
		
				if(count($page) == 12) {
					$page[1] = str_replace('</a>','',$page[1]);
					$page[1] = preg_replace('/\<a(.*)\>/','',$page[1]);                
				}
				$page = implode("<ul class='children'>", $page);
			}
			$pages[$count] = $page;
			$count++;
		}
		$pages = implode('</li>',$pages);
		echo $pages;
	}
	
	
	
	function toplevel($thePostID) {
		/* This sets up an array that looks like this:
		$top_pages[135]= (0, home, Home);
		$top_pages[2]= (1, about, About);
		$top_pages[5]= (2, research, Research);
		$top_pages[44]= (3, library, Library);
		$top_pages[58]= (4, accommodation, Accommodation);
		etc...
		*/
		
		$top_pages = array();
		$toplevel_pages=get_pages('parent=0&sort_column=menu_order');
		$n=0;
		foreach ($toplevel_pages as $toplevel_page) {
			$key=$toplevel_page->ID; // the easiest way to access info is via the ID, not the menu position
			$top_pages[$key] = array();
			$top_pages[$key][0]=$n; // the menu position
			$top_pages[$key][1]=$toplevel_page->post_name;
			$top_pages[$key][2]=$toplevel_page->post_title;
			$n++;
		}
		return $top_pages;
		//print_r($top_pages);
	}
	
	
	function wf_breadcrumbs($page_id) {
		$ancestors=(get_post_ancestors($page_id)); // parent = first, godfather = last
		if (is_home()) {
			$html  = '';
		} else {
			$html  = "<ul id='breadcrumbs'>";
			$html .= "<li><a href='".get_option('home')."'>Home</a></li>";
			for($i = count($ancestors)-1; $i >= 0; $i--){
				$html .= "<li>&nbsp;&nbsp;>&nbsp;&nbsp;<a href='".get_permalink($ancestors[$i])."'>".get_the_title($ancestors[$i])."</a></li>";
			}
			$html .= "<li>&nbsp;&nbsp;>&nbsp;&nbsp;".get_the_title()."</li></ul>";
		}
		return $html;
	}
	
	
	function wf_page_in_family($page_id_to_test, $head_id) {
		$ancestors = get_ancestors( $page_id_to_test, 'page' );
		if ($page_id_to_test == $head_id || in_array($head_id, $ancestors)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	// DEBUGGING  ////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	class Wf_Debug { // not a widget // v4.7
		
		
		static $users = array();
		static $stash = array();
	
		/*	
		This gets echoed (eg) right after $region_html['bottom']
		It has to run *after* all the code containing calls to
		Wf_Debug::stash()
		*/
		static function output($region) {
			global $current_user;
			get_currentuserinfo(); // sets $current_user
			if(!(isset($current_user) && in_array($current_user->user_login, Wf_Debug::$users)))
				return '';
			
			$html = "<div class='debug_widget wf_widget region_".$region." '>"; // not actually a widget, but class='wf_widget' simplifies css
			$html .= "<div class='hwrap'><h2>Wingfinger debugging info</h2></div>";
			$html .= "<p class='strap'>NB: This box is only visible when logged in as ".Wf_Debug::list_users($current_user->user_login)."</p>";
			//$html .= "<p class='strap'>Currently logged in as: <em>".$current_user->user_login."</em></p>";
			foreach(Wf_Debug::$stash as $debug_point) {
				foreach($debug_point as $var_name => $value) {
					$html .= dump($value, $var_name, false, false);
					//$html .= array_print($var_name, $value);
				}
			}
			$html .= "</div>";
			return $html;
		}
		
		private static function list_users($login_name) {
			$html = "<em>".implode('</em> or <em>',Wf_Debug::$users)."</em>";
			return str_replace('<em>'.$login_name.'</em>','<strong>'.$login_name.'</strong>', $html);
		}
		
		
		static function stash($v_array) {
			Wf_Debug::$stash[] = $v_array;
		}
			
	}
	
	function d($var_name, $var) { // quicker to type: d('$weekly_totals',$weekly_totals);
		Wf_Debug::stash(array($var_name=>$var));
	}
	
	/**
	 * From codeaid.net/php/improved-zend_debug::dump%28%29-and-var_dump%28%29-functions // v4.7
	 *
	 * Debug helper function.  This is a wrapper for var_dump() that adds
	 * the <pre /> tags, cleans up newlines and indents, and runs
	 * htmlentities() before output.
	 *
	 * @param mixed $var     The variable to dump.
	 * @param string $label  Label to prepend to output.
	 * @param boolean $print Print the output if true.
	 * @param boolean $exit  Exit after echoing if true
	 * @return string
	 */
	function dump($var, $label = null, $print = true, $exit = false) {
		/*
		if (defined('APPLICATION_ENV') && (APPLICATION_ENV != 'development')) {
			return $var;
		}
		*/
	
		// format the label
		$label_text = $label;
		//$label = ($label === null) ? '' : '<h2 style="margin: 0px">' . trim($label) . '</h2>';
		$label = ($label === null) ? '' : '<h2>' . trim($label) . '</h2>';// v5.5
	
		// var_dump the variable into a buffer and keep the output
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
	
		// neaten the newlines and indents
		$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
	
		if (is_array($var)) {
			$keys = array_keys_multi($var);
			$maxlen = 0;
	
			// determine the number of characters in the longest key
			foreach ($keys as $key) {
				$len = strlen($key);
				if ($len > $maxlen) {
					$maxlen = $len;
				}
			}
	
			// account for [" and "]
			$maxlen += 4;
	
			// append spaces between "] and =>
			$output = preg_replace_callback('/\[.*\]/', create_function('$matches', 'return str_pad($matches[0], ' . $maxlen . ');'), $output);
		}
	
		if (PHP_SAPI == 'cli') {
			$output = PHP_EOL . $label_text
					. PHP_EOL . $output
					. PHP_EOL;
		} else {
			if (!extension_loaded('xdebug')) {
				$output = htmlspecialchars($output, ENT_QUOTES);
			}
	
			//$output = '<pre style="font-family: \'Courier New\'; font-size: 11px; background-color: #FBFED7; margin: 5px auto; padding: 10px; border: 1px solid #CCCCCC; max-width: 1000px;">'
			$output = '
	
	
	'
	. $label.// v6.58 moved $label out of <pre> so that it validates OK
	' 
	<pre>
	'
	. $output
	. '</pre>
	'; // v5.5 added in lots of new lines here and removed inline styles so that easier to read in viewsource
		}
	
		if ($print === true) {
			print $output;
		}
	
		if ($exit === true) {
			exit;
		}
	
		return $output;
	}
	
	
	
	
	
	// more readable form of print_r
	function preint_r($array)
	   {
		  echo '<pre>';
		  print_r($array);
		  echo '</pre>';
	   }
	
	function examine_string($string) {
		$encoded ='';
		for ($i=0; $i < strlen($string); $i++)  {
			$encoded .= $i.": ".strtoupper(dechex(ord(substr($string,$i))))."\n";   
		}
		return $encoded;
	
	}
	
	
	function wf_debug($variables) { ?>
		<div id="wf_debug">
			<h3>Wingfinger debugging info</h3><?php
			foreach ($variables as $variable) {			
				if(defined($variable)) {
					$value = constant($variable);
				} else {
					$vname = str_replace('$','',$variable);
					global $$vname;
					$value = $$vname;
				}
				if(is_array($value)) {
					echo("<p><strong>".$variable."</strong> = Array(</p>\n");
					foreach($value as $value_key => $value_value) {
						echo("<p>&nbsp;<strong>".$value_key."</strong> => ".$value_value."</p>\n");
					}
					echo("<p>)</p>\n");
				} else {
					echo("<p><strong>".$variable."</strong> = ".$value."</p>\n");
				}
			} ?>
			
			<?php /*
			$cookie_elements = wp_parse_auth_cookie($cookie = '', $scheme = 'logged_in');
			extract($cookie_elements, EXTR_OVERWRITE);
			echo("<p><strong>"."$"."username</strong> = ".$username."</p>\n"); */ ?>
			
		</div><?php
	}
	
	
	function wf_debug2($variables) { 
		//global $debug_html;
		if(!isset($debug_html)) {
				$debug_html = '';
		}
	
		if(current_user_can('administrator')) {	
			foreach ($variables as $variable) {			
				if(defined($variable)) {
					$value = constant($variable);
				} else {
					$vname = str_replace('$','',$variable);
					global $$vname;
					$value = $$vname;
				}
				$debug_html .= array_print($variable, $value);
	
			} 
		}
		return $debug_html;
	}
	
	function array_print($variable, $value) {
		$debug_html ='';
		if(is_array($value)) {
			$debug_html .= "<p><strong>".$variable."</strong> = Array(</p>\n";
			foreach($value as $value_key => $value_value) {
				
				$debug_html .= array_print($value_key, $value_value);
			}
			$debug_html .= "<p>)</p>\n";
		} else {
			$debug_html .= "<p><strong>".$variable."</strong> = ".$value."</p>\n";
		}
		return $debug_html;
	}
	
	function wf_debug_box($variables) { 
		$debug_html =  wf_debug2($variables);
		//global $debug_html;
		if(current_user_can('administrator')) { ?>
			<div class="wf_debug"> <!-- Can't be an id because might want one in each column -->
				<h3>Wingfinger debugging info</h3>
				<?php echo($debug_html."</div>"); 
		}
	}
		
	
	
	//v6.60
	// Not now used because different servers return data in different formats.
	// Now using phpwhois-4.2.2 instead - see json_dns.php
	
	/*************************************************************************
	php easy :: whois lookup script
	==========================================================================
	Author:      php easy code, www.phpeasycode.com
	Web Site:    http://www.phpeasycode.com
	Contact:     webmaster@phpeasycode.com
	*************************************************************************/
	
	/*
	// For the full list of TLDs/Whois servers see http://www.iana.org/domains/root/db/ and http://www.whois365.com/en/listtld/
	function get_whoisservers() {
		return array(
			"biz" => "whois.biz",
			"com" => "whois.verisign-grs.com",
			"coop" => "whois.nic.coop",
			"eu" => "whois.eu",
			"gov" => "whois.nic.gov",
			"ie" => "whois.domainregistry.ie", // Ireland
			"info" => "whois.afilias.net",
			"me" => "whois.nic.me", // Montenegro
			"net" => "whois.verisign-grs.net",
			"org" => "whois.pir.org",
			"tv" => "tvwhois.verisign-grs.com", // Tuvalu
			"uk" => "whois.nic.uk", // United Kingdom
			"us" => "whois.nic.us", // United States
		);
	}

	function LookupDomain($domain){
		$whoisservers = get_whoisservers();
		$domain_parts = explode(".", $domain);
		$tld = strtolower(array_pop($domain_parts));
		$whoisserver = $whoisservers[$tld];
		if(!$whoisserver) {
			return "Error: No appropriate Whois server found for $domain domain!";
		}
		$result = QueryWhoisServer($whoisserver, $domain);
		if(!$result) {
			return "Error: No results retrieved from $whoisserver server for $domain domain!";
		}
		else {
			while(strpos($result, "Whois Server:") !== FALSE){
				preg_match("/Whois Server: (.*)/", $result, $matches);
				$secondary = $matches[1];
				if($secondary) {
					$result = QueryWhoisServer($secondary, $domain);
					$whoisserver = $secondary;
				}
			}
		}
		return $result; //"$domain domain lookup results from $whoisserver server:\n\n" . $result;
	}

	function QueryWhoisServer($whoisserver, $domain) {
		$port = 43;
		$timeout = 10;
		$fp = @fsockopen($whoisserver, $port, $errno, $errstr, $timeout) or die("Socket Error " . $errno . " - " . $errstr);
		//if($whoisserver == "whois.verisign-grs.com") $domain = "=".$domain; // whois.verisign-grs.com requires the equals sign ("=") or it returns any result containing the searched string.
		fputs($fp, $domain . "\r\n");
		$out = "";
		while(!feof($fp)){
			$out .= fgets($fp);
		}
		fclose($fp);
	
		$res = "";
		if((strpos(strtolower($out), "error") === FALSE) && (strpos(strtolower($out), "not allocated") === FALSE)) {
			$rows = explode("\n", $out);
			foreach($rows as $row) {
				$row = trim($row);
				if(($row != '') && ($row{0} != '#') && ($row{0} != '%')) {
					$res .= $row."\n";
				}
			}
		}
		return $res;
	}
	*/
	
	
	function wf_get_versions() { //v6.35  v6.36 renamed from wf_get_versions2()
		
		require_once(ABSPATH.'wp-admin/includes/admin.php');
		$active_plugins = get_option ( 'active_plugins', array () );
		$plugin_data = array();
		foreach($active_plugins as $path) {
			$plug_path_bits = explode('/', $path);
			$plugin_data[$plug_path_bits[0]] = get_plugin_data( PLUGINS_DIR.'/'.$path, $markup = false, $translate = true );
		}
		
		$wordpress = array('Version' => get_bloginfo('version'));
		$functions_php = get_plugin_data(get_stylesheet_directory().'/functions.php', $markup = false, $translate = true );
		$standards = explode(',',$functions_php['Description']);
		$php_type = php_sapi_name(); // v6.41  cgi, apache etc
		$php_version = PHP_VERSION;// v6.41 
		$latest_errors = array();
		if(defined('ERROR_LOG_PATH')) {// v6.42
			$error_log_path = ERROR_LOG_PATH;
					
			// v6.43
			$latest_1000_errors = array_slice(file(ERROR_LOG_PATH),-1000); // last 1000 errors, inc line endings
			$latest_10_errors = array_slice($latest_1000_errors,-10); // last 10 errors, inc line endings$err_count_24 = 0;
			$ago_24hrs = strtotime("-1 day");
			$err_count_24 = 0;
			foreach($latest_1000_errors as $line) {
				preg_match('~^\[(.*?)\]~', $line, $date);
				if(!empty($date[1])) {
					if(strtotime($date[1]) > $ago_24hrs) {
						$err_count_24++;
					}
				}
			}
		} else {
			$error_log_path = '';
		}
		
		$hostname = gethostname(); //v6.60
		/*
		$domain = $_SERVER['SERVER_NAME'];
		if(substr(strtolower($domain), 0, 4) == "www.") $domain = substr($domain, 4);
		//$domain = 'graphicdesignleeds.info';
		$domain_result = LookupDomain($domain);
		
		$domain_result = explode("\n",$domain_result);
		//var_dump($domain_result);
		$nameservers = array();
		$registrant = $registrar = $expiry = '';
		foreach($domain_result as $line) {
			if(strpos($line,"Name Server:") === 0) {
				$entry = trim(str_replace("Name Server:",'',$line));
				if($entry != '') {
					$nameservers[] = strtolower($entry);
				}
			} elseif(strpos($line,"Registrant Name:") === 0) {
				$registrant = str_replace("Registrant Name:",'',$line);
			} elseif(strpos($line,"Billing Name:") === 0) {
				$registrar = str_replace("Billing Name:",'',$line);
			} elseif(strpos($line,"Registry Expiry Date:") === 0) {
				$expiry = str_replace("Registry Expiry Date:",'',$line);
			}
		}
		$expiry = date('j/n/Y', strtotime($expiry));
		*/

		
		
		
		//v6.45
		$site_data = '{
			"plugin_data":' . json_encode($plugin_data) . ', 
			"wordpress":' . json_encode($wordpress) . ', 
			"standards":' . json_encode($standards) . ', 
			"wp_debug":' . json_encode(WP_DEBUG) . ', 
			"error_reporting":' . json_encode(error_reporting()) . ',
			"php_type":' . json_encode($php_type) . ',
			"php_version":' . json_encode($php_version) . ',
			"error_log_path":' . json_encode($error_log_path) . ',
			"latest_errors":' . json_encode($latest_10_errors) . ',
			"err_count_24":' . json_encode($err_count_24) . ',
			"hostname":' . json_encode($hostname) .
		'}';
		
		/*
			"registrant":' . json_encode($registrant) . ',
			"registrar":' . json_encode($registrar) . ',
			"expiry":' . json_encode($expiry) . ',
			"nameservers":' . json_encode($nameservers) .
		*/
		
		if(strpos($_GET['callback'],'jQuery') !== false) { //v6.45 JSON version
			echo $_GET['callback'] . '('.$site_data.')';
		} else { //v6.45 CRON version
			echo '{"'.$_GET['callback'] .'":'.$site_data.'}';
		}
		exit();	 
		
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	   
	// TEXT-MUNGING & POST-PROCESSING  ////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function sluggify($string) {// sluggify turns the string into something suitable for a slug, class or variable name
		$newstring = str_replace(' ','_',$string); // v6.30
		$newstring = str_replace('/','_',$newstring);
		$newstring = str_replace(')','_',$newstring);
		$newstring = str_replace('(','_',$newstring);
		$newstring = strtolower($newstring);
		return $newstring;
	}
	
	function strip_wordpress($link) {
		$link = str_replace('/wordpress/','/',$link); // inserted by Bill to strip out "/wordpress"
		return $link;
	}
	// add_filter( "post_link", "strip_wordpress" );
	   
	function get_wordpressless_permalink() {
		$permalink = get_permalink();
		echo( str_replace('/wordpress/', '/', $permalink));
	}
	
	
	if(!function_exists('postProcessSidepost')) { // v3.24
		function postProcessSidepost($sidepost) { 
			$content = wpautop($sidepost); // turns 2 linebreaks into a paragraph
			$content = wf_filter_content($content);
			return  $content;
		}
	}
	
	function nbsp_trim($string) { // replaces ALL &nbsp; with space and then trims front and back
		str_replace("\xA0", " ", $string); // "\xA0" is &nbsp;
		//$string = trim($string, "chr(0xC2).chr(0xA0) \t\n\r\0\x0B");
		//$string = trim($string, "\xA0 chr(0xA0) chr(0xC2).chr(0xA0) \t\n\r\0\x0B");
		$string = trim($string);
		return $string;
	}
	
	// fixes problem of double spaces appearing in html
	function wf_filter_editor($editor_content) {
		if ($editor_content != '') { // if 2nd parameter of strpos is empty, it generates an "Empty delimiter" warning
			while (strpos('  ', $editor_content) !== false) { // supposed to be 5x quicker than preg_replace
				$editor_content = str_replace('  ', ' ', $editor_content);
			}
		}
		return $editor_content;
	}
	// add_filter('the_editor_content','wf_filter_editor');
	
	
	
	// used in widgets that have 'link' parameter
	if(!function_exists('wf_linkfix')) { // default 'pass-through' version that can be overwritten in first section of functions.php 
		function wf_linkfix($link) { // adjust so that links to (say) '/about' work OK
			$return_link = $link;
			if(substr($link,0,4) != "http") {
				if(substr($link,0,12) == "/wp-content/") { // media
					$return_link = "/".SUBDOMAIN_FOLDER.$link;  // v3.24
					// could also strip out /wordpress or /cms here
				} else {
					$return_link =  "/".SUBDOMAIN_FOLDER."/".CMS_FOLDER.$link;  // v3.24
				}
			}
			$return_link = str_replace("//".CMS_FOLDER,"/".CMS_FOLDER,$return_link);
			return $return_link;
		}
	}
	
	if(!function_exists('wf_filter_content')) { // v3.24
		function wf_filter_content($content) {   
			$content=str_ireplace("<a name=", "<a class='anchor' name=",$content); // anchor class ensures a bit of vertical space before jumped-to heading
			$content = str_replace('<p><!--:en--><br />', '', $content); // removes special comments introduced by language plugin
			$content = str_replace('<!--:-->', '', $content);
			$content = str_replace("<p><img","<img",$content); // 3.33
			$content = str_replace(" /></p>"," />",$content); // 3.33
			$content = str_replace("<p><object","<object",$content); // these 2 added from Tidal to enable video embedding  3.28
			$content = str_replace("</object></p>","</object>",$content);
			$content=str_replace('href="/','href="/'.SUBDOMAIN_FOLDER.'/'.CMS_FOLDER.'/',$content); // was: 'href="/carplus/cms/'
			$content=str_replace("href='/","href='/".SUBDOMAIN_FOLDER."/".CMS_FOLDER."/",$content); // was: "href='/carplus/cms/"
			$content=str_replace("//".CMS_FOLDER,"/".CMS_FOLDER,$content); // mopping up when SUBDOMAIN_FOLDER is empty // moved v3.47
			$content=str_replace("/".CMS_FOLDER."/wp-content/","/wp-content/",$content); // this nonsense gets created when referring to media // v3.47
			
			//$content = preg_replace('/<p><!--(.*?)--><\/p>/ise', " '<!--' .  stripslashes(clean_pre('$1'))  . '-->' ", $content); // 3.29 core.trac.wordpress.org/ticket/2691 v3.64
	
			return $content;	
		}	
		add_filter('the_content','wf_filter_content');
	}
	
	
	// used in widgets because CiviCRM and BBPress plugins both hijack the_content filter for their own evil ends,
	// throwing away the post content and substituting their own
	if(!function_exists('set_up_mimic_content_filter')) {
		function set_up_mimic_content_filter() { // 3.30
			add_filter( 'mimic_content', 'wptexturize'        ); // 
			add_filter( 'mimic_content', 'convert_smilies'    );
			add_filter( 'mimic_content', 'convert_chars'      );
			add_filter( 'mimic_content', 'wpautop'            );
			add_filter( 'mimic_content', 'shortcode_unautop'  );
			add_filter( 'mimic_content', 'prepend_attachment' );
			add_filter('mimic_content','wf_filter_content');
		}
	}
	
	
	function wf_privspan_the_title($post) {
		$title = remove_square_brackets($post->post_title); // v5.1 21/3/13
		if($post->post_status == 'private') {
			$title = '<span>Private: </span>'.$title;
		}
		return $title;
	}
	
	
	
	  
	//////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	
	
	function strip_array($var) { // v3.75 used in form_base.php
		return is_array($var)? array_map("strip_array", $var):stripslashes($var);
	}
	
	//NB: THERE ARE VARIOUS VERSIONS OF PREPOP FUNCTIONS HANGING AROUND
	
	function prepopulate($fieldname, $initial_value='') {  // v3.55 v3.73
	// just works for text inputs
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': $the_request = &$_GET; break;
			case 'POST': $the_request = &$_POST; break;
		}
	
		if(isset($the_request[$fieldname])) { 
			return stripslashes($the_request[$fieldname]);
		} 
		else {
			return $initial_value; // v3.73
		}
	} 
	
	
	
	function selectprepop($itemno, $selectname) {  // v3.55
	// generic function to prepopulate dropdown menu options
	// assumes that values are (eg) poptitle0, poptitle1, poptitle2, etc
	// this version is for form responses
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': $the_request = &$_GET; break;
			case 'POST': $the_request = &$_POST; break;
		}
		if(isset($the_request[$selectname])) { // v3.57
			$value = $the_request[$selectname]; // v3.57
		} else {
			$value = '';
		}
		//preint_r($itemno."  ".$selectname."  ".$value);
		return get_prepop($itemno, $selectname, $value);
	}
	
	//echo get_prepop(2, 'poptitle', 'poptitle2');
	
	function get_prepop($itemno, $key, $value) { // v3.55
	// generic function to prepopulate dropdown menu options
	// assumes that values are (eg) poptitle0, poptitle1, poptitle2, etc
	// this version can work with database contents
		if ($value=='') { // not set
			if($itemno==0) {
			// it's the first item on the list
				return  ' selected="selected"';
			}
			else {
				return "";
			}
		}
		else {
			// there are $_POST values to enter
			if ($value == $key.$itemno) {
				return  " selected='selected'";
			}
			else {
				return "";
			}
		}
	}
	
	
	function checkprepop($fieldname) { // v3.55
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': $the_request = &$_GET; break;
			case 'POST': $the_request = &$_POST; break;
		}
		if(isset($the_request[$fieldname])) {
			if($the_request[$fieldname]=="on"){
				return "checked";
			}
			else {
				return "";
			}
		} 
	}
	
	// See: stackoverflow.com/questions/4997252/get-post-from-multiple-checkboxes
	function checkmultiprepop($fieldname, $num) {
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': $the_request = &$_GET; break;
			case 'POST': $the_request = &$_POST; break;
		}
		if(isset($the_request[$fieldname])) {
			$this_array=$the_request[$fieldname];
			if(in_array($fieldname.$num, $this_array)) {
				return "checked";
			} else {
				return "";
			}
		} 
	}
	
	
	function radioprepop($fieldname, $itemno, $defaultno) {
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': $the_request = &$_GET; break;
			case 'POST': $the_request = &$_POST; break;
		}
		if(isset($the_request[$fieldname])) {
			if ($the_request[$fieldname] == $fieldname.$itemno) {
				return  " checked='checked'";
			}
			else {
				return "";
			}
		} else { // not set yet
			if ($defaultno == $itemno) {
				return  " checked='checked'";
			}
			else {
				return "";
			}
		}
	}
	
	// v6.73
	function radioprepop2($fieldname, $itemno, $value) {
		//WFB($fieldname.' '.$itemno.' '.$value);
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': $the_request = &$_GET; break;
			case 'POST': $the_request = &$_POST; break;
		}
		if(isset($the_request[$fieldname])) {
			if ($the_request[$fieldname] == $fieldname.$itemno) {
				return  " checked='checked'";
			}
			else {
				return "";
			}
		} else { // not set yet
			if ($value == $fieldname.$itemno) { // from_db or new
				return  " checked='checked'";
			}
			else {
				return "";
			}
		}
	}
	
	// Mail header removal
	function remove_headers($string) {
		$headers = array(
		"/to\:/i",
		"/from\:/i",
		"/bcc\:/i",
		"/cc\:/i",
		"/Content\-Transfer\-Encoding\:/i",
		"/Content\-Type\:/i",
		"/Mime\-Version\:/i"
		);
		$string = preg_replace($headers, '', $string);
		return strip_tags($string);
	} 
	
	
	
	function postcode_validate($postcode) {
	// POSTCODES - will work for every UK postcode
		$original_version = $postcode;
		$result ='';
		$postcode = str_replace("\xA0", " ", $postcode); // "\xA0" is &nbsp;
		$postcode = trim($postcode);
		if ($postcode != $original_version) {
			$result[] = 'trim';
		}
		$nospace = '/(^[a-pr-uwyz]((\d{1,2})|([a-hk-y]\d{1,2})|(\d[a-hjks-uw])|([a-hk-y]\d[abehmnprv-y]))\d[abd-hjlnp-uw-z]{2}$)/i';
		$pattern = '/(^[a-pr-uwyz]((\d{1,2})|([a-hk-y]\d{1,2})|(\d[a-hjks-uw])|([a-hk-y]\d[abehmnprv-y]))\s\d[abd-hjlnp-uw-z]{2}$)/i';
		if (preg_match($nospace, $postcode)) {
			$result[] = 'nospace';
			$postcode = substr($postcode, 0,-3).' '.substr($postcode, -3);
		} elseif (!preg_match($pattern, $postcode)) {
			$result[] = 'invalid';
		}
		return array($postcode,$result); 
	}
	
	
	
	
	
	
	// Used in get_validation() and get_maxlength_html()  - and also get_validation_MCP() in Metacarpool // v3.57
	function parse_validator_string($validator_string) { // v3.57
		$func_name_end = strpos($validator_string,'(') -1;
		$func_name = substr($validator_string,0,$func_name_end+1); // eg: val_capcha
		$arg = substr($validator_string,$func_name_end+2,-1);
		$arg = str_replace("'","",$arg); // strips out any single quotes. NB $arg has to use pipe instead of comma
		return array($func_name, $arg);
	}
	
	
	function get_maxlength_html($validator_string) {
		// only applies to <input> and doesn't apply to function intminmax() // v3.57
		$maxlength_html = '';
		list($func_name, $arg) = parse_validator_string($validator_string); // v3.57
			
		if ($func_name != 'intminmax' && is_numeric($arg)) { // just a single numeric argument
			$arg = $arg + 0; // force it to float or int
			if (is_int($arg) && $arg > 0) {
				$maxlength_html = "maxlength='".$arg."'";
			}
		}
		return $maxlength_html;
	}
	
	
	
	function despace ($string) {//used in radio above
		return str_replace (' ','_',$string);
	}
	
	
	function degreaterthan ($string) { //used in radio above
	// a leading > is used to indicate an initially selected button
		  if(substr($string,0,1) == ">") {
			$string= substr($string,1);
		  }
		  return $string;
	}
	
	
	function cleanData($data) { // interferes with O'Hara etc
		$data = trim($data);
		$data = htmlentities($data);
		$data = mysql_real_escape_string($data);
		return $data;
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// NB:  v5.0  Validation functions pulled out into wf_validation.php ///////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	/*
		-f will set the From address, -r will override the default Return-path that sendmail generates (typically the From address gets used). If you want your bouncebacks 
		to go to a different address than the from address, try using both flags at once: "-f myfromemail@example.com -r mybounceemail@example.com" 
	*/
	
	
	function multipartEmail($sendname, $sendfrom, $sendto, $subject, $text, $html, $bccto) {
		$mailParams = "-f$sendfrom";
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$hour = date("h");
		$min = date("i");
		$tod = date("a");
		
		$ip=$_SERVER["REMOTE_ADDR"];
		$semi_rand = md5(time());
		$boundary1 = "==Multipart_Boundary_x{$semi_rand}x"; 
		
		if (empty($bccto)) {
		  //$header = "From: " .$sendname." <$sendfrom>\r\nReply-to: $sendfrom";//
		  $header     =<<<BILL
	From: $sendname <$sendfrom>
	Reply-To: $sendfrom
	MIME-Version: 1.0
	Content-Type: multipart/alternative;
		boundary="$boundary1"
BILL;
// v6.1 Line above REALLY doesn't like being indented!
		}
		else {
		 // $header = "From: " .$sendname." <$sendfrom>\r\nReply-to: $sendfrom\r\nBcc: $bccto"; // $bccto is second recipient for testing
		 $header     =<<<BILL
	From: $sendname <$sendfrom>
	Bcc: $bccto
	Reply-To: $sendfrom
	MIME-Version: 1.0
	Content-Type: multipart/alternative;
		boundary="$boundary1"
BILL;
// v6.1 Line above REALLY doesn't like being indented!
		}
		
	
		//$body = $subject." \n";
		//$body .= "The form below was submitted by " .$sendfrom. " from IP address: $ip on $day/$month/$year at $hour:$min $tod \n";
		//$body .= "-------------------------------------------------------------------------\n\n";
		
		//$body = $message;     //assemble_email_body(); // globals: $fields, $field_labels, $select_fields	
		
		$body  =<<<BILL
	This is a multi-part message in MIME format.
	
	--$boundary1
	Content-Type: text/plain;
		charset="iso-8859-1"
	Content-Transfer-Encoding: quoted-printable
	
	$text
	--$boundary1
	Content-Type: text/html;
		charset="iso-8859-1"
	Content-Transfer-Encoding: quoted-printable
	
	$html
	
	--$boundary1--
BILL;
// v6.1 Line above REALLY doesn't like being indented!
		
		$result = mail($sendto, $subject, $body, $header, $mailParams);
		return $result;
	
	}
	
	// NB THIS ONE IS NOW DEPRECATED. USE function textEmail2($email_params, $text) INSTEAD // v3.57
	// Full working examples in Wingtheme
	
	function textEmail($sendname, $sendfrom, $sendto, $subject, $text, $bccto) {
		$mailParams = "-f$sendfrom";
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$hour = date("h");
		$min = date("i");
		$tod = date("a");
		
		//$wf_server = new WF_Server; // v6.1
		//$ip = $wf_server->ui_ip;
		$ip = $_SERVER['REMOTE_ADDR']; // can't even say this in a comment!
		
		if (empty($bccto)) {
		  $header = "From: " . $sendname ." <$sendfrom>\r\nReply-to: $sendfrom";//
	  }
	  else {
		  $header = "From: " . $sendname ." <$sendfrom>\r\nReply-to: $sendfrom\r\nBcc: $bccto"; // $bccto is second recipient for testing
	  }
		
	
		$body = $subject." \n";
		$body .= "The form below was submitted by " .$sendfrom. " from IP address: $ip on $day/$month/$year at $hour:$min $tod \n";
		$body .= "-------------------------------------------------------------------------\n\n";
		
		$body = $text;     //assemble_email_body(); // globals: $fields, $field_labels, $select_fields	
		
			
		$result = mail($sendto, $subject, $body, $header, $mailParams);
		return $result;
	}
	
	
	
	function textEmail2($email_params, $text) { // v3.57
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$hour = date("h");
		$min = date("i");
		$tod = date("a");
		
		//$wf_server = new WF_Server; // v6.1
		$ip = constant('WF_REMOTE_ADDR');
		
			
		if (empty($email_params['bccto'])) {
			$header = "From: " . $email_params['sendname'] ." <".$email_params['sendfrom'].">\r\nReply-to: ".$email_params['sendfrom'];//
	  }
	  else {
		  $header = "From: " . $email_params['sendname'] ." <".$email_params['sendfrom'].">\r\nReply-to: ".$email_params['sendfrom']."\r\nBcc: ".$email_params['bccto']; // $bccto is second recipient for testing
	  }
		
		$body = $email_params['subject']." \n";
		$body .= "The form below was submitted by " .$email_params['user_email']. " from IP address: $ip on $day/$month/$year at $hour:$min $tod \n";
		$body .= "-------------------------------------------------------------------------\n\n";
		
		$body .= $text;
		$result = mail($email_params['sendto'], $email_params['subject'], $body, $header, "-f".$email_params['envelope']);
		return $result;
	}
	
	
	
	// check variable is an email address
	function validEmail($email){
	 return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? TRUE : FALSE;
	}
	
	function clean($string) {
		return str_replace(chr(194),'',$string); // an oddity that precedes the �
	}
	
	
	/////////////////////////////////////////////////////////////////////////////////
	
	
	// CUSTOMISING THE DASHBOARD
	//codex.wordpress.org/Dashboard_Widgets_API#Advanced:_Removing_Dashboard_Widgets
	
	if (!function_exists('remove_dashboard_widgets') ) {  // v6.32
		function remove_dashboard_widgets() {
			// main..
			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
			// side...
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		} 
		
		add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );
	}
	
	
	
	
	
	// CUSTOMISING THE ADMIN BAR
	// see www.doitwithwordpress.com/customize-wordpress-admin-bar
	
	if (!function_exists('wf_remove_admin_bar_links') ) {
		function wf_remove_admin_bar_links() {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu('appearance');
			$wp_admin_bar->remove_menu('comments');
			$wp_admin_bar->remove_menu('updates');
			// $wp_admin_bar->remove_menu('dashboard');
			$wp_admin_bar->remove_menu('new-theme', 'new-content');
			$wp_admin_bar->remove_menu('get-shortlink');
			$wp_admin_bar->remove_menu('new-link', 'new-content');
			$wp_admin_bar->remove_menu('new-user', 'new-content');
			$wp_admin_bar->remove_menu('new-plugin', 'new-content');
		}
	
		add_action( 'wp_before_admin_bar_render', 'wf_remove_admin_bar_links' );
	}
	
	if (!function_exists('wf_change_admin_bar_menu') ) {
		// NB 'id' => 'something' is now mandatory for all items v3.43  *******************
		function wf_change_admin_bar_menu() {
			global $wp_admin_bar;
			if (!is_admin_bar_showing() ) //  !is_super_admin() || 
				return;
			$wp_admin_bar->add_menu( array(
			'id' => 'help_pages',
			'title' => __( 'Help pages'),
			'href' => FALSE ) );
			$wp_admin_bar->add_menu( array(
			'id' => 'site-admin',
			'parent' => 'help_pages',
			'title' => __( 'Site admin - Introduction'),
			'href' => wf_linkfix('/site-admin-introduction') ) );
			$wp_admin_bar->add_menu( array(
			'id' => 'editing-panel',
			'parent' => 'help_pages',
			'title' => __( 'Using the editing panel'),
			'href' => wf_linkfix('/using-the-editing-panel') ) );
			$wp_admin_bar->add_menu( array(
			'id' => 'with-text',
			'parent' => 'help_pages',
			'title' => __( 'Working with text'),
			'href' => wf_linkfix('/working-with-text') ) );
			$wp_admin_bar->add_menu( array(
			'id' => 'with-images',
			'parent' => 'help_pages',
			'title' => __( 'Working with images'),
			'href' => wf_linkfix('/working-with-images') ) ); // 3.23
			$wp_admin_bar->add_menu( array(
			'id' => 'pages-and-posts',
			'parent' => 'help_pages',
			'title' => __( 'Pages and Posts'),
			'href' => wf_linkfix('/pages-and-posts') ) );
			$wp_admin_bar->add_menu( array(
			'id' => 'page-extras',
			'parent' => 'help_pages',
			'title' => __( 'Using posts as page extras'),
			'href' => wf_linkfix('/using-posts-as-page-extras') ) );
			$wp_admin_bar->add_menu( array(
			'id' => 'widget-ref',
			'parent' => 'help_pages',
			'title' => __( 'Widget reference'),
			'href' => wf_linkfix('/widget-reference') ) );
			
			$wp_admin_bar->add_menu( array(
			'id' => 'test-page',
			'parent' => 'help_pages',
			'title' => __( 'Test page'),
			'href' => wf_linkfix('/test-page') ) );
			/*
			$wp_admin_bar->add_menu( array(
			'id' => 'validator',
			'title' => __( 'W3C Validator'),
			'href' => 'http://validator.w3.org/check?uri=referer' ) );
			*/
			/*
			$obj = get_page_by_path('development-log', OBJECT, 'wf_sitenote'); // v6.15
			if($obj) {
				$devlog_link = get_edit_post_link($obj->ID);
				$wp_admin_bar->add_menu( array( // v6.15
				'id' => 'devlog',
				'title' => __( 'Edit Dev Log'),
				'href' => $devlog_link));
			}
			*/
			
			$wp_admin_bar->add_menu( array( // rejigged v6.44
				'id' => 'development',
				'title' => __( 'Development'),
				'href' => FALSE ) );
				
			$devlog = get_page_by_path('development-log', OBJECT, 'wf_sitenote'); 
			if($devlog) {
				$devlog_link = get_edit_post_link($devlog->ID);
				
				$wp_admin_bar->add_menu( array( // v6.23
				'id' => 'dev_log',
				'parent' => 'development',
				'title' => __( 'Development log'),
				'href' => wf_linkfix('/wf_sitenote/development-log/') ) );
				$wp_admin_bar->add_menu( array( // v6.15
				'id' => 'edit_devlog',
				'parent' => 'development',
				'title' => __( 'Edit Dev Log'),
				'href' => $devlog_link));
			}
			$todo = get_page_by_path('to-do', OBJECT, 'wf_sitenote'); 
			if($todo) {
				$todo_link = get_edit_post_link($todo->ID);
				$wp_admin_bar->add_menu( array(
				'id' => 'to_do',
				'title' => __('To do'),
				'parent' => 'development',
				'href' => wf_linkfix('/wf_sitenote/to-do/') ) );
			}
			$wp_admin_bar->add_menu( array( 
			'id' => 'validator_new',
			'parent' => 'development',
			'title' => __( 'W3C Validator'),
			'href' => 'http://validator.w3.org/check?uri=referer' ) );
				
			}
	
		add_action('admin_bar_menu', 'wf_change_admin_bar_menu', 1000);
	}
	
	
	
	// VISUAL EDITOR ///////////////////////////////////////////////////////////////////////////////////////////
	
	if( !function_exists('wf_change_mce_options') ){
		/*
		function wf_change_mce_options( $init ) {
			$init['theme_advanced_blockformats'] = 'p,h2,h3,h4';
			//$init['theme_advanced_disable'] = 'forecolor';
			 
			$init['theme_advanced_buttons1'] = 'bold,italic,separator,bullist,numlist,separator,link,unlink,anchor,wp_more,separator,undo,redo,separator,formatselect,styleselect';
			$init['theme_advanced_buttons2'] = '';
			$init['theme_advanced_buttons3'] = '';
			$init['theme_advanced_styles'] = 'intro=intro'; // BSR: 'Intro=intro;Staff: role=staff_role;Governance: committee=committee'
			
			$init['extended_valid_elements'] = 'div[id|class]';
			//$init['content_css'] = THEME_FOLDER_URL."/css/editor.css?time=".time();
			$init['content_css'] = dirname( get_bloginfo('stylesheet_url') )."/css/editor.css?time=".time(); // v6.34
			return $init;
		}
		*/
		
		// Add new styles to the TinyMCE "formats" menu dropdown

		function wf_change_mce_options($settings) { // v6.72
			$new_styles = array(
				//array(
					//'title'	=> 'Custom Styles',
					//'items'	=> array(
							array(
							'title'		=> 'Intro',
							'block'	=> 'p',
							'classes'	=> 'intro',
						),
					//),
				//),
			);
			//$settings['style_formats_merge'] = true;
			$settings['style_formats'] = json_encode( $new_styles );
			
			$settings['toolbar'] = json_encode(array("bold italic | bullist numlist | link unlink anchor wp_more | undo redo | formatselect styleselect"));
			$settings['toolbar2'] = false;
			$settings['block_formats'] = "Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4";
			//$settings['extended_valid_elements'] = json_encode(array('div[id|class]'));
			
			return $settings;
		}
		
		if ( is_user_logged_in() ) {	
			add_filter('tiny_mce_before_init', 'wf_change_mce_options');
			add_filter('mce_buttons', function ($buttons) {
				array_push($buttons,'styleselect');
				return $buttons;
			});
		}
	}
	
		
	
	// HTML EDITOR ///////////////////////////////////////////////////////////////////////////////////////////
	
	// myownhomeserver.com/2011/10/wordpress-addremove-quicktags-simple-editor-buttons-in-3-3/
	// See also: wordpress.org/extend/plugins/jayj-quicktag/
	// wordpress.stackexchange.com/questions/29675/add-quicktag-buttons-to-the-html-editor
	// and lots of useful comments in wp-includes/js/quicktags.dev.js
	
	
	if( !function_exists('wf_set_default_quicktags') ){ // HTML editor
		
		// WARNING !! - This will prevent any new buttons from being added, such as custom ones, unless they
		// are also explicitly added to the 'buttons' variable!
		
		function wf_set_default_quicktags( $qtInit ){
			$qtInit['buttons'] = 'spell,link,em_wf,strong_wf,ul,li,ol,code,more,h2,h3,h4,intro,date_wf'; // v6.18 
			return $qtInit;
		}
		
		if ( is_user_logged_in() ) {
			add_filter('quicktags_settings', 'wf_set_default_quicktags', 10, 1);
		}
	}
	
	
	
	if( !function_exists('wf_add_quicktags') ):
		function wf_add_quicktags() { ?>
		
			<script type="text/javascript">
				if ( typeof QTags != 'undefined' ) {
					 // Params for this are:
					 // Button HTML ID (required)
					 // Button display, value="" attribute (required)
					 // Opening Tag (required)
					 // Closing Tag (required)
					 // Access key, accesskey="" attribute for the button (optional)
					 // Title, title="" attribute (optional)
					 // Priority/position on bar, 1-9 = first, 11-19 = second, 21-29 = third, etc. (optional)
					
					QTags.addButton('h2','h2','<h2>','</h2>');
					QTags.addButton('h3','h3','<h3>','</h3>');
					QTags.addButton('h4','h4','<h4>','</h4>');
					QTags.addButton('intro','intro','<p class="intro">','</p>');
					QTags.addButton('strong_wf','strong','<strong>','</strong>');
					QTags.addButton('em_wf','emphasis','<em>','</em>');
											
					// Add function callback button
					// Params are the same as above except the 'Opening Tag' param becomes the callback function's name
					// and the 'Closing Tag' is ignored.
					
					function wf_date() {// v6.18 
						var user = jQuery("#wp-admin-bar-user-info .display-name").text();
						
						var d = new Date;
						var fullYr = d.getFullYear() +'';
						var date_string = d.getDate() + '.' + (d.getMonth()+1) + '.' + fullYr.substr(2,2);
						var time_string = d.getHours() + ':' + d.getMinutes();
						return "<p class='dev_sig'><strong>" + user +"</strong> " + date_string + " " + time_string +"</p>\n\n";
						//return x.toDateString();
					}
					QTags.addButton('date_wf','date',''+ wf_date() ); // v6.18 NB This has to come after function 
				}
				
			</script> <?php  
		}
		
		if ( is_user_logged_in() ) {
			add_action('admin_print_footer_scripts',  'wf_add_quicktags', 100); // or 'wp_footer' (for front-end only)
		}
	
	endif;
	
	
	
	
	
	// CUSTOM POST TYPES  ///////////////////////////////////////////////////////////////////////////////////////
	
	
	
	if( !function_exists('wf_create_post_type') ):
		function wf_create_post_type() {
			register_post_type( 'wf_snippet',
				array(
					'labels' => array(
						'name' => __( 'Snippets' ),
						'singular_name' => __( 'Snippet' ),
						'add_new_item' => 'Add New Snippet', // v6.56
						'edit_item' => 'Edit Snippet'
					),
					'public' => true,
					'has_archive' => true,
					'supports' => array('title','editor','custom-fields', 'revisions','thumbnail','excerpt') // v3.61
				)
			);
			register_post_type( 'wf_sitenote',
				array(
					'labels' => array(
						'name' => __( 'Site Notes' ),
						'singular_name' => __( 'Site Note' ),
						'add_new_item' => 'Add New Site Note', // v6.56
						'edit_item' => 'Edit Site Note'
					),
					'public' => true,
					'has_archive' => false,
					'supports' => array('title','editor','custom-fields', 'revisions','thumbnail','excerpt') // v3.61
				)
			);
		}
		
		add_action( 'init', 'wf_create_post_type' );
		
	endif;
	
	if( !function_exists('wf_taxonomy_init') ):
		function wf_taxonomy_init() {
			register_taxonomy(
				'wf_sitenote_type',
				'wf_sitenote',
				array(
					'hierarchical' => true,
					'label' => __( 'Site Note type' ),
					'rewrite' => true
				)
			);
			register_taxonomy(
				'wf_snippet_type',
				'wf_snippet',
				array(
					'hierarchical' => true,
					'label' => __( 'Snippet type' ),
					'rewrite' => true
				)
			);
		}
		add_action( 'init', 'wf_taxonomy_init' );
	endif;
	
	// www.ilovecolors.com.ar/add-taxonomy-columns-custom-post-types-wordpress/
	
	if( !function_exists('wf_sitenote_columns') ):
		function wf_sitenote_columns($defaults) {
			$defaults = array(
				'cb' => '',
				'title' => 'Title',
				'author' => 'Author',
				'wf_sitenote_type' => 'Site note type',
				'date' => 'Date'
			);
			return $defaults;
		}
		add_filter( 'manage_wf_sitenote_posts_columns', 'wf_sitenote_columns' );
	endif;
	
	
	
	if( !function_exists('wf_snippet_columns') ):
		function wf_snippet_columns($defaults) {
			$defaults = array(
				'cb' => '',
				'title' => 'Title',
				'author' => 'Author',
				'wf_snippet_type' => 'Snippet type',
				'date' => 'Date'
			);
			return $defaults;
		}
		add_filter( 'manage_wf_snippet_posts_columns', 'wf_snippet_columns' );
	endif;
	
	
	
	if( !function_exists('wf_custom_column') ):
		function wf_custom_column($column_name, $post_id) {
			$taxonomy = $column_name;
			$post_type = get_post_type($post_id);
			$terms = get_the_terms($post_id, $taxonomy);
		 
			if ( !empty($terms) ) {
				foreach ( $terms as $term )
					$post_terms[] = "<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " . esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
				echo join( ', ', $post_terms );
			}
			else echo '<i>No terms.</i>';
		}
		add_action('manage_wf_sitenote_posts_custom_column', 'wf_custom_column', 10, 2);
		add_action('manage_wf_snippet_posts_custom_column', 'wf_custom_column', 10, 2);
	endif;
	
	
	
	
	
	
	// from: wordpress.org/support/topic/show-categories-filter-on-custom-post-type-list
	if( !function_exists('wf_restrict_manage_posts') ):
		function wf_restrict_manage_posts() {
			global $typenow; // empty string when in media
			$taxonomy = $typenow.'_type';
			//var_dump($typenow);
			if( $typenow != "page" && $typenow != "post" && $typenow !=""){ // v3.68 empty (not attachment)
				$filters = array($taxonomy);
				//var_dump($filters);
				foreach ($filters as $tax_slug) {
					$tax_obj = get_taxonomy($tax_slug);
					//var_dump($tax_obj);
					if($tax_obj !== false) { //v3.80 coz there may not be any taxonomies
						$tax_name = $tax_obj->labels->name;
						$terms = get_terms($tax_slug);
						//var_dump($terms);
						echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
						echo "<option value=''>Show All $tax_name</option>";
						foreach ($terms as $term) { 
							$selected = isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : ''; // v3.68 wordpress.stackexchange.com/a/31225
							echo '<option value='. $term->slug, $selected,'>' . $term->name .' (' . $term->count .')</option>'; 
						}
						echo "</select>";
					}
				}
			}
		}
		add_action( 'restrict_manage_posts', 'wf_restrict_manage_posts' );
	endif;
	
	
	
	
	
	// This can be successfully overwritten in functions.php in function load_before_lib()
	// Currently only used to determine which pages the WF widget metabox gets added to
	if(!function_exists('get_widget_help_posttypes')) { 
		function get_widget_help_posttypes() { // v3.67
			//return array_merge(get_post_types( array('_builtin'=> false)),array('post','page') );
			$post_types = array(
				'post' => 'Posts',
				'page' => 'Pages'
			);
			$pt_objects = get_post_types( array('_builtin'=> false),'objects'); // v6.2 now returns objects
			foreach($pt_objects as $pt => $pt_object) {
				$post_types[$pt] =  $pt_object->label;
			}
			$post_types['attachment'] =  'Images';
			//var_dump($post_types);
			return $post_types; // in a fairly sensible order. Items can be subsequently unset or edited // v6.2
		}
	}
	
		
	function get_post_listing_dropdown($post_type, $name) { // v4.10
		global $wpdb;
		$results = $wpdb->get_results( 
			"
			SELECT ID, post_title, post_mime_type 
			FROM $wpdb->posts
			WHERE post_type = '".$post_type."' 
				AND (post_status = 'publish' OR post_status = 'future' OR post_status = 'inherit')
			"
		); // post_status 'inherit' is for attachments
		$html = "<select name='".$post_type."' class='post_type'>\n"; // v4.10 removed style='width: 90%;'
		$html .= "<option value='0' selected='selected'>".$name."</option>\n";
		foreach($results as $result) {
			if($post_type == "attachment" && strpos($result->post_mime_type,'image') === false) {
				continue;
			}
				$html .= "<option value='".$result->ID."'>";
				$html .= $result->ID.": ";
				$html .= $result->post_title;
				$html .= "</option>\n";
			}
		$html .= "</select>\n";
		return $html;
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	function remove_admin_bar_style_frontend() {  // v3.38
		echo '<style type="text/css" media="screen"> 
		html { margin-top: 0px !important; } 
		* html body { margin-top: 0px !important; } 
		</style>';  
	}  
	
	
	
	require_once( dirname( __FILE__ ) . '/widgety_functions.php' );// v6.1
	
	
	function load_wfdb() {
		//require_once( dirname( __FILE__ ) . '/wf_db_classes.php' );// v6.1
		require_once( WF_LIB_PLUGIN_PATH . '/wf_db_classes.php' ); // v6.17
	}
	
	/*
	// not required because wf_lib loads this anyway - so available for all subsequent widgets
	function load_update_checker() { // v6.52
		require_once( WF_LIB_PLUGIN_PATH . '/plugin-updates/plugin-update-checker.php' ); 
	}
	*/





}