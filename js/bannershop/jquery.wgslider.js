var $a=$.noConflict();
$a(document).ready(function() {
	//Display sliderControl
	$a(".sliderControl").show();
	$a(".sliderControl a:first").addClass("active");
	
	var imageWidth = $a(".viewer").width();
 	var imageSum = $a(".slideimages img").size();
	var imageSlideWidth = imageWidth  * imageSum;
 	
 	$a(".slideimages").css({'width' : imageSlideWidth});
	
	//sliderControl  and Slider Function
	rotate = function(){
    var triggerID = $active.attr("rel") - 1;
    var slideimagesPosition = triggerID * imageWidth;

    $a(".sliderControl a").removeClass('active');
    $active.addClass('active');

    //Slider Animation
    $a(".slideimages").animate({
        left: -slideimagesPosition
    }, 500 );

	}; 
	
	////Rotation and Time
	rotateSwitch = function(){
		play = setInterval(function(){ 
			$active = $a('.sliderControl a.active').next(); 
			if ( $active.length === 0) { //When the end is reached
				$active = $a('.sliderControl a:first');
			}
			rotate();
		}, 4000); // 4 seconds
	};
	rotateSwitch();
	
	////Click
	$a(".sliderControl a").click(function() {
		$active = $a(this); 
		clearInterval(play);
		rotate();
		rotateSwitch();
		return false;
	});
	
	////Hover
	$a(".slideimages a").hover(function() {
		clearInterval(play);
	}, function() {
		rotateSwitch();
	});	
	
});