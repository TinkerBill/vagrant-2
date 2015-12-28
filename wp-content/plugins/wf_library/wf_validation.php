<?php

// VALIDATION ROUTINES PULLED OUT OF WF_LIB //////////////////////////////////////////////////////////////////////////

/*

v6.73	11/9/15	Added function radio()

v6.71	23/3/15	Moved functions validate_postcode() and validate_function_name() from map widget to wf_validation.php

v6.64  15/10/14	Changed email validation to allow 4 letter tld. Eg: pete.richardson@phonecoop.coop

v6.57	28/3/14	Added 'w' to format param in wf_validation.php to allow display of author in list widget.

v6.8	10/5/13	Changed email validation to allow hyphens

*/



function isdate($args, $value) { // v3.58
	// To keep this function simple...
	// 1. $args *always* has 2 pipes (ie: 3 arguments - $format, $earliest, $latest)
	// 2. $format *must be specified* as either DD-MM-YYYY or YYY-MM-DD and only refers to $value
	// 3. $value is immediately converted to YYYY-MM-DD format
	// 4. $earliest and $latest (if dates) must be specified in YYYY-MM-DD format
	
	//Acceptable args: (DD-MM-YYYY||), (YYY-MM-DD||), (YYY-MM-DD|-1 year|+0 days), (DD-MM-YYYY|1948-11-10|-10 weeks)
	// NB: +0 days = today. Don't use months. No space between +/- and number, so "- 8 weeks" won't work.
	// Can use: day, days, week, weeks, year, years. Single quotes have already been stripped out?
	
	list($format, $earliest, $latest) = explode('|',$args);
	if($format == 'DD-MM-YYYY') {
		list($day, $month, $year) = explode('-',$value);
		$value = $year.'-'.$month.'-'.$day; // ie: $value is now in YYYY-MM-DD format
	} elseif($format == 'YYYY-MM-DD') {
		list($year, $month, $day) = explode('-',$value);
	} else {
		return 'Format not specified';
	}
	
	if (is_numeric($year) && is_numeric($month) && is_numeric($day)) {
		if(checkdate($month, $day, $year)) { //Validate Gregorian date. NB many of the other date functions require php 5.2
			
			if($earliest == '' && $latest == '') {
				return ''; // date is valid and no constraints
			}
			if($earliest != '') {
				if(strtotime($earliest) > strtotime($value)) { // strtotime($earliest) should work for both "1948-11-10" and "-1 year"
					return "Date outside allowable range";
				}
			}
			if($latest != '') {
				if(strtotime($latest) < strtotime($value)) {
					return "Date outside allowable range";
				}
			}		
			return ''; // date is valid and within constraints
		}
	}
	return "Format required is ".$format; // date is not valid
}




function intminmax($arg, $value) { // intminmax(-10|100) min and max values
	if(!is_numeric($value)) {    
		return 'Not a number' ;
	} elseif ($arg=='') {
		return ''; // no constraints
	} else {
		$args_array = explode('|',$arg);
		$min = $args_array[0];
		$max = $args_array[1];
		if($min != '') { // NB intminmax(|100) means no lower limit
			if($value< $min) {    
				return 'Minimum value: '.$min;
			}
		}
		if($max != '') {// NB intminmax(0|) means no upper limit
			if($value> $max) {    
				return 'Maximum value: '.$max;
			}
		}
	} 
	return ''; 
}

  
  
function money($arg, $value) {
	$error_message = '';
	if(!is_numeric($value)) {    
		$error_message = 'Not a number' ;
	} else {
		if($arg =='pos' && $value <= 0) {    
			$error_message = "Can't be negative or zero"  ;
		}
	} 
	return $error_message; 
}

function telnumber($arg, $value) { // Phone should allow 0-9, space, hyphen, round brackets and +
	$error_message = '';
	if (!preg_match('/^[+0-9\\(][0-9- \\(\\)]+$/', $value)) { // can start with +, number or opening bracket
		$error_message = 'Not a valid phone number' ;
	}
	if (strlen($value) > $arg) {
			$error_message = 'Too many characters';
	}
	return $error_message; 
}


