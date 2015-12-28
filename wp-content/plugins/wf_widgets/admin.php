<?php
/*

v6.66 23/4/14	Added 'jquery-ui-dialog' as dependency to wf_admin.js
				WordPress 3.9 breaks widget admin css - tweaked. Need to check in IE.
				Streamlined wf_widget_box_contents()
				Added fn empty_or_cancelled() and changed conditions to reflect use of left_cancel etc

v6.60  7/2/14	Anon function in widget_default_specs.php and admin.php doesn't work with PHP <5.3
				Replaced now with function wf_get_active_plugin_dirs($value) in wf_widgets.php
		6/2/14	Adding "Break inheritance?" checkbox.

v6.57	2/2/14	Moved wf_admin.js to scripts folder.

v6.55	9/12/13	Added "Selecting doesn't do anything" line to Available items box in admin.php

v6.52  30/10/13	Now using get_insertable_posttypes() in post widget. Epilepsy site kept losing widgets because of 
				tu post types. Changes to functions.php (if override required), frontend.php and wf_widgets.php. 
				Function posttype_accepts_widgets($postType) and default get_insertable_posttypes() both start
				from new function get_widget_accepting_posttypes(). Also posttype_accepts_debugbox(). 
				All except posttype_accepts_widgets() can be overridden in functions.php.
				
v6.47	23/9/13	Found a couple of instances in wf_widgets.php of class endlink (deprecated). Added in coverlink as well.

				Specifying appropriate regions list for a post/page: Removed Wf_Widget::$regions and replaced with 
				function get_regions_list($post) defined in widget_config.php. Files affected: wf_widgets.php and
				admin.php.
				
v6.45	5/9/13	Loading wf_widget_admin.css later (100) so it comes after the default wp styles

v6.40	15/8/13	Changed add_wf_widget_box($postType) in admin.php so that it now checks pluggable function 
				posttype_accepts_widgets($postType). Same with save_wf_widget_metadata(). Default version of
				posttype_accepts_widgets() is now in wf_widgets.php

v6.28	27/7/13	Added post_type 'page' to the 'Available items' box. Also tweaked Post and Slideshow widgets to allow page IDs.

v6.26	25/7/13	Added in v6.11 stuff that had somehow got skipped over

v6.25	13/7/13	Added function add_wf_debug_dashboard_box() so we can see debugging info in dashboard
				Hooked loading of wf_widget_admin.css to 'admin_print_styles' so it works for dashboard too.
				
v6.11	13/6/13	Replaced hardwired urls for glob.theme_dir and glob.plugins_dir in wf_admin.js with wp_localize_script() versions.

v6.5	24/5/13	Added div#user_roles = comma-separated list of available roles

v5.13	2/5/13	Minor change to function add_wf_widget_box() so that styles and scripts only get enqueued when required.

v5.9	25/4/13	Sorted warning when doing Quick Edit. Bail out of function save_wf_widget_metadata() if 
				$_POST['list_of_deletes'] not set.
				
v4.12	9/4/13	function get_regional_inheritance() changed to return current and inherited widgets, for use
				in new widget interface. Changed function wf_widgets($regions) to match 

*/ 
  
 
 // add a custom admin stylesheet - this will be global for admin and also login
function custom_admin_style() { // v6.25
	wp_enqueue_style('wf_widget_admin_css', WF_WIDGETS_PLUGIN_URL.'/css/wf_widget_admin.css');
}
add_action( 'admin_print_styles', 'custom_admin_style', 100 ); //v6.45




// Wf widget picker    
function add_wf_widget_box($postType) {  

	if(posttype_accepts_widgets($postType)) { // 6.40
		add_meta_box( 
			'wf_widget_box_id',
			'Wingfinger Widgets',
			'wf_widget_box_contents',
			$postType, // v3.67
			'normal'
		);
		
		wp_register_style( 'jquery_ui_css', WF_WIDGETS_PLUGIN_URL.'/css/jquery-ui.min.css' ); //v4.13
		wp_enqueue_style( 'jquery_ui_css');		
		wp_enqueue_style( 'awesome_css', 'http://netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css'); // v5.16
		
		wp_enqueue_script('jquery-ui-sortable'); // v3.84 for new widget interface
		wp_enqueue_script('jquery-ui-tabs'); // v3.84 for new widget interface
		wp_enqueue_script( 'wf_admin_js', WF_WIDGETS_PLUGIN_URL.'/scripts/wf_admin.js', array('jquery','jquery-ui-sortable','jquery-ui-tabs','jquery-ui-dialog')); //v6.57 //v6.66 'jquery-ui-dialog'
		$wf_admin_params = array( // v6.11
			'theme_dir' => get_stylesheet_directory_uri(),
			'plugins_dir' => plugins_url()
		);
		wp_localize_script( 'wf_admin_js', 'wf_admin_params', $wf_admin_params); // v6.11
	}
}

