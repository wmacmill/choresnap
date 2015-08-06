<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
	exit; 

/**
 * GFG_Admin_Edit_Form_Page
 * 
 * Modify the "Form Editor" page of a form; Apply GGF settings to this page
 */
class GFG_Admin_Edit_Form_Page {
	
	/**
	 * GGF Geocoder Usage
	 *
	 * @since 	2.0
	 * @var 	String
	 */
	public static $ggf_usage = false;
	
	/**
	 * GGF GMW Usage
	 *
	 * @since 	2.0
	 * @var 	String
	 */
	public static $ggf_gmw_usage = false;
	
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if ( !isset( $_GET['page'] ) || $_GET['page'] != 'gf_edit_forms' || ( isset( $_GET['subview'] ) && $_GET['subview'] == 'settings' ) || empty( $_GET['id'] ) ) 
			return;

		//get form
		$form = RGFormsModel::get_form_meta_by_id( $_GET['id'] );
		
		//verify if GGF field being used in this form
		if ( empty( $form[0]['ggf_settings']['address_fields']['use'] ) || $form[0]['ggf_settings']['address_fields']['use'] == '0' ) 
			return;

		self::$ggf_usage     = $form[0]['ggf_settings']['address_fields']['use'];
		self::$ggf_gmw_usage = ( !empty( $form[0]['ggf_settings']['address_fields']['gmw']['use'] ) ) ? true : false;

		add_filter( 'gform_add_field_buttons', 		 	  array( $this, 'field_groups' 	  ), 10, 1 );
		add_filter( 'gform_field_type_title' , 		 	  array( $this, 'fields_title' 	  ), 10, 1 );
		add_action( 'gform_field_standard_settings', 	  array( $this, 'fields_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', 				 	  array( $this, 'tooltips' 				 ) );
		add_action( "gform_editor_js", 				 	  array( $this, 'js_editor' 			 ) );
		add_action( 'gform_admin_pre_render', 		 	  array( $this, 'render_form' 			 ) );
		add_action( 'gform_editor_js_set_default_values', array( $this, "set_default_labels" 	 ) );

		//include fields
		include( 'ggf-form-fields.php' );
	}

	/**
	 * Create GGF form editor buttons
	 */
	function field_groups( $field_groups ) {
				
		/*
		 * advanced address field button
		 */		
		if ( self::$ggf_usage == 3 ) {
			
			//advanced address field
			$ggf_addres_fields[] = array(
					"class"		=>"button ggf-button ggf_advanced_address-field-button",
					"style" 	=> "font-size:12px;",
					"data-type" => "address",
					"value" 	=> __( "Advanced Address", "GGF" ),
					"onclick" 	=> "StartAddField('address')"
			);
				
			$field_groups[] = array(
					"name" 		=> "ggf_address_fields",
					"label"		=> __( "GEO Fields - Geocoded Address Fields" , "GGF") ,
					"fields" 	=> apply_filters( 'ggf_field_buttons', $ggf_addres_fields, $field_groups )
			);
		}
			
		return $field_groups;
	}

	/**
	 * Apply title name for GGF fields
	 */
	function fields_title( $type ) {

		if ( $type == 'mapIcons' ) {
			return __( 'GGF Map Icons', 'GFG' );
		}
	}

