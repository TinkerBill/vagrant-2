<?php 
/*
v6.47	12/9/13	Added check for $_SESSION["capcha_width"] set.
*/

// based on script from www.webcheatsheet.com/PHP/create_captcha_protection.php


//Start the session so we can store what the security code actually is
session_start();

if(!isset($_SESSION["capcha_width"])) { // v6.47 to minimise error log
	error_log('WF bug trap: capcha.php, $_SESSION["capcha_width"] not set, REMOTE_ADDR = '.$_SERVER["REMOTE_ADDR"]);
	exit(); 
}

$width = 0+$_SESSION["capcha_width"];
$height = 0+$_SESSION["capcha_height"];
$character_count = 0+$_SESSION["capcha_character_count"];
$font = '../files/wf_widget_forms/'.$_SESSION["capcha_font"];

//Send a generated image to the browser 
create_image($width,$height,$character_count,$font); 
//create_image(120,40,$character_count,'fonts/CarbonType/carbontype-webfont.ttf'); 
exit(); 

function create_image($width,$height,$character_count,$font) {
    //Let's generate a totally random string using md5 
    $md5_hash = md5(rand(0,999)); // 32-character hexadecimal number
    //We don't need a 32 character long string so we trim it down to 5 
    $security_code = substr($md5_hash, 15, $character_count); 

    //Set the session to store the security code
    $_SESSION["security_code"] = $security_code;

    //Create the image resource 
    $image = ImageCreate($width, $height);  

    //We are making three colors, white, black and gray 
    $white = ImageColorAllocate($image, 250, 250, 250); 
    $black = ImageColorAllocate($image, 0, 0, 0); 
    $grey = ImageColorAllocate($image, 204, 204, 204); 

    //Make the background black 
    ImageFill($image, 0, 0, $black);
	
	//$font = 'CarbonType/carbontype-webfont.ttf'; 

    //Add randomly generated text string in white to the image
	imagettftext($image, 22, 0, 9, 30, $white, $font, $security_code);//size,angle,x,y are numbers

    //Throw in some lines to make it a little bit harder for any bots to break 
    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
    imageline($image, $width/7, $height/2, $width, 0, $grey); 
    imageline($image, $width/5, $height, $width/10, 0, $grey); 
    imageline($image, $width/4, $height, $width/2, 0, $grey);
	
	//throw in curved lines
	$T1 = 20;
	$T2 = 15;
	
	//calculate x-value and y-value point by point
	$points = array();
	for ($i=0; $i<1000; $i=$i+1)
	{
		//define curve's function
		$x = 60*cos($i/$T1); //define x-value
		$y = 19*sin($i/$T2);//define y-value
		
		//move the coordinate, append a point's x-value and y-value
		$points[] = 60+$x; //x-value
		$points[] = 20-$y;  //y-value
	}
	
	//count points
	$totalPoints = count($points)/2;
	
	
	/** drawing points one by one, notice if there 
	 * are 10 points, we need to draw 9 lines: 
	 * 1) point 0 to 1; 
	 * 2) point 1 to 2;
	 * ...
	 * ...
	 * 9) point 8 to 9; 
	 */
	for ($i=0; $i<$totalPoints-1; $i++)
	{
		imageLine($image, $points[2*$i], $points[1+2*$i], $points[2+2*$i], $points[3+2*$i], $grey);    
	} 

 
    //Tell the browser what kind of file is come in 
    header("Content-Type: image/png"); 

    //Output the newly created image in jpeg format 
    Imagepng($image); 
    
    //Free up resources
    ImageDestroy($image); 
} 
