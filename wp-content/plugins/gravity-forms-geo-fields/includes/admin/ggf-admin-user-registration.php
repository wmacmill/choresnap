<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
	exit; 

/**
 * GGF function - add options section to user registration setting page
 * @since 1.2
 */
function gfg_options_section( $config, $form, $is_validation_error ) {
	
	//get options
	$ggfSettings = $config['meta']['ggf_settings'];
	
	//address fields
	$address_fields = array(
			__('Street number','GFG') 			  	=> 'street_number',
			__('Street name','GFG') 			  	=> 'street_name',
			__('Street ( number + name )','GFG') 	=> 'street',
			__('Apt','GFG')    			  			=> 'apt',
			__('City','GFG')   			  			=> 'city',
			__('State','GFG')  			  			=>  'state',
			__('State long name','GFG')   			=> 'state_long',
			__('Zipcode','GFG') 		  			=> 'zipcode',
			__('Country','GFG') 		  			=> 'country',
			__('Country long name','GFG') 			=> 'country_long',
			__('Full Address','GFG')      			=> 'address',
			__('Formatted Address','GFG') 			=> 'formatted_address',
			__('Latitude','GFG') 		  			=> 'lat',
			__('Longitude','GFG') 		  			=> 'lng'
	);

	$gmwbp_use = ( isset( $ggfSettings['address_fields']['gmwbp']['use']) && $ggfSettings['address_fields']['gmwbp']['use'] == 1 ) ? 'checked="checked"' : '';
	
	if ( function_exists('gmw_loaded') || class_exists( 'GEO_my_WP' ) ) {
		
		$addons   = get_option( 'gmw_addons' );
		$disabled = '';
		$message  = '';
		
		if ( !isset( $addons['friends'] ) ) {
			$message  = '<span style="color:#666;font-weight:normal">'. sprintf( __( "Requires GEO my WP's <a %s>\"Member Locator\" add-on</a>", 'GFG'), 'href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=gmw-add-ons"').'</span>';
			$disabled = 'disabled="disabled"';
		}
	} else {
		
		$gmw_on   = array();
		$disabled = 'disabled="disabled"';
		$message  = '<span style="color:#666;font-weight:normal">'.sprintf( __( "Requires <a %s>GEO my WP</a> plugin", "GGF" ), 'href="http://geomywp.com" target="_blank"' ).'</span>';			
	}
	?>
    <div id="ggf_options" class="ggf_options">
    
        <h3><?php _e('Geolocation Options','GFG'); ?></h3>
        
        <div id="ggf_geo_fields_setup" class="margin_vertical_10 ">
        <label class="left_header" for="gf_user_registration_username">
        	<?php _e( 'Address Fields Setup', 'GFG' ); ?>
        	<a href="#" 
        		onclick="return false;" 
        		class="gf_tooltip tooltip tooltip_geo_fields_setup" 
        		title="<?php _e( "In the table below you can setup the fields where you would like to save each of the geocoded location fields. You can save the location information in user meta as well in Xprofile Fields when BuddyPress plugin is install and activated.", "GGF" ); ?>">
        		<i class="fa fa-question-circle"></i>
        	</a>
        </label>
      
      	<br />
      	                 
        <table>
        	<tbody>
		        <tr>
					<td colspan="2" class="gf_sub_settings_cell">
		                <div class="gf_animate_sub_settings">
		                    <table>
		                    	<tr>
		                    		<th style="border: 1px solid #ddd;background: #f7f7f7;padding:8px 5px;"><?php _e('Address Field','GFG'); ?></th>
									<th style="border: 1px solid #ddd;background: #f7f7f7;padding:8px 5px;"><?php _e('User Meta','GFG'); ?></th>
									<?php if ( class_exists('BuddyPress') ) { ?>
										<th style="border: 1px solid #ddd;background: #f7f7f7;padding:8px 5px;"><?php _e('Xprofile fields','GFG'); ?></th>
									<?php } ?>
								</tr>
		                   		<tbody>
									<?php foreach ( $address_fields as $name => $value ) { ?>
										<tr id="ggf_address_field_address" class="child_setting_row" style="">
			            					<th style="text-transform:capitalize;text-align: left;font-weight: normal;min-width: 170px;"><?php echo $name; ?></th>
			            					<td>
			            						<?php $fieldValue = ( !empty( $ggfSettings['address_fields']['user_meta_fields'][$value] ) ) ? esc_attr( sanitize_text_field( $ggfSettings['address_fields']['user_meta_fields'][$value] ) ) : ''; ?>
			            						<input type="text" id="ggf_user_meta_address_field_'.$value.'" name="ggf_settings[address_fields][user_meta_fields][<?php echo $value; ?>]" size="25px" class="ggf_user_meta_address_field" value="<?php echo $fieldValue; ?>">
			            					</td>
										<?php if ( class_exists('BuddyPress') ) {	?>					
			            					<td>
			            						<select name="ggf_settings[address_fields][bp_fields][<?php esc_attr_e( $value ); ?>]">
													<option value="0"><?php _e('N/A','GFG'); ?></option>
													<?php foreach ( GFUser::get_buddypress_fields() as $field ) { ?>
														<?php  $selected = ( isset( $ggfSettings['address_fields']['bp_fields'] ) && $field['value'] == $ggfSettings['address_fields']['bp_fields'][$value] ) ? 'selected="selected"' : ''; ?>
														<option value="<?php echo esc_attr( $field['value'] ); ?>" <?php echo $selected; ?> ><?php echo esc_attr( $field['name'] ); ?></option>
													<?php } ?>
			            						</select>
			            					</td>
										<?php } ?>
			        					</tr>
									<?php } ?>            			    
		                    	</tbody>
		                    </table>
		                </div>
		            </td>
		        </tr>
     		</tbody>
   		</table> 
    </div>
    <div id="gmw_options" class="gmw_options">
    
        <h3><?php _e('GEO my WP options','GFG'); ?></h3>
        <div id="gmw_settings_use" class="margin_vertical_10" style="">
        	<label class="left_header">
        		<?php _e( "Integrate with GEO my WP", "GGF" )?> 
        		<a href="#" onclick="return false;" class="gf_tooltip tooltip" title="<?php _e( 'Save members location in GEO my WP database table. This way members will be searchable via GEO my WP search forms','GFG'); ?>">
        			<i class="fa fa-question-circle"></i>
        		</a>
        	</label>
        	<input type="hidden" value="0" name="ggf_settings[address_fields][gmwbp][use]" />
            <input type="checkbox" id="gmw_gf_on" value="1" <?php echo $disabled; ?> name="ggf_settings[address_fields][gmwbp][use]" <?php echo $gmwbp_use; ?> />
        	<label for="gmw_settings_use" class="checkbox-label"><?php echo $message; ?></label>
        </div>
    </div>
    <?php
}
add_action( "gform_user_registration_add_option_section", "gfg_options_section", 10, 3 );

//save options
function gfg_save_options($config) {
	
	$config['meta']['ggf_settings']  = RGForms::post("ggf_settings");

	return $config;
}
add_filter( "gform_user_registration_save_config", "gfg_save_options" );

?>