	/**
	 * GGF function - Create GGF custom form fields 
	 */
	function fields_settings( $position, $form_id ) {

		//locator button field 
		if ( $position == 10 ) {

			?>
			<!-- Locator button fields -->
			
			<li class="ggf-locator-label field_setting ggf-locator-settings">
				<label for="ggf-locator-label"> 
					<?php _e( "Button Label", "GGF"); ?> 
					<?php gform_tooltip("ggf-locator-label_tt"); ?>
				</label> 
				<input type="text" size="35" id="ggf-locator-label" class="" onkeyup="SetFieldProperty('ggfLocatorLabel', this.value);">
			</li>
			
			<li class="ggf-locator-auto-submit field_setting ggf-locator-settings">
				<input type="checkbox" id="ggf-locator-auto-submit" onclick="SetFieldProperty('ggfLocatorAutoSubmit', this.checked);" />
				<label for="ggf-locator-auto-submit" class="inline"> 
					<?php _e( "Automatic Form Submission", "GGF" ); ?> 
					<?php gform_tooltip("ggf_locator_auto_submit_tt"); ?>
				</label>
			</li>
			
			<li class="ggf-locator-hide-submit field_setting ggf-locator-settings">
				<input type="checkbox" id="ggf-locator-hide-submit" onclick="SetFieldProperty('ggfLocatorHideSubmit', this.checked);" />
				<label for="ggf-locator-hide-submit" class="inline"> 
					<?php _e( "Hide Submit Button", "GGF" ); ?> <?php gform_tooltip("ggf_locator_hide_submit_tt"); ?>
				</label>
			</li>
			
			<li class="ggf-locator-found-message-enabeld field_setting ggf-locator-settings">
				<input type="checkbox" id="ggf-locator-found-message-enabled" onclick="SetFieldProperty('ggfLocatorFoundMessageEnabled', this.checked);" />
				<label for="ggf-locator-found-message-enabled" class="inline"> 
					<?php _e( "Enable Location Alert Message", "GGF" ); ?>
					<?php gform_tooltip("ggf_enabled_found_message_tt"); ?>
				</label>
			</li>
			
			<li class="ggf-locator-found-message field_setting ggf-locator-settings">
				<label for="ggf-locator-found-message"> 
					<?php _e( "Location Found Message", "GGF"); ?> 
					<?php gform_tooltip("ggf_locator_found_message_tt"); ?>
				</label> 
				<input type="text" size="35" id="ggf-locator-found-message" class="" onkeyup="SetFieldProperty('ggfLocatorFoundMessage', this.value);">
			</li>
			
		<?php } ?>
		
		<?php if ( $position == 10 ) { ?>
			
			<!--  Map fields -->
			
			<li class="ggf-map-width field_setting ggf-map-settings ">
				<label for="ggf-map-width"> 
					<?php _e( "Map Width", "GGF" ); ?> 
					<?php gform_tooltip("ggf_map_width_tt"); ?>
				</label> 
				<input type="text" id="ggf-map-width" class="" size="15" onkeyup="SetFieldProperty('ggfMapWidth', this.value);">
			</li>
			
			<li class="ggf-map-height field_setting ggf-map-settings ">
				<label for="ggf-map-height"> 
					<?php _e( "Map Height", "GGF" ); ?> 
					<?php gform_tooltip("ggf_map_height_tt"); ?>
				</label> 
				<input type="text" id="ggf-map-height" class="" size="15" onkeyup="SetFieldProperty('ggfMapHeight', this.value);">
			</li>
			
			<li class="ggf-map-latitude field_setting ggf-map-settings ">
				<label for="ggf-map-latitude"> 
					<?php _e( "Latitude", "GGF" ); ?> 
					<?php gform_tooltip("ggf_map_default_lat_tt"); ?>
				</label> 
				<input type="text" id="ggf-map-latitude" class="" size="25" onkeyup="SetFieldProperty('ggfMapLatitude', this.value);">
			</li>
			
			<li class="ggf-map-longitude field_setting ggf-map-settings ">
				<label for="ggf-map-longitude"> 
					<?php _e( "Longitude", "GGF" ); ?> 
					<?php gform_tooltip("ggf_map_default_long_tt"); ?>
				</label> 
				<input type="text" id="ggf-map-longitude" class="" size="25" onkeyup="SetFieldProperty('ggfMapLongitude', this.value);">
			</li>
			
			<li class="ggf-map-type field_setting ggf-map-settings ">
				<label for="ggf-map-type">
					<?php _e( "Map Type", "GGF" ); ?> 
					<?php gform_tooltip("ggf_map_type_tt"); ?>
				</label> 
				<select name="ggf_map_type" id="ggf-map-type" onchange="SetFieldProperty('ggfMapType', jQuery(this).val());">
						<option value="ROADMAP">ROADMAP</option>
						<option value="SATELLITE">SATELLITE</option>
						<option value="HYBRID">HYBRID</option>
						<option value="TERRAIN">TERRAIN</option>
				</select>
			</li>
			
			<li class="ggf-zoom-level field_setting ggf-map-settings">
				<label for="ggf-zoom-level"> 
					<?php _e( "Zoom Level", "GGF" ); ?> 
					<?php gform_tooltip("ggf_map_zoom_level_tt"); ?>
				</label> 
				<select name="ggf_zoom_level" id="ggf-zoom-level" onchange="SetFieldProperty('ggfZoomLevel', jQuery(this).val());">
						<?php $count = 18; ?>
						<?php
						for ( $x=1; $x<=18; $x++ ) {
							echo '<option value="'.$x.'">'. $x .'</option>';
						}
						?>
				</select>
			</li>
			
		<?php } ?>
		
		<?php if ( $position == 50 ) { ?>
		
			<!-- Text field and custom post fields -->
			
			<?php if ( self::$ggf_usage == 3 ) { ?>
				<li class="field_setting ggf-advanced-address-geocode ggf-field-settings">
					<input type="checkbox" id="ggf-advanced-address-geocode" onclick="SetFieldProperty( 'ggf_advanced_address_geocode', this.checked );" /> 
					<label for="ggf-advanced-address-geocode" class="inline"> 
						<?php _e( "Geocode this Field", "GGF" ); ?> 
						<?php gform_tooltip("ggf_advanced_address_geocode"); ?>
					</label>
				</li>

			<?php } ?>
						
			<li class="field_setting ggf-locator-autofill ggf-field-settings">
				<input type="checkbox" id="ggf-locator-fill" onclick="SetFieldProperty( 'ggfLocatorAutoFill', this.checked );" /> 
				<label for="ggf-locator-fill" class="inline"> 
					<?php _e( "Enable Locator Auto-fill", "GGF" ); ?> <?php gform_tooltip("ggf_locator_autofill_tt"); ?>
				</label>
			</li>
			
			<li class="field_setting ggf-map-autofill ggf-field-settings">
				<input type="checkbox" id="ggf-map-autofill" onclick="SetFieldProperty( 'ggfMapAutoFill', this.checked );" /> 
				<label for="ggf-map-autofill" class="inline">
					<?php _e("Enable Map Auto-fill", "GGF"); ?> <?php gform_tooltip("ggf_map_autofill_tt"); ?>
				</label>
			</li>
			
			<li class="field_setting ggf-autocomplete-wrapper ggf-field-settings">
				<input type="checkbox" id="ggf-autocomplete" onclick="SetFieldProperty( 'ggfAutocomplete', this.checked );" />
				<label for="ggf-autocomplete" class="inline"> 
					<?php _e( "Enable Google Address Autocomplete", "GGF" ); ?> <?php gform_tooltip("ggf_autocomplete_tt"); ?>
				</label>
			</li>
				
			<?php if ( self::$ggf_usage == 3 ) { ?>

				<li class="field_setting ggf-aa-autocomplete-placeholder-wrapper ggf-field-settings">
					<label for="ggf-aa-autocomplete-placeholder"> 
						<?php _e( "Placeholder", "GGF"); ?> 
						 <?php gform_tooltip( "ggf_aa_autocomplete_placeholder_tt" ); ?>
					</label> 
					<input type="text" size="35" id="ggf-aa-autocomplete-placeholder" onkeyup="SetFieldProperty('ggfAutocompletePlaceholder', this.value);">
				</li>

				<li class="field_setting ggf-aa-autocomplete-desc-wrapper ggf-field-settings">
					<label for="ggf-aa-autocomplete-desc"> 
						<?php _e( "Field description", "GGF"); ?> 
						 <?php gform_tooltip( "ggf_aa_autocomplete_desc_tt" ); ?>
					</label> 
					<input type="text" size="35" id="ggf-aa-autocomplete-desc" onkeyup="SetFieldProperty('ggfAutocompleteDesc', this.value);">
				</li>

			<?php } ?>

			<li class="field_setting ggf-map-update-wrapper ggf-field-settings">
				<input type="checkbox" id="ggf-update-map" onclick="SetFieldProperty( 'ggfUpdateMap', this.checked );" /> 
				<label for="ggf-update-map" class="inline">
					<?php _e("Update Map Based on autocomplete choice", "GGF"); ?> <?php gform_tooltip("ggf_update_map_tt"); ?>
				</label>
			</li>
					
			<li class="field_setting ggf-autocomplete-country-wrapper ggf-field-settings">
				<label for="post_custom_field_type"> 
					<?php _e( 'Restrict Autocomplete Results','GFG' ); ?>
					<?php gform_tooltip("ggf_autocomplete_country_tt"); ?>
				</label> 
				&#32;&#32;<select name="ggf_autocomplete_country" id="ggf-autocomplete-country"
					class="ggf-autocomplete-country"
					onchange="SetFieldProperty('ggf_autocomplete_country', jQuery(this).val());">
					<option value="">All Countries</option>
					<option value="AF">Afghanistan</option>
					<option value="AX">Aland Islands</option>
					<option value="AL">Albania</option>
					<option value="DZ">Algeria</option>
					<option value="AS">American Samoa</option>
					<option value="AD">Andorra</option>
					<option value="AO">Angola</option>
					<option value="AI">Anguilla</option>
					<option value="AQ">Antarctica</option>
					<option value="AG">Antigua and Barbuda</option>
					<option value="AR">Argentina</option>
					<option value="AM">Armenia</option>
					<option value="AW">Aruba</option>
					<option value="AU">Australia</option>
					<option value="AT">Austria</option>
					<option value="AZ">Azerbaijan</option>
					<option value="BS">Bahamas</option>
					<option value="BH">Bahrain</option>
					<option value="BD">Bangladesh</option>
					<option value="BB">Barbados</option>
					<option value="BY">Belarus</option>
					<option value="BE">Belgium</option>
					<option value="BZ">Belize</option>
					<option value="BJ">Benin</option>
					<option value="BM">Bermuda</option>
					<option value="BT">Bhutan</option>
					<option value="BO">Bolivia, Plurinational State of</option>
					<option value="BQ">Bonaire, Sint Eustatius and Saba</option>
					<option value="BA">Bosnia and Herzegovina</option>
					<option value="BW">Botswana</option>
					<option value="BV">Bouvet Island</option>
					<option value="BR">Brazil</option>
					<option value="IO">British Indian Ocean Territory</option>
					<option value="BN">Brunei Darussalam</option>
					<option value="BG">Bulgaria</option>
					<option value="BF">Burkina Faso</option>
					<option value="BI">Burundi</option>
					<option value="KH">Cambodia</option>
					<option value="CM">Cameroon</option>
					<option value="CA">Canada</option>
					<option value="CV">Cape Verde</option>
					<option value="KY">Cayman Islands</option>
					<option value="CF">Central African Republic</option>
					<option value="TD">Chad</option>
					<option value="CL">Chile</option>
					<option value="CN">China</option>
					<option value="CX">Christmas Island</option>
					<option value="CC">Cocos (Keeling) Islands</option>
					<option value="CO">Colombia</option>
					<option value="KM">Comoros</option>
					<option value="CG">Congo</option>
					<option value="CD">Congo, the Democratic Republic of the</option>
					<option value="CK">Cook Islands</option>
					<option value="CR">Costa Rica</option>
					<option value="CI">Cote d'Ivoire</option>
					<option value="HR">Croatia</option>
					<option value="CU">Cuba</option>
					<option value="CW">Curacao</option>
					<option value="CY">Cyprus</option>
					<option value="CZ">Czech Republic</option>
					<option value="DK">Denmark</option>
					<option value="DJ">Djibouti</option>
					<option value="DM">Dominica</option>
					<option value="DO">Dominican Republic</option>
					<option value="EC">Ecuador</option>
					<option value="EG">Egypt</option>
					<option value="SV">El Salvador</option>
					<option value="GQ">Equatorial Guinea</option>
					<option value="ER">Eritrea</option>
					<option value="EE">Estonia</option>
					<option value="ET">Ethiopia</option>
					<option value="FK">Falkland Islands (Malvinas)</option>
					<option value="FO">Faroe Islands</option>
					<option value="FJ">Fiji</option>
					<option value="FI">Finland</option>
					<option value="FR">France</option>
					<option value="GF">French Guiana</option>
					<option value="PF">French Polynesia</option>
					<option value="TF">French Southern Territories</option>
					<option value="GA">Gabon</option>
					<option value="GM">Gambia</option>
					<option value="GE">Georgia</option>
					<option value="DE">Germany</option>
					<option value="GH">Ghana</option>
					<option value="GI">Gibraltar</option>
					<option value="GR">Greece</option>
					<option value="GL">Greenland</option>
					<option value="GD">Grenada</option>
					<option value="GP">Guadeloupe</option>
					<option value="GU">Guam</option>
					<option value="GT">Guatemala</option>
					<option value="GG">Guernsey</option>
					<option value="GN">Guinea</option>
					<option value="GW">Guinea-Bissau</option>
					<option value="GY">Guyana</option>
					<option value="HT">Haiti</option>
					<option value="HM">Heard Island and McDonald Islands</option>
					<option value="VA">Holy See (Vatican City State)</option>
					<option value="HN">Honduras</option>
					<option value="HK">Hong Kong</option>
					<option value="HU">Hungary</option>
					<option value="IS">Iceland</option>
					<option value="IN">India</option>
					<option value="ID">Indonesia</option>
					<option value="IR">Iran, Islamic Republic of</option>
					<option value="IQ">Iraq</option>
					<option value="IE">Ireland</option>
					<option value="IM">Isle of Man</option>
					<option value="IL">Israel</option>
					<option value="IT">Italy</option>
					<option value="JM">Jamaica</option>
					<option value="JP">Japan</option>
					<option value="JE">Jersey</option>
					<option value="JO">Jordan</option>
					<option value="KZ">Kazakhstan</option>
					<option value="KE">Kenya</option>
					<option value="KI">Kiribati</option>
					<option value="KP">Korea, Democratic People's Republic of</option>
					<option value="KR">Korea, Republic of</option>
					<option value="KW">Kuwait</option>
					<option value="KG">Kyrgyzstan</option>
					<option value="LA">Lao People's Democratic Republic</option>
					<option value="LV">Latvia</option>
					<option value="LB">Lebanon</option>
					<option value="LS">Lesotho</option>
					<option value="LR">Liberia</option>
					<option value="LY">Libya</option>
					<option value="LI">Liechtenstein</option>
					<option value="LT">Lithuania</option>
					<option value="LU">Luxembourg</option>
					<option value="MO">Macao</option>
					<option value="MK">Macedonia, the former Yugoslav Republic of</option>
					<option value="MG">Madagascar</option>
					<option value="MW">Malawi</option>
					<option value="MY">Malaysia</option>
					<option value="MV">Maldives</option>
					<option value="ML">Mali</option>
					<option value="MT">Malta</option>
					<option value="MH">Marshall Islands</option>
					<option value="MQ">Martinique</option>
					<option value="MR">Mauritania</option>
					<option value="MU">Mauritius</option>
					<option value="YT">Mayotte</option>
					<option value="MX">Mexico</option>
					<option value="FM">Micronesia, Federated States of</option>
					<option value="MD">Moldova, Republic of</option>
					<option value="MC">Monaco</option>
					<option value="MN">Mongolia</option>
					<option value="ME">Montenegro</option>
					<option value="MS">Montserrat</option>
					<option value="MA">Morocco</option>
					<option value="MZ">Mozambique</option>
					<option value="MM">Myanmar</option>
					<option value="NA">Namibia</option>
					<option value="NR">Nauru</option>
					<option value="NP">Nepal</option>
					<option value="NL">Netherlands</option>
					<option value="NC">New Caledonia</option>
					<option value="NZ">New Zealand</option>
					<option value="NI">Nicaragua</option>
					<option value="NE">Niger</option>
					<option value="NG">Nigeria</option>
					<option value="NU">Niue</option>
					<option value="NF">Norfolk Island</option>
					<option value="MP">Northern Mariana Islands</option>
					<option value="NO">Norway</option>
					<option value="OM">Oman</option>
					<option value="PK">Pakistan</option>
					<option value="PW">Palau</option>
					<option value="PS">Palestinian Territory, Occupied</option>
					<option value="PA">Panama</option>
					<option value="PG">Papua New Guinea</option>
					<option value="PY">Paraguay</option>
					<option value="PE">Peru</option>
					<option value="PH">Philippines</option>
					<option value="PN">Pitcairn</option>
					<option value="PL">Poland</option>
					<option value="PT">Portugal</option>
					<option value="PR">Puerto Rico</option>
					<option value="QA">Qatar</option>
					<option value="RE">Reunion</option>
					<option value="RO">Romania</option>
					<option value="RU">Russian Federation</option>
					<option value="RW">Rwanda</option>
					<option value="BL">Saint Barthelemy</option>
					<option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
					<option value="KN">Saint Kitts and Nevis</option>
					<option value="LC">Saint Lucia</option>
					<option value="MF">Saint Martin (French part)</option>
					<option value="PM">Saint Pierre and Miquelon</option>
					<option value="VC">Saint Vincent and the Grenadines</option>
					<option value="WS">Samoa</option>
					<option value="SM">San Marino</option>
					<option value="ST">Sao Tome and Principe</option>
					<option value="SA">Saudi Arabia</option>
					<option value="SN">Senegal</option>
					<option value="RS">Serbia</option>
					<option value="SC">Seychelles</option>
					<option value="SL">Sierra Leone</option>
					<option value="SG">Singapore</option>
					<option value="SX">Sint Maarten (Dutch part)</option>
					<option value="SK">Slovakia</option>
					<option value="SI">Slovenia</option>
					<option value="SB">Solomon Islands</option>
					<option value="SO">Somalia</option>
					<option value="ZA">South Africa</option>
					<option value="GS">South Georgia and the South Sandwich Islands</option>
					<option value="SS">South Sudan</option>
					<option value="ES">Spain</option>
					<option value="LK">Sri Lanka</option>
					<option value="SD">Sudan</option>
					<option value="SR">Suriname</option>
					<option value="SJ">Svalbard and Jan Mayen</option>
					<option value="SZ">Swaziland</option>
					<option value="SE">Sweden</option>
					<option value="CH">Switzerland</option>
					<option value="SY">Syrian Arab Republic</option>
					<option value="TW">Taiwan, Province of China</option>
					<option value="TJ">Tajikistan</option>
					<option value="TZ">Tanzania, United Republic of</option>
					<option value="TH">Thailand</option>
					<option value="TL">Timor-Leste</option>
					<option value="TG">Togo</option>
					<option value="TK">Tokelau</option>
					<option value="TO">Tonga</option>
					<option value="TT">Trinidad and Tobago</option>
					<option value="TN">Tunisia</option>
					<option value="TR">Turkey</option>
					<option value="TM">Turkmenistan</option>
					<option value="TC">Turks and Caicos Islands</option>
					<option value="TV">Tuvalu</option>
					<option value="UG">Uganda</option>
					<option value="UA">Ukraine</option>
					<option value="AE">United Arab Emirates</option>
					<option value="GB">United Kingdom</option>
					<option value="US">United States</option>
					<option value="UM">United States Minor Outlying Islands</option>
					<option value="UY">Uruguay</option>
					<option value="UZ">Uzbekistan</option>
					<option value="VU">Vanuatu</option>
					<option value="VE">Venezuela, Bolivarian Republic of</option>
					<option value="VN">Viet Nam</option>
					<option value="VG">Virgin Islands, British</option>
					<option value="VI">Virgin Islands, U.S.</option>
					<option value="WF">Wallis and Futuna</option>
					<option value="EH">Western Sahara</option>
					<option value="YE">Yemen</option>
					<option value="ZM">Zambia</option>
					<option value="ZW">Zimbabwe</option>
				</select>
			</li>
			
			<!-- Location output fields -->
			
			<li class="field_setting ggf-location-output" >
				<label for="ggf-location-output">
					<?php _e( "Output Location Field", "GGF" ); ?><?php gform_tooltip("ggf_location_output_tt"); ?>
				</label> 
				<select name="ggf_location_output" id="ggf-location-output" onchange="SetFieldProperty('ggf_location_output', jQuery(this).val());">
					<option value=""><?php _e( 'N/A', 'GFG' ); ?></option>
					<option value="street_number"><?php _e( 'Street number', 'GFG' ); ?></option>
					<option value="street_name"><?php _e( 'Street name' ); ?></option>
					<option value="street"><?php _e( 'Street ( street number + street name )', 'GFG' ); ?></option>
					<option value="apt"><?php _e( 'Apt #', 'GFG' ); ?></option>
					<option value="city"><?php _e( 'City', 'GFG' ); ?></option>
					<option value="state"><?php _e( 'State ( short-name )', 'GFG' ); ?></option>
					<option value="state_long"><?php _e( 'State ( long-name )', 'GFG' ); ?></option>
					<option value="zipcode"><?php _e( 'Zip-code / Postal-code', 'GFG' ); ?></option>
					<option value="country"><?php _e( 'Country ( short-name )', 'GFG' ); ?></option>
					<option value="country_long"><?php _e( 'Country ( long-name )', 'GFG' ); ?></option>
					<option value="formatted_address"><?php _e( 'Formatted Address', 'GFG' ); ?></option>
					<option value="lat"><?php _e( 'Latitude', 'GFG' ); ?></option>
					<option value="lng"><?php _e( 'Longitude', 'GFG' ); ?></option>
				</select>
			</li>
		<?php
		}//get form details
	}

