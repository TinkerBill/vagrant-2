<?php

// Not currently used. Repository for experimental widgets

/*

// INSERT SCROLLER //////////////////////////////////////////////////////////////////////////////////

// This is the generic (vertical or horizontal) version


$current_specs['scroller'] = array( 

	'display_name' => 'Scroller widget',
	
	'class_name' => 'Scroller_widget',
	
	'inserts' => true,
	
	'params' => array(
		'ids' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'ids'
			),
		'ppv' => array(
			'default' => 1,
			'reqd' => false,
			'validation' => 'ppv'
		),
		
		'direction' => array( ///  NEW ONE  v4.2
			'default' => 'vertical',
			'reqd' => false,
			'validation' => '=vertical|horizontal'
		),
		
		'speed' => array(
			'default' => 500,
			'reqd' => false,
			'validation' => 'posint'
		),		
		'interval' => array(
			'default' => 5000,
			'reqd' => false,
			'validation' => 'posint'
		),		
		
		'comment' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'string'
			),	
		'style' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'style'
			),
		
		
		'heading' => array(
			'default' => false,
			'reqd' => false,
			'validation' => ''
			),
		
		'img_size' => array(
			'default' => 100,
			'reqd' => false,
			'validation' => 'posint'
		),
		
		'cat' => array(
			'default' => false,
			'reqd' => false,
			'validation' => ''
			),
		'type' => array(
			'default' => 'titles',
			'reqd' => false,
			'validation' => '=full|excerpts|titles'
			),
		'order' => array(
			'default' => 'desc',
			'reqd' => false,
			'validation' => '=desc|asc'
			),
		'show_posts' => array(
			'default' => 5,
			'reqd' => false,
			'validation' => ''
			),
		'status' => array(
			'default' => 'publish',
			'reqd' => false,
			'validation' => ''
			),
		'format' => array(
			'default' => false,
			'reqd' => false,
			'validation' => ''
			),
			
		'debug' => array(
			'default' => false,
			'reqd' => false,
			'validation' => ''
		)
	)
);			
*/


class Scroller_widget extends Wf_Widget { // v4.2  NB: 'vscroller' has been changed to 'scroller' thoughout
		
			/*
			public $default_params = array(
				'ids' => false,
				'style' => false,
				'heading' => false,
				'ppv' => 1,
				'img_size' => 100,
				
				'type' => 'titles', // these are from insert_list
				'order' => 'DESC',
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
			
			$params['style'] = $params['style']." ".$params['direction']." ppv".$params['ppv']." img_size".$params['img_size']; // adds classes of (eg) "vertical img_size75 ppv2"
			
			$html = "\n\n<div class='".$params['style']."'>\n"; // manually tweak classes ppv2, ppv3 etc to set height of this div
			$html .= extra_markup($params); // v4.2
			if($params['heading'] !='') {
				$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; // <div class='hwrap'> added for carplus v3.16
			}
			
			$html .= "<!-- root element for scrollable -->\n";
			$html .= "<div class='scrollable ".$params['direction']."'>\n";	
		
			$html .= "<!-- root element for the scrollable elements -->\n";
			$html .= "<div class='items'>\n";
			
			if(!$params['cat']) { // specific posts are specified
			
				$args = array(
				//'post_type' =>  array('post', 'wf_sitenote', 'wf_snippet'), // 3.18
				'post_type' => array_merge(get_post_types( array('_builtin'=> false)),array('post') ), // v3.48
				'posts_per_page' => -1,
				'post_status' => null,
				'post_parent' => null, 
				'post__in'=> $params['ids'] 
				); 
				
				$scrollposts = get_posts($args);
			
				$imax = count($params['ids']);	
				for($i=0; $i < $imax; $i++ ) {	
					$content = wf_filter_content($scrollposts[$i] -> post_content);
					//$title = $scrollposts[$i] -> post_title;
					$title = remove_square_brackets($scrollposts[$i] -> post_title); //v3.36
					$link = get_permalink($scrollposts[$i] ->ID);
					if($i % $params['ppv'] ==0) { // 0, 2, 4  - it's the start of a new scrollable screenful		 
						$html .= "\n<div class='view'>\n"; // v4.2
					} 
					$html .= "<div class='item'>\n"; // v4.2
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
					$html .= wpautop( $content);
					$html .= "\n<div class='fade'>&nbsp;</div>\n"; 
					$html .= "</div>\n"; // <div class='content'>
					
					$custom_fields = get_post_custom($scrollposts[$i] ->ID);  // v3.37
					// preint_r($custom_fields);
					
					
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
				'post_type' => array_merge(get_post_types( array('_builtin'=> false)),array('post') ), // v3.48
				'post_status' => $params['status'],
				'posts_per_page' => $params['show_posts'],
				'category__in' => $params['cat'],// was array($params['cat']) v3.77  to allow multiple categories
				'order' => strtoupper($params['order']), // v3.39
				'paginate' => false 
				);
				
				$html .= get_catposts($args,$params); // all wrapped in <div class="posts"></div>
			}
			$html .= "</div>\n"; // class='items'		
			$html .= "</div>\n"; // class='scrollable vertical'	
			$html .= "</div>\n\n"; // id='vert_scroller'	
			
			
			$widget_params['interval'] = $params['interval'];
			$widget_params['speed'] = $params['speed'];
			$widget_params['direction'] = $params['direction'];
			
			// Clever wheeze to get round lack of anonymous function support before PHP 5.3
			if ( ! function_exists('pass_scroller_params') ) {
				function pass_scroller_params($widget_params) { 
					wp_enqueue_script('init_scroller');
					wp_localize_script( 'init_scroller', 'scroller_params', $widget_params);
				}
			}	
			pass_scroller_params($widget_params);
			
			return $html;
		}
	
	}
	