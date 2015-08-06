<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

$ggf_usage     = $form[0]['ggf_settings']['address_fields']['use'];
$ggf_gmw_usage = ( !empty( $form[0]['ggf_settings']['address_fields']['gmw']['use'] ) ) ? true : false;

/**
 * Locator button
 */
class GF_Field_GFG_auto_Locator extends GF_Field {

	public $type = 'ggf_locator';

	/**
	 * Button fields and group
	 * @param [type] $field_groups [description]
	 */
	public function add_button( $field_groups ) {

		//locator button
		$ggf_geolocation_fields[] = array(
				'class' 	=> 'button',
				"data-type" => "ggf_locator",
				'value' 	=> __( "Auto Locator", "GGF" ),
				'onclick' 	=> "StartAddField('ggf_locator');"
		);

		//geolocation fields group
		$field_groups[] = array(
				"name" 		=> "ggf_geolocation_fields",
				"label"		=> __( "GEO Fields - Geolocation Fields" , "GGF"),
				"fields" 	=> apply_filters( 'ggf_field_buttons', $ggf_geolocation_fields, $field_groups )
		);
		
		return $field_groups;
	}

	//Field title
	public function get_form_editor_field_title() {
		return __( 'Auto Locator Button ( GEO Field )', 'GFG' );
	}

	//field settings
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'label_setting',
			'ggf-locator-settings', 
			'description_setting', 
			'css_class_setting', 
			'visibility_setting'
		);
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function allow_html() {
		return false;
	}
}
GF_Fields::register( new GF_Field_GFG_auto_Locator() );

/**
 * Map field
 */
class GF_Field_GFG_Map extends GF_Field {

	public $type = 'ggf_map';

	//field label
	public function get_form_editor_field_title() {
		return __( 'Map ( GEO Field )', 'GFG' );
	}

	//button
	public function get_form_editor_button() {
		return array(
			'group' => 'ggf_geolocation_fields',
			'text'  => __( 'Map', 'GFG' )
		);
	}

	//settings
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'ggf-map-settings',
			'label_setting', 
			'description_setting',
			'css_class_setting',
			'visibility_setting'
		);
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function allow_html() {
		return false;
	}
}
GF_Fields::register( new GF_Field_GFG_map() );


/**
 * Single Address fields
 */
if ( $ggf_usage == 1 ) {

	class GF_Field_GFG_Full_Address extends GF_Field {

		public $type = 'ggf_address';

		/**
		 * Button fields and group
		 * @param [type] $field_groups [description]
		 */
		public function add_button( $field_groups ) {

			//address field button
			$ggf_address_fields[] = array(
					'class' 	=> 'button',
					"data-type" => "ggf_address",
					'value' 	=> __( "Address", "GGF" ),
					'onclick' 	=> "StartAddField('ggf_address');"
			);

			//address field group
			$field_groups[] = array(
					"name" 		=> "ggf_address_fields",
					"label"		=> __( "GEO Fields - Geocoded Address Fields" , "GGF" ) ,
					"fields" 	=> apply_filters( 'ggf_address_fields_group', $ggf_address_fields, $field_groups )
			);
			
			return $field_groups;
		}

		//field title
		public function get_form_editor_field_title() {
			return __( 'Address ( GEO Field )', 'GFG' );
		}

		//settings
		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting',
				'ggf-autocomplete-wrapper', 
				'ggf-map-update-wrapper', 
				'ggf-autocomplete-country-wrapper'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_Full_Address() );
}

/**
 * Multiple address fields
 */