	/**
	 * GGF buttons tooltips
	 */
	function tooltips($tooltips){

		//locator field tooltips
		$tooltips["ggf-locator-label_tt"]         = __("Enter the Label for this locator button.","GGF");
		$tooltips["ggf_locator_auto_submit_tt"]   = __("Dynamically submit this form once a location successfully found.","GGF");
		$tooltips["ggf_locator_hide_submit_tt"]   = __("Hide the submit button of this form. This feature can be useful when using the locator button to auto-submit the form once a location is found.</h6>","GGF");
		$tooltips["ggf_enabled_found_message_tt"] = __('Enable alert message when the user poisiton was found.','GFG');	
		$tooltips["ggf_locator_found_message_tt"] = __('Enter the message which you would like to be displayed once location is found.','GFG');	
		
		//map fields tooltips
		$tooltips["ggf_map_width_tt"]        = __("Enter the map width in pixels or percentage.","GGF");
		$tooltips["ggf_map_height_tt"] 		 = __("Enter the map height in pixels or percentage.","GGF");
		$tooltips["ggf_map_default_lat_tt"]  = __("Enter the latitude of the initial point that will be displayed on the map.","GGF");
		$tooltips["ggf_map_default_long_tt"] = __("Enter the longitude of the initial point that will be displayed on the map.","GGF");	
		$tooltips["ggf_map_type_tt"]         = __("Choose the map type.","GGF");
		$tooltips["ggf_map_zoom_level_tt"]   = __("Set the zoom level of the map.","GGF");
		
		$tooltips["ggf_locator_autofill_tt"] 	  		= __("Dynamically populate this field with the address found via the auto-locator.","GGF");
		$tooltips["ggf_map_autofill_tt"]    	 	 	= __("Dynamically update this field with the address found once the map is being updated.","GGF");
		$tooltips["ggf_autocomplete_tt"] 		 	 	= __("Apply Google address autocomplete feature to this field. Google address autocomplete displays suggested results while the user is typing an address.","GGF");
		$tooltips["ggf_update_map_tt"]           	 	= __("Update the Marker's location on the map once an address was selected using the address autocomplete.","GGF");		
		$tooltips["ggf_autocomplete_country_tt"]  	 	= __('Restrict Google address autocomplete results to a certain country by choosing the country from the drop-down menu.','GFG');
		$tooltips["ggf_advanced_address_geocode"]    	= __('Check this checkbox if you want this field to be geocoded.','GFG');
		$tooltips["ggf_aa_autocomplete_placeholder_tt"] = __('Enter the placeholder of the address autocomplete text field.','GFG');
		$tooltips["ggf_aa_autocomplete_desc_tt"] 	 	= __('Enter a description that you would liket to show below the address autocomplete text field.','GFG');
		
		$tooltips["ggf_location_output_tt"] = __("Dynamically populate this field with the selected location field after geocoding.","GGF");
		
		return $tooltips;
	}

