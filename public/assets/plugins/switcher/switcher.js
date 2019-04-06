/*==============================
	STYLE SWITCHER SCRIPT INSTALLATION
 ===============================*/
 
(function($) {
	"use strict";

	 var colorCss =  $("#colors");
	 var color =  $(".color" );
	 var color1 =  $(".color1" );
	 var color2 =  $(".color2" );
	 var color3 =  $(".color3" );
	 var color4 =  $(".color4" );
	 var color5 =  $(".color5" );
	 var color6 =  $(".color6" );
	 var color7 =  $(".color7" );
	 var styleSwitcher =  $('#style-switcher');
	 var styleSwitcherh2A = $('#style-switcher h2 a');
	 var colorCssLiA = $('.colors li a');
	 
	color.click(function(){
		colorCss.attr("href", "assets/css/color.css" );
		return false;
	});

	color1.click(function(){
		colorCss.attr("href", "assets/css/color1.css" );
		return false;
	});

	color2.click(function(){
		colorCss.attr("href", "assets/css/color2.css" );
		return false;
	});

	color3.click(function(){
		colorCss.attr("href", "assets/css/color3.css" );
		return false;
	});

	color4.click(function(){
		$("#colors").attr("href", "assets/css/color4.css" );
		return false;
	});

	color5.click(function(){
		colorCss.attr("href", "assets/css/color5.css" );
		return false;
	});

	color6.click(function(){
		colorCss.attr("href", "assets/css/color6.css" );
		return false;
	});

	color7.click(function(){
		colorCss.attr("href", "assets/css/color7.css" );
		return false;
	});
	
	// Style Switcher	
	styleSwitcher.animate({
		left: '-220px'
	});

	styleSwitcherh2A.click(function(e){
		e.preventDefault();
		var div = $('#style-switcher');
		
		console.log(div.css('left'));
		if (div.css('left') === '-220px') {
			styleSwitcher.animate({
				left: '0px'
			}); 
		} else {
			styleSwitcher.animate({
				left: '-220px'
			});
		}
	});

	colorCssLiA.click(function(e){
		e.preventDefault();
		$(this).parent().parent().find('a').removeClass('active');
		$(this).addClass('active');
	});
	
    
})(jQuery);

