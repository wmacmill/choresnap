(function($, w){
	/**
	 * The object that encapsulates all plugin related data and functions
	 *
	 * @type {{textInputID: string, hiddenInputID: string}}
	 */
	var WooCommerceThankYou = {
		textInputID: '#product-thank-you-label',
		hiddenInputID: '#product-thank-you',

		autoCompleteOptions: {
			autoFocus: true,
			minLength: 2,

			/**
			 * Performs the AJAX request to the WP ajax endpoint to get the hints
			 *
			 * @param request
			 * @param response
			 */
			source: function(request, response){
				$.ajax({
					url: 'admin-ajax.php',
					data: {
						action: 'wc-thank-you-hint',
						search: request.term
					},
					method: "post",
			        dataType: "json",
					success: function(data){
						if(typeof data.success !== "undefined"){
							response(data.data);
						} else {
							response({});
						}
					},
					error: function(){
						response({});
					}
		        });
			},

			/**
			 * Select callback for setting the value into the right input field (the hidden one)
			 *
			 * @param event
			 * @param ui
			 */
			select: function(event, ui){
				var item = ui.item;

				// The hidden field
				$(WooCommerceThankYou.hiddenInputID).val(item.value);

				// The display field
				$(WooCommerceThankYou.textInputID).val(item.label);

				event.preventDefault();
			},

			/**
			 * Select callback for setting the value into the right input field (the hidden one)
			 *
			 * @param event
			 * @param ui
			 */
			focus: function(event, ui){
				event.preventDefault();
			}
		}
	};

	/**
	 * Empties the value of the hidden input field (that is only filled when used with autocomplete)
	 */
	WooCommerceThankYou.emptyHiddenOnChange = function(){
		var trimmedLabelFieldValue = $(WooCommerceThankYou.textInputID).val().trim();

		if(trimmedLabelFieldValue.indexOf('http') === 0 || trimmedLabelFieldValue === ""){
			$(WooCommerceThankYou.hiddenInputID).val('');
		}
	};

	/**
	 * Function that sets up all necessary hooks
	 */
	WooCommerceThankYou.init = function(){
		$(WooCommerceThankYou.textInputID)
			.change(WooCommerceThankYou.emptyHiddenOnChange)
			.autocomplete(WooCommerceThankYou.autoCompleteOptions);
	};

	// Init onload
	$(function(){
		WooCommerceThankYou.init();
	});
})(window.jQuery, window);