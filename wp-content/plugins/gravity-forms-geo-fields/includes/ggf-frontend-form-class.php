<?php
/**
 * GFG_Form_Functions class
 * 
 * The class responsible for the modification of the form in the front-end.
 * 
 * @author FitoussiEyal
 *
 */
class GFG_Form_Functions {

	/**
	 * GGF Settings
	 *
	 * @since 	2.0
	 * @var 	Array
	 */
	public static $ggf_settings = array();
	
	/**
	 * GGF Geocoder Usage
	 *
	 * @since 	2.0
	 * @var 	String
	 */
	public static $ggf_usage = false;
	
	/**
	 * Post ID of the post being updated
	 * When using Gravity Forms Update post plugin
	 *
	 * @since 	2.0
	 * @var 	String
	 */
	public static $update_post_id = false;
	
	/**
	 * GGF Location data attached to a post
	 * Being saved in the hidden custom field _ggf_location_fields
	 *
	 * @since 	2.0
	 * @var 	Array
	 */
	public static $ggf_post_location_data = false;
	
	/**
	 * GGF address Custom fields set in Form Settings page
	 *
	 * @since 	2.0
	 * @var 	Array
	 */
	public static $ggf_address_custom_fields = array();
	
	
	/**
	 * __constructor
	 */
	function __construct() {
	
		//Gravity Forms Update Post plugin
		add_filter( 'gform_update_post/setup_form', array( $this, 'get_updated_post_id' ), 10 );
		
		//Modify the form before it is being displayed
		add_filter( 'gform_pre_render', array( $this, 'render_form' ) );
		
		//Append GGF hidden location fields to the form
		add_filter( 'gform_form_tag', array( $this, 'form_tag' ), 2, 10 );
		
		//create GGF custom fields
		add_filter( 'gform_field_input', array( $this, 'extra_fields' ), 23, 5 );

		add_filter( 'gform_field_content', array( $this, 'address_field' ), 10, 5 );
	}

	/**
	 * Update post ID
	 * 
	 * Get the post ID of the post being updated when 
	 * updating form using Gravity Form Update Post plugin
	 * @param unknown_type $form_attr
	 */
	public function get_updated_post_id( $args ) {
		
		if ( is_array( $args ) ) {
			
			//get post id from shortcode "update" attibute
			if ( !empty( $args['post_id'] ) ) {	
				self::$update_post_id = $args['post_id'];
				
			//get post ID of the post being displayed
			} elseif ( !empty( $GLOBALS['post'] ) ) {
				self::$update_post_id = $GLOBALS['post']->ID;
			}
			
		//get post ID from URL
		} elseif ( !empty( $args ) ) {
			self::$update_post_id = $args;
		}		
	}
	