add_action( 'add_meta_boxes', 'add_wf_widget_box' ); // do_action('add_meta_boxes', $post_type, $post);
 

function add_wf_debug_box($postType) {
	if(class_exists('Wf_Debug') && Wf_Debug::output('bottom') != '' && posttype_accepts_debugbox($postType)) { // v6.52
		add_meta_box( // v6.20
			'wf_debug_box_id',
			'Wingfinger debugging',
			'wf_debug_box_contents',
			$postType, // v3.67
			'normal'
		);
	}
}

   
add_action( 'add_meta_boxes', 'add_wf_debug_box', 99 ); 

function wf_debug_box_contents() {
	echo Wf_Debug::output('bottom');
}

function wf_debug_dashboard_box() {// v6.25
		wf_debug_box_contents();
} 

function add_wf_debug_dashboard_box() {// v6.25
	if(class_exists('Wf_Debug') && Wf_Debug::output('bottom') != '') { 
		 wp_add_dashboard_widget('wf_debug_box_id', 'Wingfinger debugging info', 'wf_debug_dashboard_box'); // NB: same id as metabox version
	}
} 
add_action('wp_dashboard_setup', 'add_wf_debug_dashboard_box', 99 ); // v6.25


//echo Wf_Debug::output('bottom'); // class defined in wf_lib, with users set in widget_config.php

