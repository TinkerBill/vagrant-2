<?php


//v5.15	8/5/13	Now returns the validation function name as well as feedback - so we can do client-side checks
global $user_roles;
$user_roles = $_GET['userRoles']; // v6.5 
$active_plugins = $_GET['activePlugins']; // v6.5 so we can avoid setting up specs for non-active pluginns

$val = $_GET['val'];
$name = $_GET['name'];
$widget = $_GET['widget'];

require_once( dirname( __FILE__ ) . '/widget_default_specs.php' );
require_once( dirname(dirname( __FILE__ )) . '/files/wf_widgets/widget_config.php' ); // v5.2
//require_once(THEME_FOLDER_PATH . '/wf_validation.php'); // THEME_FOLDER_PATH should be defined in widget_config.php
require_once( dirname(dirname( __FILE__ )) . '/wf_library/wf_validation.php' );// v6.2
 
$param_vals = $current_specs[$widget]['params'][$name]; // default, reqd, validate for this particular widget param
$fname = get_fname($name, $param_vals);
$feedback = false;
if(function_exists($fname)) {
	$feedback = $fname($val);
}

echo $_GET['callback'] . '({"feedback":' . json_encode($feedback) . ', "validation":' . json_encode($fname) . '});'; //v5.15

