/*
v6.70 	17/9/14	Slideshow widget: init_slideshow.js - exit from slideshow if just one slide. Allows us to set up
				static pic with caption as in slideshow. See frontpage of L4C.

*/


jQuery(document).ready(function($) {
	var speed = parseInt(slideshow_params.speed);
	var interval = parseInt(slideshow_params.interval);
	
	
    var $slideshow = $('#slidemain');
    var $slides = [];
    var active = null;
	var lastactive = null; // now global so we can change it by clicking
	
	if(!$slideshow.length || $slideshow.children().length == 1) { // effectively checks if #slideshow exists // v6.70 - or just one slide
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
		
		$('#slidethumb'+ active).addClass('active'); // v6.29
		$('#slidethumb'+ lastactive).removeClass('active'); // v6.29
		
        var $nextActive = $slides[active];
        $nextActive.css({opacity: 0.0})
            .addClass('active')
            .animate({opacity: 1.0}, speed, function() {
                $lastActive.removeClass('active last-active');
            });
    }

    // start the interval
	var playSlideshow =  setInterval( slideSwitch, interval );
   
	$('#slideshow').hover(function() {
		clearInterval(playSlideshow);
	},
	function() {
		playSlideshow =  setInterval( slideSwitch, interval );
	});
	
	
	$('#slideshow .darkner').click(function() {
		var cover_id = ($(this).parent().attr('id')); // eg: slidethumb0	
		var slidenum = cover_id.substring(10); // eg: 0  Should cope OK with double digit numbers
		$slides[slidenum].addClass('active');
		$(this).parent().addClass('active'); // v6.29
		$slides[active].removeClass('active last-active');
		$('#slidethumb'+ active).removeClass('active'); // v6.29
		$('#slidethumb'+ slidenum).addClass('active'); // v6.29
		active = slidenum;
	});


				
});