// NB: magic_quotes_gpc etc and CleanData function tend to interfere with apostrophes
function name($arg, $value) { // First name, Surname, Town should allow a-z, A-Z, space, hyphen (Newcastle-upon-Tyne) and apostrophe (O'Hara)
	$error_message = '';
	if (!preg_match('/^[A-Za-z\' -]+$/', $value)) { 
		$error_message = 'Not a valid name' ;
	}
	if (strlen($value) > $arg) {
		$error_message = 'Too many characters';
	}
	return $error_message; 
}


function address($arg, $value) { // Address should allow 0-9, a-z, A-Z, space, hyphen, apostrophe, comma, fullstop, round brackets
	$error_message = '';
	if (!preg_match('/^[0-9a-zA-Z-,\. (&amp;)\'\\(\\)]+$/', $value)) { // '
		$error_message = 'Not a valid address' ;
	}
	if (strlen($value) > $arg) {
		$error_message = 'Too many characters';
	}
	return $error_message; 
}


function zip($arg, $value) { // Zip should allow 0-9, a-z, A-Z, space,   plus possibly hyphen, comma, fullstop, round brackets
	$error_message = '';
	if (!preg_match('/^[0-9a-zA-Z ]+$/', $value)) { // '
		$error_message = 'Not a valid code' ;
	}
	if (strlen($value) > $arg) {
		$error_message = 'Too many characters';
	}
	return $error_message; 
}

  
function txt($arg, $value) {
	$error_message = '';
	if(!is_string($value)) {    
		$error_message = 'Not a string' ;
	} 
	else {
		if($arg !='') { // v3.56 Can use validator of "txt()" if no limit required
			if (strlen($value) > $arg) {
				$error_message = 'Too many characters.';
			}
		}
	}
	return $error_message; 
}
  
function weblink($arg, $value) { //v6.31
	$error_message = '';
	$pattern = '/^(https?)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/'; 
	if(!preg_match($pattern, $value)) { 
		$error_message = "Not recognised as a url";
	}
	return $error_message; 
}

function email($arg, $value) {	//v6.8
	$error_message = '';
	//if (!preg_match('/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/',  $value)) {
	//if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",  $value)) { // 10/1/13 to allow tony@co-wheels.org.uk
	if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$^",  $value)) { // v6.64 to allow pete.richardson@phonecoop.coop
		 $error_message= "Not a valid email address";
	} else {
		if (strlen($value) > $arg) {
			$error_message = 'Too many characters.';
		}
	}
	return $error_message; 
}  


function selectmin($arg, $value, $key) { // eg: 1, 'account3', 'account'
	$value_num = 0+substr($value,strlen($key)); // eg: 3
	$error_message = '';
	if(!is_int($value_num)) { 
		$error_message = "Not an integer ";  // not very likely 
	} else {
		if($value_num < $arg) {    
			$error_message = "Not selected ";
		}
	} 
	return $error_message; 
}


function pc($arg, $value) {
	$error_message = '';
	if(!is_string($value)) {    
		$error_message = 'Not a postcode' ;
	} else {
		if (strlen($value) > $arg) {
			$error_message = 'Too many characters.';
		}
	}
	return $error_message; 
}

function radio($arg, $value) { // v6.73
	$error_message = '';
	if(empty($value)) {    
		$error_message = 'Please select a button' ;
	}
	return $error_message; 
}


function val_capcha($arg, $value) {  // eg: val_capcha('','feecd')
	$error_message = '';
	if ($_SESSION["security_code"] != $value) {
		$error_message='Please fill in the security field accurately.';
	 }
	return $error_message;
}




// FROM json_validate.php ///////////////////////////////////////////////////////////////////////////////////////////////////


