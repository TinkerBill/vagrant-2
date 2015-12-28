<?php 

 // JWBP150406 copied from dev site
/*
This loads immediately after widget_default_specs.php and is used to customise the widget specifications for this site.

Don't put anything in here that relies on other files/functions/classes: the AJAX files json_specs.php and json_validate.php call it and have to be able to run it on its own.    

*/


//$default_specs['tweets']['params']['method']['default'] = 'outlaw'; // v6.78
 
$current_specs = $default_specs;

//$current_specs = set_styles($current_specs, '=morenews|infobox|quote|full|paleblue'); // set all style validation strings in $current_specs 

// These can then be overwritten like this... 
//$current_specs['random']['params']['style']['validation'] = "=quiet|in_yer_face";

// BILL 8/1/15 
// Master array to identify a godfather for each category
$catfather_array = array (
	'news' => 10, //about carplus
	'event' => 290 //  Event   40=tools and resources
);



function get_form_specs_file($formname) {
	return 'carplus_form_specs.php';
}


		
if (class_exists('Wf_Widget')) { // ie: AJAX Keep Out!


	function get_regions_list($post) { //v6.49
		switch($post->post_name) {
			//case 'home':
				//$regions = array('right','top_right','top','main','bottom_right');
				//break;
			default:
				$regions = array('right', 'bottom');
		}
		return $regions;
	}
	
	
	if(!is_admin()) {
		add_action( 'init', 'add_more_widgets', 11 ); // was wp_head, wp
	
		function add_more_widgets() {
			
			// NEW WIDGET DEFINITIONS ////////////////////////////////////////////////////////////////////////////////////////////
			
					
			// END OF NEW WIDGET DEFINITIONS ////////////////////////////////////////////////////////////////////////////////////////////
		
		} // add_more_widgets
	} // if(!is_admin())


}


	
//////////////////////////////////////////////////////////////////////////////////////////////////////

// TO REMOVE A WIDGET, UNCOMMENT THE RELEVANT LINE BELOW:

//unset($current_specs['post']);
//unset($current_specs['list']);
//unset($current_specs['random']);
//unset($current_specs['vscroller']);
//unset($current_specs['slideshow']);
//unset($current_specs['popup']);
//unset($current_specs['tweets']);
//unset($current_specs['form']);
//unset($current_specs['reveal']);


// TO MODIFY A WIDGET, EITHER REDEFINE IT...

// Eg:
/*
$current_specs['random'] = array( 

	'display_name' => 'Random text widget',
	
	'class_name' => 'Random_widget_NEW', // *** defined below ***
	
	'params' => array(	
		'id' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'id'
			),
		
		'comment' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'string'
			)
			
		// *** style parameter removed ***
	)
);

class Random_widget_NEW extends Random_widget {  // NOW NOT SURE IF WE CAN REALLY DO THIS HERE!
	
	// *** Now just overwrite any properties or methods that need changing ***
}

*/



// ... OR OVERWRITE SPECIFIC BITS...

// Eg:
/*
$current_specs['random']['params']['style']['default'] = "quiet";
$current_specs['random']['params']['style']['validation'] = "=quiet|in_yer_face";
*/
