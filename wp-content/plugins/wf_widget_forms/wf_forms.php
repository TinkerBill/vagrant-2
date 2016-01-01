<?php

/*
Plugin Name: Wingfinger Forms plugin  
Plugin URI: http://www.wingfinger.co.uk
Description: A plugin for inserting a form on a page
Version: 6.49
Author: Wingfinger
Author URI: http://www.wingfinger.co.uk
License: It's copyright!
*/

/*
This program is NOT free software; if you want to use it, please contact
info[at]wingfinger.co.uk for details of pricing. Thankyou.
*/

/*

v6.49 	15/4/14	Introduce $params['logged_in'] - bail-out dependent on whether user is logged in
				Started to implement disabled inputs with code 'd' - but couldn't think of easy way of getting
				value into field.

v6.48	24/2/14	Added update checker and set up form_widget_common()

v6.47	12/9/13	Added check for $_SESSION["capcha_width"] set in capcha.php.

v3.61	30/7/13	Added email_params['test_mode']. True if any of the TEST_EMAILS have been entered in the user email field.
				Standard do_on_completion() bails out with appropriate message.

v6.30	29/7/13	Changed widget css classes to include sluggify($formname) instead of $this->form_file_root_name. 
				Allows us to differentiate different forms from same specs file.

v6.10	12/6/13	Rejigged the $do_on_completion stuff because any function defined in the specs.php file is outside the class

v6.8	10/6/13	Experimental: instead of using $params['css'], look for .css file in same place and with same root name 
				as _specs.php file - eg: /files/wf_widget_forms/chaco_form.css
				
				To get this to work, introduced private properties $form_file_root_name, $css_path, $email_elements and $form_elements.
				
				Added $form_file_root_name to widget style classes

v5.11 	29/4/13	'$form_global' is now '$params' - for compatibility with other widgets

				function get_custom_popup_elements($custom_popups,$params) now uses 2nd parameter instead of global
				
				!!!! TO DO: Currently, the new widget interface doesn't cope with custom popup elements in forms

*/



//define('WF_FORMS_VERSION', '1.0.0'); // v6.48 not used
//define('THIS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ )); // v6.48 not used
//define('TEST_EMAILS', 'bill@wingfinger.co.uk,richard@wingfinger.co.uk,amy@wingfinger.co.uk,test@wingfinger.co.uk'); // v6.31 (can't "define" arrays) // v6.48 moved to form_widget_common()

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

add_action( 'init', 'form_widget_common', 10 );