	/**
	 * Set default labels when adding new field
	 */
	function set_default_labels() {

	   ?>
	   
	    case "ggf_locator" :
	        field.label = "Auto Locator";
	        field.ggfLocatorLabel = "Get my current position";
	        field.ggfLocatorFoundMessageEnabled = true;
	        field.ggfLocatorFoundMessage = "Location found.";
	    break;

	    case "ggf_map" :
	        field.label = "Map";
	        field.ggfMapWidth = "100%";
	        field.ggfMapHeight = "300px";
	        field.ggfZoomLevel = "12";
	        field.ggfMapType = "ROADMAP";
	        field.ggfMapLatitude = "40.7827096";
	        field.ggfMapLongitude = "-73.965309";

	    break;

	    case "ggf_address" :
	        field.label = "Address";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.ggfAutocomplete = true;
	        field.ggfUpdateMap = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_street" :
	        field.label = "Street";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_apt" :
	        field.label = "Apt/House";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_city" :
	        field.label = "City";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_state" :
	        field.label = "State";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_zipcode" :
	        field.label = "Zipcode";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_country" :
	        field.label = "County";
	        field.ggfLocatorAutoFill = true;
	        field.ggfMapAutoFill = true;
	        field.inputType = 'text';
	    break;

	    case "ggf_gmw_phone" :
	        field.label = "Phone";
	        field.inputType = 'text';
	    break;

	    case "ggf_gmw_fax" :
	        field.label = "Fax";
	        field.inputType = 'text';
	    break;

	    case "ggf_gmw_email" :
	        field.label = "Email";
	        field.inputType = 'text';
	    break;

	    case "ggf_gmw_website" :
	        field.label = "Website";
	        field.inputType = 'text';
	    break;

	    <?php
	}

