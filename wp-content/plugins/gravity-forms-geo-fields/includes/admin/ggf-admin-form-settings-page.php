<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
	exit; 

/**
 * GFG_Admin class
 * 
 * Modify the "Form Settings" page of a form; Apply GGF settings to this page
 */
class GFG_Admin_Form_Settings_Page {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		//create new GEolocation tab
		add_filter( 'gform_form_settings_menu', array( $this, 'new_tab' ) );

		//geolocation page content
        add_action( "gform_form_settings_page_gfg_geolocation", array( $this, 'page_content') );
	}	

	/**
	 * Create new Geolocation tab in form settings page
	 * @param  array $setting_tabs form settings tab
	 * @return array
	 */
	function new_tab( $setting_tabs ) {
        $setting_tabs['50'] =  array( 'name' => 'gfg_geolocation', 'label' => __( 'Geolocation', 'GFG' ) );
        return $setting_tabs;
    }

    /**
     * Form settings Geolocation page content
     * @return [type] [description]
     */
	public function page_content() {

		$form_id  = rgget( 'id' );
		$form 	  = RGFormsModel::get_form_meta_by_id( $form_id );
		$form     = $form[0];

		//update settings if form submitted
		$form = $this->update_settings( $form_id, $form );

		//page content starts
		GFFormSettings::page_header( 'Geolocation' );
	
		//get settings
		$ggfSettings = ( !empty( $form['ggf_settings'] ) ) ? $form['ggf_settings'] : array();
		
		//address fields
		$address_fields = array( 
				__( 'Street number', 'GFG' ) 	 => 'street_number', 
				__( 'Street name', 'GFG' ) 	     => 'street_name', 
				__( 'Street', 'GFG' ) 			 => 'street', 
				__( 'Apt', 'GFG' )    			 => 'apt', 
				__( 'City', 'GFG' )   			 => 'city', 
				__( 'State', 'GFG' )  			 => 'state',
				__( 'State long name', 'GFG' )   => 'state_long',
				__( 'Zipcode', 'GFG' ) 		  	 => 'zipcode',
				__( 'Country', 'GFG' ) 		  	 => 'country',
				__( 'Country long name', 'GFG' ) => 'country_long',
				__( 'Full Address', 'GFG' )      => 'address',
				__( 'Formatted Address', 'GFG' ) => 'formatted_address',
				__( 'Latitude', 'GFG' ) 		 => 'lat',
				__( 'Longitude', 'GFG' ) 		 => 'lng'
		);
		
		$ggfUsageSingle = $ggfUsageMultiple = $ggfUsageAddress = '';
		$ggfCurrentUsage = 0;
		
		if ( isset( $ggfSettings['address_fields']['use'] ) ) {
			
			if ( $ggfSettings['address_fields']['use'] == 1 ) {
				$ggfUsageSingle = 'checked="checked"';
				$ggfCurrentUsage = 1;
			} elseif ( $ggfSettings['address_fields']['use'] == 2 ) {
				$ggfUsageMultiple = 'checked="checked"';
				$ggfCurrentUsage = 2;
			} elseif ( $ggfSettings['address_fields']['use'] == 3 ) {
				$ggfUsageAddress = 'checked="checked"';
				$ggfCurrentUsage = 3;
			}
		}

		if ( !class_exists('GEO_my_WP') ) {
			$disabled = 'disabled="disabled"';
			$message  = '<span style="color:#666;font-weight:normal"> '.sprintf( __( 'requires <a %s>GEO my WP</a> plugin','GFG' ), 'href="http://geomywp.com" target="_blank"' ) .'</span>';
			$gmwOn 	  = 0;
		} else {
			$gmwOn 	  = 1;
			$disabled = '';
			$message  = '';
		}
		
		$gmw_use 	 = ( !empty( $ggfSettings['address_fields']['gmw']['use'] ) ) ? 'checked="checked"' : '';
		$autoLocator = ( !empty( $ggfSettings['auto_locator']['use'] ) ) 		  ? 'checked="checked"' : '';
		
		//display settings on page
		$settings['addressFieldsUse'] = '
			<tr>
				<th>'.__( 'Address Fields Usage','GFG' ).' <a href="#" onclick="return false;" class="gf_tooltip tooltip ggf_tooltip ggf_address_field)usage_tooltip" title="'.__( 'This is where you set the input address fields type that you would like to use with this form. The input address fields are the fields that will be geocoded. You can choose between a single address field that can be used as full address, multiple address fields ( street, city, state, zipcode and country ) or advanced address field which uses that native "Address" Field provided by Gravity Forms plugin','GFG').'"><i class="fa fa-question-circle"></i></a></th>
				<td>
					<input type="radio" class="ggf_usage-toggle ggf_usage_toggle_none" name="ggf_settings[address_fields][use]" value="0" name="ggf_method" checked="checked" />
					<label for="na" class="inline">'.__('Disable Geolocation for this form','GFG').'</label><br />
					<input type="radio" class="ggf_usage-toggle ggf_usage_toggle_single" name="ggf_settings[address_fields][use]" value="1" name="ggf_method" ' . $ggfUsageSingle . '  />
					<label for="single-address-field" class="inline">'.__('Single address field','GFG').'</label><br />
					<input type="radio" class="ggf_usage-toggle ggf_usage_toggle_multiple" name="ggf_settings[address_fields][use]" value="2" name="ggf_method" ' . $ggfUsageMultiple . ' />
					<label for="multiple-address-fields" class="inline">'.__('Multiple address fields','GFG').'</label><br />
					<input type="radio" class="ggf_usage-toggle ggf_usage_toggle_gform_address" name="ggf_settings[address_fields][use]" value="3" name="ggf_method" ' . $ggfUsageAddress . ' />
					<label for="gform-address-fields" class="inline">'.__('Advanced address field','GFG').'</label>
				</td>
				<input type="hidden" id="ggf_current_usage" value="'.$ggfCurrentUsage.'" />
			</tr>';
		
		//display settings on page
		$settings['autoLocator'] = '
		<tr class="ggf-toggle-additional-fields-warpper">
			<th>'.__( 'Enable auto-locator on form load','GFG' ).' <a href="#" onclick="return false;" class="gf_tooltip tooltip ggf_tooltip ggf_address_field)usage_tooltip" title="'.__( "Dynamically get the user's current location on the initial load of the form.",'GFG').'"><i class="fa fa-question-circle"></i></a></th>
            <td>
            	<input type="hidden" value="0" name="ggf_settings[auto_locator][use]" />
                <input type="checkbox" id="gmw_gf_on" value="1" name="ggf_settings[auto_locator][use]" ' . $autoLocator . '>
            </td>
		</tr>';
		
		$settings['addressFieldsValues'] = '
		<tr class="ggf-toggle-additional-fields-warpper">
			<th style="font-weight: bold">'.__("Post's custom fields",'GFG').' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_button_text" title="'.__('Enter the custom fields where you would like to save each of the location fields on form submission.','GFG').'"><i class="fa fa-question-circle"></i></a></th>
		</tr>
		<tr class="ggf-toggle-additional-fields-warpper">
			<td colspan="2" class="gf_sub_settings_cell">
                <div class="gf_animate_sub_settings">
                    <table>
                   		<tbody>';
                   			foreach ( $address_fields as $name => $value ) {
								$settings['addressFieldsValues'] .= '
								<tr id="ggf_address_field_address" class="child_setting_row" style="">
	            					<th style="text-transform:capitalize;">'.$name.'</th>
	            					<td>
	            						<input type="text" id="ggf_cf_address_field_'.$value.'" name="ggf_settings[address_fields][fields]['.$value.']" size="25px" class="ggf_cf_address_field" value="'; if ( isset($ggfSettings['address_fields']['fields'][$value]) ) { $settings['addressFieldsValues'] .= $ggfSettings['address_fields']['fields'][$value]; } $settings['addressFieldsValues'] .= '">
	            					</td>
	        					</tr>';	
							}
                   			      
                    		$settings['addressFieldsValues'] .= '
                   			         
                    	</tbody>
                    </table>
                </div>
            </td>
        </tr>
        <tr class="ggf-toggle-additional-fields-warpper">
			<th style="font-weight: bold">'.__( 'GEO my WP Options', 'GFG' ).' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_button_text" title="'.__( 'Integration with GEO my WP plugin.','GFG').'"><i class="fa fa-question-circle"></i></a></th>
			<td>'.$message.'</td>
		</tr>
        <tr class="ggf-toggle-additional-fields-warpper">
			<td colspan="2" class="gf_sub_settings_cell">
                <div class="gf_animate_sub_settings">
                    <table>
                   		<tbody>
                   			<tr class="child_setting_row" style="">
            					<th>
                					'.__('Save posts location','GFG').' <a href="#" onclick="return false;" class="gf_tooltip tooltip tooltip_form_button_text" title="'.__( 'Save location information to GEO my WP database. This way posts will be searchable via GEO my WP search forms.','GFG').'"><i class="fa fa-question-circle"></i></a>
            					</th>
            					<td>
            						<input type="hidden" value="0" name="ggf_settings[address_fields][gmw][use]" />
                					<input type="checkbox" id="gmw_gf_on" value="1" '.$disabled.' name="ggf_settings[address_fields][gmw][use]" ' . $gmw_use . '>
            					</td>
        					</tr>
        				</body>
                    </table>
                </div>
            </td>
        </tr>';
       
       	//display the page content
    	?>         	     			
		<h3>
			<span>
				<i class="fa fa-map-marker"></i> 
				<?php _e( 'Geolocation', 'GFG' ) ?>
			</span>
			<span style="font-size:12px">
			<i class="fa fa-question-circle"></i>
			<a href="http://docs.geomywp.com/gravity-forms-geolocation-fields-user-guide/" title="Docs" target="_blank">Click here</a> for the full, detailed user guide.
			</span>
		</h3>

		<form action="" method="post" id="gfg_geolocation_settings">

			<table class="gforms_form_settings">
				<?php
				//write out array of table rows
				if ( is_array( $settings ) ) {

					foreach ( $settings as  $value ) {
						echo $value;
					}
				}
				?>
			</table>

			<?php wp_nonce_field( "gfg_geolocation_save_settings", 'gfg_geolocation_save_settings' ); ?>
			<input type="hidden" id="gfg_meta" name="gfg_meta" value="post" />
			<input type="submit" id="gfg_save_settings" name="gfg_save_settings" value="<?php _e( 'Update Form Settings', 'gravityforms' ); ?>" class="button-primary gfbutton" />
		</form>

		<script>
			//toggle checkboxes
			 jQuery(document).ready(function($) {	
			 	if ( $('.ggf_usage_toggle_none').is(':checked') ) {
			 		$('.ggf-toggle-additional-fields-warpper').hide();
				} else {
					$('.ggf-toggle-additional-fields-warpper').show();
				}
				
				$('.ggf_usage-toggle').change(function() {
					if ($(this).val() == 0 ) {
						$('.ggf-toggle-additional-fields-warpper').hide();
					} else {
						$('.ggf-toggle-additional-fields-warpper').show();		
					}  		
				});

				//check if address type changed
				jQuery('#gfg_geolocation_settings').submit(function(e) {
					if ( jQuery('#ggf_current_usage').val() != 0 && jQuery('#ggf_current_usage').val() != jQuery('.ggf_usage-toggle:checked').val() ) {
						if ( !confirm( 'You are about to change the address fields type of this form. By doing the current geolocation address fields ( "GEO Fields - Geocoded Address Fields" fields group ) of this form will become invalid. That means that the fields will still be in the form but will not function as intended. Once approved You will need to navigate to the "Form Editor" page and manage the geolocation address fields of this form again based on the new address fields type.' ) ) {
							e.preventDefault();
						}
					}
				});
			 });
		</script>
	<?php
	}
	
	/**
	 * save custom fields and unique custom field checkbox used with Gravity forms update post plugin
	 */
	function update_settings( $form_id, $form ) {

		//verify if form meta need to be update. IF form was submitted
		if ( !empty( $_POST ) && !empty( $_POST['gfg_meta'] ) && check_admin_referer( 'gfg_geolocation_save_settings', 'gfg_geolocation_save_settings' ) ) {

			
			//remove address fields when address fields type changes
			if ( isset( $_POST['ggf_settings']['address_fields']['use'] ) && isset( $form['ggf_settings']['address_fields']['use'] ) && $form['ggf_settings']['address_fields']['use'] != $_POST['ggf_settings']['address_fields']['use'] ) {
				
				$fields_Array = array( 'ggf_address', 'ggf_street','ggf_apt', 'ggf_city', 'ggf_state', 'ggf_zipcode', 'ggf_country' );

				//if ( $_POST['ggf_settings']['address_fields']['use'] == 0 ) {
					//$fields_Array = array_merge( $fields_Array, array( 'ggf_map', 'ggf_locator', 'ggf_gmw_phone', 'ggf_gmw_fax', 'ggf_gmw_email', 'ggf_gmw_website' ) );
				//} 

				foreach ( $form['fields'] as $key => $field ) {
					
					if ( in_array( $field['type'], $fields_Array ) ) {

						$form['fields'][$key]['label'] = 'Invalide GEO Field';
					}
				}
			}			
		
			//append geolocation data into form meta
			$form['ggf_settings'] = rgpost( 'ggf_settings' );

			//update from meta with new settings
			GFFormsModel::update_form_meta( $form_id, $form );
		}

		//return form with new data
		return $form;
	}
}
new GFG_Admin_Form_Settings_Page;