<?php
/**
 * GFG_Submission_Functions class
 *
 * The class responsible for updating location for posts, entries and users after form submission.
 *
 * @author Fitoussi Eyal
 * @since 2.0
 */
class GFG_Submission_Functions {
	
	/**
	 * __constructor
	 */
	function __construct() {
		
		//update locaiton after form submission
		add_action( 'gform_after_submission', array( $this, 'update_location' ), 5, 2  );
	
		//if GEO my WP exists
		add_action( 'ggf_after_location_updated', array( $this, 'update_gmw_posts' ), 10, 5 );
		
		//update user location after activation ( manually or automatically )
		add_action( 'gform_user_registered',  array( $this, 'update_user_location' ), 10, 4 );
	
		//update user location when user updated
		add_action( 'gform_user_updated', 	  array( $this, 'update_user_location' ), 10, 4 );		
	}
	
	/**
	 * Update location after form submission
	 */
	function update_location( $entry, $form ) {
		
		$ggfSettings = ( !empty( $form['ggf_settings'] ) ) ? $form['ggf_settings'] : array();
		
		//make sure Form settings is set
		if ( empty( $ggfSettings['address_fields']['use'] ) )
			return;
			
		//post ID
		$postID = $entry['post_id'];

		$ggfLocation = array(
				'street_number'  	=> '',
				'street_name'  		=> '',
				'street'  			=> '',
				'city' 	  			=> '',
				'state'   			=> '',
				'state_long'   		=> '',
				'zipcode' 			=> '',
				'country' 			=> '',
				'country_long' 		=> '',
				'formatted_address' => '',
				'lat' 				=> '',
				'lng' 				=> '',
				'address'			=> '',
				'org_location'		=> array(
						'street'  => '',
						'apt' 	  => '',
						'city' 	  => '',
						'state'   => '',
						'zipcode' => '',
						'country' => ''
				)
		);

		$additional_info = array(
				'phone'   => '',
				'fax' 	  => '',
				'email'   => '',
				'website' => ''
		);
		
		$ggfLocation = array_merge( $ggfLocation,  array_map( 'sanitize_text_field', $_POST['ggf_field_location'] ) );
		
		//when single address field being used
		if ( $ggfSettings['address_fields']['use'] == 1 ) {
		
			//get address fields from geocoded information since single address field cannot provide it
			$ggfLocation['org_location']['street']  = $ggfLocation['street'];
			$ggfLocation['org_location']['apt']  	= ( !empty( $ggfLocation['apt'] ) ) ? $ggfLocation['apt'] : '';
			$ggfLocation['org_location']['city'] 	= $ggfLocation['city'];
			$ggfLocation['org_location']['state']   = $ggfLocation['state'];
			$ggfLocation['org_location']['zipcode'] = $ggfLocation['zipcode'];
			$ggfLocation['org_location']['country'] = $ggfLocation['country'];
		
			//find the address field and get its value
			foreach ( $form['fields'] as $field ) {
		
				//check if full address field exists and not empty
				if ( $field['type'] == 'ggf_address' ) {
		
					//update full address field based on its value
					$ggfLocation['address'] = $_POST['input_'.$field['id']];
				}
			}
		
		//when using multiple fields
		} elseif ( $ggfSettings['address_fields']['use'] == 2 ) {
		
			//loop through fields and get the values of the different address fields
			foreach ( $form['fields'] as $field ) {
		
				//if not empty append the a
				if ( in_array( $field['type'], array( 'ggf_street', 'ggf_apt', 'ggf_city', 'ggf_state', 'ggf_zipcode', 'ggf_country' ) ) ) {
		
					$ggfLocation['org_location'][str_replace('ggf_', '', $field['type'])] =  $_POST['input_'.$field['id']];
				}
			}
		
			$ggfLocation['address'] = implode( ' ', $ggfLocation['org_location'] );
		
		//when using advanced address field
		} elseif ( $ggfSettings['address_fields']['use'] == 3 ) {
		
			//loop through fields and get the values of the different address fields
			foreach ( $form['fields'] as $field ) {
		
				if ( $field['type'] == 'address' && !empty( $field['ggf_advanced_address_geocode'] ) ) {
		
					$aaf = array( 'street', 'apt', 'city', 'state', 'zipcode', 'country' );
		
					foreach ( $aaf as $key => $val ) {
						$key++;
						$ggfLocation['org_location'][$val] = ( !empty( $_POST['input_'.$field['id'].'_'.$key] ) ) ? $_POST['input_'.$field['id'].'_'.$key] : '';
					}
				}
			}
		
			$ggfLocation['address'] = implode( ' ', $ggfLocation['org_location'] );
		}
			
		//make sure address field is not empty.
		if ( empty( $ggfLocation['address'] ) ) {
			$ggfLocation['address'] = $ggfLocation['formatted_address'];
		}
		
		/*
	 	 * Create location array which holds all the location fields before
	 	 * and after geocode and save it in entry meta. Doing that we will be 
	 	 * able to easily use it when updating posts and users.
	 	 * 
	 	 */
		gform_update_meta( $entry['id'], 'ggf_location', $ggfLocation );
		
		//update gmw contact information if needed
		if ( !empty( $ggfSettings['address_fields']['gmw']['use'] ) ) {
			
			foreach ( $form['fields'] as $field ) {

				if ( in_array( $field['type'], array( 'ggf_gmw_phone', 'ggf_gmw_fax', 'ggf_gmw_email', 'ggf_gmw_website' ) ) ) {
					
					$additional_info[str_replace('ggf_gmw_', '', $field['type'])] = $_POST['input_'.$field['id']] ;
				
					//update address field in custom field if needed
					if ( !empty( $postID ) && !empty( $field['postCustomFieldName'] ) ) {
						update_post_meta( $postID, $field['postCustomFieldName'], sanitize_text_field( $_POST['input_'.$field['id']] ) );
					}
				}
			}		
		}
				
		//create/update custom fields
		//check if post ID exist. Only then we will create/update custom fields.
		if ( !empty( $postID ) ) { 
			
			update_post_meta( $postID, '_ggf_location_fields', array( 'location_fields' => $ggfLocation, 'contact_fields' => array_map( 'sanitize_text_field', $additional_info ) ) );
			
			/*
			 * loop through fields and update custom field if needed. The custom fields are not being
			 * updated automatically by Gravity Form because the address fields are custom fields
			 * created by GGF add-on. For some reason the yare not being update and so we are
			 * doing it manually
			 */
			foreach ( $form['fields'] as $field ) {
			
				//check if this is GGF location field type
				if ( in_array( $field['type'], array( 'ggf_address', 'ggf_street', 'ggf_apt', 'ggf_city', 'ggf_state', 'ggf_zipcode', 'ggf_country' ) ) ) {
			
					//update location field in custom field if needed
					if ( !empty( $field['postCustomFieldName'] ) ) {
						update_post_meta( $postID, $field['postCustomFieldName'], sanitize_text_field( $_POST['input_'.$field['id']] ) );
					}
				}
			}
			
			//save location field to the custom fields set in the Form Settigns page
			$custom_fields = ( !empty( $ggfSettings['address_fields']['fields'] ) ) ? $ggfSettings['address_fields']['fields'] : array();
			
			foreach ( $custom_fields as $key => $value ) {
	
				if ( in_array( $key, array( 'street', 'apt', 'city', 'zipcode' ) ) ){
					
					if ( !empty( $ggfLocation['org_location'][$key] ) ) {
						
						update_post_meta($postID, $value, sanitize_text_field( $ggfLocation['org_location'][$key] ) );
						
					} elseif ( $key != 'apt' ) {
						
						update_post_meta( $postID, $value, sanitize_text_field( $ggfLocation[$key] ) );
						$ggfLocation['org_location'][$key] = $ggfLocation[$key];
					}	
				} else {
					if ( !empty( $ggfLocation[$key] ) ) update_post_meta( $postID, $value, sanitize_text_field( $ggfLocation[$key] ) );
				}
			}
		}
		
		do_action( 'ggf_after_location_updated', $postID, $entry, $form, $ggfLocation, $additional_info );
	}

