<?php  

 
// MAP 
$map_html ="
<div id='home_map' class='rounded_box'>\n
	<div id='home_map_text'>\n
		<a href='/resources/mapping/'>Survey the local activist landscape!</a>
	</div>
	<a class='coverlink' href='/resources/mapping/'>&nbsp;</a>
</div>";   

$welcome_obj = new Post_widget('welcome', 'post', 'ids=5311&link=/five-ways-leeds-for-change-is-your-one-stop-fightback-shop/&show_title=false&widget=post');

$welcome = $welcome_obj->get_html();


// OUTPUT

$html = array();


//row 1
if(is_user_logged_in()) {
	$html['home1_left'] = get_user_panel2();
} else {
	$html['home1_left'] = get_region_html('home1_left'); // not logged in: Make the most
}
$html['home1_middle'] = $welcome;


//row2
$html['home2_left'] = get_region_html('home2_left'); // the vscroller

$html['home2_middle'] = $map_html; // // the map link



//row3
$html['home3_left'] = get_region_html('home3_left'); // events

$html['home3_middle'] = get_region_html('home3_middle'); // skills

$html['home3_right'] = get_region_html('home3_right'); // comments



	
$custom_content = "
<div id='row1' class='row-fluid row'>
	<div id='home1_left' class='col-md-4 col-sm-5'>".
		$html['home1_left']."
	</div>
	<div id='home1_middle' class='col-md-8 col-sm-7'>".
		$html['home1_middle']."
	</div>
</div>
<div id='row2' class='row-fluid row'>
	<div id='home2_middle' class='col-md-8 col-md-push-4 col-sm-7'>".
		$html['home2_middle']."
	</div>
	<div id='home2_left' class='col-md-4 col-md-pull-8 col-sm-5'>".
		$html['home2_left']."
	</div>
</div>
<div id='row3' class='row-fluid row'>
	<div id='home3_left' class='col-md-4 col-sm-5'>".
			$html['home3_left']."
	</div>
	<div id='home3_middle' class='col-md-4 col-sm-3'>".
		$html['home3_middle']."
	</div>
	<div id='home3_right' class='col-md-4 col-sm-4'>".
		$html['home3_right']."
	</div>
</div>";

require_once( dirname( __FILE__ ).'/index.php' ); 