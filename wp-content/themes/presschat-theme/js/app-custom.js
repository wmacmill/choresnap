function apppCustomModal(event) {
	
	$ = jQuery;
	
	event.preventDefault();
	
	$('.gallery-modal-image').attr( 'src',  '' );

	var UpClasses   = 'slide-in-up-add ng-animate slide-in-up slide-in-up-add-active';
	var downClasses = 'slide-in-up-remove slide-in-up-remove-active';

	//get href of button that matches id of modal div
	var ImageURL = $(this).attr('href');

	// need to move .css to css file
	$('#gallery-modal').css('display', 'block').removeClass(downClasses).addClass(UpClasses);
	
	setTimeout(function() { 
		$('.gallery-modal-image').attr( 'src',  ImageURL );
	}, 300 );

}

jQuery(document).on('ready load_ajax_content_done', function($) {
	jQuery('.gallery-icon a').on( 'click', apppCustomModal );
});