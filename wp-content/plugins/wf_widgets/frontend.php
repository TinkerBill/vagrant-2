<?php 

/*
v6.80	3/10/15	Added bug trap in function get_regional_inheritance($generations,$region) because 1,000 + daily warnings in error log!
				
				wf_widgets.php: function get_catposts($args,$params) moved from a function to a method in abstract class Wf_Widget because 
				(a) it's used by both List and Vscroller widgets and (b) overloading a function (as opposed to a method) doesn't 
				allow as much control over what gets overwritten. (Eg: extending Lists widget to ToDo widget causes all lists to 
				have ToDo junk added!


v6.79	22/9/15	Added outlaw_key to Tweets widget. Added class wfpid_1234 to div.post for easier jQ manipulation.

v6.78	9/4/15	Changing Tweets widget to use either script - $params['method'] = approved|outlaw
				(Outlaw version currently hardwired for Carplus!) Need to give it another parameter for the key.

v6.77	30/3/15	Used remove_square_brackets() in get_catposts()

v6.76	27/2/15	Added param 'omit_self' to list widget.

v6.75	9/1/15	Now using get_insertable_posttypes() in slideshow widget (see v6.52). Carplus slideshow was failing
				because of bbPress post-types.

v6.72  10/10/14	Added 'wf_pagination_summary' filter to static function wf_paginate_list() in frontend.php for Amy.
				Params: $summary,$countposts,$current_page,$max. 
				See example in action at www.graphicdesignleeds.info/wingtheme/widgets/lists-widget

v6.69 	16/9/14	Add speed and interval params to slideshow

v6.68 	20/8/14	List widget post_status corrected to 'publish from 'published'! Also, 'type'=>'DATETIME' added to
				$args['meta_query'].

v6.67 	12/8/14	Was getting errors "should not be called statically", so made various methods static in 
				wf_widgets.php and frontend.php

v6.65	9/4/14	Added extra_markup($params) to slideshow, vscroller, tweets, popup and random

v6.64	 3/3/14	Added 'w' to format param to allow display of author in list widget.	

v6.63	 3/3/14	List and Vscroller widgets now have $params: 
				'orderby' - order posts by date, title or randomly.
				'date_field' - specify a custom-field to fetch the mySql date from. (And fixes future and order.)
				'dateformat' - specify a format for the date - including a lead-in if you escape all the characters.
				Extensive changes to get_catposts(), List, Vscroller and widget_default_specs.php  Should all
				be backwards compatible.

v6.62   26/2/14	Introduce $params['orderby'] and pic_size to vscroller. Various other changes to frontend.php to bring
				vscroller more in line with list widget. Added autopause: true to init_vscroller.js - to no avail.

v6.61 	24/2/14	Introduce $params['logged_in'] - bail-out dependent on whether user is logged in

v6.53  18/11/13	Added #twitter_fetcher to tweets widget

v6.52  30/10/13	Now using get_insertable_posttypes() in post widget. Epilepsy site kept losing widgets because of 
				tu post types. Changes to functions.php (if override required), frontend.php and wf_widgets.php. 
				Function posttype_accepts_widgets($postType) and default get_insertable_posttypes() both start
				from new function get_widget_accepting_posttypes(). Also posttype_accepts_debugbox(). 
				All except posttype_accepts_widgets() can be overridden in functions.php.

v6.49	2/10/13	Random widget: replaced single return with <br/> and added editlink

v6.30 	29/7/13	Slideshow: changed .slidethumb and .darkner divs to spans. IE7/8 doesn't like inline-block on divs.

v6.29	28/7/13	Slideshow: After a lot of faffing with SOP and WCTS versions have removed 'buttons' option as this is now 
				identical to 'tabs'.

v6.28	27/7/13	Slideshow and Post: Added 'page' as a possible posttype and 'page' to the 'Available items' box. 

v6.27	26/7/13	Moved old wf_lib.js to new files in plugins folder

				THIS IS NOW OBSOLETE - SEE 6.29 ABOVE: [OBSOLETE]Slideshow: added 'buttons'  as 4th option for layout (default is ''):
				*Buttons* are the little blobs as in WCTS. They are identical codewise to *thumbnails* (as in BIEA) except
				that no mini-images are loaded into #slidethumb0 etc. The darkner divs are retained for the buttons, to 
				avoid having to change the javascript. 
				Both *buttons* and *thumbnails* are gathered together into div#slidethumbs. 
				By contrast, each *tab* is attached to and part of a particular slide.[/OBSOLETE]
				
v6.21			Changed Reveal widget to include <i> element in reveal_head, so we can use icons.
				Probably needs a bit of tweaking to cope with existing classes. Parameter for icons?

v6.11 	13/6/13	Changed Slideshow so wf_get_credits($img_id) can bail out for videos

v6.9	11/6/13	Calls to d() changed to Wf_Debug::stash(), to reduce errors when upgrading from old versions of wf_lib etc

v6.2	18/5/13	Changed params['format'] params['layout'] in Slideshow_widget so that it doesn't clash with params['format']
				in List_widget, Vscroller_widget or get_catposts().

v5.12	30/4/13	Popup widget changes. Added width parameter. If width of popup widget is not full width of its container,
				width should be specified via CSS or via this parameter. 
				
				Vscroller: Added call to get_excerpt_or_full() so we can use excerpts.

v5.9	25/4/13	Added static function wf_editlink($post_id) to wf_widgets.php to allow editing of widget inserted items
				when user has relevant capability. Now using it in posts and lists.
				
				Added 'filter' param to function get_catposts(). Value = name of function (in widget_config.php)
				that filters the posts. Works!

v5.7	23/4/13	Added function get_endlink($params) to wf_widgets.php and we're now using it in lists and tweets.

v5.6	22/4/13	Enqueued widget_base.css
				Slideshow: Seems like width and height are not required (at least for the simple one) and are not used.
				Slideshow css: made #slide0 relative to ensure whole slideshow has a height.
				Removed class of (eg) "w560_h260" and replaced with class based on format [6.2: now 'layout'] eg: 'tabs' or 'thumbnails'

v4.3	12/2/13	Added $params['region'] to get rid of warning in list widget

				Dealt with undefined q_array in slideshow

v4.1	26/1/13	These version numbers need synchronising across wf_lib and many other files

				Slideshow not now backwards compatible. Each slide now needs customfield 'extras' instead of 'slidepic'.
				Added params 'showtitles=false' and 'selflinks=false'
				Moved across most of code from Richard's Stamp Out Poverty version (allows: tabs, featured videos, and 
				links to self-same posts),




*/




