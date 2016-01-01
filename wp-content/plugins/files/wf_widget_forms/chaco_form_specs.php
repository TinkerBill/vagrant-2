<?php 

function custom_validate($formname,$errors,$form_field_array) { 
		// If this function is defined before function return_form_elements($form_field_array) is called,
		// it can be used to perform additional checks on the user input
		//var_dump($_POST);
	if($formname == 'Friend of ChaCo') { //contact
		//var_dump($_POST['consent']);
		if(!isset($_POST['consent'])) {
			
			//$check = array('error_class' => ' error', 'feedback' => "Consent: this box needs to be checked.");	
			$errors['errors_html'] .= "<li>Consent: this box needs to be checked.</li> \n";
			$errors['count']++;
			
		}
	}
	return $errors;
}

$this->email_params = array(
	'sendname' => "The ChaCo website",
	'sendfrom' => 'info@chapeltowncohousing.org.uk',
	'user_email' => 'email',
	'sendto' => 'info@chapeltowncohousing.org.uk',//
	'subject' => "Form submission from the ChaCo website ".$formname. " form",
	'bccto' => 'bill.phelps@ntlworld.com',
	'envelope' => 'info@chapeltowncohousing.org.uk',
	'legend' => $formname, // default, which should prob be overwritten
	'end_html' => "\n<div id='fsnote'><p>* Required field</p></div>\n",
	'success_url' => 'http://www.chapeltowncohousing.org.uk/thank-you/',
	'fail_message' => "<p>Sorry. There was a problem sending your form data. Please use the email address on the Contact page.</p>"
);


/*
if($params['css']) {
	function custom_css() {
		global $params;
		d('$params',$params);
		wp_enqueue_style('chaco_form_css', plugins_url( 'files/wf_widget_forms/'.$params['css'])); 
	}
	add_action('wp_enqueue_scripts', 'custom_css');
}
*/





if ($formname == 'ChaCo Response Form') { //contact

	$this->email_params['legend'] = 'Contact details';	

	$this->form_elements = array(
	
	"text1"			=>"m,,<h3 class='text1'>A bit about you...</h3>",
	"firstname"		=>"i*,name(25),First name:",
	"surname"		=>"i*,name(25),Last name:",
	"postcode"		=>"i*,zip(9),Postcode:",
	"text2"			=>"m,,<p class='text2'>...so we know if you&rsquo;re in the area</p>",
	
	"text3"			=>"m,,<h3 class='text3'>How to contact you...</h3>",
	"email"			=>"i*,email(35),Email:",
	"text4"			=>"m,,<p class='text4'>The rest is optional...</p>",
	"telephone"		=>"i,telnumber(25),Phone:",
	"street"		=>"i,address(35),Street address:",
	"town"			=>"i,name(25),Town:",
	
	"text5"			=>"m,,<h3 class='text5'>Your response...</h3>",
	"mailing" 		=>"c,,Please keep me informed with occasional mailings (no more than four a year)",
	"happen" 		=>"c,,I&rsquo;d like to help make it happen",
	"great_idea" 	=>"c,,I think this is a great idea for Chapeltown",
	"move_in" 		=>"c,,I want to move in!",
	
	
	"cmessage" 		=>"t,txt(10000),Send us a message?",
	"capcha" 		=>"x*,val_capcha(),Security: Please type these characters,120,40,5,CarbonType/carbontype-webfont.ttf"
	);
	
}



if ($formname == 'Contact') { //contact

	$this->email_params['legend'] = 'Contact details';	

	$this->form_elements = array(
	
	"text1"			=>"m,,<h2>ChaCo Contact Form</h2><h3 class='text1'>A bit about you...</h3>",
	"firstname"		=>"i*,name(25),First name:",
	"surname"		=>"i*,name(25),Last name:",
	"email"			=>"i*,email(35),Email:",
	"postcode"		=>"i*,zip(9),Postcode:",
	"text2"			=>"m,,<p class='text2'>...so we know if you&rsquo;re in the area</p>",
	
	//"text3"			=>"m,,<h3 class='text3'>How to contact you...</h3>",
	
	//"text4"			=>"m,,<p class='text4'>The rest is optional...</p>",
	//"telephone"		=>"i,telnumber(25),Phone:",
	//"street"		=>"i,address(35),Street address:",
	//"town"			=>"i,name(25),Town:",
	
	"text5"			=>"m,,<h3 class='text5'>Your response...</h3><p>I&rsquo;m interested in finding out about:</p>",
	"befriending" 	=>"c,,becoming a Friend of ChaCo",
	"moving_in" 	=>"c,,moving into ChaCo",
	"supporting" 	=>"c,,supporting ChaCo",
	"investing" 	=>"c,,investing in ChaCo",
	
	
	"cmessage" 		=>"t,txt(10000),Send us a message?",
	"capcha" 		=>"x*,val_capcha(),Security: Please type these characters,120,40,5,CarbonType/carbontype-webfont.ttf"
	);
	
}