/*
For each region, the visible widgets are listed as a table with columns: order, widget_type, description(=comment).

For current (as opposed to inherited) widgets:
‰¥¢ The table rows (widget instances) are sortable by dragging.
‰¥¢ Each row has a td.edit and a td.delete  [OBSOLETE: and a td.params_store.]
[OBSOLETE: ‰¥¢ td.params_store contains an invisible .pstore_div containing a div for each parameter, with class = $pname and contents = $pvalue.]
[OBSOLETE: ‰¥¢ When 'Edit' link is clicked, a temporary editor table is constructed using the data in .pstore_div and placed in div#widget_editor.]
[OBSOLETE: ‰¥¢ #widget_editor is wrapped in div#inner_wrap, which is then appended to td.params_store. (Divs allow visibility and positioning.)]

Original system carried widget info across from PHP to Javascript by stashing it in td.params_store.

In the new system, Javascript extracts this data directly from the customfields.

*/

	
function wf_widget_box_contents() {
	
	// v6.66
	function empty_or_cancelled($current_or_inherited) {
		return (empty($current_or_inherited) || $current_or_inherited[0]['widget_type'] == 'cancel');
	}

	
	global $post; // v6.47
	$regions = get_regions_list($post); // v6.47
	$wf_widgets = Wf_Widget::list_wf_widgets($regions); // v6.47
	$html_start = "\n\n
		<div id='widget_box'>\n
		<div id='hidden_fields'>\n
		<textarea  cols='30' rows='2' name='list_of_deletes' id='list_of_deletes'></textarea>\n
		<textarea  cols='30' rows='2' name='list_of_updates' id='list_of_updates'></textarea>\n
		<div id='cfields_to_update'>\n</div>\n
		</div>\n
		<div id='admin_region_list'>\n\n
		<ul class='category-tabs'>\n";
	
	$html = "</ul>\n\n"; // start by finishing off 	$html_start and then assemble later
		
	foreach($regions as $region_num => $region) { // v6.47
		$html_start .= "<li><a href='#region".$region_num."'>Region ".$region."</a></li>\n";
		
		$html .= "<div id='region".$region_num."' class='region region_".$region."'>\n"; // id used by tabs
		$html .= "<h4 class='".$region."'>Region ".$region."</h4>\n";
		
		$current = $wf_widgets[$region]['current'];// v6.66
		$inherited = $wf_widgets[$region]['inherited'];// v6.66
		
		$inherit_vis = empty_or_cancelled($current) ? "" : " style='display: none;' "; // v6.66
		
		
		//d('$current ('.$region.')',$current);	
		//d('$inherited ('.$region.')',$inherited );	
		//d('$inherit_vis',$inherit_vis);	
		
		
		// makes sense to include this in .inherited as it gets displayed under similar conditions
		if(empty_or_cancelled($current) && empty_or_cancelled($inherited)) {// v6.66
			$html .= "\n
					<div class='inherited' ".	$inherit_vis.">\n
					<p class='strapline'>There are no widgets for this region.</p>
					</div>\n";
		} else {
			if(!empty_or_cancelled($inherited)) {// v6.66
				// Even if there are 'current' widgets, we still need to create this (hidden) list so that js can display it if
				// the last current widget is removed.
				$html .= "\n
					<div class='inherited' ".$inherit_vis.">\n
					<p class='strapline'>Widgets inherited from ".$inherited[0]['aboveness']." page(s) above:</p>\n
					<table cellspacing='0' class='widget_table inherited'>\n
					<tbody>\n";
				
				foreach($inherited as $c => $r_widget) {
					$params = trim_parse($r_widget['qstring']); // v6.4 Was Wf_Widget::trim_parse
					$comment = (isset($params['comment'])) ? $params['comment'] : "&nbsp;";
					$html .= "\n
						<tr class='".$r_widget['widget_type']."'>\n
						<td class='order'>".($c + 1)."</td>\n
						<td class='widget_type'>".Wf_Widget::$current_specs[$r_widget['widget_type']]['display_name']."</td>\n
						<td class='widget_instance_name'>".$comment."</td>\n
						<td class='edit'>&nbsp;</td>\n
						<td class='delete'>&nbsp;</td>\n
						</tr>\n";
				}
				$html .= "</tbody>\n</table>\n"; //v6.60
				//v6.60
				$html .= "<div class='break_inherit'>\n";
				$html .= "<label for='".$region."_break_inherit'>Cancel inheritance?</label>\n";
				$html .= "<input id='".$region."_break_inherit' type='checkbox'  value='' name='".$region."_break_inherit'>"; //v6.60
				$html .= "</div>\n";
				
				$html .= "</div>\n"; // finish off div#inherited
			} // if(!empty_or_cancelled($inherited))
			
			if(!empty_or_cancelled($current)) {// v6.66
				$html .= "\n
					<div class='current'>\n
					<p class='strapline'>Widgets on this page: &nbsp;(Drag to reorder)</p>\n
					<table cellspacing='0' class='widget_table current'>\n
					<tbody class='widget_sort'>\n";
			
				foreach($current as $c => $r_widget) {
					$params = trim_parse($r_widget['qstring']); // v6.4 Was Wf_Widget::trim_parse
					$comment = (isset($params['comment'])) ? $params['comment'] : "&nbsp;";
					$html .= "\n
						<tr class='".$r_widget['widget_type']."'>\n
						<td class='order'>".($c + 1)."</td>\n
						<td class='widget_type'>".Wf_Widget::$current_specs[$r_widget['widget_type']]['display_name']."</td>\n
						<td class='widget_instance_name'>".$comment."</td>\n
						<td class='edit'><i class='icon-large icon-edit' title='Edit this widget'></i></td>\n
						<td class='delete'><i class='icon-large icon-remove-sign' title='Delete this widget'></i></td>\n
						</tr>\n";
				}
				$html .= "</tbody>\n</table>\n</div>\n"; // finish off div#current
			} // if(!empty_or_cancelled($current)) 
		}
		$html .= "</div>\n\n"; // .region		
	}
	$html .= "<input id='update2' class='button button-primary button-large' type='button' value='Update'>"; // copy of blue update button
	$html .= "</div>\n";
	$html .= get_widget_dialog(); // the (initially hidden) skeleton of the dialog box, complete with available items	
	$html .= "<br/ class='clearboth'>\n";
	$html .= "</div>\n\n";
	echo $html_start.$html;
}



// Builds the modal dialog box for Add/Edit a widget
function get_widget_dialog() {
	
	$post_types = get_insertable_posttypes(); // v6.52 Was get_widget_help_posttypes();
	d('$post_types',$post_types); // (F3) calls Wf_Debug
	
	$html = "<div id='widget_dialog_box'>\n"; // wrapper round whole thing
	$html .= "<div id='dialog-form'></div>\n"; // the form
	$html .= "<div id='available_items'>\n"; // the available posts etc to insert
	$html .= "<h3>Available items to insert...</h3>\n"; 
	$html .= "<p>(For info only: &lsquo;selecting&rsquo; doesn't do anything)</p>\n"; 
	
	$cat_html = "<div id='post_type_picker'>\n";
	$cat_html .= "<select name='post_type' class='widefat' id='post_type'>\n" ; 
	$cat_html .= "<option value='0'>Select...</option>\n";  
	
	foreach($post_types as $post_type => $name) { 
		$html .= get_post_listing_dropdown($post_type, $name);// defined in wf_library.php
		$html .= get_term_dropdown($post_type, $name);
		$cat_html .= get_post_type_option($post_type, $name); //v5.19 v6.2
	}
	
	// Now add the post_type param selector (that is moved by js)  // v5.19
	$html .= $cat_html;
	$html .= "</select>\n";
	$html .= "</div>\n";
	
	// Now add the list of active plugins to pass via Ajax as a comma-separated string
	//$active_plugins = get_option('active_plugins'); // typical array item: "wf_widget_forms/wf_forms.php" // v6.2
	/*
	//v6.60 replaced now with function wf_get_active_plugin_dirs($value) in wf_widgets.php
	$active_plugin_dirs = array_map(function($value) { // v6.3
		$path_array = explode('/',$value);
		return $path_array[0];
	}, get_option('active_plugins'));
	*/
	
	$active_plugin_dirs = array_map('wf_get_active_plugin_dirs', get_option('active_plugins'));	//v6.60
	
	//var_dump($active_plugin_dirs);
	$html .= "<div id='active_plugin_dirs'>".implode(',',$active_plugin_dirs)."</div>\n"; // eg: syntaxhighlighter,wf_library,wf_widget_forms,wf_widgets
	$html .= "<div id='user_roles'>".implode(',',array_keys(get_role_names()))."</div>\n"; // comma-separated list of available roles
	
	//var_dump(implode(',',array_keys(get_role_names()))); // comma-separated list of available roles

	$html .= "</div>\n"; // #available_items
	$html .= "</div>\n"; // #widget_dialog_box
	return $html;		
}

