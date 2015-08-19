<?php

class AppPresser_Notifications_Settings {

	public function __construct( $api_ready ) {
		$this->api_ready = $api_ready;
	}

	public function hooks() {

		// Add setting rows to Apppresser settings
		add_action( 'apppresser_add_settings', array( $this, 'notifications_settings' ), 30 );
		add_filter( 'apppresser_field_override_notifications_post_types', array( $this, 'notifications_post_types' ), 10, 4 );
		add_filter( 'apppresser_sanitize_setting_notifications_post_types', array( $this, 'sanitize_array_values' ),10, 2 );
	}

	// Notifications settings Settings
	public function notifications_settings( $appp ) {

		$appp->add_setting_tab( __( 'Notifications', 'apppresser-push' ), 'appp-notifications' );

		$appp->add_setting( AppPresser_Notifications::APPP_KEY, __( 'AppPush License Key', 'apppresser-push' ), array( 'type' => 'license_key', 'tab' => 'appp-notifications', 'helptext' => __( 'Adding a license key enables automatic updates.', 'appp-notifications' ) ) );

		$appp->add_setting( 'notifications_pushwoosh_app_key', __( 'Pushwoosh App Code', 'apppresser-push' ), array(
			'tab' => 'appp-notifications',
			'helptext' => __( 'Your Pushwoosh Application Code.', 'apppresser-push' ),
			'description' => '00000-00000',
		) );

		$appp->add_setting( 'notifications_pushwoosh_api_key', __( 'Pushwoosh API Token', 'apppresser-push' ), array(
			'tab' => 'appp-notifications',
			'helptext' => __( 'Your Pushwoosh API Access token.', 'apppresser' ),
		) );

		$appp->add_setting( 'notifications_gcm_sender', __( 'Google API Project Number', 'apppresser-push' ), array(
			'tab' => 'appp-notifications',
			'helptext' => __( 'Android only. Project number from your Google Developers Console', 'apppresser-push' ),
		) );

		$appp->add_setting( 'notifications_title', __( 'Notification Title', 'apppresser-push' ), array(
			'tab' => 'appp-notifications',
			'helptext' => __( 'The title on each notification, usually your App Name.', 'apppresser' ),
			'type' => 'text'
		) );

		if ( $this->api_ready ) {
			$appp->add_setting( 'notifications_post_types', __( 'Push Post Types', 'apppresser-push' ), array(
				'type' => 'notifications_post_types',
				'tab' => 'appp-notifications',
				'helptext' => __( 'Choose Post Types that can send Push Notifications', 'apppresser-push' ),
			) );
		}

	}

	public function notifications_post_types( $field, $key, $value, $args ) {

		$post_types    = get_post_types( array(), 'objects' );
		$exclude_types = array( 'attachment', 'revision', 'nav_menu_item', AppPresser_Notifications::$cpt );
		$saved         = appp_get_setting( 'notifications_post_types' );

		foreach ( $post_types as $post_type => $object ) {
			if ( ! in_array( $post_type, $exclude_types ) ) {

				$checked = is_array( $saved ) && in_array( $post_type, $saved, true );
				$field .= '<label><input '. checked( $checked, 1, 0 ).' type="checkbox" name="appp_settings[notifications_post_types][]" value="'. esc_attr( $post_type ) .'">&nbsp;'. $object->labels->name .'</label><br>'."\n";
			}
		}

		return $field;

	}

	public function sanitize_array_values( $empty, $value ) {
		// Sanitize
		foreach ( $value as $key => $val ) {
			$value[ $key ] = sanitize_text_field( $val );
		}
		return $value;
	}

}