if ($formname == 'Friend of ChaCo') { //contact

	$this->email_params['legend'] = 'Contact details';	

	$this->form_elements = array(
	
	"text1"			=>"m,,<h2>Friends of ChaCo: Application form</h2>", //<p>As a Friend of ChaCo| you get information about our events| meals| activities and progress.</p><h3 class='text1'>A bit about you...</h3>",
	"firstname"		=>"i*,name(25),First name:",
	"surname"		=>"i*,name(25),Last name:",
	"postcode"		=>"i*,zip(9),Postcode:",
	"text2"			=>"m,,<p class='text2'>...so we know if you&rsquo;re in the area</p>",
	
	"text3"			=>"m,,<h3 class='text3'>How to contact you...</h3>",
	"email"			=>"i*,email(35),Email:",
	//"text4"			=>"m,,<p>(By giving us your email address; you are agreeing to us sending you occasional email updates &ndash; usually no more than one a month.)</p><p class='text4'>The rest is optional...</p>",
	"telephone"		=>"i,telnumber(25),Phone:",
	"street"		=>"i,address(35),Street address:",
	"town"			=>"i,name(25),Town:",
	
	"text5" 		=>"m,,<h3 class='text5'>Your response...</h3>",
		
	"interested_msg" 	=>"t,txt(10000),I&rsquo;m interested because:",
	"help_msg" 			=>"t,txt(10000),I might be able to help:",
	"consent" 		=>"c*,,I agree to ChaCo storing this data electronically and sending me occasional email updates &ndash; usually no more than one a month.",
	"capcha" 		=>"x*,val_capcha(),Security: Please type these characters,120,40,5,CarbonType/carbontype-webfont.ttf"
	);
	
}

// We will use the contact details you have provided to keep you up to date on our campaigns and how you can help us.

if ($formname == 'ChaCo Loanstock Form') { 

	$this->email_params['legend'] = 'Contact details';


	$this->form_elements = array(
	
	"text1"			=>"m,,<h3 class='text1'>I want a share in this vision...</h3>",
	"amount" 		=>"i*,intminmax(500|),Loan offer: &pound;",
	"text2"			=>"m,,<p class='text2'>minimum &pound;500</p>",
	"interest" 		=>"s*,selectmin(1),Interest:,Please select,0%,0.5%,1%,1.5%,2%,2.5%,3%,3.5%,4%",
	"text2a"		=>"m,,<p class='text2a'>As we are Cooperative seeking to develop affordable housing we offer this loanstock as an opportunity for 
						those who share our vision to help us make it a reality rather than as an opportunity to make a financial return on an 
						investment &ndash; choosing a lower interest rate offers us more support.</p>",
	"repaid_on" 	=>"s*,selectmin(1),Full repayment:,Please select,31 Dec 2016,31 Dec 2017,31 Dec 2018,31 Dec 2020,31 Dec 2023,31 Dec 2028,31 Dec 2033",
	"text3"			=>"m,,<h3 class='text3'>Contact details...</h3>",
	"firstname"		=>"i*,name(25),First name:",
	"surname"		=>"i*,name(25),Last name:",
	"street"		=>"i*,address(35),Street address:",
	"town"			=>"i*,name(25),Town:",
	"postcode"		=>"i*,zip(9),Postcode:",
	
	"email"			=>"i*,email(35),Email:",
	"telephone"		=>"i,telnumber(25),Phone:",
	
	"cmessage" 		=>"t,txt(10000),Send us a message?",
	"capcha" 		=>"x*,val_capcha(),Security: Please type these characters,120,40,5,CarbonType/carbontype-webfont.ttf"
	);
	
}
