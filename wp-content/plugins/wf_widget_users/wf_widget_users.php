<?php

/*
Plugin Name: Wingfinger Users plugin  
Plugin URI: http://www.wingfinger.co.uk
Description: A plugin of Wingfinger widgets for displaying and uploading comments and photos and inserting YouTube videos 
Version: 6.58
Author: Wingfinger
Author URI: http://www.wingfinger.co.uk
License: It's copyright!
*/

/*
This program is NOT free software; if you want to use it, please contact
info[at]wingfinger.co.uk for details of pricing. Thankyou.
*/

 
/*

v6.58	6/1/16	Added reset_comments qstring facility (open|registered_only|closed)

v6.57	6/1/16	Added PluginUpdateChecker

v6.56	30/1/14	Previous version Comments_widget 	quite UTA-specific. Attempt to make more generic.
				NB: extra_markup($params) can be defined in widget_config.php.

v6.06	30/5/13	Added wf_linkfix() to guid, to catch old domains

v5.8	24/4/13	Comments widget: Allow suppression of avatars using 'img_size=0'.
				Can now specify 'post_id' including 'current'.
				Boolean parameter 'show_source' added, which displays the title of the post that the comment is aattached to.



*/ 


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}


add_action( 'init', 'this_plugin_init', 1 ); // v6.57

function this_plugin_init() { // v6.57
	
	// The plugins_url() function should not be called in the global context of plugins, but rather in a hook like "init" or "admin_init" 
	define( 'WF_USERS_PLUGIN_PATH', dirname( __FILE__ ) );
	define( 'WF_USERS_PLUGIN_URL', plugins_url( '', __FILE__ ) ); // v6.55  See note about odd urls that are sometimes generated 
	define( 'WF_USERS_PLUGIN_FILE', plugin_basename( __FILE__ ) );
	define( 'WF_USERS_DIR_PATH', dirname(dirname( __FILE__ )) ); 
	
	// already loaded by wf_lib
	if(class_exists('PluginUpdateChecker')) { // just in case
		$UpdateChecker = new PluginUpdateChecker(
			'http://www.graphicdesignleeds.info/lib/wf_widget_users.json',
			__FILE__,
			'wf_widget_users'
		);
	}
	
}



