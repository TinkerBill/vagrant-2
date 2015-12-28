
/* 

v6.69 	16/9/14	Add speed and interval params to slideshow. JS moved to init_sldeshow.js

v6.29	28/7/13	Now adding .active to #slidethumb0 etc, rather than .darkner within it

v6.27	26/7/13	Moved to plugins/wf_widgets/scripts 

				Changed so that .darkner gets .active rather than hidden - allows more flexibility with css
					
		

Added login_link for carplus version

Will also need: 
	init_popups.js
	init_tweets.js
	jquery.tweet.js

*/




jQuery(document).ready( function($){
	/*
	// v3.39 widget hints when editing customfields
	$('#available_widgets').change(function() {
		alert($(this).val);
	});
	*/
	
	
	
	// REVEAL ///////////////////////////////////////////////////////////////
	
	$(".reveal_tail")
		.hide()
		.parent()
		.addClass('closed');
			
		// one of the reveal headings is clicked
		$(".reveal_head").click(function(e){
			var parent = $(this).parent(); // eg: div.reveal h3.reveal_head							
			e.stopPropagation();
			var is_closed = parent.hasClass('closed');
			
			var tail = parent.find('.reveal_tail');
			if(is_closed) {
				tail.slideDown(function(){
					parent.removeClass('closed');
				});	
			} else {
				tail.slideUp(function(){
					parent.addClass('closed');
				});
			}
			return false;
	});
	
	
});




/* removed v6.69


// SLIDESHOW ///////////////////////////////////////////

// slideshow
jQuery(function($) { // from dev.jonraasch.com/simple-jquery-slideshow/
	// "The $ prefix on the jQuery variables is not essential by any means, I just use it to denote 
	// whether the variable represents a jQuery object rather than a plain variable."
    var $slideshow = $('#slidemain');
    var $slides = [];
    var active = null;
	var lastactive = null; // now global so we can change it by clicking
	
	if(!$slideshow.length) { // effectively checks if #slideshow exists
		return;
	}
    // build the slides array from the children of the slideshow.  this will pull in any children, so adjust the scope if needed
	$slideshow.children().each(function(i) { // doesn't matter here if these children are <a>s, <img>s or <div>s ?
        var $thisSlide = $(this);
        if ( $thisSlide.hasClass('active') ) active = i;
        $slides.push( $thisSlide );
    });
    
    // if no active slide, take the first one
    if ( active === null ) {
		active = 0; //$slides.length - 1;
	}
    //$('#slidethumb0 .darkner').addClass('active'); // v6.27 was .hide()
	$('#slidethumb0').addClass('active'); // v6.29
	$slides[0].addClass('active');
	 
	
    function slideSwitch() {
        var $lastActive = $slides[active];
        $lastActive.addClass('last-active');
		lastactive = active; // originally this was a local variable
        active++;
		if ( active >= $slides.length ) {
			 active = 0;
		}
		
		//$('#slidethumb'+ active +' .darkner').addClass('active'); //.hide(); // ie: remove cover  v6.27
		//$('#slidethumb'+ lastactive +' .darkner').removeClass('active'); //.show(); // ie: darken it  v6.27
		$('#slidethumb'+ active).addClass('active'); // v6.29
		$('#slidethumb'+ lastactive).removeClass('active'); // v6.29
		
        var $nextActive = $slides[active];
        $nextActive.css({opacity: 0.0})
            .addClass('active')
            .animate({opacity: 1.0}, 1000, function() {
                $lastActive.removeClass('active last-active');
            });
    }

    // start the interval
	var playSlideshow =  setInterval( slideSwitch, 5000 );
   
	$('#slideshow').hover(function() {
		clearInterval(playSlideshow);
	},
	function() {
		playSlideshow =  setInterval( slideSwitch, 5000 );
	});
	
	
	$('#slideshow .darkner').click(function() {
		var cover_id = ($(this).parent().attr('id')); // eg: slidethumb0	
		var slidenum = cover_id.substring(10); // eg: 0  Should cope OK with double digit numbers
		//alert('Clicked: ' + slidenum);
		$slides[slidenum].addClass('active');
		//$(this).addClass('active'); // v6.27
		$(this).parent().addClass('active'); // v6.29
		$slides[active].removeClass('active last-active');
		//$('#slidethumb'+ active +' .darkner').removeClass('active'); //.show(); // ie: darken it  v6.27
		//$('#slidethumb'+ slidenum +' .darkner').addClass('active'); //.hide(); // ie: remove cover  v6.27
		$('#slidethumb'+ active).removeClass('active'); // v6.29
		$('#slidethumb'+ slidenum).addClass('active'); // v6.29
		active = slidenum;
	});


});

*/

