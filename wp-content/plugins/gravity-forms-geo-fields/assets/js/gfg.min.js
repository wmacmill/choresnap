jQuery(document).bind('gform_post_render', function(){

	/**
	 * Clear all location fields except input address field
	 * @return {[type]} [description]
	 */
	function ggfClearAllFields() {
		jQuery('#ggf-text-fields-wrapper [id^="ggf-field"], [class*="ggf-cf-"] input[type="text"], [class*="ggf-cf-"] input[type="hidden"]').val('');
		jQuery('[class*="ggf-cf-"] input[type="radio"], [class*="ggf-cf-"] input[type="checkbox"]').prop( 'checked', false );
	}

	//set address field for gForm address field if exists
	if ( jQuery('.gform-address-field')[0] ) {
				
		jQuery('.gform-address-field').each(function() {
	
			var gfAddress = jQuery(this).attr('id').replace('field', 'input');
			var lfClass = '';
			var aField  = '';
			
			if ( jQuery(this).hasClass('ggf-autocomplete') ) {

				autocompleteWrapper = jQuery(this).find('.ggf-advanced-address-autocomplete-wrapper');

				tabindex = jQuery(this).find('.ginput_complex input[type=text]').not('.ggf-advanced-address-autocomplete').attr('tabindex');

				autocompleteWrapper.detach().prependTo(jQuery(this).find('.ginput_complex')).show().find('input[type=text]').attr('tabindex', tabindex);
			}
			
			if ( jQuery(this).hasClass( 'locator-fill') ) {
				lfClass += ' locator-fill';
				aField = 'ggf-cf';
			}
			
			if ( jQuery(this).hasClass( 'map-autofill') ) {
				lfClass += ' map-autofill';
				aField = 'ggf-cf';
			}
			
			if ( jQuery(this).hasClass( 'ggf-advanced-geocode-true') ) {
				aField = 'ggf-field ggf-field';
			}
			
			jQuery(this).find( jQuery("[id$=_1_container]") ).addClass(aField+'-street ggf-advanced-address-cf-field '+lfClass);
			jQuery(this).find( jQuery("[id$=_2_container]") ).addClass(aField+'-apt ggf-advanced-address-cf-field '+lfClass);
			jQuery(this).find( jQuery("[id$=_3_container]") ).addClass(aField+'-city ggf-advanced-address-cf-field '+lfClass);
			jQuery(this).find( jQuery("[id$=_4_container]") ).addClass(aField+'-state ggf-advanced-address-cf-field '+lfClass);
			jQuery(this).find( jQuery("[id$=_5_container]") ).addClass(aField+'-zipcode ggf-advanced-address-cf-field '+lfClass);
			jQuery(this).find( jQuery("[id$=_6_container]") ).addClass(aField+'-country ggf-advanced-address-cf-field '+aField+'-country_long '+lfClass);	
		});
	}
	
	//function ggf_init( ggfSettings ) {
	//hide submit button if needed. when using locator button
	if ( ggfSettings['locator_hide_submit'] == 1 ) {
		jQuery('#gform_submit_button_'+ggfSettings['id']).hide();
	}

	//set the autocomplete field
	ggfAutocomplete = '.ggf-autocomplete input[type="text"]';
	
	//clear location fields when address changes
	jQuery('.ggf-field input[type="text"]').on("input", function() {
		ggfClearAllFields();
		jQuery('#ggf-update-location').addClass('update');
	});

	//check if map exists in the form and if so trigger it
	if( jQuery('#ggf-map').length ) {

		var latlng = new google.maps.LatLng(mapArgs.latitude,mapArgs.longitude);
	
		var options = {
			zoom: parseInt(mapArgs.zoom_level),
			center: latlng,
			mapTypeId: google.maps.MapTypeId[mapArgs.map_type]
		};
	
		// create the map
		ggfMap = new google.maps.Map(document.getElementById("ggf-map"), options);
	
		// the geocoder object allows us to do latlng lookup based on address
		geocoder = new google.maps.Geocoder();
	
		ggfMarker = new google.maps.Marker({
			position:latlng,
			map: ggfMap,
			draggable: true
		});
		
		//when dragging the marker on the map
		google.maps.event.addListener( ggfMarker, 'dragend', function(evt){
			
			jQuery('#ggf-update-location').removeClass('update');
			jQuery('#ggf-update-location').addClass('mapUpdating');

			//update coordinates fields
			jQuery("#ggf-field-lat").val( evt.latLng.lat() );
			jQuery("#ggf-field-lng").val( evt.latLng.lng() );
			jQuery(".ggf-cf-lat input").val( evt.latLng.lat() ).trigger('change');
			jQuery(".ggf-cf-lng input").val( evt.latLng.lng() ).trigger('change');

			//get the rest of the address fields
			returnAddress( evt.latLng.lat(), evt.latLng.lng(), false );  
		});
	}
	
	locatorID = false;
	
	//run autolocator on page load if needed
	if ( ggfSettings['auto_locator']['use'] == 1 && jQuery('#ggf-autolocator').val() != 'located' ) {
		locatorID = 'pageLoad';

		jQuery('#ggf-update-location').removeClass('update');
        jQuery('#ggf-update-location').addClass('autolocating');
        
        //add value to tell the plugin that autolocator already happened once
        jQuery('#ggf-autolocator').val('located');
        
		getLocationBP();
	}
	
    //locator button clicked 
    jQuery('.ggf-locator-button').click(function() {
    	locatorID = jQuery(this).attr('locator-id');
    	jQuery('#ggf-update-location').removeClass('update');
        jQuery('#ggf-update-location').addClass('autolocating');
    	jQuery(".ggf-locator-spinner-wrapper").show();
  		getLocationBP();
  	}); 
  	
    //get current location
    function getLocationBP() {
    	//if browser supported
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition( showPosition, showError, {timeout:10000} );
		} else {
   	 		alert("Geolocation is not supported by this browser.");
   	 		jQuery(".ggf-locator-spinner-wrapper").hide();
   	 		jQuery('#ggf-update-location').removeClass('autolocating');
   		}
		
	}
    
    //show results of current location
	function showPosition(position) {	

		if ( ggfSettings['fields'][locatorID]['locator_found_message_use'] != undefined && ggfSettings['fields'][locatorID]['locator_found_message_use'] == 1 ) {
			alert(ggfSettings['fields'][locatorID]['locator_found_message']);
		}
   		
   		console.log(position);
   		//update coordinates fields
		jQuery("#ggf-field-lat").val( position.coords.latitude );
		jQuery("#ggf-field-lng").val( position.coords.longitude );
		jQuery(".ggf-cf-lat input").val( position.coords.latitude ).trigger('change');
		jQuery(".ggf-cf-lng input").val( position.coords.longitude ).trigger('change');

		//get the rest of the address fields
  		returnAddress( position.coords.latitude, position.coords.longitude, true );
  		jQuery(".ggf-locator-spinner-wrapper").hide();
  		
  		if ( ggfSettings['fields'][locatorID]['locator_autosubmit'] == 1 ) {
  			jQuery('#ggf-update-location').addClass('autosubmit');
  		}
	}

	//error message for locator button
	function showError(error) {
  		
		//if ( ggfSettings['fields'][locatorID]['locator_found_message_use'] == 1 ) {
			switch( error.code) {
	   	 		case error.PERMISSION_DENIED:
	   	 			if ( locatorID != 'pageLoad' ) {
	   	 				alert('User denied the request for Geolocation.');
	   	 			}
	     		break;
	   		 	case error.POSITION_UNAVAILABLE:
	   		   		alert("Location information is unavailable.");
	    	  	break;
	    		case error.TIMEOUT:

	      			alert("The request to get user location timed out.");
	     		break;
	    		case error.UNKNOWN_ERROR:
	      			alert("An unknown error occurred.");
	      		break;
			}
		//}
		jQuery(".ggf-locator-spinner-wrapper").hide();
		jQuery('#ggf-update-location').removeClass('autolocating');
	}
	
	//update map when using autocomplete field
	function update_map() {
		
		//check that map exists on the form
		if ( !jQuery('#ggf-map').length ) 
			return;
			
		var latLng = new google.maps.LatLng( jQuery('#ggf-field-lat').val(), jQuery('#ggf-field-lng').val() );
		
		ggfMarker.setMap(null);
		
		ggfMarker = new google.maps.Marker({
		    position: latLng,
		    map: ggfMap,
            draggable: true
		});
		ggfMap.setCenter(latLng);
		
		//when dragging the marker on the map
		google.maps.event.addListener( ggfMarker, 'dragend', function(evt){

			jQuery('#ggf-update-location').removeClass('update');
			jQuery('#ggf-update-location').addClass('mapUpdating');

			//update coordinates fields
			jQuery("#ggf-field-lat").val( evt.latLng.lat() );
			jQuery("#ggf-field-lng").val( evt.latLng.lng() );
			jQuery(".ggf-cf-lat input").val( evt.latLng.lat() ).trigger('change');
			jQuery(".ggf-cf-lng input").val( evt.latLng.lng() ).trigger('change');

			//get the rest of the address fields
			returnAddress( evt.latLng.lat(), evt.latLng.lng(), false );  
		});		
	}
					
	//trigger autocomplete
	function ggfAutocompleteInit() {
				
		//do it for each autocomplete field in the form
		jQuery('.ggf-autocomplete').each(function() {
				
			//prevent form submission on enter to be able to select an address by pressing enter key
			jQuery(this).keypress(function(e){
			    if ( e.which == 13 ) return false;
			});

			var thisField	  = jQuery(this);
			var fieldID 	  = jQuery(this).attr('id').split('_');
			fieldID 		  = fieldID[fieldID.length-1];
			var fieldSettings = ggfSettings['fields'][fieldID];
	        var faField 	  = ( jQuery(this).hasClass('ggf-full-address') || jQuery(this).hasClass('ggf-advanced-geocode-true')  ) ? true : false;    
	        
	        if ( jQuery(this).hasClass('gform-address-field') ) {
	        	var input = document.getElementById( jQuery(this).find('.ggf-advanced-address-autocomplete').attr('id') );
	        } else {
	        	var input = document.getElementById( jQuery(this).find('div :input').attr('id') );
	        }

	        //if displaying results worldwide
	        if ( fieldSettings['restrictions'] == false ) {
		        var options = {
		        };
		    //otherwise restrict to single country
	        } else {
	        	var options = {
		        		componentRestrictions: { country: fieldSettings['restrictions'] }
		        };
	        }
	
	        var autocomplete = new google.maps.places.Autocomplete(input, options);
	        
	        google.maps.event.addListener(autocomplete, 'place_changed', function(e) {

	        	var place = autocomplete.getPlace();
	
				if (!place.geometry) {
					return;
				}
	
				//dynamically trigger change event when choice was selected
				jQuery(input).trigger('change');
				
				//if we updating hidden fields of location
				if ( faField == true ) {
					
                    jQuery('#ggf-update-location').removeClass('update');	
                    var item = autocomplete.getPlace();
                    breakAddress(item);
                }
				
	        	// update map if needed
				if ( fieldSettings['update_map'] == 1 ) {
					
					//return if no map exists
					if ( !jQuery('#ggf-map').length ) return;
					
					if (place.geometry.viewport) {
						ggfMap.fitBounds(place.geometry.viewport);
					} else {
						ggfMap.setCenter(place.geometry.location);
					}
					
					ggfMarker.setPosition(place.geometry.location);
					ggfMarker.setVisible(true);
				}
				
				if ( input.className == 'ggf-advanced-address-autocomplete' ) {
		
					address = place.address_components;
					thisField.find('.ggf-advanced-address-cf-field input').val('');
					
					var street_number = false;
					
					for ( x in address ) {
						
						//update street_number fields
						if ( address[x].types == 'street_number' ) {

							street_number = address[x].long_name;

							thisField.find('.ggf-cf-street_number input, .ggf-field-street_number input').val(street_number).trigger('change');
						}
						
						//update street_name and street fields
						if ( address[x].types == 'route' ) {

							street_name = address[x].long_name;  

							thisField.find('.ggf-cf-street_name input, .ggf-field-street_name input').val(street_name).trigger('change');
							
							if ( street_number != false ) {
								
								street = street_number + ' ' + street_name;

								thisField.find('.ggf-cf-street input, .ggf-field-street input').val(street).trigger('change');
							
							} else {
								thisField.find('.ggf-cf-street input, .ggf-field-street input').val(street_name).trigger('change');
							}
						}
				
						//get city
						if (address[x].types == 'locality,political') {
			            	
				            city = address[x].long_name;

				            thisField.find('.ggf-cf-city input, .ggf-field-city input').val(city).trigger('change');
			            } 
						
						//get state
						if ( address[x].types == 'administrative_area_level_1,political' ) {
							
			                state = address[x].short_name;
			                state_long = address[x].long_name;
			                
			                //update hidden and custom location fields
			                thisField.find('.ggf-cf-state input, .ggf-field-state input').val(state).trigger('change');             
			                thisField.find('.ggf-cf-state option[value="'+state_long+'"], .ggf-field-state option[value="'+state_long+'"]').attr("selected","selected");
			                thisField.find('.ggf-cf-state_long option[value="'+state_long+'"], .ggf-field-state_long option[value="'+state_long+'"]').attr("selected","selected");		       
			            } 

						//get zipcode
			            if (address[x].types == 'postal_code') {
			            	
			                zipcode = address[x].long_name;
			                
			                thisField.find('.ggf-cf-zipcode input, .ggf-field-zipcode input').val(zipcode).trigger('change');			                			                
			            } 

			            //get country
			            if (address[x].types == 'country,political') {
			            	
			                country = address[x].short_name;
			                country_long = address[x].long_name;
			                
			                thisField.find('.ggf-cf-country input, .ggf-field-country input').val(country).trigger('change');
			                thisField.find('.ggf-cf-country option[value="'+country_long+'"], .ggf-field-country option[value="'+country_long+'"]').attr("selected","selected");
			                thisField.find('.ggf-cf-country_long option[value="'+country_long+'"], .ggf-field-country_long option[value="'+country_long+'"]').attr("selected","selected");
			             } 
			        }
				}
				
		    });
	        
		});

	}
	ggfAutocompleteInit();
	
	//reverse geocode coords to address
	function returnAddress( gotLat, gotLng, updateMap ) {
				
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(gotLat ,gotLng);
	
		//geocode lat/lng to address
		geocoder.geocode( {'latLng': latlng }, function(results, status) {
      		
			if (status == google.maps.GeocoderStatus.OK) {
                if ( results[0] ) {
                    breakAddress(results[0]);
                    if ( updateMap == true ) update_map();
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
   		});
	} 
	
	//address components
	function breakAddress(location) {
		
		fieldsIdNot    = '';	
		fieldsClassNot = '';

		//update fields with address from map
		if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {

			var tempLat = jQuery('#ggf-text-fields-wrapper #ggf-field-lat').val();
			var tempLng = jQuery('#ggf-text-fields-wrapper #ggf-field-lng').val();

			ggfClearAllFields();
			jQuery('.map-autofill input[type="text"], .map-autofill input[type="hidden"]').val('');

			jQuery('.ggf-cf-lat input[type="text"], .ggf-cf-lat input[type="hidden"], #ggf-field-lat').val(tempLat);
			jQuery('.ggf-cf-lng input[type="text"], .ggf-cf-lng input[type="hidden"], #ggf-field-lng').val(tempLng);

			jQuery('.map-autofill.ggf-full-address input[type="text"]').val(location.formatted_address);
			
		//update fields with values from auto locator
		} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) {
             
            var tempLat = jQuery('#ggf-text-fields-wrapper #ggf-field-lat').val();
			var tempLng = jQuery('#ggf-text-fields-wrapper #ggf-field-lng').val();

			ggfClearAllFields();
			jQuery('.locator-fill input[type="text"], .locator-fill input[type="hidden"]').val('');

			jQuery('.ggf-cf-lat input[type="text"], .ggf-cf-lat input[type="hidden"], #ggf-field-lat').val(tempLat);
			jQuery('.ggf-cf-lng input[type="text"], .ggf-cf-lng input[type="hidden"], #ggf-field-lng').val(tempLng);

			jQuery('.locator-fill.ggf-full-address input[type="text"]').val(location.formatted_address);

        } else {

        	//clear all geo fields
        	ggfClearAllFields();

			jQuery("#ggf-field-lat").val( location.geometry.location.lat() );
			jQuery("#ggf-field-lng").val( location.geometry.location.lng() );
			jQuery('.ggf-cf-lat input[type="text"], .ggf-cf-lat input[type="hidden"]').val( location.geometry.location.lat() ).trigger('change');
			jQuery('.ggf-cf-lng input[type="text"], .ggf-cf-lng input[type="hidden"]').val( location.geometry.location.lng() ).trigger('change');
        }
        
        //populate full address fields
        jQuery('#ggf-field-formatted_address').val(location.formatted_address);
		jQuery('.ggf-cf-formatted_address input[type="text"], .ggf-cf-formatted_address input[type="hidden"]').val(location.formatted_address);

		address = location.address_components;
		
		var street_number = false;
		
		//loop through the address components
		for ( x in address ) {

			//get street number
			if ( address[x].types == 'street_number' ) {
				
				street_number = address[x].long_name;
	  
	            jQuery('#ggf-field-street_number, .ggf-cf-street_number input[type="text"], .ggf-cf-street_number input[type="hidden"]' ).val(street_number).trigger('change');
	
	            //update fields with address from map
    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
    				
    				jQuery('.map-autofill.ggf-field-street_number input[type="text"], .map-autofill.ggf-field-street_number input[type="hidden"]').val(street_number).trigger('change');

    			//update fields with values from auto locator
    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
    				
    	            jQuery('.locator-fill.ggf-field-street_number input[type="text"], .locator-fill.ggf-field-street_number input[type="hidden"]').val(street_number).trigger('change');

    	        } 
			}
			
			//street field
			if ( address[x].types == 'route' ) {
				
				street_name = address[x].long_name;  
				
				//update street_name fields
				jQuery('#ggf-field-street_name, .ggf-cf-street_name input[type="text"], .ggf-cf-street_name input[type="hidden"]').val(street_name);
				
				 //update fields with address from map
    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
    				
    				jQuery('.map-autofill.ggf-field-street_name input[type="text"], .map-autofill.ggf-field-street_name input[type="hidden"]').val(street_name).trigger('change');

    			//update fields with values from auto locator
    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
    				
    	            jQuery('.locator-fill.ggf-field-street_name input[type="text"], .locator-fill.ggf-field-street_name input[type="hidden"]').val(street_name).trigger('change');
    	        } 

	    	    //udpate street ( number + name ) fields  if street_number exists
				if ( street_number != false ) {
					   	        
	    	        street = street_number + ' ' + street_name;

					jQuery('#ggf-field-street, .ggf-cf-street input[type="text"], .ggf-cf-street input[type="hidden"]').val(street);
					
					 //update fields with address from map
	    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
	    				
	    				jQuery('.map-autofill.ggf-field-street input[type="text"], .map-autofill.ggf-field-street input[type="hidden"]').val(street).trigger('change');	

	    			//update fields with values from auto locator
	    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
	    				
	    	            jQuery('.locator-fill.ggf-field-street input[type="text"], .locator-fill.ggf-field-street input[type="hidden"]').val(street).trigger('change');
	    	        } 

	    	    //udpate street ( name ) fields  if street_number not exists
				} else {

					jQuery('#ggf-field-street, .ggf-cf-street input[type="text"], .ggf-cf-street input[type="hidden"]').val(street_name);
					
					 //update fields with address from map
	    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
	    				
	    				jQuery('.map-autofill.ggf-field-street input[type="text"], .map-autofill.ggf-field-street input[type="hidden"]').val(street_name).trigger('change');	

	    			//update fields with values from auto locator
	    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
	    				
	    	            jQuery('.locator-fill.ggf-field-street input[type="text"], .locator-fill.ggf-field-street input[type="hidden"]').val(street_name).trigger('change');
	    	        } 
				}
			}
	
			//get city
			if (address[x].types == 'locality,political') {
            	
	            city = address[x].long_name;
	  
	            jQuery('#ggf-field-city, .ggf-cf-city input[type="text"], .ggf-cf-city input[type="hidden"]' ).val(city).trigger('change');
	
	            //update fields with address from map
    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
    				
    				jQuery('.map-autofill.ggf-field-city input[type="text"], .map-autofill.ggf-field-city input[type="hidden"]').val(city).trigger('change');

    			//update fields with values from auto locator
    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
    				
    	            jQuery('.locator-fill.ggf-field-city input[type="text"], .locator-fill.ggf-field-city input[type="hidden"]').val(city).trigger('change');
    	        } 
            } 
			
			//get state
			if ( address[x].types == 'administrative_area_level_1,political' ) {
				
                state = address[x].short_name;
                state_long = address[x].long_name;
                
                //update hidden and custom location fields
                jQuery('#ggf-field-state, .ggf-cf-state input[type="text"], .ggf-cf-state input[type="hidden"]').val(state).trigger('change');                    
                jQuery('#ggf-field-state_long, .ggf-cf-state_long input[type="text"], .ggf-cf-state_long input[type="hidden"]').val(state_long).trigger('change');

				//update select fields                         
                jQuery('.ggf-cf-state select option[value="'+state+'"]').attr("selected","selected");
                jQuery('.ggf-cf-state_long select option[value="'+state_long+'"]').attr("selected","selected");
                
                //update radio and checkbox fields
                jQuery('.ggf-cf-state ul.gfield_checkbox input[value="'+state+'"], .ggf-cf-state ul.gfield_radio input[value="'+state+'"]').prop('checked', true);
                jQuery('.ggf-cf-state_long ul.gfield_checkbox input[value="'+state_long+'"], .ggf-cf-state_long ul.gfield_radio input[value="'+state_long+'"]').prop('checked', true);
	
                //update fields with address from map
    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
    				
    				jQuery('.map-autofill.ggf-field-state input[type="text"]').val(state).trigger('change');
    				jQuery('.map-autofill.ggf-field-state option[value="'+state_long+'"]').attr("selected","selected");
    				jQuery('.map-autofill.ggf-field-state ul.gfield_radio input[value="'+state_long+'"]').prop('checked', true);

    			//update fields with values from auto locator
    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
    				
    	            jQuery('.locator-fill.ggf-field-state input[type="text"]').val(state).trigger('change');
    	            jQuery('.locator-fill.ggf-field-state option[value="'+state_long+'"]').attr("selected","selected");
    	            jQuery('.locator-autofill.ggf-field-state ul.gfield_radio input[value="'+state_long+'"]').prop('checked', true);
    	        } 
            } 

			//get zipcode
            if (address[x].types == 'postal_code') {
            	
                zipcode = address[x].long_name;
                
                jQuery('#ggf-field-zipcode, .ggf-cf-zipcode input[type="text"], .ggf-cf-zipcode input[type="hidden"]').val(zipcode).trigger('change');
                
                //update fields with address from map
    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {
    				
    				jQuery('.map-autofill.ggf-field-zipcode input[type="text"], .map-autofill.ggf-field-zipcode input[type="hidden"]').val(zipcode).trigger('change');	

    			//update fields with values from auto locator
    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
    				
    	            jQuery('.locator-fill.ggf-field-zipcode input[type="text"], .map-autofill.ggf-field-zipcode input[type="hidden"]').val(zipcode).trigger('change');
    	        } 
            } 

            //get country
            if (address[x].types == 'country,political') {
            	
                country = address[x].short_name;
                country_long = address[x].long_name;
                
                jQuery('#ggf-field-country, .ggf-cf-country input[type="text"], .ggf-cf-country input[type="hidden"]').val(country).trigger('change');
                jQuery('#ggf-field-country_long, .ggf-cf-country_long input[type="text"], .ggf-cf-country_long input[type="hidden"]').val(country_long).trigger('change');

                //update select fields                         
                jQuery('.ggf-cf-country select option[value="'+country+'"]').attr("selected","selected");
                jQuery('.ggf-cf-country_long select option[value="'+country_long+'"]').attr("selected","selected");
                
                //update radio and checkbox fields
                jQuery('.ggf-cf-country ul.gfield_checkbox input[value="'+country+'"], .ggf-cf-country ul.gfield_radio input[value="'+country+'"]').prop('checked', true);
                jQuery('.ggf-cf-country_long ul.gfield_checkbox input[value="'+country_long+'"], .ggf-cf-country_long ul.gfield_radio input[value="'+country_long+'"]').prop('checked', true);
                
                //update fields with address from map
    			if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {

    				jQuery('.map-autofill.ggf-field-country input[type="text"]').val(country).trigger('change');	
    				jQuery('.map-autofill.ggf-field-country option[value="'+country_long+'"]').attr("selected","selected");
    				jQuery('.map-autofill.ggf-field-country ul.gfield_radio input[value="'+country_long+'"]').prop('checked', true);

    			//update fields with values from auto locator
    			} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) { 
    				
    	            jQuery('.locator-fill.ggf-field-country input[type="text"]').val(country).trigger('change');
    	            jQuery('.locator-fill.ggf-field-country option[value="'+country_long+'"]').attr("selected","selected");
    	            jQuery('.map-autofill.ggf-field-country ul.gfield_radio input[value="'+country_long+'"]').prop('checked', true);
    	        } 
             } 
        }
		
		if ( jQuery('#ggf-update-location').hasClass('update') || jQuery('#ggf-update-location').hasClass('autosubmit') ) {
			
			jQuery('#ggf-update-location').removeClass(function() {
				setTimeout(function() {
					jQuery('#gform_'+ggfSettings['id'] ).submit();	
				}, 800);
			},'update');		
		}
		
		if ( jQuery('#ggf-update-location').hasClass('mapUpdating') ) {

			jQuery('#ggf-update-location').removeClass('mapUpdating');
			
		//update fields with values from auto locator
		} else if ( jQuery('#ggf-update-location').hasClass('autolocating') ) {
				
	        jQuery('#ggf-update-location').removeClass('autolocating');
	    }  
	}
	
	//convert address to lat/lng
	jQuery('#gform_submit_button_'+ggfSettings['id'] ).click(function(e) {
		
		//if there arent any fields to geocode submit the form
		if ( !jQuery('.ggf-geocode-field').length )
			return true;
		
		//check if address field need to be geocoded 
		if ( ( jQuery('#ggf-update-location').hasClass('update') || jQuery('#ggf-field-lat').val() == '' || jQuery('#ggf-field-lng').val() == '' ) ) {
			
			//we add the update class in order to later submit the form
			//we adding it here in case that we geocoded because no lat/lng exists
			jQuery('#ggf-update-location').addClass('update');
			
			e.preventDefault();		
			getLatLong();
		}	
	});
	
	var geoAddress = [];
	
	/* convert address to lat/long */
	function getLatLong() {
		
		geoAddress = [];
		if ( ggfSettings.address_fields.use == 1 ) {
			
			//make sure address field is not empty
			geoAddress.push(jQuery('.ggf-full-address input[type="text"]').val());
			geoAddress = geoAddress.join(' ');

		} else if ( ggfSettings.address_fields.use == 2 || ggfSettings.address_fields.use == 3 ) {
			geoAddress = [];
			
			jQuery.each(['street','city','state','zipcode','country'], function(index, value) {
				if ( jQuery('.ggf-field-'+value ).length ) {
					if ( jQuery.trim( jQuery('.ggf-field-'+value + ' input[type="text"]').val() ).length )  {
						geoAddress.push(jQuery('.ggf-field-'+value + ' input[type="text"]').val());
					}
				}
			});
			geoAddress = geoAddress.join(' ');
		}
		
		//do nothing if address field empty
		if ( geoAddress == undefined || geoAddress == null || !jQuery.trim(geoAddress).length ) {
     		jQuery('#gform_'+ggfSettings['id'] ).submit();	
     		return;
		}

		//if address found, geocod it
    	geocoder = new google.maps.Geocoder();
   	 	geocoder.geocode( { 'address': geoAddress }, function(results, status) {
    
   	 		if (status == google.maps.GeocoderStatus.OK) {     		
        		breakAddress(results[0]);       		       						
    		} else {
    			alert( 'Geocode was not successful for the following reason: ' + status + '. Please check the address you entered.' );     
    		}
    	});
	}  	
});