	/**
	 * Execute function on form load
	 * @param unknown_type $form
	 * @return unknown|string
	 */
	function render_form( $form ) {
		
		self::$ggf_settings = ( !empty( $form['ggf_settings'] ) ) ? $form['ggf_settings'] : array();
		
		//abort if GGF is disabled in Form Settings page
		if ( empty( self::$ggf_settings['address_fields']['use'] ) )
			return $form;
		
		//form ID. Needed for JS file
		self::$ggf_settings['id'] = $form['id'];
		
		//usage of this form
		self::$ggf_usage = self::$ggf_settings['address_fields']['use'];
		
		//address custom fields
		self::$ggf_address_custom_fields = ( !empty( self::$ggf_settings['address_fields']['fields'] ) ) ? self::$ggf_settings['address_fields']['fields'] : array();
				
		//if updating post
		if ( self::$update_post_id ) {
			
			//get all the custom fields belong to the post being updated
			$ggf_custom_fields = get_post_custom( self::$update_post_id );
			
			//get the location data attached to a post ( since GGF 2.0 ) 
			self::$ggf_post_location_data = get_post_meta( self::$update_post_id, '_ggf_location_fields', true );
		}
		
		//Disable alert message when using autolocator on page load
		self::$ggf_settings['fields']['pageLoad']['locator_found_message_use'] = 0;
		self::$ggf_settings['auto_locator']['use'] = ( !empty( self::$ggf_settings['auto_locator']['use'] ) ) ? 1 : 0;
	
		//add ggf classes to location fields
		foreach ( $form['fields'] as $key => $field ) {
			
			//auto locator features
			if ( $field['type'] == 'ggf_locator' ) {
				
				//these settings will pass to javascript   
				self::$ggf_settings['locator_hide_submit'] 								 = ( !empty( $field['ggfLocatorHideSubmit'] ) ) ? 1 : 0;								
				self::$ggf_settings['fields'][$field['id']]['locator_autosubmit']    	 = ( !empty( $field['ggfLocatorAutoSubmit'] ) ) ? 1 : 0;
				self::$ggf_settings['fields'][$field['id']]['locator_found_message_use'] = ( !empty( $field['ggfLocatorFoundMessageEnabled'] ) ) ? 1 : 0;
				self::$ggf_settings['fields'][$field['id']]['locator_found_message'] 	 = ( !empty( $field['ggfLocatorFoundMessage'] ) ) ? $field['ggfLocatorFoundMessage'] : __( 'Location found.', 'GFG' );
			}
				
			//Modify advanced address field
			if ( $field['type'] == 'address' ) {
				
				$advancedClass = '';
				$advancedClass = ( !empty( $form['fields'][$key]['ggfLocatorAutoFill'] ) ) ? "gform-address-field" : $advancedClass;
				$advancedClass = ( !empty( $form['fields'][$key]['ggfMapAutoFill'] ) ) ? "gform-address-field" : $advancedClass;
				
				//apply the geocoder on this field
				if ( self::$ggf_usage == 3 ) {
					$advancedClass = ( !empty( $form['fields'][$key]['ggf_advanced_address_geocode'] ) ) ? 'gform-address-field ggf-advanced-geocode-true ggf-geocode-field' : $advancedClass;
				}
				
				$form['fields'][$key]['cssClass'] .= $advancedClass;	
				
				//$form['fields'][$key]['defaultState'] = '';
				//$form['fields'][$key]['defaultCountry'] = '';	
			}
								
			self::$ggf_settings['fields'][$field['id']]['restrictions'] = ( !empty( $field['ggf_autocomplete_country'] ) ) ? $field['ggf_autocomplete_country'] : false;
			
			//set fields to false as default to prevent JS errors
			$ac = $dlf = $maf = $um = $louput = '';
			self::$ggf_settings['fields'][$field['id']]['autocomplete'] = false;
			self::$ggf_settings['fields'][$field['id']]['locator_fill'] = false;
			self::$ggf_settings['fields'][$field['id']]['update_map']   = false ;
			
			//enable autocomplete
			if ( !empty( $field['ggfAutocomplete'] ) ) {	
				self::$ggf_settings['fields'][$field['id']]['autocomplete'] = 1;
				$ac  = ' ggf-autocomplete';
			} 
			
			//enable locator autofill
			if ( !empty( $field['ggfLocatorAutoFill'] ) ) {
				self::$ggf_settings['fields'][$field['id']]['locator_fill'] = 1;			
				$dlf = 'locator-fill';
			}
			
			//enable locator autofill
			if ( !empty( $field['ggfMapAutoFill'] ) ) {
				$maf = 'map-autofill';
			}
			
			//enable locator autofill
			if ( !empty( $field['ggfUpdateMap'] ) ) {
				self::$ggf_settings['fields'][$field['id']]['update_map'] = 1;
				$um = 'autocomplete-update-map';
			}
			
			if ( !empty( $field['ggf_location_output'] ) ) {
				$louput = 'ggf-cf-'.$field['ggf_location_output'];
			}
			
			//apply GGF classes to the field
			$form['fields'][$key]['cssClass'] .=  ' '.$ac . ' ' . $dlf . ' ' . $um .' '. $maf .' '. $louput;
			
			//loop through GGF address fields
			if ( in_array( $field['type'], array( 'ggf_address', 'ggf_street_number', 'ggf_street_name', 'ggf_street', 'ggf_apt', 'ggf_city', 'ggf_state', 'ggf_zipcode', 'ggf_country', 'ggf_gmw_phone', 'ggf_gmw_fax', 'ggf_gmw_email', 'ggf_gmw_website' ) ) ) {
				
				if ( self::$ggf_usage == 1 && $field['type'] == 'ggf_address' ) {
					$form['fields'][$key]['cssClass'] .= ' ggf-field ggf-full-address ggf-geocode-field ';
				} 
				if ( self::$ggf_usage == 2 && in_array( $field['type'], array( 'ggf_street', 'ggf_apt', 'ggf_city', 'ggf_state', 'ggf_zipcode', 'ggf_country' ) ) ) {
					$form['fields'][$key]['cssClass'] .= ' ggf-field ggf-field-'.str_replace( 'ggf_', '', $field['type'] ).' ggf-geocode-field ';
				}

				//populate ggf fields values when updating posts using Gravity Forms Update Posts plugin
				if ( self::$update_post_id && isset( $ggf_custom_fields[$field['postCustomFieldName']] ) ) {
					$form['fields'][$key]['defaultValue'] = end($ggf_custom_fields[$field['postCustomFieldName']]);
				}
			}	
		}

		//run JS
		if ( !wp_script_is( 'google-maps', 'enqueued' ) ) {
			wp_enqueue_script( 'google-maps' );
		}
		
		wp_enqueue_script( 'gfg-script' );
		wp_localize_script( 'gfg-script', 'ggfSettings', self::$ggf_settings );

		echo '<style>.ggf-map img {max-width:initial !important;}</style>';
		
		return $form;
	}

