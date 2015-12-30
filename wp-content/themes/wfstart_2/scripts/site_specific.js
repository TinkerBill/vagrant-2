// JavaScript Document
;(function ($, window, document, undefined) { // now have access to globals jQuery (as $) and window (as W)

/*
if(init_params.dataset.length == 0) {
	init_params.dataset = {}; // for some reason, wp_localize_script brings it in as an empty array!
}
*/
// brings in params:{ajax_url,group_array:{}} 



	$(function() {	
			   
		
		
		
		
		$('#navmain ul ul')
			.addClass('dropdown-menu')
			.find('li:has(ul)')
			.addClass('dropdown-submenu');
		$('#navmain .nav > li:has(ul)')
			// all the rest added v2.28 for bootstrap 3
			.addClass('dropdown')
			.find('> a')
			.addClass('dropdown-toggle')
			.attr("data-toggle","dropdown")
			.attr("aria-expanded","false")
			.attr("role","button")
			.append(" <span class='caret'></span>");
		//$('#navmain .dropdown-menu li:has(ul)').addClass('dropdown-submenu');
		
		
		$('#wp-admin-bar-toggle_debug').click(function() {
			var debug_box = $('.debug_widget');
			if(debug_box.is(':visible')) {
				debug_box.hide();
			} else {
				debug_box.show();
			}
			return;										   
		});
		
		
	});


})(jQuery, window, document);