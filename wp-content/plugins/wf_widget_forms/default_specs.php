<?php

$default_specs['form'] = array(
 
	'display_name' => 'Form widget',
	
	'class_name' => 'Form_widget',
	
	'inserts' => false,
	
	'description' => 'Lets you insert a form on the page.',

	'params' => array(
		'formname' => array(
			'default' => false,
			'reqd' => true,
			'validation' => 'text',
			'paramdesc' => 'The title of the form'
		),		
		
		'style' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'style',
			'paramdesc' => 'Pick one of the pre-defined styles for this widget.'
		),		
		'css' => array(
			'default' => false,
			'reqd' => false,
			'validation' => 'css',
			'paramdesc' => 'The name of a css file in &lsquo;files/wf_widget_forms/css&rsquo;.'
		),
		'logged_in' => array( // v6.49
			'default' => false,
			'reqd' => false,
			'validation' => '=show|hide',
			'paramdesc' => 'Make display of post(s) dependent on whether user is logged in.'
		)
		/*, 
		'message_label' => array( // Don't think there's much point in this.
			'default' => false,
			'reqd' => false,
			'validation' => 'form_message_label',
		)
		*/
	)
);

