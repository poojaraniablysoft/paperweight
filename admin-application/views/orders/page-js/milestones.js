$(document).ready(function() {
	//Set default open/close settings
	$('.togglecontent').hide(); //Hide/close all containers
	$('.togglehead:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container

	//On Click
	$('.togglehead').click(function() {
		if ($(this).next().is(':hidden')) { //If immediate next container is closed...
			$('.togglehead').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
			$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
		}
		
		return false; //Prevent the browser jump to the link anchor
	});
});