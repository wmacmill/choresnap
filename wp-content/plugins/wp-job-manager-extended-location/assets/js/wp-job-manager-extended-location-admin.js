
jQuery( '#_job_location' ).geo_tag_text({ latOutput : '#wpjmel_geo_lat', lngOutput : '#wpjmel_geo_long' });
jQuery( '#_job_location' ).mapify({ startGeoLat : wpjmel.listing_lat, startGeoLng : wpjmel.listing_long });


jQuery( '#setting-wpjmel_map_start_location' ).geo_tag_text({ latOutput : '#wpjmel_start_geo_lat', lngOutput : '#wpjmel_start_geo_long' });
jQuery( '#setting-wpjmel_map_start_location' ).mapify({ startGeoLat : wpjmel.start_geo_lat, startGeoLng : wpjmel.start_geo_long, latInputId : 'wpjmel_start_geo_lat', lngInputId : 'wpjmel_start_geo_long' });