	/**
	 * updated GEO my WP database with the posts location
	 * 
	 */
	function update_gmw_posts( $postID, $entry, $form, $ggfLocation, $additional_info ) {

		$ggfSettings = ( !empty( $form['ggf_settings'] ) ) ? $form['ggf_settings'] : array();
		
		if ( empty( $ggfSettings['address_fields']['gmw']['use'] ) || !class_exists( 'GEO_my_WP' ) ) 
			return;

		//Save information to database
		global $wpdb;
		$wpdb->replace( $wpdb->prefix . 'places_locator',
				array(
						'post_id'			=> $postID,
						'feature'  			=> 0,
						'post_type' 		=> get_post_type($postID),
						'post_title' 		=> get_the_title($postID),
						'post_status'		=> 'publish',
						//'street_number' 	=> $ggfLocation['street_number'],
						//'street_name' 		=> $ggfLocation['street_name'],
						'street' 			=> $ggfLocation['org_location']['street'],
						'apt' 				=> $ggfLocation['org_location']['apt'],
						'city' 				=> $ggfLocation['org_location']['city'],
						'state' 			=> $ggfLocation['state'],
						'state_long' 		=> $ggfLocation['state_long'],
						'zipcode' 			=> $ggfLocation['zipcode'],
						'country' 			=> $ggfLocation['country'],
						'country_long' 		=> $ggfLocation['country_long'],
						'address' 			=> $ggfLocation['address'],
						'formatted_address' => $ggfLocation['formatted_address'],
						'phone' 			=> $additional_info['phone'],
						'fax' 				=> $additional_info['fax'],
						'email' 			=> $additional_info['email'],
						'website' 			=> $additional_info['website'],
						'lat' 				=> $ggfLocation['lat'],
						'long' 				=> $ggfLocation['lng'],
						'map_icon'  		=> '_default.png',
				)
		);
	}

