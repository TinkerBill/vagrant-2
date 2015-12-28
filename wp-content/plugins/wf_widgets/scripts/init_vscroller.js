



// scrollable
jQuery(document).ready(function($) {
	//alert(vscroller_params.interval + ",  " + vscroller_params.speed);
	// initialize scrollable with mousewheel support
	var vspeed = parseInt(vscroller_params.speed);
	var vinterval = parseInt(vscroller_params.interval);
	jQuery(".scrollable")
		.scrollable({
			vertical: true,
			circular: true,
			items: 'items',
			speed: vspeed,
			easing: 'linear'
		})
		.autoscroll({
			interval: vinterval,
			autopause: true // v6.62
		});	
		
		/*
		
		
		// net.tutsplus.com/tutorials/javascript-ajax/build-a-simple-jquery-news-ticker/
		var ticker = $("#ticker"); //cache the ticker
		ticker.children().filter("dt").each(function() { //wrap dt:dd pairs in divs
			var dt = $(this),
		    container = $("<div>");
			dt.next().appendTo(container);
			dt.prependTo(container);
		  	container.appendTo(ticker);
		});
				
		ticker.css("overflow", "hidden");//hide the scrollbar
		
		function animator(currentItem) {//animator function
			var distance = currentItem.height();//work out new anim duration
			duration = (distance + parseInt(currentItem.css("marginTop"))) / 0.025;
			currentItem.animate({ marginTop: -distance }, duration, "linear", function() {//animate the first child of the ticker
				currentItem.appendTo(currentItem.parent()).css("marginTop", 0);//move current item to the bottom
				animator(currentItem.parent().children(":first"));//recurse
		 	 }); 
		};
		
		animator(ticker.children(":first"));//start the ticker
		ticker.mouseenter(function() {//set mouseenter
			ticker.children().stop();//stop current animation
		});
		
		ticker.mouseleave(function() {//set mouseleave
			animator(ticker.children(":first"));//resume animation
		});
		
		*/
		
				
});