if ( $ggf_usage == 2 ) {

	//street
	class GF_Field_GFG_Street extends GF_Field {

		public $type = 'ggf_street';

		/**
		 * Button fields and group
		 * @param [type] $field_groups [description]
		 */
		public function add_button( $field_groups ) {

			//address field button
			$ggf_address_fields[] = array(
					'class' 	=> 'button',
					"data-type" => "ggf_street",
					'value' 	=> __( "Street", "GGF" ),
					'onclick' 	=> "StartAddField('ggf_street');"
			);

			//address field group
			$field_groups[] = array(
					"name" 		=> "ggf_address_fields",
					"label"		=> __( "GEO Fields - Geocoded Address Fields" , "GGF") ,
					"fields" 	=> apply_filters( 'ggf_address_fields_group', $ggf_address_fields, $field_groups )
			);
			
			return $field_groups;
		}

		public function get_form_editor_field_title() {
			return __( 'Street ( GEO Field )', 'GFG' );
		}

		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_Street() );

	//apt
	class GF_Field_GFG_Apt extends GF_Field {

		public $type = 'ggf_apt';

		public function get_form_editor_field_title() {
			return __( 'Apt/House ( GEO Field )', 'GFG' );
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_address_fields',
				'text'  => __( 'Apt/House', 'GFG' )
			);
		}

		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_Apt() );

	//city
	class GF_Field_GFG_City extends GF_Field {

		public $type = 'ggf_city';

		public function get_form_editor_field_title() {
			return __( 'City ( GEO Field )', 'GFG' );
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_address_fields',
				'text'  => __( 'City', 'GFG' )
			);
		}

		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_City() );

	//state
	class GF_Field_GFG_State extends GF_Field {

		public $type = 'ggf_state';

		public function get_form_editor_field_title() {
			return __( 'State ( GEO Field )', 'GFG' );
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_address_fields',
				'text'  => __( 'State', 'GFG' )
			);
		}

		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_State() );

	//zipcode
	class GF_Field_GFG_Zipcode extends GF_Field {

		public $type = 'ggf_zipcode';

		public function get_form_editor_field_title() {
			return __( 'Zipcode ( GEO Field )', 'GFG' );
		}

		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_address_fields',
				'text'  => __( 'Zipcode', 'GFG' )
			);
		}

		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_Zipcode() );

	//country
	class GF_Field_GFG_Country extends GF_Field {

		public $type = 'ggf_country';

		public function get_form_editor_field_title() {
			return __( 'Country ( GEO Field )', 'GFG' );
		}

		/**
		 * Add field button
		 * @return [type] [description]
		 */
		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_address_fields',
				'text'  => __( 'Country', 'GFG' )
			);
		}

		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'ggf-locator-autofill', 
				'ggf-map-autofill',
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_Country() );
}

//gmw contact fields
if ( $ggf_gmw_usage && class_exists( 'GEO_my_WP' ) ) {

	//phone
	class GF_Field_GFG_GMW_Phone extends GF_Field {

		public $type = 'ggf_gmw_phone';

		/**
		 * Button fields and group
		 * @param [type] $field_groups [description]
		 */
		public function add_button( $field_groups ) {

			//field button
			$ggf_gmw_fields[] = array(
					'class' 	=> 'button',
					"data-type" => "ggf_gmw_phone",
					'value' 	=> __( "Phone", "GGF" ),
					'onclick' 	=> "StartAddField('ggf_gmw_phone');"
			);

			//GMW contact fields group
			$field_groups[] = array(
					"name" 		=> "ggf_gmw_fields",
					"label"		=> __( "GEO Fields - GMW Contact Fields" , "GGF" ) ,
					"fields" 	=> apply_filters( 'ggf_gmw_buttons', $ggf_gmw_fields, $field_groups )
			);

			return $field_groups;
		}

		//Field title
		public function get_form_editor_field_title() {
			return __( 'GEO my WP Phone', 'GFG' );
		}

		//field settings
		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_GMW_Phone() );

	//fax
	class GF_Field_GFG_GMW_Fax extends GF_Field {

		public $type = 'ggf_gmw_fax';

		//Field title
		public function get_form_editor_field_title() {
			return __( 'GEO my WP Fax', 'GFG' );
		}

		/**
		 * Add field button
		 * @return [type] [description]
		 */
		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_gmw_fields',
				'text'  => __( 'Fax', 'GFG' )
			);
		}

		//field settings
		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_GMW_Fax() );

	//email
	class GF_Field_GFG_GMW_Email extends GF_Field {

		public $type = 'ggf_gmw_email';

		//Field title
		public function get_form_editor_field_title() {
			return __( 'GEO my WP Email', 'GFG' );
		}

		/**
		 * Add field button
		 * @return [type] [description]
		 */
		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_gmw_fields',
				'text'  =>  __( 'Email', 'GFG' )
			);
		}

		//field settings
		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_GMW_Email() );

	//Website
	class GF_Field_GFG_GMW_Website extends GF_Field {

		public $type = 'ggf_gmw_website';

		//Field title
		public function get_form_editor_field_title() {
			return __( 'GEO my WP Website', 'GFG' );
		}

		/**
		 * Add field button
		 * @return [type] [description]
		 */
		public function get_form_editor_button() {
			return array(
				'group' => 'ggf_gmw_fields',
				'text'  => __( 'Website', 'GFG' )
			);
		}

		//field settings
		function get_form_editor_field_settings() {
			return array(
				'post_custom_field_unique', 
				'post_custom_field_type_setting', 
				'post_custom_field_setting', 
				'conditional_logic_field_setting', 
				'prepopulate_field_setting', 
				'error_message_setting', 
				'label_setting', 
				'label_placement_setting', 
				'admin_label_setting', 
				'size_setting', 
				'rules_setting', 
				'visibility_setting', 
				'duplicate_setting', 
				'description_setting', 
				'css_class_setting'
			);
		}

		public function is_conditional_logic_supported() {
			return true;
		}

		public function allow_html() {
			return false;
		}
	}

	GF_Fields::register( new GF_Field_GFG_GMW_Website() );
}