if ( is_admin()) {	

	// any admin stuff
	
	
} else { // not admin

	add_action( 'wp', 'users_frontend', 100 ); // was wp_head

	function users_frontend() {
		
		wp_register_style( 'users_base_css', plugins_url( 'users_base.css', __FILE__ )); // v5.6
		wp_enqueue_style( 'users_base_css');
		
		
		
		/// DISPLAY USER COMMENTS ////////////////////////////////////////////////
				
		class Comments_widget extends Wf_Widget {
	
			public function get_html(){	
				global $post;
				$params = $this->params;
				//wf_load_stylesheet( 'trip_and_comment_widgets_css', THEME_FOLDER_URL.'/css/trip_and_comment_widgets.css' );
				
				if($params['number'] =='all') {
					$params['show_posts'] = null;
				}
				
				
				
				
				$args = array(
					'user_id' => '', //$user->ID, // use user_id
					'number' => $params['number'],
					'status' => 'approve',
					'post_id' => ($params['post_id'] == 'current') ? $post->ID : $params['post_id'] // v5.8
				);
				
				if($params['user_id'] == 'current') {
					$user = wp_get_current_user();
					$args['user_id'] = $user->ID;
				}
				
				//unset($args['post_id']);
				
				$html = '';
				$html = "\n\n<div class='".$params['style']."'>\n"; 
				/* v6.56
				if(!is_front_page()) {
					$html .= "<div class='boxtop'> </div><div class='boxbot'> </div>\n";
				}
				*/
				$html .= extra_markup($params); // for UTA we hide these on the front page
				
				if($params['heading'] !='') {
					$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; 
				}
				
				$comments = get_comments($args); // an array of objects 
				Wf_Debug::stash(array('$args' => $args)); // (F3) calls Wf_Debug
								
				if(empty($comments))
					return ''; // don't output box if there's nothing to put in it
				
				if($params['show_source']) { // v5.8 do one query to get all the data we're going to need
					$source_ids = '';
					foreach($comments as $comment) {
						$source_ids .= $comment->comment_post_ID.',';
					}
					$source_ids = substr($source_ids,0,-1); // remove last comma
					global $wpdb;
					//$source_ids = implode(',',array_column($comments, 'comment_post_ID'));
					$source_data = $wpdb->get_results( 
						"
						SELECT ID, post_title, guid 
						FROM $wpdb->posts
						WHERE ID IN ($source_ids)
						"
					, OBJECT_K); // associative array of row objects, using first column's values as keys
				}
				
				
				foreach($comments as $comment) :
					$html .= "\n\n<div class='comment'>\n";
					//$html .=  "<div class='divatar'>".get_avatar( $comment, $params['img_size'] )."</div>";
					if($params['img_size'] != '0') { //v5.8
						$html .=  get_avatar( $comment, $params['img_size'] );
					}
				
					$html .= "\n\n<div class='wrap'>\n";
					$html .=  "<p class='com_meta'>";
					if($params['user_id'] != 'current') {
						$html .= "<span class='com_author'>".$comment->comment_author."</span> ";
					}
					
					$datehtml = date($params['date_format'], strtotime($comment->comment_date_gmt));
					$datehtml = str_replace(' ', '&nbsp;', $datehtml);
					$html .= " <span class='com_date'>".$datehtml.'</span> ';// v5.8 need space before this to let it wrap
					if($params['show_source']) { // v5.8
						$source_obj = $source_data[$comment->comment_post_ID];
						$html .= "<span class='com_source'>Commenting on: <a href='".wf_linkfix($source_obj->guid)."'>".$source_obj->post_title."</a></span>"; 
					}
					$html .= "</p>";
					//$html .=  date($params['date_format'], strtotime($comment->comment_date_gmt))."</p>\n";
					$html .=  "<p class='com_content'>".$comment->comment_content."</p>";
					$html .= "</div>";
					$html .= "</div>";
				endforeach;
				
				$html .= "</div>";
		
				return $html;
			}
			
		}
		
		
		/// DISPLAY FORM FOR COMMENTS ////////////////////////////////////////////////
			
		class Comment_form_widget extends Wf_Widget {
			
			// MAKE SURE THE "DISCUSSION" BOX IS TICKED FOR ANY PAGE WHERE YOU WANT THIS TO APPEAR!!
			
			public function get_html(){	
				$params = $this->params;
				
				if($params['roles']) { // list of roles that can post comments
					$user = wp_get_current_user();
					if($user->ID == 0)
						return '';
					//var_dump($user);
					$current_role = $user->roles; // an array
					$current_role = $current_role[0];
					$white_list = str_replace(" ","", $params['roles']); // get rid of any spaces
					$white_list = explode(',',$white_list); 
					if(!in_array($current_role, $white_list))
						return '';
				}
					
				//wf_load_stylesheet( 'trip_and_comment_widgets_css', THEME_FOLDER_URL.'/css/trip_and_comment_widgets.css' );
				
				
				$html = '';
				$html = "\n\n<div class='".$params['style']."'>\n"; 
				$html .= extra_markup($params);
				if($params['heading'] !='') {
					$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; 
				}
				
				
				$comments_args = array(
				'title_reply'=>'Write a Reply or Comment',
				'comment_notes_after' => '', // remove "Text or HTML to be displayed after the set of comment fields"
				'comment_field' => '<p class="comment-form-comment">
										<label for="comment">' . _x( 'Comment', 'noun' ) . '</label><br />
										<textarea id="comment" name="comment" aria-required="true"></textarea>
									</p>' // redefine textarea (the comment body)
				);
				ob_start();
				comment_form($comments_args);
				$html .= ob_get_clean();
				$html .= "</div>";
				return $html;
			}
			
			// v6.58
			static function control_comments_for_all($value){ //metinsaylan.com/4571/how-to-enable-comments-for-all-posts-in-wordpress/
				if(!in_array($value,array('open','registered_only','closed'))) {
					return;					  
				}
				global $wpdb;
				//$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET comment_status = 'open'")); // Enable comments
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET comment_status = %s", $value)); //make.wordpress.org/core/2012/12/12/php-warning-missing-argument-2-for-wpdb-prepare/
				//$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET ping_status = 'open'")); // Enable trackbacks
			}
			
		}
		
		
		$reset_comments = readQstring('reset_comments');// v6.58
		if(!empty($reset_comments)) {
			Comment_form_widget::control_comments_for_all($reset_comments); // open|registered_only|closed
		}
		/*
		switch($reset_comments) {
			case 'open':
				Comment_form_widget::enable_comments_for_all();
				break;
		}
		*/
		
		
				
			
		/// YOUTUBE VIDEO WIDGET //////////////////////////////////////////
			
						
		class YouTube_widget extends Wf_Widget {
			
			public function get_html(){	
				$params = $this->params;
		
				$html = "\n\n<div class='".$params['style']."'>\n"; 
				$html .= extra_markup($params);
				if($params['heading'] !='') {
					$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; 
				}
				$html .= "<iframe width='".$params['width']."' height='".$params['width']."' 
					src='http://www.youtube.com/embed/".$params['code']."?rel=0&enablejsapi=1' frameborder='0' allowfullscreen></iframe>"; //600 x 336
				$html .= "</div>\n";
				return $html;
			}
			
		}
		
		
		
		/// USER PHOTO WIDGET //////////////////////////////////////////
						
			
		class UserPhoto_widget extends Wf_Widget {
			
			public function get_html(){	
			
				global $post;
				global $globals;
				$user = wp_get_current_user();
				$params = $this->params;
				d('$params',$params); // (F3) calls Wf_Debug
				
				if(!$params['gallery'] && $user->ID == 0) 
					return ''; // if gallery not wanted and we're not logged in, no point in outputting anything
				
				$html = "\n\n<div class='".$params['style']."'>\n"; 
				$html .= extra_markup($params);
				if($params['heading'] !='') {
					$html .= "<div class='hwrap'><h2>".$params['heading']."</h2></div>\n"; 
				}
				
				if($params['gallery']) { // do we want to include gallery?
					
					if($params['attached_to'] =='current')
						$params['attached_to'] = $post->ID;
					if($params['owner_id'] =='author')
						$params['owner_id'] = $post->post_author;
					if($params['owner_id'] =='user')
						$params['owner_id'] = $user->ID;
					if($params['owner_id'] =='quser')
						$params['owner_id'] = $globals['quser_id'];
					if($params['owner_id'] =='all') {
						$params['owner_id'] = '';
						foreach(UTA_Trips::$miniusers as $id => $user) {
							if(in_array($user['roles'], array('member','volunteer'))) {
								$params['owner_id'] .=  $id.',';
							}
						}
						$params['owner_id'] .= 28; // UTA-admin - which makes this very specific
						Wf_Debug::stash(array("params['owner_id']" => $params['owner_id'])); // (F3) calls Wf_Debug
					}
					$args = array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'author' => $params['owner_id'],
						'post_parent' => $params['attached_to'],
						'post_status' => null
					);
					d('$args',$args); // (F3) calls Wf_Debug
					$image_ids = array();
					$attachments = get_posts($args);
				
					foreach ($attachments as $attachment) {
						$image_ids[]= $attachment->ID;
					}
					$image_id_string = implode(',',$image_ids);
					d('$image_id_string',$image_id_string); // (F3) calls Wf_Debug	
					
					if($image_id_string != '') {
						$html .= do_shortcode("[gallery ids='".$image_id_string."' columns='".$params['columns']."']"); 
					} else {
						$html .=  "<p class='empty'>There are no photos to show</p>\n";
					}
				}
				
				if($params['upload']) { // do we want to include upload facility? 
					$html .= "
					<div id='upload_wrap' class='reveal'>
						<p class='reveal_head'>Upload a photo from your computer</p>
						<div id='upload' class='reveal_tail'>
							<form id='thumbnail_upload' method='post' action='#' enctype='multipart/form-data' >
							
								<div class='fileinputs'>
									<input type='file' class='file' name='thumbnail' id='thumbnail' >
									<div class='fakefile'>
										<div class='fakelabel'>&nbsp;</div>
										
										<input id='fakeinput' name='fakeinput' type='text' />
									</div>
								</div>
								
								<!--<input type='file' name='thumbnail' id='thumbnail' >-->
								<div id='caption_div'>
								<label for='caption'>Caption</label>
								<input type='text' name='caption' id='caption'>
								</div>
								<input type='hidden' value='".wp_create_nonce( 'upload_thumb' )."' name='_wpnonce' />
								<input type='hidden' name='post_id' id='post_id' value='".$post->ID."'>
								<input type='hidden' name='action' id='action' value='my_upload_action'>
								<input id='submit-ajax' name='submit-ajax' type='submit' value='upload'>
							</form>
						<div id='output1'></div>
						</div>
					</div>\n";
		
				}			
				$html .= "</div>\n";
				return $html;
			} 
		}

		
	} // function users_frontend()
		
} // not admin