function get_role_names() {
	global $wp_roles;
	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();
	return $wp_roles->get_names();
}

function get_term_dropdown($post_type, $name) {
	if($post_type == 'attachment' || $post_type == 'page' ) // v6.28 added page
		return '';
	$taxonomy = $post_type.'_type';
	if($post_type == 'post')
		$taxonomy = 'category';
	$args = array(
    	'hide_empty' => false
	);
	$terms = get_terms($taxonomy, $args); 
	$count = count($terms);
	if( $count > 0 ){
		$html = "<select name='".$taxonomy."' class='taxonomy'>\n";
		$html .= "<option value='0' selected='selected'>".substr($name, 0, -1)." categories</option>\n";
		foreach($terms as $term) {
			$html .= "<option value='".$term->term_id."'>";
			$html .= $term->term_id.": ";
			$html .= $term->name;
			$html .= " (".$term->count." items)";
			$html .= "</option>\n";
		}
		$html .= "</select>\n";
	return $html;
	 }
}

function get_cat_option($post_type, $name) { // v5.19
	if($post_type == 'attachment' || $post_type == 'page' ) // v6.28 added page
		return '';
	$taxonomy = $post_type.'_type';
	$tax_name = substr($name, 0, -1)." type";
	if($post_type == 'post') {
		$taxonomy = 'category';
		$tax_name = 'Post category';
	}
	return "<option value='".$taxonomy."'>".$tax_name."</option>\n";
}

function get_post_type_option($post_type, $name) { // v6.2 eg: "wf_snippet","Snippets"
	if(in_array($post_type, array('attachment'))) { // neither can be used with list widget// v6.28 removed page
		return '';
	} else {
		return "<option value='".$post_type."'>".substr($name, 0, -1)."</option>\n";
	}
}



function save_wf_widget_metadata($post_id) {
	$post_type = get_post_type( $post_id );
	if(!posttype_accepts_widgets($post_type)) // v6.40
        return;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    	return $post_id;
	if(!isset($_POST['post_title']))
		return;
	if(!isset($_POST['list_of_deletes'])) // v5.9 bail out if doing Quick Edit
		return;
		
	$list_of_deletes = array_map('trim', explode("\n",$_POST['list_of_deletes']));// trim appears to be necessary (to get rid of invisible "\r" perhaps?)
	$list_of_updates = array_map('trim', explode("\n",$_POST['list_of_updates']));

	// Delete any customfields on the delete list	
	if($list_of_deletes[0] != '') {
		foreach($list_of_deletes as $item_to_delete) {
			if(!in_array($item_to_delete, $list_of_updates)) { // no point in deleting it if it's going to be updated
				if(!delete_post_meta($post_id, $item_to_delete)) { // originally with trim
					//return  new WP_Error('delete_cfield', __("Can't delete customfield: ".$item_to_delete));
					echo "Can't delete customfield: ".$item_to_delete;
				}
			}
		}
	}
		
	// Update any customfields on the update list
	if($list_of_updates[0] != '') {
		foreach($list_of_updates as $item_to_update) {
			if(!update_post_meta($post_id, $item_to_update, $_POST[$item_to_update])) {
				//return  new WP_Error('update_cfield', __("Problem updating customfield: ".$item_to_update));
				//echo "Problem updating customfield: ".$item_to_update;
				// NB: returns false if value is unchanged - so can't check for genuine errors
			}
		}
	}

}

add_action( 'save_post', 'save_wf_widget_metadata');