	/**
	 * execute some javascript technicalitites for the field to load correctly
	 */
	function js_editor(){
		?>
		<script type='text/javascript'>
        
			jQuery(document).ready(function($) {

				ggfFieldsArray = ['ggf_map', 'ggf_address', 'ggf_street','ggf_apt','ggf_city','ggf_state','ggf_zipcode','ggf_country', 'ggf_gmw_fax', 'ggf_gmw_phone', 'ggf_gmw_email', 'ggf_gmw_website'];
								
             	//Map icons - Premium settings add-on
             	fieldSettings["mapIcons"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting"; 
				
				//Other Gform fields
                fieldSettings["post_custom_field"] += ", .ggf-location-output, .ggf-autocomplete-wrapper, .ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper";
                fieldSettings["hidden"]	+= ", .css_class_setting, .ggf-location-output";
				fieldSettings["address"] += ', .ggf-advanced-address-geocode, .ggf-locator-autofill, .ggf-map-autofill, .ggf-autocomplete-wrapper, .ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper, .ggf-aa-autocomplete-placeholder-wrapper, .ggf-aa-autocomplete-desc-wrapper';
				fieldSettings["text"] += ", .ggf-location-output, .ggf-autocomplete-wrapper, .ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper";
             	fieldSettings["select"] += fieldSettings["radio"] += fieldSettings["multiselect"] += fieldSettings["checkbox"] += ", .ggf-location-output";

                //This script triggers once form field opens in admin                                 
				jQuery(document).bind("gform_load_field_settings", function(event, field, form){

					//set the field settings
					//locator field
					jQuery("#ggf-locator-label").val(field["ggfLocatorLabel"]);
					jQuery("#ggf-locator-auto-submit").attr("checked", field["ggfLocatorAutoSubmit"] == true);
                    jQuery("#ggf-locator-hide-submit").attr("checked", field["ggfLocatorHideSubmit"] == true);
                    jQuery("#ggf-locator-found-message-enabled").attr("checked", field["ggfLocatorFoundMessageEnabled"] == true);
                    jQuery("#ggf-locator-found-message").val(field["ggfLocatorFoundMessage"]);

					//map field
					jQuery("#ggf-map-width").val(field["ggfMapWidth"]);
					jQuery("#ggf-map-height").val(field["ggfMapHeight"]);
					jQuery("#ggf-map-latitude").val(field["ggfMapLatitude"]);
					jQuery("#ggf-map-longitude").val(field["ggfMapLongitude"]);
                    jQuery("#ggf-map-type").val(field["ggfMapType"]);
                    jQuery("#ggf-zoom-level").val(field["ggfZoomLevel"]);

                    //advanced address field
                    jQuery("#ggf-advanced-address-geocode").attr("checked", field["ggf_advanced_address_geocode"] == true);
                    jQuery("#ggf-aa-autocomplete-placeholder").val(field["ggfAutocompletePlaceholder"]);
                    jQuery("#ggf-aa-autocomplete-desc").val(field["ggfAutocompleteDesc"]);

                    //autocomplete 
                    jQuery("#ggf-autocomplete").attr("checked", field["ggfAutocomplete"] == true);
                    jQuery("#ggf-autocomplete-country").val(field["ggf_autocomplete_country"]);
                    jQuery("#ggf-update-map").attr("checked", field["ggfUpdateMap"] == true);

                    
                    jQuery("#ggf-map-autofill").attr("checked", field["ggfMapAutoFill"] == true);
                    jQuery("#ggf-locator-fill").attr("checked", field["ggfLocatorAutoFill"] == true);				
					jQuery("#ggf-location-output").val(field["ggf_location_output"]);

					//hide different field settings from certain fields
					if ( jQuery.inArray( field.type, ggfFieldsArray ) !== -1 ) {
										
						if ( field.type == 'ggf_address' ) {
							jQuery('li.ggf-location-output').hide(); 
						} else {
			    			jQuery('li.ggf-autocomplete-wrapper, li.ggf-map-update-wrapper, li.ggf-autocomplete-country-wrapper, li.ggf-location-output').hide();
						}
			    	} else if ( field.type == 'text' || field.type == 'post_custom_field' ) {
			    		jQuery('li.ggf-autocomplete-wrapper, li.ggf-map-update-wrapper, li.ggf-autocomplete-country-wrapper, li.ggf-location-output').show();
			    	}
			    	
					//show/hide related Google autocomplete based on checkbox is checked/unchecked - once field is opened
                    if ( $('#ggf-autocomplete').is(":checked") ) { 

                    	if ( field.type == 'address' ) {
                    		jQuery('.ggf-aa-autocomplete-placeholder-wrapper, .ggf-aa-autocomplete-desc-wrapper').show();
                    	}
                    	jQuery('.ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper').show();
                    	jQuery('.ggf-location-output select').attr('disabled', true);

					} else { 
						jQuery('.ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper, .ggf-aa-autocomplete-placeholder-wrapper, .ggf-aa-autocomplete-desc-wrapper').hide();
						jQuery('.ggf-location-output select').attr('disabled', false);
					}

                    //show/hide related Google autocomplete when checkbox is checked/unchecked
					$('#ggf-autocomplete').change(function() {
						if ( jQuery(this).is(":checked") ) { 
							
							if ( field.type == 'address' ) {
                    			jQuery('.ggf-aa-autocomplete-placeholder-wrapper, .ggf-aa-autocomplete-desc-wrapper').show();
                    		}

							jQuery('.ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper').show();
							jQuery('.ggf-location-output select').attr('disabled', true);
						} else { 

							if ( field.type == 'address' ) {
                    			jQuery('.ggf-aa-autocomplete-placeholder-wrapper, .ggf-aa-autocomplete-desc-wrapper').hide();
                    		}

							jQuery('.ggf-map-update-wrapper, .ggf-autocomplete-country-wrapper').hide();
							jQuery('#ggf-update-map').attr("checked", false);
							jQuery('.ggf-location-output select').attr('disabled', false);
						}
					});	

					//show/hide related Google autocomplete based on checkbox is checked/unchecked - once field is opened
                    if ( !$('#ggf-locator-found-message-enabled').is(":checked") ) { 
                    	jQuery('.ggf-locator-found-message').hide();
					}

                    //show/hide related Google autocomplete when checkbox is checked/unchecked
					$('#ggf-locator-found-message-enabled').change(function() {
						if ( jQuery(this).is(":checked") ) { 
							jQuery('.ggf-locator-found-message').show();
						} else { 
							jQuery('.ggf-locator-found-message').hide();
						}
					});	
					
                    jQuery('#ggf-advanced-address-geocode').attr('disabled',false);
                    
                    jQuery.each( form['fields'], function(key,val) {
						if ( val.type == 'address' ) {	
							if ( val.ggf_advanced_address_geocode != undefined && val.ggf_advanced_address_geocode == true && val.id != field.id ) {
								jQuery('#ggf-advanced-address-geocode').attr('disabled',true);
							} 
						} 
					});						
				});
			});
   
		</script>
	<?php 
	}
	
	function render_form( $form ) {	

		?>
		<script type="text/javascript">

			//One field per form
			gform.addFilter( 'gform_form_editor_can_field_be_added', function ( canFieldBeAdded, type ) {

	            if ( jQuery.inArray( type, ['ggf_map', 'ggf_address', 'ggf_street','ggf_apt','ggf_city','ggf_state','ggf_zipcode','ggf_country'] ) !== -1 ) {
	               if (GetFieldsByType([type]).length) {
	                    alert( "Only one "+ type.replace('ggf_', '', type) + " field can be added to the form" );
	                    return false;
	                }
	            }

	            if ( jQuery.inArray( type, ['ggf_gmw_fax', 'ggf_gmw_phone', 'ggf_gmw_email', 'ggf_gmw_website'] ) !== -1 ) {
	               if (GetFieldsByType([type]).length) {
	                    alert( "Only one "+ type.replace('ggf_gmw_', '', type) + " field can be added to the form" );
	                    return false;
	                }
	            }

	            return canFieldBeAdded;
	        });
		</script>
		<?php
		//return the form object from the php hook	
		return $form;
	}	
}
new GFG_Admin_Edit_Form_Page;