jQuery( document ).ready( function( $ ) {
	if ( '1' == wpjmel.enable_map ) {

		var geo_lat = ( typeof wpjmel.list_geo_lat != undefined && '' != wpjmel.list_geo_lat ) ? wpjmel.list_geo_lat : wpjmel.start_geo_lat;
		var geo_long = ( typeof wpjmel.list_geo_long != undefined && '' != wpjmel.list_geo_lat ) ? wpjmel.list_geo_long : wpjmel.start_geo_long;

		// Add a geo locator to a textbox
		jQuery( '#job_location' ).geo_tag_text({ latOutput : '#wpjmel_geo_lat', lngOutput : '#wpjmel_geo_long' });
		jQuery( '#job_location' ).mapify({ startGeoLat : geo_lat, startGeoLng : geo_long });
	}

	// Don't set the user location when it already has a value
	if ( $( '#search_location' ).val() != '' ) {
		return;
	}

	var user_location = wpjmel.user_location;
	var input_location = undefined != user_location.city ? user_location.city : '';

	if ( '' == input_location ) {
		input_location = undefined != user_location.region ? user_location.region : '';
	}

	$( '#search_location' ).val( input_location );

});