	/**
	 * Update users location using "User registration" gravity forms add-on
	 */
	function update_user_location( $user_id, $config, $entry, $user_pass ) {
			
		//get form details
		$form = RGFormsModel::get_form_meta_by_id( $config['form_id'] );
		$form = $form[0];

		//get options
		$ggfSettings = ( !empty( $form['ggf_settings'] ) ) ? $form['ggf_settings'] : array();

		if ( empty( $ggfSettings['address_fields']['use'] ) )
			return;
		
		//get GEO user registration fields
		$ggfURSettings = $config['meta']['ggf_settings'];
				
		/* 
		 * get user location from entry meta. we save it  in entry meta in case
		 * that the user activation is happening manually.
		 * in this case we do not have the live data of the location fields
		 * which created on form submission. That is why we save it in entry meta.
		 */
		$ggfLocation = gform_get_meta( $entry['id'], 'ggf_location' );
				
		//save location in user_meta
		if ( !empty( $ggfURSettings['address_fields']['user_meta_fields'] ) ) {
		
			foreach ( $ggfURSettings['address_fields']['user_meta_fields'] as $key => $value ) {
			
				if ( in_array( $key, array( 'street', 'city', 'zipcode' ) ) ) {
			
					if ( !empty( $ggfLocation['org_location'][$key] ) ) {
							
						update_user_meta( $user_id, $value, sanitize_text_field( $ggfLocation['org_location'][$key] ) );
							
					} elseif ( !empty( $ggfLocation[$key] ) ) {
							
						update_user_meta( $user_id, $value, sanitize_text_field( $ggfLocation[$key] ) );
			
					} else {
						delete_user_meta( $user_id, $value );
					}

				} elseif ( $key == 'apt' ) {

					if ( !empty( $ggfLocation['org_location'][$key] ) ) {
							
						update_user_meta( $user_id, $value, sanitize_text_field( $ggfLocation['org_location'][$key] ) );
										
					} else {
						delete_user_meta( $user_id, $value );
					}

				} else {
					if ( !empty( $ggfLocation[$key] ) ) {
						update_user_meta($user_id, $value, sanitize_text_field( $ggfLocation[$key] ) );
					} else { 
						delete_user_meta( $user_id, $value );
					}
				}
			}
		}
		
		//check if buddypress activated
		if ( class_exists('BuddyPress') && !empty( $ggfURSettings['address_fields']['bp_fields'] ) ) {
		
			//save location to custom fields
			foreach ( $ggfURSettings['address_fields']['bp_fields'] as $key => $value ) {
			
				if ( in_array( $key, array( 'street', 'apt', 'city', 'zipcode' ) ) ){
			
					if ( !empty( $ggfLocation['org_location'][$key] ) ) {
			
						xprofile_set_field_data( $value, $user_id, sanitize_text_field( $ggfLocation['org_location'][$key] ) );
								
					} elseif ( $key != 'apt' ) {
			
						xprofile_set_field_data( $value, $user_id, sanitize_text_field( $ggfLocation[$key] ) );
					}
				} else {
					if ( !empty( $ggfLocation[$key] ) ) {
						xprofile_set_field_data($value, $user_id, sanitize_text_field( $ggfLocation[$key] ) );
					}
				}
			}
		}

		//check if GEO my WP activated and we need to save locaiton to member
		if ( !empty( $ggfURSettings['address_fields']['gmwbp']['use'] ) && !empty( $ggfLocation['lat'] ) && ( class_exists( 'GEO_my_WP' ) || function_exists('gmw_loaded') ) ) {

			$map_icon = ( isset($_POST['map_icon']) ) ? $_POST['map_icon'] : '_default.png';

			//save location into GEO my WP members table in database
			global $wpdb;

			$wpdb->replace( 'wppl_friends_locator', array(
					'member_id'			=> $user_id,
					'street'			=> $ggfLocation['org_location']['street'],
					'apt'				=> $ggfLocation['org_location']['apt'],
					'city' 				=> $ggfLocation['org_location']['city'],
					'state' 			=> $ggfLocation['state'],
					'state_long' 		=> $ggfLocation['state_long'],
					'zipcode'			=> $ggfLocation['zipcode'],
					'country' 			=> $ggfLocation['country'],
					'country_long'	 	=> $ggfLocation['country_long'],
					'address'			=> $ggfLocation['address'],
					'formatted_address' => $ggfLocation['formatted_address'],
					'lat'				=> $ggfLocation['lat'],
					'long'				=> $ggfLocation['lng'],
					'map_icon'			=> $map_icon
			));
		}
		
		//hook and do something with the information
		do_action( 'ggf_after_member_location_saved', $user_id, $config, $entry, $user_pass, $ggfLocation );
	}
}
new GFG_Submission_Functions;