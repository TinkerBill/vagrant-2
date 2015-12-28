//alert(popup_params.start_height);
jQuery(document).ready(function($){
	
	//alert(vscroller_params.interval + ",  " + vscroller_params.speed);
	//var vspeed = vscroller_params.speed;
	// Slide up the cover panel when moused-over
		$(".popup_widget").mouseover(function() {
			$(this).find('.sliding_bit').animate({"top": 0}, 300, 'swing');
		});
		$(".popup_widget").mouseleave(function() {
			$(this).find('.sliding_bit').animate({"top": popup_params.start_height}, 300, 'swing'); // 130
		});
		
		/*
		// initialize scrollable with mousewheel support
		$(".scrollable")
		.scrollable({
			vertical: true,
			circular: true,
			speed: 0 + vspeed, 
			interval: vscroller_params.interval
		//}).autoscroll({
			//interval: vscroller_params.interval
		});	
		*/
		/*
		// initialize scrollable with mousewheel support
		$(".scrollable")
			.scrollable({
				vertical: true,
				circular: true,
				speed: parseInt(vscroller_params.speed)
			})
			.autoscroll({
				interval: 0
		});	
		*/		
	
});