//([a-zA-Z0-9_-]){11}
/*

groups.google.com/forum/?fromgroups=#!topic/youtube-api-gdata/maM-h-zKPZc

If you need to validate that random user input corresponds to a valid
video id, I'd recommend doing an empirical test. Attempt to access

  http://gdata.youtube.com/feeds/api/videos/VIDEO_ID

If you get a 200 response, then VIDEO_ID is valid.


function validate_youtube_code($val) {
	$feedback = 'ok';
	$pattern = '/^([a-zA-Z0-9_-]){11}$/'; 
	if(!preg_match($pattern, $val)) { 
		$feedback = '11 chars: alphanumerics, hyphen, underscore';
	}
	return $feedback;
}

// Check if youtube video item exists by the existance of the the 200 response
$headers = get_headers('http://gdata.youtube.com/feeds/api/videos/' . $youtubeId);
if (!strpos($headers[0], '200')) {
    echo "The YouTube video you entered does not exist";
    return false;
}
*/


function validate_postcode($val) { // v6.71 
	$feedback = 'ok';
	$pattern = '/^([A-PR-UWYZ0-9][A-HK-Y0-9][AEHMNPRTVXY0-9]?[ABEHMNPRVWXY0-9]? {1,2}[0-9][ABD-HJLN-UW-Z]{2}|GIR 0AA)$/i'; 
	if(!preg_match($pattern, $val)) { 
		$feedback = 'Not a valid UK postcode';
	}
	if($val == 'function') {
		$feedback = 'ok';
	}
	return $feedback;
}

function validate_function_name($val) { // v6.59  // v6.71
	$pattern = '/^[a-zA-Z]+[_a-zA-Z0-9-]*$/'; // I've omitted the possibility of a leading hyphen or escaped characters
	$feedback = 'ok';
	if(!preg_match($pattern, $val)) { 
		$feedback = "alphanumerics, underscores, hyphens, no spaces, starting with a letter";
	}
	return $feedback;
}


function validate_anything($val) { // no checks at all!
	$feedback = 'ok';
	return $feedback;
}


function validate_filter($val) {
	if(function_exists($val)) { // assumes function is in widget_config.php
		$feedback = 'ok';
	} else {
		$feedback = 'function not found';
	}
	return $feedback;
}

function validate_css($val) {
	$pattern = '/^[_a-zA-Z]+[_a-zA-Z0-9-]*\.css$/'; // I've omitted the possibility of a leading hyphen or escaped characters
	$feedback = 'ok';
	if(!preg_match($pattern, $val)) { 
		$feedback = "filename ending '.css'";
	}
	return $feedback;
}


function validate_status($val) { // used as $args['post_status'] for get_posts()
	$feedback = 'ok';
	if(!in_array($val, array( 'publish','future','private','any' ))) { // excludes: 'pending','draft','auto-draft','inherit','trash'
		$feedback = 'not a valid post status';					 
	}
	return $feedback;
}

function validate_style($val) {
	$val_array = explode(',',$val); //
	// stackoverflow.com/questions/448981/what-characters-are-valid-in-css-class-names
	$pattern = '/^[_a-zA-Z]+[_a-zA-Z0-9-]*$/'; // I've omitted the possibility of a leading hyphen or escaped characters
	$feedback = 'ok';
	foreach($val_array as $val_item) {
		if(!preg_match($pattern, trim($val_item))) { // deals with leading and trailing spaces
			$feedback = 'comma-separated alphanumerics, no spaces, starting with a letter';
		}
	}
	return $feedback;
}

function validate_ids($val) { // PLURAL
	$val_array = explode(',',$val);
	$feedback = 'ok';
	foreach($val_array as $val_item) {
		if(intminmax('1|', trim($val_item)) != '') { // deals with leading and trailing spaces
			$feedback = 'comma-separated positive integers';
		}
	}
	return $feedback;
}