add_action( 'init', 'widget_headstuff' ); // was wp_head, wp

function widget_headstuff() {

	if(!function_exists('extra_markup')) { // v4.2
		function extra_markup($params) {
			return '';
		}
	}
	
	wp_register_style( 'widget_base_css', plugins_url( 'css/widget_base.css' , __FILE__ )); // v5.6
	wp_enqueue_style( 'widget_base_css');
	
	wp_enqueue_script( 'wf_widgets_frontend_js', WF_WIDGETS_PLUGIN_URL.'/scripts/wf_widgets_frontend.js', array('jquery')); // v6.27  
	
	wp_register_script( 'jquery_tools', WF_WIDGETS_PLUGIN_URL.'/scripts/jquery.tools_1.2.7.min.js', array('jquery')); // subset without jQuery
	
	wp_register_script( 'init_popups', WF_WIDGETS_PLUGIN_URL.'/scripts/init_popups.js', array('jquery'));
	
	
	//wp_register_script( 'twitter_script', WF_WIDGETS_PLUGIN_URL.'/scripts/jquery.tweet.js', array('jquery')); // v6.78
	wp_register_script( 'twitter_approved', WF_WIDGETS_PLUGIN_URL.'/scripts/jquery.tweet.js', array('jquery')); // v6.78
	wp_register_script( 'twitter_outlaw', WF_WIDGETS_PLUGIN_URL.'/scripts/twitter_fetcher.js', array('jquery'));   // v6.78
	 //v6.78 changing Tweets widget so that either script can be used
	wp_register_script( 'init_tweets_js', WF_WIDGETS_PLUGIN_URL.'/scripts/init_tweets.js', array('jquery','twitter_approved','twitter_outlaw' )); // v6.78
	
	
	
	wp_register_script( 'init_vscroller', WF_WIDGETS_PLUGIN_URL.'/scripts/init_vscroller.js', array('jquery', 'jquery_tools')); 
	wp_register_script( 'init_slideshow', WF_WIDGETS_PLUGIN_URL.'/scripts/init_slideshow.js', array('jquery')); // v6.69
	
	
	// INSERT POST ///////////////////////////////////////////////////////////////////////////////////////
	
	class Post_widget extends Wf_Widget {
		/*
		protected $default_params = array( // v3.50
				'ids' => false,
				'link' => false,
				'show_title' => false,
				'pic_size' => 'full',
				'type' => 'full', // 3.25  'full' or 'excerpts'
				'caption' => false, // 3.26  'default' or the literal string
				'style' => false,
				'logged_in' => false // 6.61
				);
		*/
		public function get_html(){
			$params = $this->params; // 6.61
			if($params['logged_in']) { // 6.61 bail-out dependent on whether user is logged in
				if(
					(is_user_logged_in() && $params['logged_in'] == 'hide') ||
					(!is_user_logged_in() && $params['logged_in'] == 'show') 
				) {
					return '';
				}
			} // 6.61
			global $post;
			$post_store = $post; // v3.40	
				
			$html = '';
			//$posttypes = array_merge(get_post_types( array('_builtin'=> false)),array('page','post','attachment') ); // v6.28 added page
			$posttypes = array_keys(get_insertable_posttypes()); // v6.52
			$args = array(
				'post_type' => $posttypes,
				'posts_per_page' => -1,
				'post_status' => 'any', // attachments require 'inherit' or 'any'
				'post_parent' => null, 
				'post__in'=> $this->params['ids'] // v3.51 Was $id_array 
				); 
				d('$args',$args);
			$rankedposts = get_ranked_posts($args); // v6.4 Was $this->get_ranked_posts
			foreach($rankedposts as $rankedpost) {
				$html .= $this->output_a_post($rankedpost, $this->params); // v3.50
			}
			
			$post = $post_store; // v3.40
			return $html; 
		 }
	
	}
	
	
	
	
	
	
	function wf_get_caption($attachment,$caption) { // 3.26
		if($caption == '') {
			return '';
		}
		if($caption == 'default') { // go and get the stored version
			$caption = $attachment->post_excerpt;
		}
		return "<p class='caption'>".$caption."</p>";
	}
	
	
	// allows us to put comments into post titles [like this]
	function remove_square_brackets($title) {
		$title = preg_replace('/\[.*\]/', '', $title);
		return $title;	
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	
	// INSERT LIST ///////////////////////////////////////////////////////////////////////////////////////
	
	class List_widget extends Wf_Widget {
		/*
		protected $default_params = array(
				'type' => 'titles',
				'order' => 'DESC',
				'show_posts' => 5,
				'cat' => false, // 3.28
				'style' => false,
				'format' => '',
				'status' => 'publish',
				'heading' => false,
				//'widgnum'=> false, // v3.43 changed from widg_num
				'paginate' => false,
				'taxonomy' => false,
				'post_type' => false, // 3.28
				'link' => false, // 3.34
				'linktext' => false, // 3.34
				'pic_size' => 0, // v3.51 - for featurepic
				'debug' => false, // v3.79
				'omit_self' => false, // v6.76
				);
		*/
		
			
		public function get_html(){	
			global $post; // v6.76
			$params = $this->params;
			//var_dump($params);
			Wf_Debug::stash(array('$params'=>$params));
			$html = "\n<div class='".$params['style']."'>\n"; // v3.51 renamed from $class
			$html .= extra_markup($params); // v4.2
			if($params['heading']) {
				$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; // <div class='hwrap'> added for carplus v3.15
			}
			$widg_id = $params['widgnum']."_".$params['region']; // eg: 0_left   or __shortcode // v4.3 $params['region']
			
			$args = array(
			'post_type' => $params['post_type'], // 3.18, 3.28 tidy_params() sets the default to a sensible array
			'post_status' => $params['status'],
			'posts_per_page' => $params['show_posts'],
			'order' => strtoupper($params['order']), // v3.39
			'orderby' => $params['orderby'], // v6.63
			'widg_id' => $widg_id, // It's OK to add in additional parameters
			'list' => true, // 3.31 - so <more> items in a list get treated the same as automatic excerpts
			'paginate' =>$params['paginate'] // boolean
			);
			
			if($params['paginate']) {
				parse_str($_SERVER['QUERY_STRING'], $q_array); // eg: ?pgd_0_left=3
				if(isset($q_array["pgd_".$widg_id])){
					$args['paged'] = $q_array["pgd_".$widg_id];
				} else {
					$args['paged'] = 1;
				}
			}
			// v6.63
			if($params['date_field']) {
				if($params['orderby']=='date') {
					$args['orderby'] = 'meta_value';
					$args['meta_key'] = $params['date_field'];
				}
				if($params['status'] == 'future') {
					$args['post_status'] = 'publish'; // v6.68 not 'published'!
					$args['meta_query'] = 	array(
												array(
													'key' => $params['date_field'],
													'type'    => 'DATETIME', // v6.68
													'value' => date("Y-m-d H:i"), // eg: 2014-04-01 18:30  $today = date("Y-m-d H:i:s");
													'compare' => '>'
												)
											);
				}
			}
			
			if($params['cat'] !== false) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => ($params['post_type'] == 'post') ? 'category' : $params['post_type'].'_type', // eg: site_note_type
						'field' => 'id',
						'terms' => $params['cat'], // needs to be an array of ids
						'operator' => 'IN'
					)
				);
				if($params['omit_self']) { // v6.76
					//$args['post__not_in'] = array($params['host_post']);
					$args['post__not_in'] = array($post->ID);
				}
			}	
			
			Wf_Debug::stash(array('$args'=>$args));
			
			//v6.80
			if(function_exists('get_catposts')) {
				$html .= get_catposts($args,$params); // overloaded version
			} else {
				$html .= $this->get_catposts($args,$params); // defined in abstract class Wf_Widget
			}
			//
			//$html .= get_catposts($args,$params);
			$html .= Wf_Widget::get_endlink($params); //v5.7
			$html .= "</div>\n"; // class = 'list'
			return $html;
		}
		
		
		// v6.67 was getting errors "should not be called statically", so made static
		public static function wf_paginate_list($args, $countposts) { // only used in insert_list
			if($args['paginate'] === false){
				return '';
			}
			
			if(!function_exists('pagin_cell_html')) { // this is nested solely for neatness - not used elsewhere
				function pagin_cell_html($widg_id, $n, $current_page) {
				if($n == $current_page) {
						return "<span class='current_pagin'>".$n."</span>";   
					} else {
						return "<a href='".SELF_URL."?pgd_".$widg_id."=".$n."'>".$n."</a>";   // eg: ?pgd-0-left=3
					}
				}
			}
		
			$html='';
			$max = ceil($countposts / $args['posts_per_page']);
			
			if ($max > 1){ // otherwise no point!
				$current_page = max(1, $args['paged']);
				$html .= "<p class='list_pagination'>";
				
				if($current_page ==1) {
					$html .= "<span class='disabled'>&lt;</span>"; 
				} else {  
					$html .= "<a href='".SELF_URL."?pgd_".$args['widg_id']."=".($current_page-1) ."'>&lt;</a>";   // eg: ?pgd-0-left=3
				}
				$html .= pagin_cell_html($args['widg_id'], 1, $current_page); // need the first one, whatever
				
				if($max < 8) { // output them all (2 to max-1)
					for($n=2; $n< $max; $n++) { // if max=2, shouldn't do anything
						$html .= pagin_cell_html($args['widg_id'], $n, $current_page);
					}
				} elseif($current_page < 5) { // output all the start ones (2 to current+1)
					for($n=2; $n< 6; $n++) { // 2 to 5 at the most
						$html .= pagin_cell_html($args['widg_id'], $n, $current_page);
					}
					$html .= "<span class='ellipsis'>...</span>";
				} elseif($max-$current_page < 4) { // output all the end ones (current-1 to max-1)
					$html .= "<span class='ellipsis'>...</span>";
					for($n=$max-4; $n< $max; $n++) { // eg: 5 to 8
						$html .= pagin_cell_html($args['widg_id'], $n, $current_page);
					}
				} else { // it's in the middle somewhere
					$html .= "<span class='ellipsis'>...</span>";
					for($n=$current_page-1; $n< $current_page+2; $n++) { // eg: 4 to 6
						$html .= pagin_cell_html($args['widg_id'], $n, $current_page);
					}
					$html .= "<span class='ellipsis'>...</span>";
				}
				
				$html .= pagin_cell_html($args['widg_id'], $max, $current_page); // need the last one, whatever
				if($current_page ==$max) {
					$html .= "<span class='disabled'>&gt;</span>"; 
				} else {  
					$html .= "<a href='".SELF_URL."?pgd_".$args['widg_id']."=".($current_page+1) ."'>&gt;</a>";   // eg: ?pgd-0-left=3
				}
				// v6.72
				$summary = $max." pages&nbsp;&nbsp;&nbsp;".$countposts." entries"; // eg: 18 entries
				$summary = apply_filters('wf_pagination_summary',$summary,$countposts,$current_page,$max);
				$html .= "<span class='summary'>".$summary."</span>";
				//
				$html .= "</p>";
			}
			return $html;	
		}
	
	
	
	}
	
	
	/* // v6.80
	if(!function_exists('get_catposts')) {
		
		// Experimental version that includes reveal facility v3.84
		function get_catposts($args,$params) { // v3.51  Was $args, $format, $type
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
	
	}
	*/
	
	// NB: This hasn't been applied anywhere yet!
	function shorten_text($text, $wordmax) {  
		$words = preg_split("/[\n\r\t ]+/", $text, $wordmax + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $wordmax ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . "...";
		} else {
			$text = implode(' ', $words);
		}
		return $text;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	// INSERT RANDOM ////////////////////////////////////////////////////////////////////////////////////// 
	
	class Random_widget extends Wf_Widget {
		/*
		public $default_params = array(
				'id' => false, // changed from ids 20/2/12
				'style' => false
				);
		*/
		public function get_html(){	
			$params = $this->params;
			
			$factspost = get_post($this->params['id']); // Facts
			$facts=($factspost ->post_content); 
			$lines=explode("\r\n\r\n", $facts);
			$random_fact = $lines[rand(0, count($lines)-1)];
			$random_fact = str_replace("\r\n", "<br/>\r\n", $random_fact); // v6.49 
			$html =  "\n<div class='".$this->params['style']."'>\n";
			$html .= extra_markup($params); // v6.65
			$html .= Wf_Widget::wf_editlink($this->params['id']);// v6.49
			$html .= "<p>".$random_fact."</p>\n</div>\n\n";		
			
			return $html;
		}
	
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	// INSERT VSCROLLER //////////////////////////////////////////////////////////////////////////////////
	
	class Vscroller_widget extends Wf_Widget {
		
			/*
			public $default_params = array(
				'ids' => false,
				'style' => false,
				'heading' => false,
				'ppv' => 1,
				'img_size' => 100,
				
				'type' => 'titles', // these are from insert_list
				'order' => 'DESC',
				'orderby' => 'date', // v6.62 (or title)
				'show_posts' => 5,
				'cat' => '',
				'format' => '',
				'status' => 'publish',
				
				'speed' => 500, // to pass to js
				'interval' => 5000
				);
			*/
			
		
		public function get_html(){	
			$params = $this->params;
			
			$params['style'] = $params['style']." ppv".$params['ppv']." img_size".$params['img_size']; // adds classes of (eg) "img_size75 ppv2"
			
			$html = "\n\n<div class='".$params['style']."'>\n"; // manually tweak classes ppv2, ppv3 etc to set height of this div
			$html .= extra_markup($params); // v6.65
			if($params['heading'] !='') {
				$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; // <div class='hwrap'> added for carplus v3.16
			}
			
			$html .= "<!-- root element for scrollable -->\n";
			$html .= "<div class='scrollable vertical'>\n";	
		
			$html .= "<!-- root element for the scrollable elements -->\n";
			$html .= "<div class='items'>\n";
			
			//var_dump($params['ids']);
			//if(!$params['cat']) { // specific posts are specified
			if($params['ids'][0] != '') { // specific posts are specified v6.62
			
				$args = array(
				//'post_type' =>  array('post', 'wf_sitenote', 'wf_snippet'), // 3.18
				//'post_type' => array_merge(get_post_types( array('_builtin'=> false)),array('post') ), // v3.48
				'post_type' => $params['post_type'], // 6.62 tidy_params() sets the default to a sensible array
				'posts_per_page' => -1,
				'post_status' => null,
				'post_parent' => null,
				'orderby' => $params['orderby'], // v6.62 - or 'title', 'rand'
				'post__in'=> $params['ids'] 
				); 
				
				$scrollposts = get_posts($args);
			
				$imax = count($params['ids']);	
				for($i=0; $i < $imax; $i++ ) {	
					//$content = wf_filter_content($scrollposts[$i] -> post_content); //v5.12
					//$title = $scrollposts[$i] -> post_title;
					$title = remove_square_brackets($scrollposts[$i] -> post_title); //v3.36
					$link = get_permalink($scrollposts[$i] ->ID);
					if($i % $params['ppv'] ==0) { // 0, 2, 4  - it's the start of a new scrollable screenful		 
						$html .= "\n<div>\n";
					} 
					$html .= "<div class='item'>\n";
					if (has_post_thumbnail($scrollposts[$i] ->ID ) && $params['img_size'] != 0) {  // v3.38
						$img_html = wp_get_attachment_image(get_post_thumbnail_id($scrollposts[$i] ->ID), 'thumbnail');
						//$img_html= add_class($img_html,'alignleft');
						$img_html = change_width_and_height($img_html,$params['img_size'],$params['img_size']); // v3.49  Was 100,100
						$html .= $img_html;
					}
					$html .= "<div class='content'>\n";
					if(strpos($params['format'],'h') === false) { // v3.45
						$html .= "<h3 class='h3 link'><a href='".wf_linkfix($link)."'>".$title."</a></h3>\n";
					}
					
					//$html .= wpautop( $content);
					
					if(isset($params['format']) && strpos($params['format'],'l') !== false) { //v5.12
						$list = true;
					} else {
						$list = false;
					}
									
					$html .= Wf_Widget::get_excerpt_or_full($scrollposts[$i],$params['type'],$list)."\n";  //v5.12
					
					$html .= "\n<div class='fade'>&nbsp;</div>\n"; 
					$html .= "</div>\n"; // <div class='content'>
					
					$custom_fields = get_post_custom($scrollposts[$i] ->ID);  // v3.37					
					
					// v3.37 This section now looks for a customfield "extras" applied to the post, and gives priority to this for "link"
					if(isset($custom_fields['extras'])) { // v3.37 
						$qstring = qscode($custom_fields['extras'][0]); // the QUERYSTRING version
						parse_str($qstring, $q_array); // parses the querystring into an associative array
						if($q_array['link']) {
							if($q_array['link'] == 'default') { // go and get the stored version
								$coverlink = $link;
							} else {
								$coverlink = wf_linkfix($q_array['link']);
							}
							$html .= "<a class='coverlink' href='".$coverlink."'>&nbsp;</a>\n"; // v3.37
						}
					}
					
					
					
					$html .= "</div>\n"; // <div class='item'>
					if(($i+1) % $params['ppv'] ==0) { // 0, 2, 4  - it's the end of this scrollable screenful	
						$html .= "</div>\n";
					} 	
				} // for loop
			
			} else { // category has been specified
				$args = array(
				//'post_type' => array('post', 'wf_sitenote', 'wf_snippet'), // 3.18
				//'post_type' => array_merge(get_post_types( array('_builtin'=> false)),array('post') ), // v3.48
				'post_type' => $params['post_type'], // 6.62 tidy_params() sets the default to a sensible array
				'post_status' => $params['status'],
				'posts_per_page' => $params['show_posts'],
				//'category__in' => $params['cat'],// was array($params['cat']) v3.77  to allow multiple categories. v6.62 removed
				'order' => strtoupper($params['order']), // v3.39
				'orderby' => $params['orderby'], // v6.62 - 'date' or 'title', 'rand'
				'paginate' => false 
				);
				
				// v6.62
				if($params['cat'] !== false) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => ($params['post_type'] == 'post') ? 'category' : $params['post_type'].'_type', // eg: site_note_type
							'field' => 'id',
							'terms' => $params['cat'], // needs to be an array of ids
							'operator' => 'IN'
						)
					);
				}	
				
				//v6.80
				if(function_exists('get_catposts')) {
					$html .= get_catposts($args,$params); // all wrapped in <div class="posts"></div>
				} else {
					$html .= $this->get_catposts($args,$params); // defined in abstract class Wf_Widget
				}
				//
			}
			$html .= "</div>\n"; // class='items'		
			$html .= "</div>\n"; // class='scrollable vertical'	
			$html .= "</div>\n\n"; // id='vert_scroller'	
			
			
			$widget_params['interval'] = $params['interval'];
			$widget_params['speed'] = $params['speed'];
			
			// Clever wheeze to get round lack of anonymous function support before PHP 5.3
			if ( ! function_exists('pass_vscroller_params') ) {
				function pass_vscroller_params($widget_params) { 
					wp_enqueue_script('init_vscroller');
					wp_localize_script( 'init_vscroller', 'vscroller_params', $widget_params);
				}
			}	
			pass_vscroller_params($widget_params);
			
			return $html;
		}
	
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	
	
	// INSERT SLIDESHOW ///////////////////////////////////////////////////////////////////////////////////////
	
	
	class Slideshow_widget extends Wf_Widget {
		
		/*			
		public $default_params = array(
			'ids' => false, // required
			'height' => false, // required
			'width' => false, // required
			'style' => false,
			'pic_size' => 'full',
			'format' => false, // currently can be 'thumbnails'  // v6.2 CHANGED TO 'layout'
			'wordmax' => false,
			'heading' => false // v3.36
			);
			

		*/	
		
		//public $js_params;
		protected $js_params = array(); // v6.69
		
		function pass_js_params() {  // v6.69
			wp_enqueue_script('init_slideshow');
			wp_localize_script( 'init_slideshow', 'slideshow_params', $this->js_params);
		}	
			

				
		public function get_html(){	
			$params = $this->params;
					
			if($params['ids'] === false) { // v6.2 || $params['height'] === false || $params['width'] === false) {
				return; // bail out if we haven't got the required parameters
			} 
			
			if($params['layout']) { // v5.6
				$params['style'] = $params['style']." ".$params['layout']; // adds class of (eg) 'tabs'
			}
			
			//$posttypes = array_merge(get_post_types( array('_builtin'=> false)),array('post','attachment','page') ); // v6.28 added page
			$posttypes = array_keys(get_insertable_posttypes()); // v6.52
			$args = array(
			'post_type' => $posttypes,//array_values($posttypes), // not a parameter for the widget
			'posts_per_page' => -1,
			'post_status' => 'any', // attachments require 'inherit' or 'any'
			'post_parent' => null, 
			'post__in'=> $params['ids'] // $params['ids'] is an array
			); 
			//FB::info($args,'$args');
			$rankedposts = get_ranked_posts($args); // v6.4 Was $this->get_ranked_posts
			$html_slidemain = "\n\n<div id='slidemain'>\n";
			$html_text =''; // needed for attachment version
			$html_thumb = "<div id ='slidethumbs'>\n";
			$c = 0; // NB non-human counting from 0!
			$video_ids = array(); // an array of ids used by the YouTube API // v4.1
			foreach($rankedposts as $rankedpost) {
				$img_id = ''; // v6.11 so wf_get_credits($img_id) can bail out for videos
				unset($q_array); // v6.28 so that values from last time get destroyed. Don't need to check isset() first
				$html_slidemain .= "\n<div id ='slide".$c."' class='slide'>\n";
				if($rankedpost->post_type != 'attachment') { // a post, so get image id (and link?) from customfield in the post (not the page)
					$custom_value = get_post_meta($rankedpost->ID, 'extras', true); // the contents of customfield extras - or ''
					if($custom_value != '') {
						$qstring = qscode($custom_value); // the QUERYSTRING version
						parse_str($qstring, $q_array); // parses the querystring into an associative array
					}
					if(isset($q_array['featured_video'])) { // v4.1  'featured_video' takes precedence over 'slidepic_id' and featured image
						$this_img = "<iframe  id='player_".$c."' width='290' height='200' src='http://www.youtube.com/embed/".
							$q_array['featured_video']."?rel=0&enablejsapi=1' frameborder='0' allowfullscreen></iframe>";
						$video_ids[] = 'player_'.$c;
					} else {
						if(isset($q_array['slidepic_id'])) {
							$img_id = $q_array['slidepic_id']; // v4.1  'slidepic_id' takes precedence over featured image
						} else {
							if (has_post_thumbnail($rankedpost->ID)) {
								$img_id = get_post_thumbnail_id( $rankedpost->ID );
							} else {
								throw new WF_Exception('Slideshow: no image or video found'); // v4.1
							}
						}
						$this_img = wp_get_attachment_image($img_id, $size=$params['pic_size'], $icon=false, $attr=array('alt' =>'', 'title' =>'')); 
					}
					$html_slidemain .= $this_img;
					
					if($params['text_type'] != 'none') { // v6.28
						$html_slidemain .= "\n<div class='slide_text'>\n";
						if($params['showtitles'] || $params['text_type'] == 'titles'){ // v6.28
							$html_slidemain .= "<h4>".remove_square_brackets($rankedpost->post_title)."</h4>\n";  // v6.28
						}
						//$html_slidemain .= postProcessSidepost($rankedpost->post_content)."</div>\n"; 
						$html_slidemain .= Wf_Widget::get_excerpt_or_full($rankedpost,$params['text_type'],$list=false)."</div>\n"; // v6.28
					}
					
					if(isset($q_array['link']) || $params['selflinks']) { // some sort of link is required // v4.1 v4.3
						$coverlink = ($params['selflinks']) ? get_permalink( $rankedpost->ID ) : wf_linkfix($q_array['link']); // v4.1
						$html_slidemain .= "<a class='coverlink' href='".$coverlink."'>&nbsp;</a>\n"; // v3.22 (was slidelink)
					}
				//} else {
					//throw new WF_Exception('Slideshow: no customfield found'); // v4.1 
				} else { // it's an attachment
					$img_id = $rankedpost->ID;
					$this_img = wp_get_attachment_image($img_id, $size=$params['pic_size'], $icon=false, $attr=array('alt' =>'', 'title' =>''));
					$html_slidemain .= $this_img."\n";
				}
				$html_slidemain .= wf_get_credits($img_id); // returns empty if no 'credit=' in description, otherwise a <div> (or <a> if it's a link)
				
				// v6.27
				switch($params['layout']) {
					case 'tabs':
					//case 'buttons':
						$this_img =  "&nbsp;";
					case 'thumbnails':	// and 'buttons'
						$html_thumb .= "\n<span class='slidethumb' id ='slidethumb".$c."'>\n".$this_img."\n";
						$html_thumb .= "<span class='darkner'></span>\n</span>\n"; // v6.30 changed divs to spans
						break;
				}
				
				$html_slidemain .= "</div>\n"; // v6.27
				$c++;
				
			} // foreach
			
			$html = "\n\n<div id='slideshow' class='".$params['style']."'>";	 // assemble it all
			$html .= extra_markup($params); // v6.65
			if($params['heading']) { // v3.36
				$html .= "\n<div class='hwrap'>\n<h2>".$params['heading']."</h2>\n</div>\n"; 
			}
			$html .= $html_slidemain."\n</div>\n\n";
			if($params['layout']) { // == 'thumbnails' || $params['layout'] == 'buttons') { // v6.27
				$html .= $html_thumb."\n</div>\n\n";
			}
			$html .= "</div>\n\n";
			$html = str_replace('title=""', '', $html);
			
			if(!empty($video_ids)) { // v4.1
				global $widget_params;
				$widget_params = $video_ids;
				
				if ( ! function_exists('pass_youtube_params') ) {
					function pass_youtube_params($widget_params) { 
						wp_enqueue_script('init_videos_js');
						global $widget_params;			
						wp_localize_script( 'init_videos_js', 'video_params', $widget_params);
					}
				}
				add_action('wp_enqueue_scripts', 'pass_youtube_params');
			}
			
			// v6.69
			$this->js_params['speed'] = $params['speed'];
			$this->js_params['interval'] = $params['interval'];
			self::add_action('wp_enqueue_scripts', 'pass_js_params'); 

			return $html; 
		}
	
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	// INSERT POPUP ///////////////////////////////////////////////////////////////////////////////////////
	
	class Popup_widget extends Wf_Widget {
		
		static $popup_params = array();
		/*
		public $default_params = array(
			'style' => false,
			'back_id' => false,
			'front_id' => false,
			'start_height' => false,
			'link' => false,
			'width' => false // v5.12
			);
		*/		
				
		public function get_html(){	
			$params = $this->params;
				
			if($params['back_id'] === false || $params['front_id'] === false || $params['start_height'] === false) {
				return; // bail out if we haven't got the required parameters
			} 
			
			$width = ($params['width']) ? " style='width: ".$params['width']."px;' " : ''; //v5.12 
			
			$html = ''; 
			$html .= "\n\n<div class='".$params['style']."' ".$width.">\n";// v5.12
			$html .= extra_markup($params); // v6.65
			$html .= wp_get_attachment_image($params['back_id'], $size='full', $icon=false)."\n"; // the "background" image
			//$html .= "<div class='sliding_bit' style='top:".$params['start_height']."px;'>\n";
			$html .= "<div class='sliding_bit'>\n"; // v5.17
			$html .= wp_get_attachment_image($params['front_id'], $size='full', $icon=false)."\n"; // the "sliding text" image
			$html .= "</div>\n"; // .sliding_bit
			/* // v5.12
			$html .= "<div class='popup_frame'>\n";
			if($params['link']) {
				$html .= "<a href='".$params['link']."'>&nbsp;</a>";
			} else {
				$html .= "&nbsp;";
			}
			$html .= "</div>\n</div>\n\n"; // added v3.15 to allow fancy borders
			*/
			if($params['link']) {
				$html .= "<a class='coverlink' href='".$params['link']."'>&nbsp;</a>";//v5.12
			}
			$html .= "</div>\n\n";
			
			self::$popup_params['start_height'] = $params['start_height'];
			self::add_action('wp_enqueue_scripts', 'pass_popup_params'); 
			return $html; 
		}
		
		function pass_popup_params() { 
			wp_enqueue_script('init_popups');
			wp_localize_script( 'init_popups', 'popup_params', self::$popup_params);
		}	
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	// INSERT TWEETS ///////////////////////////////////////////////////////////////////////////////////////
	
	// not to be used with shortcodes, and only one per page
	// however, still using same architecture for max compatibility and ease of debugging etc
	
	
	
	class Tweets_widget extends Wf_Widget {
		/*
		public $default_params = array(
				'style' => false,
				'count' => 3,
				'username' => false, // required
				'heading' => false,
				'link' => false, 
				'linktext' => false, // v3.35 if linktext = 'heading', link is applied to heading text, otherwise, it's a separate endlink
				'method' => 'approved', // v6.78
				);
		*/	
			
		public function get_html(){	
			$params = $this->params;
						
			if($params['username'] === false || !is_int($params['count'] + 0)) {
				return; // bail out if we haven't got the required parameters
			} 
			
			$html  =  "\n<div id='tweets' class='".$params['style']."'>\n";
			$html .= extra_markup($params); // v6.65
			if($params['heading']) {
				$html .= "<div class='hwrap'>\n<h2>".$params['heading']."</h2>\n</div>\n"; // including its link if there is one
			}
			$html .= "<div class='tweet_wrapper' id='twitter_fetcher'></div>\n"; // v6.53 added #twitter_fetcher
			$html .= Wf_Widget::get_endlink($params);// v5.7		
			$html .= "</div>\n\n";
			
			global $widget_params;
			$widget_params['username'] = $params['username'];
			$widget_params['count'] = $params['count'];
			$widget_params['method'] = $params['method'];
			$widget_params['outlaw_key'] = $params['outlaw_key']; //v6.79 - so don't have to hardwire for Carplus!
		
			if ( ! function_exists('pass_tweet_params') ) {
				function pass_tweet_params($widget_params) { 
				
					global $widget_params;
					//wp_enqueue_script('twitter_script'); 
					wp_enqueue_script('twitter_'.$widget_params['method']); // v6.78  approved or outlaw
					wp_enqueue_script('init_tweets_js');
					
					wp_localize_script( 'init_tweets_js', 'twitter_params', $widget_params);
					// Necessary that the array declaration and the wp_localize_script stay before wp_head()	
					// wp_localize_script() MUST be called after the script it's being attached to has been enqueued.
				}
			}
			add_action('wp_enqueue_scripts', 'pass_tweet_params');
			return $html; 
		}
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	// SHORTCODE REVEAL //////////////////////////////////////////////////////////////////////////////////
	
	class Reveal_widget extends Wf_Widget {
		/*
		public $default_params = array(
				);
		*/	
	
		function  get_html() {	
			// no $atts
			$content = $this->shortcode_content;
			$html = "\n<div class='reveal'>\n";
			// find first <tag>
			preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/i", $content, $matches); // gathers all the html elements
			//preint_r($matches);
			// NB won't work if there are spaces in closing tag
			$head = $matches[0][0]; // the first html element found (the full thing)
			$head_opening_tag = $matches[1][0]; // eg: <h5 class='something'>
			$head_opening_tag = str_replace('"',"'", $head_opening_tag); //only single quotes now
				$head_opening_tag = preg_replace("/class\s*=\s*\'/i", "class='reveal_head ", $head_opening_tag , 1, $count);
			if($count == 0) { // no existing classes
				$head_opening_tag = str_replace(">"," class='reveal_head'>", $head_opening_tag); // v6.21
			}
			$head_opening_tag = str_replace(">","><i class='icon-'></i>", $head_opening_tag); // v6.21	
			$head = str_replace($matches[1][0], $head_opening_tag, $head); // 3.29
			//$html .= "<i class='icon-large'></i>".$head."\n"; // v6.21
			$html .= $head;
			$head_closing_tag_pos = strpos($content,$matches[4][0]); // the position of eg: </h5>
			$tail_start_pos = strpos($content,'<',$head_closing_tag_pos+1 );
			$tail = substr($content, $tail_start_pos);
			$html .= "\n<div class='reveal_tail'>\n".$tail."\n</div>\n</div>\n";
			return $html;
		}
	
	}

	
	
	
}