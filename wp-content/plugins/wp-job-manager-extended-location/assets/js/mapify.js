(function( $ ) {

	$.fn.mapify = function( options ) {

		// Default settings
		var settings = $.extend({
			mapify			: this,
			mapHeight		: '200px',
			startGeoLat		: 40.712784,
			startGeoLng		: -74.005941,
			latInputId		: 'geo_lat',
			lngInputId		: 'geo_lng'
		}, options );

		return this.each(function( ) {

			var map,
				geocoder 		= new google.maps.Geocoder(),
				mapOptions 		= { center: new google.maps.LatLng( settings.startGeoLat, settings.startGeoLng ), zoom: 13, streetViewControl: false },
				marker,
				autoComplete	= this,
				infoWindow;

			var mapifyHandler = {

				init: function() {
					$( settings.mapify ).parent().closest( 'div, p, td' ).wrap( '<div class="mapified"></div>' )
						.after( '<div id="map-canvas" style="width: 100%; height: '+settings.mapHeight+';"></div><input type="hidden" name="'+settings.latInputId+'" id="'+settings.latInputId+'" value="'+settings.startGeoLat+'"><input type="hidden" name="'+settings.lngInputId+'" id="'+settings.lngInputId+'" value="'+settings.startGeoLng+'">' );

					infoWindow			= new google.maps.InfoWindow();
					map 				= new google.maps.Map( document.getElementById( 'map-canvas' ), mapOptions );
					autoCompleteField	= new google.maps.places.Autocomplete( jQuery( autoComplete )[0] );
					autoCompleteField.bindTo( 'bounds', map );
					marker 				= new google.maps.Marker({
						map				: map,
						draggable		: true,
						animation		: google.maps.Animation.DROP,
						anchorPoint		: new google.maps.Point(0, -29),
						position		: mapOptions.center
					});

					$( 'body' ).on( 'change', $( autoComplete ), function() {
						mapifyHandler.geoCode( $( autoComplete ).val() );
					});

					// Autocomplete change
					google.maps.event.addListener( autoCompleteField, 'place_changed', function() {
						mapifyHandler.autoCompleteChange();
					});

					// Marker change
					google.maps.event.addListener( marker, 'dragend', function( event ) {
						mapifyHandler.markerChange();
					});

					// TEMP hack to get the settings page correct.
					jQuery( '.nav-tab' ).on( 'click', function() {
						if ( map != undefined ) {
							google.maps.event.trigger( map, 'resize' );
							map.setCenter( { "lat" : parseFloat( $( '#' + settings.latInputId ).val() ), "lng" : parseFloat( $( '#' + settings.lngInputId ).val() ) } );
							marker.setPosition( { "lat" : parseFloat( $( '#' + settings.latInputId ).val() ), "lng" : parseFloat( $( '#' + settings.lngInputId ).val() ) } );
						}
					});

				},
				autoCompleteChange: function( event ) {
					var place = autoCompleteField.getPlace();
					mapifyHandler.updateLocation( place.geometry.location.lat(), place.geometry.location.lng() );
				},
				markerChange: function( position ) {
					mapifyHandler.updateLocation( marker.getPosition().lat(), marker.getPosition().lng() );
					mapifyHandler.reverseGeoCode( marker.getPosition().lat(), marker.getPosition().lng() );
				},
				setAddress: function( address ) {
					$( autoComplete ).val( address );
				},
				setMarker: function( latLng ) {
					marker.setVisible(false);
					marker.setPosition( latLng );
					marker.setVisible( true );
				},
				updateLocation: function( lat, lng ) {
					var latLng = new google.maps.LatLng( lat, lng );
					$( '#' + settings.latInputId ).val( lat );
					$( '#' + settings.lngInputId ).val( lng );
					map.panTo( latLng );
					mapifyHandler.setMarker( latLng );
				},
				geoCode: function( address ) {
					geocoder.geocode( { 'address': address }, function( results, status ) {
						if ( status == google.maps.GeocoderStatus.OK && results[0] ) {
							mapifyHandler.updateLocation( results[0].geometry.location.lat(), results[0].geometry.location.lng() );
						}
					});
				},
				reverseGeoCode: function( lat, lng ) {
					geocoder.geocode({ 'latLng': marker.getPosition() }, function( results, status ) {
						if ( status == google.maps.GeocoderStatus.OK && results[0] ) {
							mapifyHandler.setAddress( results[0].formatted_address );
						}
					});
				}
			};

			mapifyHandler.init();

		});

	};

}( jQuery ));
