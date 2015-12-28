<?php

$active_plugins = $_GET['activePlugins']; // v6.3 so we can avoid setting up specs for non-active pluginns

require_once( dirname( __FILE__ ) . '/widget_default_specs.php' );
require_once( dirname(dirname( __FILE__ )) . '/files/wf_widgets/widget_config.php' ); // v5.2

$json_current_specs = $current_specs; //v6.5  - because this change only applies to json
foreach($json_current_specs as $widget => $specs) { // all because "default" is a reserved word in Javascript
	foreach($specs['params'] as $param => $param_vals) {
		$json_current_specs[$widget]['params'][$param]['dfault'] = $param_vals['default'];
		unset($json_current_specs[$widget]['params'][$param]['default']);
	}
}

echo $_GET['callback'] . '('.json_encode($json_current_specs).');';
	 
