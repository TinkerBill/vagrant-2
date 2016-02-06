<?php

// Experimental devlog.php for wf_library - a single place where all the site changes are logged.
// ...means we don't have to go searching for latest version number

/*
v6.74 8/12/15	wf_validation.php: Added function uk_postcode().

		2/12/15	BEWARE!  array_column() built-in php version has additional $index_key parameter - means that wf_lib version behaves differently! 

	  17/11/15	wf_db_classes.php: Changed how we set $csv_settings['intro']
				Also, replaced function backup() with more versatile function export_csv().
				NB: Will now need to adjust metacarpool csv stuff to match.
				Validation of dropdowns etc can be done by specifying a csv_settings['validate_function']. See (eg) function carplus_csv_validate().

	  21/10/15	wf_library.php: Added function write_file($filename, $somecontent) - with function_exists(). See wf_newsletter.php	
				wf_db_classes.php: Added function csv_from_array().  Also export_csv() - with download option.

		5/10/15	wf_db_classes.php: Now using qs_restore() in function csv_upload($qmode)
				Added private function has_auto_index(). Added csv_settings['pre_insert_function'] to allow optional
				backing up and deletion of existing data before adding any more. 
				Added public function backup($where=false,$backup_folder,$slug_base) and public function delete($where=false).
				Changed Form_MCP->table_name to public (from protected).
				
		13/9/15	Removed mysql_real_escape_string from function clean_up($text) because adds slashes to MySQL

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
*/