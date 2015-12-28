<?php

// These are functions used in widgets, but also possibly used elsewhere as well.
// Eventually add them to wf_lib?

/*

v6.65  18/10/14	Moved various general WP functions from l4c functions.php into wf_library.php	
				
				SITE-BREAKER: $catfather is now called $catfather_array and is defined in widget_config.php
				Major changes to get_godfather() which now uses get_generations() to do all the work.
				Other changes in wf_widgets.php (v6.73), widgety_functions.php, index.php
				
v6.57  28/3/14	Added function widget_html($widget_class, $style, $qstring) to widgety functions. 
				Reduces dependency on wf_widgets in frontend. 
				
v6.51  26/10/13	Changed deprecated split() to explode() in get_godfather()

v6.50 	7/10/13	Changed get_godfather() to work with sub-categories - but not if more than 1 box ticked

v6.49	2/10/13	Changed get_godfather() so that $catfather gets treated as an array  

v6.39	14/8/13	Copied pluggable function add_to_message() to widgety_functions.php from wf_forms.php. Required by 
				wf_db_classes.php and wf_forms.php. Was causing fatal error for UTA.
				
v6.11	13/6/13	Changed function wf_get_credits($img_id) so it bails out if $img_id is empty. Don't want to get credits for 
				videos in Slideshow widget.
*/


/* //v6.65
function get_godfather($page_id, $catfather) { // $catfather is eg: array ('events' => 12, 'voices' => 18);
	$ancestors=(get_post_ancestors($page_id)); // parent = first, godfather = last
	$godfather = $page_id; // default for single posts as well  3.20
	if(count($ancestors) > 0){ // ie: this is not a toplevel page
		$godfather=end($ancestors);
	}	
	if(is_single() && !empty($catfather)) { // v6.49  != '') {
		$cfkeys = array_keys($catfather); // because $catfather doesn't have numeric keys
		for($i=0;$i< count($catfather); $i++) {
			$cat = get_the_category(); // array of objects
			
			if(count($cat) > 0) { // v6.50 changed to work with sub-categories - but not if more than 1 box ticked
				$parentCatList = get_category_parents($cat[0]->cat_ID,false,',',true); //param 4 = Whether to use nice name for display. 
				$parentCatListArray = explode(",",$parentCatList);// v6.51 Was split()
				array_pop($parentCatListArray); // remove last item because string ends with separator
				if($catfather[$cfkeys[$i]] == $parentCatListArray[0]) {
					$godfather = $catfather[$cfkeys[$i]];
				}
			}
			
			//if(in_category($cfkeys[$i])) {
				//$godfather = $catfather[$cfkeys[$i]];
			//}
			
		}
	}
	return $godfather;
}
*/

// v6.65
function get_generations($id,$catfather_array=array()) { // $catfather_array eg: array ('events' => 12, 'voices' => 18) - these are 'category_nicename' aka 'slug'
	// $catfather_array associates categories with pages - not necessarily at top level
	$lowest_page_id = $id; // but if this a post with a catfather, use the catfather instead
	if(is_single($id) && !empty($catfather_array)) { // v6.49  != '') {
		$cat = get_the_category($id); // array of objects, one for each category assigned - but only $cat[0] is used
		d('$cat',$cat);
		if(count($cat) > 0) { // v6.50 changed to work with sub-categories - but not if more than 1 box ticked
			$parentCatList = get_category_parents($cat[0]->cat_ID,false,',',true); //param 4 = Whether to use catslug (=nicename) for display. 
			$parentCatListArray = explode(",",$parentCatList);// array of catslugs
			$top_parent_cat = $parentCatListArray[0];
			d('$top_parent_cat',$top_parent_cat);
			foreach($catfather_array as $catslug => $cf_id) {
				if($catslug == $top_parent_cat) { // AND category inheritance hasn't been turned off?
					$lowest_page_id = $cf_id; 
				}
			}
		}
	}
	d('$lowest_page_id',$lowest_page_id);
	// ie: we're now starting either from the current ID or the $catfather ID, neither of which is necessarily at top level
	$generations = get_post_ancestors($lowest_page_id); // parent = first, godfather = last
	array_unshift($generations, $lowest_page_id); // $lowest_page_id followed by all ancestors
	if($lowest_page_id != $id) { // ie: $id is a post with a catfather of $lowest_page_id
		array_unshift($generations, $id); // can't get rid of this without breaking current/inherited stuff
	}
	d('$generations',$generations);
	return $generations;
}

// v6.65
function get_godfather($id,$catfather_array=array()) {
	$generations = get_generations($id,$catfather_array);
	return end($generations);
}


function qscode($cv_value) {
	$cv_value = preg_replace("/[ \t]*=[ \t]*/","=",$cv_value); // get rid of any spaces and tabs around =
	$query_string = str_replace("\r\n","&", $cv_value); // Windows uses \r\n
	$query_string = str_replace("\n","&", $query_string); // Mac uses \n
	return $query_string;
}