function form_widget_common() {
	
	define('WF_FORM_PLUGIN_URL', plugin_dir_url( __FILE__ ));
	define('TEST_EMAILS', 'bill@wingfinger.co.uk,richard@wingfinger.co.uk,amy@wingfinger.co.uk,test@wingfinger.co.uk');
	// v6.57 already loaded by wf_lib
	if(class_exists('PluginUpdateChecker')) { // v6.58 - just in case
		$UpdateChecker = new PluginUpdateChecker(
			'http://www.graphicdesignleeds.info/lib/wf_widget_forms.json', // v6.58
			__FILE__,
			'wf_widget_forms'
		);
	}
	
} 




	
if ( is_admin()) {	

	function wf_form_admin_actions() {  
		add_options_page("WF Form Settings", "WF Forms", 'administrator', "wf_form_settings", "wf_form_admin");  
		// $page_title, $menu_title, $capability, $menu_slug, $function
	} 
	
	function wf_form_admin() { // callback function to handle contents of options page "WF Newsletter Settings"
		include('wf_form_admin.php');
	}
	// add_action('admin_menu', 'wf_form_admin_actions');  
		
			
} else { // not admin

	add_action( 'wp', 'forms_frontend', 100 ); // was wp_head, wp, init

	function forms_frontend() {
				
		// INSERT FORM //////////////////////////////////////////////////////////////////////////////////
	
		// Bit complicated because widget adds code to index.php, which then generates the html to insert back into
		// the appropriate region. Needs further genericising of index.php.
		
		
		/* params...
			'formname'
			'style'
			'css'
			'message_label'
		*/
		
		
		class Form_widget extends Wf_Widget {
			
			private $form_file_root_name; // all these v6.8
			private $css_path; 
			private $email_params;
			private $form_elements;
			
			public $form_obj;
			
			public function __construct($region,$widget_type,$data) { //$region,$qstring
				parent::__construct($region,$widget_type,$data); // Call the parent class's constructor
				$this->form_file_root_name = str_replace('_specs.php','',get_form_specs_file($this->params['formname'])); //v6.8 eg: 'chaco_form'
				$this->css_path = dirname(dirname( __FILE__ )).'/files/wf_widget_forms/'.$this->form_file_root_name.'.css';// v6.8
			}  
			
			
			// stackoverflow.com/questions/6184337/best-practice-php-magic-methods-set-and-get
			public function __get($p) { //v6.8
				$m = "get_$p";
				if(method_exists($this, $m)) return $this->$m();
				user_error("undefined property $p");
			}
			
			// set undeclared property
			public function __set($property, $value) { //v6.8
				$this->$property = $value;
			}
			
			
			public function  get_html() {
				
				//$params = $this->params; //v5.11
				
				if($this->params['logged_in']) { // v6.49 bail-out dependent on whether user is logged in
					if(
						(is_user_logged_in() && $this->params['logged_in'] == 'hide') ||
						(!is_user_logged_in() && $this->params['logged_in'] == 'show') 
					) {
						return '';
					}
				} // v6.49
				$formname = $this->params['formname']; //v6.8
				
				require_once(dirname(dirname( __FILE__ )).'/files/wf_widget_forms/'.$this->form_file_root_name.'_specs.php'); // v6.8
				
				$this->email_params['test_mode'] = false; // v6.31  adding extra element to email_params set in specs file
				$this->load_css(); // v6.8
				
				$this->form_obj = new Form();
				$this->form_obj->set_specification($this->form_elements);
				$this->form_obj->form_field_array= ($this->form_obj ->get_field_array());
				
				$form_field_array = $this->form_obj->form_field_array;
				
				
				if(session_id() == ''){ // v3.74
					session_start();
				}
				
				if (get_magic_quotes_gpc()) {
					$_POST = strip_array($_POST);
					$_SESSION = strip_array($_SESSION);
					$_GET = strip_array($_GET);
				}
				
				$result_html  = '';
				$message=''; // this one is only used when mail() hits a problem
				$next_form_pass = return_form_elements($formname,$form_field_array);
				if(isset($_GET['try']) && $_GET['tryform'] == sluggify($formname)) { //v6.8
					unset($_POST['capcha']);
				}
				Wf_Debug::stash(array('$this->params'=>$this->params));
				Wf_Debug::stash(array('$this->email_params'=>$this->email_params));
				Wf_Debug::stash(array('$_POST'=>$_POST));
				

				if($next_form_pass['complete']) {
					$next_form_pass['complete'] = false;
					if(isset($_POST[$this->email_params['user_email']])) { // v6.30 If not set, we prob don't need it
						$this->email_params['user_email'] = $_POST[$this->email_params['user_email']]; // ie: original value identifies the relevant form field // v6.10
						if(in_array($this->email_params['user_email'],explode(',',TEST_EMAILS))) {
							$this->email_params['test_mode'] = true; // v6.31
						}
					}
					if(!function_exists(sluggify($formname))) { // v3.76
						//$do_on_completion = 'do_on_completion';
						$result_html = $this->do_on_completion($formname,$form_field_array,$this->email_params); // v6.10 a method of this class
					} else {
						$do_on_completion = sluggify($formname);
						$result_html = $do_on_completion($formname,$form_field_array,$this->email_params); // v6.10 a function, not a method
					}
					//$this->email_params['user_email'] = $_POST[$this->email_params['user_email']]; // ie: original value identifies the relevant form field // v3.76
					//$result_html = $this->$do_on_completion($formname,$form_field_array,$this->email_params);
				}
				
				//$form_html = "\n<div class='".$this->params['style']." ".$this->form_file_root_name."'>\n"; // v6.8
				$form_html = "\n<div class='".$this->params['style']." ".sluggify($formname)."'>\n"; // v6.30  Style was $this->form_file_root_name
				$form_html  .= $result_html;
				$form_html  .=	
				"<form method='post' action='".SELF_URL."?try=true&amp;tryform=".sluggify($formname)."#formtop'>\n". // v3.74 &amp; v3.83
				"<div class='fswrapper'>\n
				<fieldset>\n
				<a name='formtop' id='formtop'></a>\n
				<legend>".$this->email_params['legend']."</legend>\n";
				
				$form_html .=	$next_form_pass['html'];
				$form_html .= $this->email_params['end_html'];
				$form_html .=
				"</fieldset>
				<!-- end of fswrapper -->
				</div>
				</form>\n
				</div>\n";
				return $form_html;
	
			}
			
			
			
			public function do_on_completion($formname,$form_field_array,$email_params) {
				if($email_params['test_mode']) {
					return "Test mode: no email sent."; // v3.61
				}
				$text = assemble_email_body($form_field_array);
				if(textEmail2($email_params, $text)) {
					header("Location: ".$email_params['success_url']);
					exit();
				} else {
					return $email_params['fail_message'];
				}
			}
			
			
			private function load_css() { // v6.8
				if (file_exists($this->css_path)) {
					add_action('wp_enqueue_scripts',array(&$this, 'custom_css'));
				}
			}
			
			function custom_css() { // v6.8
				wp_enqueue_style($this->form_file_root_name.'_css', plugins_url( 'files/wf_widget_forms/'.$this->form_file_root_name.'.css'));
			}
					
		}
		
		
		
		class Form {
			public $specification;
			//public $dropdowns;
			public $field_array;
			//public $field_titles;
			
			public function get_specification() {
				return $this->specification;
			}
			
			public function set_specification($new_specification) { 
				$this->specification = $new_specification;  
			}
			
			
			public function get_field_array() {
				foreach($this->specification as $key => $value) {
					$element = explode(',',$value);
					$this->field_array[$key]['code']=$element[0];
					$this->field_array[$key]['validator']=$element[1];
					$this->field_array[$key]['title']=$element[2];
					$this->field_array[$key]['dropdowns']= ''; // bit of a misnomer because also used for radio buttons and capcha v3.73
					$this->field_array[$key]['initial_value'] ='';
					if(count($element) > 3) {
						if(in_array(substr($element[0],0,1), array('i','t'))) { // if it's an <input> or <textarea> v3.73
							$this->field_array[$key]['initial_value']=$element[3]; // ie: any initial value can be added as 4th element v3.73
						} else {
							$this->field_array[$key]['dropdowns']=array_slice($element,3); // removes first 3 items
						} 
					}
				}
				return $this->field_array;
			}
			
			
		}
			
			
			
	} // function forms_frontend()
	
	
	
	
	function return_form_elements($formname,$form_field_array) { 

	// No validation required when displaying initial form for new item.
	
	
	// array key is used for id of enclosing div and name
	// array value string is exploded into another array:
	// [code] is code: i=input, s=select, c=checkbox, t=textarea, h=hidden  (OMIT 'i'), *=required
	// [1] is validation
	// [2] is label
	// [3] etc are the displayed options. The values are always 0, 1, 2, 3 etc
	// this system was chosen to make it quicker to type! (not too many double quotes etc)
	// <label> relates to id, not name, so containing div id is (eg) "firstname_div" for <input> id='firstname'
	
		$html=''; // v3.66	
		$errors = array ('errors_html'=>'','count'=>0);
		$complete = false; // v3.59
		
		foreach ($form_field_array as $key => $row_elements) {
					
			if (strpos($row_elements['code'],'*') === false) {
				$required = '';
			} else {
				$required = '<span>*</span>';
			}
	
			$two_up = '';
			$check = array('feedback' =>'','error_class' =>''); // v3.74 default if no validation required
			
			switch (substr($row_elements['code'],0,1)) { // first character of code
			
				case 'h': // hidden input element
					//$check = array('feedback' =>'','error_class' =>''); // v3.70 ie no validation required
					$html .= "<div id='".$key."_div'  class='hidden'>\n";
					$html .= "<input type='hidden' name='".$key."' id='".$key."' value='".$row_elements['title']."' />\n";
					$html .= "</div>\n";
					$html .= "<!-- -->\n";
					break;
					
					
				case 'i': // input element
					$check = get_validation($key, $form_field_array, $formname); //v3.74
					// returns $check['error_class'] => '' or ' error', $check['feedback'] => a feedback message.
					//$disabled = (strpos($row_elements['code'],'2') !== false) ? " disabled='disabled' " : ''; // v6.49 aborted
					if (strpos($row_elements['code'],'d') !== false) {
						$two_up = " two-up_input";
					}
					$html .= "<div id='".$key."_div' class='".$key.$check['error_class'].$two_up."'>\n";
					//if ($two_up == '') {
						$html .= "<label for='".$key."'>".$required.$row_elements['title']."</label>\n";//was 1 Rich
					//}
					$maxlength_html = get_maxlength_html($row_elements['validator']);
					$html .= "<input type='text' name='".$key."' id='".$key."' ".$maxlength_html." value=\"".stripslashes(prepopulate($key,$row_elements['initial_value']))."\" />\n";// v3.73
					$html .= "</div>\n";
					$html .= "<!-- -->\n";
					break;
					
			
				case 'c': // checkbox element
					if (strpos($row_elements['code'],'2') !== false) {
						$two_up = " two-up_check";
					}
					$html .= "<div id='".$key."_div' class='checkbox".$two_up."'>\n";
					$html .= "<input type='checkbox' name='".$key."' id='".$key."' ".checkprepop($key)." />\n";
					$html .= "<label for='".$key."'>".$required.$row_elements['title']."</label>\n";
					$html .= "</div>\n";
					$html .= "<!-- -->\n";
					break;
				
				
				case 'r':// radio element added by richard
					$radio_intro = $element[1];
					$radio_check = strpos($element, '<');
					$html .= "<div id='".$key."_div' class='radio'>\n";
					$html .= "<p>".$radio_intro."</p>\n";
					for ($opt=0; $opt < count($element)-2; $opt++) {
						$html .= "<input type='radio' name='".$key."' value='".$key.$opt."' ".radioprepop($key, $opt, $radio_check)." />\n"; 
						$html .= "<label for='".despace($element[$opt+2])."'>".$required.degreaterthan($element[$opt+2])."</label>\n";
					}
					$html .= "</div>\n";
					$html .= "<!-- -->\n";
					break;
					
					
				case 't': // textarea element
					$check = get_validation($key, $form_field_array, $formname); //v3.74
					$html .= "<div id='".$key."_div' class='".$key.$check['error_class']."'>\n";
					$html .= "<label for='".$key."'>".$required.$row_elements['title']."</label>\n";
					$html .= "<textarea rows= '6' cols='46'  name='".$key."' id='".$key."'>".prepopulate($key,$row_elements['initial_value'])."</textarea>\n";// v3.73
					$html .= "</div>\n";
					$html .= "<!-- -->\n";
					break;
					
					
				case 's': // select element
					$check = get_validation($key, $form_field_array, $formname); //v3.74
					if (strpos($row_elements['code'],'2') !== false) {
						$two_up = " two-up_select";
					}
					$html .= "<div id='".$key."_div' class='".$key.$check['error_class'].$two_up."'>\n";
					$html .= "<label for='".$key."'>".$required.$row_elements['title']."</label>\n";//was 1 Rich
					$html .= "<select name='".$key."' id='".$key."'>\n";
					for ($opt=0; $opt < count($row_elements['dropdowns']); $opt++) {
						$html .= "<option value='".$key.$opt."'".selectprepop($opt, $key).">".$row_elements['dropdowns'][$opt]."</option>\n"; // value = (eg) firstname0, firstname1 etc
					}
					$html .= "</select>\n";
					$html .= "</div>\n";
					$html .= "<!-- -->\n";						
					break;	
					
			case 'p': // paragraph element
					$html .= "<p class='".$key.$check['error_class']."'>".$required.$row_elements['title']."</p>\n";
					$html .= "<!-- -->\n";
					break;
					
			case 'm': // html element // v3.69
					$html .= $row_elements['title']."\n";
					$html .= "<!-- -->\n";
					break;
						
						
				case 'x': // capcha check, followed by capcha_function_name,width, height, character count
					$check = get_validation($key, $form_field_array, $formname); //v3.74
					$html .= write_capcha($check,$row_elements);
					break;
					
			
			} // switch
			$errors = add_to_message($errors, $check);
		} //for each 
		
		$html .= "<input type='hidden' name='formname' id='name_".sluggify($formname)."' value='".$formname."' />\n"; // v3.74
		
		// needs submit.gif in standard place - might make more sense to do this via css
		if(function_exists('better_button')) { // v3.73
			$html .= better_button($formname);
		} else {
			$html .= "\n\n<input type='image' name='do_formsend' src='".THEME_FOLDER_URL."/images/submit.gif' id='do_formsend' class='button' />\n";
		}
		if(function_exists('custom_validate') && isset($_GET['try']) && $_GET['tryform'] == sluggify($formname)) { // v6.8 inserted && $_GET['tryform']
		// allows customised validation/checking via this function if it exists (eg at start of THEME_FOLDER_URL/form.php file)
			$errors = custom_validate($formname,$errors,$form_field_array);
		}
		if ($errors ['errors_html'] != '') {
			$errors ['errors_html'] = "\n\n<div class='form_errors'>\n<p><strong>There are problems with the form data...</strong></p>\n<ul>\n".$errors['errors_html']."</ul>\n</div>\n\n";
		} else {
			if(isset($_GET['try']) && $_GET['tryform'] == sluggify($formname)) { // v3.74
				$complete = true; // v3.59
			}
		}
		return array('html' => $errors ['errors_html'].$html, 'complete' => $complete); // v3.59
	} //end of function return_form_elements 
	


	function assemble_email_body($form_field_array) {
		$body ='';
		foreach($form_field_array as $field_name => $field_elements) { 
			$label = strip_tags($field_elements['title']);
			if(isset($_POST[$field_name])) { // which it isn't for capcha (x) and html (m) elements // v5.10
				$emailed_value = remove_headers($_POST[$field_name]); // added remove_headers 5.1.12
			}
			switch (substr($field_elements['code'],0,1)) { // first character of code
			
				case 'x': // capcha
				case 'h': // hidden
					continue;
					break;
						
				case 's': // select
				case 'r': // radio
				
					// This is a select/drop-down or radio
					$select_value = $_POST[$field_name]; // eg: payment3
					$select_num = str_replace($field_name, '', $select_value); // eg: 3
					$emailed_value = degreaterthan($field_elements['dropdowns'][$select_num]); // eg: "Standing order (details will be sent)"
					$body .= $label." \t".$emailed_value."\n\n";
					break;
			
				
				
				case 'c': // checkbox
					if($emailed_value == 'on') {
						$emailed_value = 'YES';
					} else {
						$emailed_value = 'NO';
					}
					$body .= "Checkbox: ".$field_name." \t".$emailed_value."\n\n";
					break;
					
				case 'm': // html element  v3.72
					$body .= $label."\n\n";
					break;
					
					
				default: // i, t
					$body .= $label." \t".$emailed_value."\n\n";
					//echo $label.$emailed_value;
			
			}// switch
			
		} // foreach
		$body = str_replace("&amp;", "&", $body);
		$body = str_replace("&#44;", ";", $body);
		$body = str_replace("&#44;", ";", $body);
		return $body;
	}
	
	
	
	if(!function_exists('add_to_message')) {  // v3.56
		function add_to_message($errors, $check) {
			if ($check['error_class'] != '') {
				$errors['errors_html'] .= "<li>".$check['feedback']."</li> \n";
				$errors['count']++;
			}
			return $errors;
		}
	}
	
	
	function get_validation($key, $form_field_array, $formname) { // v3.74
	// NB We don't currently check back to see if valid "+" fields are preceded by a ticked box or a dropdown "Other.."
	// It wouldn't be too tricky to reset the "+" field to empty - but frustrating for the user if they'd typed a lot into a <textarea>!
	// ...or maybe keep previous item's html in a buffer and do a str_replace on it?
		if(!(isset($_GET['try']) && sluggify($formname) == $_GET['tryform'])) { // v3.74
			return array('feedback' => '','error_class' =>''); // v3.66
		}
		$value = $_POST[$key];
		$element = $form_field_array[$key]; 
		$error_message = '';
		list($func_name, $arg) = parse_validator_string($element['validator']); // v3.57
		$brief_title = $element['title']; // used in feedback/error messages
		$code1 = substr($element['code'],0,1);
		
		if (strpos($element['code'],'+') !== false) { // <input> or <textarea> used to explain previous item (checkbox or dropdown "Other...")
		  $array_keys = array_keys($form_field_array);
		  $rownum = array_search($key,$array_keys);
		  $previous_key = $array_keys[$rownum-1];
		  $prev1 = substr($form_field_array[$previous_key]['code'],0,1);
		  $prev_title = $form_field_array[$previous_key]['title'];
		  Wf_Debug::stash(array('$prev_title'=>$prev_title));

		}
	
		if ($value != '') {
			if($func_name == 'selectmin') {
				$error_message = $func_name($arg, $value, $key);
			} else {
				$error_message = $func_name($arg, $value); //eg: val_capcha('','feecd')
			}
				if (strpos($element['code'],'+') !== false  &&  $prev1 == 'c') { 
				$brief_title = $prev_title.":"; // feedback messages refer to label of previous item
				}// 
		  } else { //value is blank
			if (strpos ($element['code'],'*') !== false)  { 
				$error_message = 'Required field';
			}
			if (strpos($element['code'],'+') !== false  && ($code1 == 'i' || $code1 == 't')) { // 
				// It's a blank '+' field or text area...
				Wf_Debug::stash(array('$prev1'=>$prev1));
				Wf_Debug::stash(array('$previous_key'=>$previous_key));
				if( $prev1 == 'c' && isset($_POST[$previous_key])) { // ... and the previous one was a ticked checkbox. v6.8 was $_POST[$previous_key] !=''
					$error_message = "Please specify ".substr($element['title'],0,-1); // lop off final colon
					$brief_title = $prev_title.":"; // we want to identify it with the label of the previous one
				}
				if( $prev1 == 's') { // ... and the previous one was a select...
					$value_num = 0+substr($_POST[$previous_key],strlen($previous_key));
					if($value_num+1 == count($form_field_array[$previous_key]['dropdowns'])) { // ... and last item on the menu was selected
						$error_message = "Please specify."; // assume last one is "Other"
						$brief_title = $prev_title; // we want to identify it with the label of the previous item
					}
				}
			} 
			
		} //value is blank
	 
		if($error_message == '') {
			$check['feedback'] = '';
			$check['error_class'] = '';
		} else {			  
			if (strpos($element['code'],'x') !== false) { 
				$brief_title = 'Security: ';
			}
			$check['feedback'] = $brief_title." ".$error_message;
			$check['error_class'] = ' error';
		}
		return $check;
	}
	


	if(!function_exists('write_capcha')) { // 3.30  (was !defined)
		function write_capcha($check,$row_elements) {
			$_SESSION["capcha_width"] = $row_elements['dropdowns'][0];
			$_SESSION["capcha_height"] = $row_elements['dropdowns'][1];
			$_SESSION["capcha_character_count"] = $row_elements['dropdowns'][2];
			$_SESSION["capcha_font"] = $row_elements['dropdowns'][3];
			//echo $_SESSION["capcha_character_count"];
			$html  = "\n<div id='capchatextdiv' class='capcha".$check['error_class']."'>\n";
			$html .= "<label for='capcha'><span>*</span>".$row_elements['title']."</label>\n";
			//$html .= "<img src='".THEME_FOLDER_URL."/forms/capcha.php' alt='securityimage'/>"; // v3.59
			$html .= "<img src='".plugins_url('wf_widget_forms/capcha.php')."' alt='securityimage'/>";
			$html .= "<input type='text' name='capcha'  id='capcha' />\n</div>\n";
			return $html;
		}
	}
	
	
	// originally was get_event_elements() in carplus_form_specs.php
	
	function get_custom_popup_elements($custom_popups,$params) { // v5.11
		//global $form_global;// all the parameters as an array eg: 'booktype1' => 'Carplus member/London Borough - free of charge'
		$popup_elements = array();
		$customkeys = array_keys($params); // 5.11  eg: formname, style, css
		foreach ($custom_popups as $key => $value) {
			if(in_array($key."1", $customkeys)) {//replaced $customkeys with $formname
				$popup_element = array("s*","selectmin(1)", $value, "Please select...") ; // construct array to match $element in return_form_elements()
				$opt=1;
				while(in_array($key.$opt, $customkeys)) {
					$popup_element[]= $params[$key.$opt]; //v5.11
					$opt++;
				}
				$popup_elements[$key] = implode(",", $popup_element);
			} 
		}
		return $popup_elements;
	}
	




} // not admin