function validate_id($val) { // SINGULAR
	$feedback = 'ok';
	if(intminmax('1|', $val) != '') { 
		$feedback = 'positive integer';
	}
	return $feedback;
}


function validate_posint($val) {
	$feedback = 'ok';
	if(intminmax('1|', $val) != '') { 
		$feedback = 'positive integer';
	}
	return $feedback;
}


function validate_int($val) {
	$feedback = 'ok';
	if(intminmax('0|', $val) != '') { 
		$feedback = 'positive integer or 0';
	}
	return $feedback;
}


function validate_cat($val) { // v6.2 comma-separated positive integers
	return validate_ids($val);
}

function validate_show_posts($val) {
	$feedback = 'ok';
	if(intminmax('1|', $val) != '' && $val != '-1') { 
		$feedback = 'positive integer or -1 for all';
	}
	return $feedback;
}

function validate_format($val) {
	$feedback = 'ok';
	$pattern = '/^[dtrlahw, ]+$/'; // v6.57
	if(!preg_match($pattern, $val)) { 
		$feedback = 'Allowed characters: d,t,r,l,a,h,w';//commas and spaces ignored
	}
	return $feedback;
}

function validate_text($val) {
	$feedback = 'ok';
	$pattern = '/^[0-9a-zA-Z-,\. (&amp;)\'\\(\\)]+$/'; 
	if(!preg_match($pattern, $val)) { 
		$feedback = 'Allowed: alphanumerics, spaces and -,.()&';
	}
	return $feedback;
}


function validate_heading($val) {
	return validate_text($val);
}

function validate_comment($val) {
	return validate_text($val);
}

function validate_caption($val) {
	return validate_text($val);
}





function validate_link($val) {
	$feedback = 'ok';
	
	$pattern = '/^(https?)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$|^(\/[a-zA-Z0-9\-_]+)+\/?$|^\/$/'; 
	if(!preg_match($pattern, $val)) { 
		$feedback = "Allowed: urls starting '/', 'http://' or 'https://'";
		/*
		Actually allows: 
		EITHER fairly permissive 'http(s)://' urls, inc queries, anchors and %-codings 
		OR much less permissive paths starting with '/' 
		OR just '/'
		*/
	}
	return $feedback;
}


/* regexlib.com/REDetails.aspx?regexp_id=146

^(http|https|ftp)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&amp;%\$#\=~])*$

Modified URL RegExp that requires (http, https, ftp)://, A nice domain, and a decent file/folder string. Allows : after domain name, and these characters in the file/folder sring (letter, numbers, - . _ ? , ' / \ + &amp; % $ # = ~). Blocks all other special characters-good for protecting against user input!
*/




function validate_tweets_username($val) {
	$feedback = 'ok';
	$pattern = '/^[A-Za-z0-9_]{1,15}$/'; // stackoverflow.com/questions/4424179/how-to-validate-a-twitter-username-using-regex
	if(!preg_match($pattern, $val)) { 
		$feedback = 'Alphanumerics, underscores, no spaces, 15 characters max';
	}
	return $feedback;
}

function validate_bool($val) {
	return validate_dropdown($val);
}

function validate_taxonomy($val) {
	return validate_dropdown($val);
}

function validate_post_type($val) {
	return validate_dropdown($val);
}

function validate_dropdown($val) { // $choices = (eg) "quiet|in_yer_face" - the leading '=' has been removed
	$feedback = 'ok';
	if($val == '0') {
		$feedback = 'Please select an option';
	}
	return $feedback;
}




function get_fname($name, $param_vals) {
	$validate = ($param_vals['validation'] == '') ? $name : $param_vals['validation']; // eg: 'style'
	
	switch (true) {
		
		case substr($validate,0,1) == '=':
			
			//$feedback = validate_dropdown(substr($validate,1), $val);
			$fname = "validate_dropdown";
			break;
			
		default:
			$fname = 'validate_'.$validate; // eg: validate_style
	}
	return $fname;
}


