<?php

// FUNCTIONS.PHP - CHILD THEME VERSION: Loads before parent theme functions.php

// to override styles in parent theme
//wordpress.org/support/topic/plugin-wordpress-seo-by-yoast-sitemap-autodiscovery-validation-problems
add_action('wp_enqueue_scripts', function() {
	wp_enqueue_style('child_css',get_stylesheet_directory_uri().'/css/child.css');
}, 20); 