	/**
	 * Append hidden location fields to form
	 * @param unknown_type $input
	 * @return string
	 */
	function form_tag( $input, $form ) {

		if ( empty( self::$ggf_usage ) ) 
			return $input;

		$address_fields = array( 'street_number', 'street_name', 'street', 'city', 'state', 'state_long', 'zipcode', 'country', 'country_long', 'formatted_address', 'lat', 'lng' );
		
		//append hidden fields to the form
		$input .= '<div id="ggf-text-fields-wrapper">';

		//append fields based on submitted information
		//In case that form could not submit ( maybe missing/inccorect fields )
		if ( !empty( $_POST['ggf_field_location'] ) ) {
			
			foreach ( $address_fields as $field ) {

				$post_field = ( !empty( $_POST['ggf_field_location'][$field] ) ) ? $_POST['ggf_field_location'][$field] : '';

				$input .= '<input type="hidden" id="ggf-field-'.$field.'" class="ggf-cf-'.$field.'" name="ggf_field_location['.$field.']" value="'.esc_attr( sanitize_text_field( $post_field ) ).'" />';
			}
			
		//in case of form update ( Update Post plugin )
		} elseif ( self::$update_post_id ) {
			
			foreach ( $address_fields as $field ) {
				
				$value = '';
	
				//check if the locaiton saved in location custom field 
				//Will work for posts saved after the update of GGF 2.0 and up
				if ( !empty( self::$ggf_post_location_data ) ) {

					$value = self::$ggf_post_location_data['location_fields'][$field];
					
				//try to get the information from teh custom fields saved in Form Settings custom fields.
				} else {					
					$value = get_post_meta( self::$update_post_id, self::$ggf_address_custom_fields[$field], true );
				}
					
				$input .= '<input type="hidden" id="ggf-field-'.$field.'" class="ggf-cf-'.$field.'" name="ggf_field_location['.$field.']" value="'.esc_attr( sanitize_text_field( $value ) ).'" />';	
			}
		//for new form - create the fields with no value
		} else {
			
			foreach ( $address_fields as $field ) {		
				$input .= '<input type="hidden" id="ggf-field-'.$field.'" class="ggf-cf-'.$field.'" name="ggf_field_location['.$field.']" value="" />';
			}
		}
		
		//auto locator hidden field
		$post_field = ( !empty( $_POST['ggf_field_location']['autolocator'] ) ) ? $_POST['ggf_field_location']['autolocator'] : '';
		
		$input .= '<input type="hidden" id="ggf-autolocator" name="ggf_field_location[autolocator]" value="'.esc_attr( sanitize_text_field( $post_field ) ).'" />';
		$input .= '<input type="hidden" id="ggf-update-location" />';
		$input .= '</div>';

		return $input;
	}

