/**
 * Functionality to add a GEO tag to a textbox
 */
(function ( $ ) {

	$.fn.extend({
		geo_tag_text: function( options ) {

			var geocoder;
			var position;
			var address;

			// Set default options
			var settings = $.extend({
				addressOutput 	: this,
				latOutput 		: '.geo_lat',
				lngOutput 		: '.geo_lng',
			}, options );

			// Get GEO on click
			$( 'body' ).on( 'click', '.geo-tag .location', function() {
				if ( navigator.geolocation ) {
					navigator.geolocation.getCurrentPosition( function ( geo_position ) { // On success
						position = geo_position;

						$( settings.latOutput ).val( position.coords.latitude );
						$( settings.lngOutput ).val( position.coords.longitude );

						geocoder = new google.maps.Geocoder();
						geocoder.geocode({ 'latLng': new google.maps.LatLng( position.coords.latitude, position.coords.longitude )}, function ( results, status ) {
							if ( status == google.maps.GeocoderStatus.OK ) {
								$( settings.addressOutput ).val( results[0].formatted_address );
								$( settings.addressOutput ).change();
								$( '.geo-tag .location' ).toggleClass( 'loading' );
							}
						});
				    });

				}
			});

			// Loading icons
			$( 'body' ).on( 'click', '.geo-tag .location', function() {
				$( this ).toggleClass( 'loading' );
			});

			$( this ).wrap( '<span class="geo-tag"></span>' ).before( '<i class="location"></i>' );

		}
	});

}( jQuery ));