// returns the value from one name/value pair in a custom field
function get_cf_pair_value($post_id, $customfield_name, $pair_name) { 
	$cf_value = get_post_meta($post_id, $customfield_name, true);// returns empty or string
	$cf_value_array = trim_parse(qscode($cf_value));
	return (isset($cf_value_array[$pair_name])) ? $cf_value_array[$pair_name] : ''; // v6.4
}


// cleans up output of parse_str by removing leading and trailing white space 
// also used in functions.php
function trim_parse($qstring) { // v3.51 now copes with legacy versions "87" or "5,93,14"
	if(strpos($qstring,"=")===false) { // no "=" found, so... 
		$q_array['ids']=$qstring; // it's the simpler version: "87" or "5,93,14"
	} else {
		parse_str($qstring, $q_array); // parses the querystring into an associative array
	}
	foreach($q_array as $key => $value) {
		$q_array[$key] = trim($value);
	}
	return $q_array;
}

function get_ranked_posts($args) { // used in functions scode_insert_slideshow3 and scode_insert_items
	$id_array = array_flip($args['post__in']); // [155] -> 0, [317] => 1, [158] => 2
	$rankedposts= array();
	$sideposts = get_posts($args); // which returns them in an unpredictable order (or, possibly, order of ID)
	foreach($sideposts as $sidepost) {
		$sid = $sidepost->ID;
		$n = $id_array[$sid]; // the required rank
		$rankedposts[$n] = $sidepost;
	}
	ksort($rankedposts);
	// preint_r($rankedposts);
	return $rankedposts;
}

function change_width_and_height($img_html, $width, $height) {
	$find_w = "/width=\"(\d+)\"/";
	$replace_w = "width=\"".$width."\"";
	$img_html = preg_replace($find_w,$replace_w,$img_html);
	$find_h = "/height=\"(\d+)\"/";
	$replace_h = "height=\"".$width."\"";
	$img_html = preg_replace($find_h,$replace_h,$img_html);
	return $img_html;
}


// Looks to see if there's a featured pic for this post. If so, drops it in at the size specified
function get_feature_pic($id, $size) { // v3.65
	if (has_post_thumbnail( $id) ) {
		$feature_pic_id = get_post_thumbnail_id( $id );	 // v3.65	
		$img_html = wp_get_attachment_image($feature_pic_id, $size);
		//$img_html .= wf_get_caption($rankedpost,$params['caption']); // 3.26
		$img_html .= wf_get_credits($feature_pic_id); // returns empty if no 'credit=' in description, otherwise a suitable <div> (or <a> if it's a link) v3.65
		$img_html = "<div class='picwrap'>".$img_html."</div>\n"; // v3.65
		return $img_html; // v3.65
	}
}

function insert_feature_pic($id, $size) { // v3.65
	echo get_feature_pic($id, $size);
}




function wf_get_credits($img_id) { // returns empty if no 'credits=' in description, otherwise a suitable <div> (or <a> if it's a link)
	if($img_id == '') { // v6.11 
		return '';
	}
	$attachment = get_post( $img_id );
	//var_dump($attachment);
	$qstring = qscode($attachment->post_content); // the QUERYSTRING version
	parse_str($qstring, $q_array); // parses the querystring into an associative array
	if(!isset($q_array['credits'])) {
		return '';
	} else {
		//return $q_array['credits'];
		$credits = $q_array['credits'];
		if(substr($credits,0,4) == 'http') {
			return "<a class='credits' href='".$credits."' target='_blank' title='Photo: ".$credits."'>Photo: ".$credits."</a>\n";
		} else {
			return  "<div class='credits' title='Photo: ".$credits."'>Photo: ".$credits."</div>\n";
		}
	}
}


// This gets called in wf_db_classes.php and wf_forms.php
// It's also defined in wf_forms.php
if(!function_exists('add_to_message')) {  // v6.39
	function add_to_message($errors, $check) {
		if ($check['error_class'] != '') {
			$errors['errors_html'] .= "<li>".$check['feedback']."</li> \n";
			$errors['count']++;
		}
		return $errors;
	}
}

// this version will live in wf_library, so has access to its functions etc // v6.57
function widget_html($widget_class, $style, $qstring) {
	//with(new Map_widget('group_map', 'map', 'postcode=function&widget=map'))->get_html();
	if(is_category() || is_tag()) {
		return'';
	}
	if(!class_exists($widget_class)) {
		return "<p class='not_found'>Class ".$widget_class." not found. It's either deactivated or not loaded.</p>";
	}
	parse_str($qstring);
	if(!isset($widget)) {
		return "<p class='not_found'>Incomplete query string for ".$widget_class.".</p>";
	}
	$obj = new $widget_class($style, $widget, $qstring);
	return $obj->get_html();
}