	/**
	 * Create dynamic fields ( map, locator... )
	 */
	function extra_fields( $input, $field, $value, $lead_id, $form_id ){
		
		if ( empty( self::$ggf_usage ) || IS_ADMIN )
			return $input;
		
		//create the map field
		if ( $field["type"] == "ggf_map" ) {

			//set the deafult values
			$map_type   = ( !empty( $field['ggfMapType' ] ) )   ? $field['ggfMapType' ]   : 'ROADMAP';
			$zoom_level = ( !empty( $field['ggfZoomLevel' ] ) ) ? $field['ggfZoomLevel' ] : '12';
			$map_width  = ( !empty( $field['ggfMapWidth'] ) )   ? $field['ggfMapWidth']   : "300px";
			$map_height = ( !empty( $field['ggfMapHeight'] ) )  ? $field['ggfMapHeight']  : "300px";
			$latitude 	= '40.7827096';
			$longitude 	= '-73.965309';
			
			//set Initial coords based on post being updated ( Update Post plugin ). 
			if ( self::$update_post_id ) {
				
				//check if coords found in post location field ( GGF 2.0 and up )
				if ( !empty( self::$ggf_post_location_data['location_fields']['lat'] ) && !empty( self::$ggf_post_location_data['location_fields']['lng'] ) ) {
						
						$latitude  = self::$ggf_post_location_data['location_fields']['lat'];
						$longitude = self::$ggf_post_location_data['location_fields']['lng'];
				
				//otherwise check for coords in address custom fields ( Form Settings page )
				} elseif ( !empty( self::$ggf_address_custom_fields ) ) {
					
					$lat = get_post_meta( self::$update_post_id, self::$ggf_address_custom_fields['lat'], true );
					$lng = get_post_meta( self::$update_post_id, self::$ggf_address_custom_fields['lng'], true );
					
					if ( !empty( $lat ) && !empty( $lng ) ) {
						$latitude = $lat;
						$longitude = $lng;
					}
				}		

			//use coords from URL. Can be pass between forms
			} elseif ( !empty( $_GET['latitude'] ) && !empty( $_GET['longitude'] ) ) {
				
				$latitude = $_GET['latitude'];
				$longitude = $_GET['longitude'];
			
			//use the coords if form failed to be submitted
			} elseif ( !empty( $_POST['ggf_field_location']['lat'] ) && !empty( $_POST['ggf_field_location']['lng'] ) ) {
				
				$latitude  = $_POST['ggf_field_location']['lat'];
				$longitude = $_POST['ggf_field_location']['lng'];
				
			//else use coords saved in form editor field
			} elseif ( !empty( $field['ggfMapLatitude'] ) && !empty( $field['ggfMapLongitude'] ) ) {
				
				$latitude = $field['ggfMapLatitude'];
				$longitude = $field['ggfMapLongitude'];
			}
			
			$mapArgs = array_map( 'sanitize_text_field', array (
					'latitude'  => $latitude,
					'longitude' => $longitude,
					'map_type'  => $map_type,
					'zoom_level'=> $zoom_level
			) );

			//create the map element
			$input  = '<div id="ggf-map-wrapper" class="ggf-map-wrapper">';
			$input .= '<div id="ggf-map" class="ggf-map" style="height:'.esc_attr( $map_height ).';width:'.esc_attr( $map_width ).'"></div>';
			$input .= '</div><!-- map holder -->';
			
			//pass map's settings to JS
			wp_localize_script( 'gfg-script', 'mapArgs', $mapArgs );
		}

		//auto-locator button 
		if ( $field["type"] == "ggf_locator" ) {

			$field['ggfLocatorLabel'] = ( !empty( $field['ggfLocatorLabel'] ) ) ? esc_attr( sanitize_text_field( $field['ggfLocatorLabel'] ) ) : '';
			
			$input = '';
			//if ( !empty( $field['label'] ) ) {
				$input  = '<div class="ginput_container">';
			//}
			
			$input .= '<input type="button" id="input_'.esc_attr( $form_id ).'_'.esc_attr( $field['id'] ).'" locator-id="'.esc_attr( $field['id']).'" class="ggf-locator-button" value="'.$field['ggfLocatorLabel'].'" />';
			$input .= '<span class="ggf-locator-spinner-wrapper" style="display:none">';
			$input .= '<img class="ggf-locator-spinner" style="box-shadow: none;border-radius:0" src="'.GFG_URL .'/assets/images/loader.gif'.'" />';
			$input .= '</span>';
			
			//if ( !empty( $field['label'] ) ) {
				$input .= '</div>';
			//}
		}
	
		return $input;
	}

	/**
	 * modify the advanced address field.
	 * Append the autocompelte field
	 */
	function address_field( $content, $field, $value, $lead_id, $form_id ) {
		
		if ( IS_ADMIN || empty( self::$ggf_usage ) )
			return $content;

		//Modify advanced address field
		if ( $field['type'] == 'address' ) {

			$placeholder = ( !empty( $field['ggfAutocompletePlaceholder'] ) ) ? esc_attr( $field['ggfAutocompletePlaceholder'] ) : '';
			$desc		 = ( !empty( $field['ggfAutocompleteDesc'] ) ) ? esc_attr( $field['ggfAutocompleteDesc'] ) : '';
			$rand   	 = rand(1,100);

			$input  = '<span id="ggf-advanced-address-autocomplete-input_'.esc_attr( $form_id ).'_'.esc_attr( $field['id'] ).'_0-wrapper" class="ggf-advanced-address-autocomplete-wrapper ginput_full" style="display:none;">';
			$input .= '<input id="ggf-advanced-address-autocomplete-input_'.esc_attr( $form_id ).'_'.esc_attr( $field['id'] ).'_0_'.$rand .'" class="ggf-advanced-address-autocomplete" type="text" placeholder="'.$placeholder.'" />';
			$input .= '<label for="ggf-advanced-address-autocomplete-input_'.esc_attr( $form_id ).'_'.esc_attr( $field['id'] ).'_0_'.$rand .'">'.$desc.'</label>';
			$input .= '</span>';

			$content = $input.$content;
		}
		
		return $content;
	}
}
new GFG_Form_Functions;
?>