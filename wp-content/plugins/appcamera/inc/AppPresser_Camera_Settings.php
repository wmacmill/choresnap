<?php

class AppPresser_Camera_Settings extends AppPresser_Camera {

	public static $moderation_slug = 'apppresser_sub_photo_moderation';
	public static $photos          = null;
	public $is_106;

	/**
	 * Setup our hooks
	 * @since 1.0.0
	 */
	public function hooks() {

		// Add setting rows to Apppresser settings
		add_action( 'apppresser_add_settings', array( $this, 'appcam_settings' ) );
		add_action( 'init', array( $this, 'photo_moderation_check' ) );
		add_filter( 'apppresser_woocom_gallery_ids', array( $this, 'moderate_gallery_images' ) );
		add_action( 'wp_ajax_image_approval_handler', array( $this, 'image_approval_handler' ) );
		// Only run filter on display
		if ( ! defined( 'APPP_IMPORTING' ) || false == 'APPP_IMPORTING' )
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'moderate_gallery_images_admin' ), 10, 2 );

		$this->is_106 = version_compare( AppPresser::VERSION, '1.0.6' ) >= 0;
		if ( $this->is_106 ) {
			add_filter( 'apppresser_notifications', array( $this, 'photo_count' ) );
		}
	}

	/**
	 * AppCam Settings Tab
	 * @since  1.0.0
	 */
	public function appcam_settings( $appp ) {

		$appp->add_setting_tab( __( 'Camera', 'apppresser-camera' ), 'appp-cam' );
		// $roles = array( 'None', 'Subscriber', 'Contributor', 'Author', 'Editor', 'Administrator' );
		// $appp->add_setting( 'photo_upload_minimum_role', __( 'Minimum role to upload photos', 'apppresser-camera' ), array( 'type' => 'checkbox', 'options' => $roles ) );
		$appp->add_setting( self::APPP_KEY, __( 'Camera License Key', 'apppresser-camera' ), array( 'type' => 'license_key', 'tab' => 'appp-cam', 'helptext' => __( 'Adding a license key enables automatic updates.', 'apppresser-camera' ) ) );
		$appp->add_setting( 'photo_upload_moderation', __( 'Uploaded photos must be moderated', 'apppresser-camera' ), array( 'type' => 'checkbox', 'tab' => 'appp-cam', 'helptext' => __( 'Check this if you want to review all photo uploads before they are displayed to users.', 'apppresser-camera' ) ) );
		$appp->add_setting( 'photo_upload_email', __( 'Email new photos to admin email', 'apppresser-camera' ), array( 'type' => 'checkbox', 'tab' => 'appp-cam', 'helptext' => __( 'Check this if you want to be notified by email whenever a user submits a new photo.', 'apppresser-camera' ) ) );
		$appp->add_setting( 'upload_to_feat_img', __( 'Save Photos to Featured Image', 'apppresser-camera' ), array( 'type' => 'checkbox', 'tab' => 'appp-cam', 'helptext' => __( 'If this is not checked, and the action=new shortcode parameter is used, the image will be saved to the new post\'s content instead.', 'apppresser-camera' ), 'description' => sprintf( __( 'This only applies if using the \'action="new"\' %s.', 'apppresser-camera' ), '<a href="http://apppresser.com/docs/extensions/appcamera/" target="_blank">'. __( 'shortcode parameter', 'apppresser-camera' ) .'</a>' ) ) );
		$appp->add_setting( 'photo_upload_description', __( 'Photo upload description', 'apppresser-camera' ), array( 'tab' => 'appp-cam', 'helptext' => __( 'This is the description of the fields that will be displayed to logged in users.', 'apppresser-camera' ) ) );
		$appp->add_setting( 'photo_upload_not_logged_in', __( 'Text to display if logged out', 'apppresser-camera' ), array( 'tab' => 'appp-cam', 'helptext' => __( 'This is the text that will be shown to logged out users, who can not upload until logged in.', 'apppresser-camera' ) ) );

		if ( self::get_photos() )
			add_action( 'apppresser_tab_buttons_appp-cam', array( $this, 'photo_moderation_link' ) );
	}

	/**
	 * Moderation page url wrapped in link markup
	 * @since  1.0.0
	 */
	public function photo_moderation_link() {
		echo '<a href="'. $this->photo_moderation_url() .'">'. __( 'Photo Moderation Panel', 'apppresser-camera' ) .'</a>';
	}

	/**
	 * Moderation page url
	 * @since  1.0.0
	 */
	public function photo_moderation_url() {
		return esc_url( add_query_arg( 'page', self::$moderation_slug, admin_url( 'admin.php' ) ) );
	}

	/**
	 * Add moderation menu/page and perform moderation check
	 * @since  1.0.0
	 */
	public function photo_moderation_check() {
		if ( appp_get_setting( 'photo_upload_moderation' ) == 'on' ) {
			add_action( 'admin_menu', array( $this, 'photo_moderation_plugin_menu' ) );
			$this->handle_photo_moderation();
		}
	}

	/**
	 * Count of the pending photos
	 * @since  1.0.3
	 *
	 * @param int $count  Current count of pending photos
	 * @return int        Number of photos pending moderation + any existing notifications.
	 */
	public function photo_count( $count = 0 ) {
		$photos        = self::get_photos();
		$pending_count = ! empty( $photos ) ? count( $photos ) : 0;
		return $count + $pending_count;
	}

	/**
	 * Moderation Menu
	 * @since  1.0.0
	 */
	public function photo_moderation_plugin_menu() {

		// If we have pending photos to be moderated, add a count bubble to the submenu page item
		$settings_slug = AppPresser_Admin_Settings::$page_slug;
		$pending_count = $this->photo_count();
		$parent_slug   = $pending_count ? $settings_slug : null;

		// Add moderation badge to submenu if we have photos to moderate
		$bubble_count = sprintf( ' <span class="update-plugins count-%d"><span class="plugin-count">%s</span></span>', $pending_count, number_format_i18n( $pending_count ) );

		add_submenu_page( $parent_slug, __( 'Moderate Photos', 'apppresser-camera' ), __( 'Moderate Photos', 'apppresser-camera' ) . $bubble_count, 'manage_options', self::$moderation_slug, array( $this, 'photo_moderation_page' ) );

		// Don't bother if no pending count, or if apppresser core is greater than 1.0.6 (has hooks in place)
		if ( ! $pending_count || $this->is_106 )
			return;

		global $menu;
		// Add the count bubble to our top level menu as well
		foreach ( $menu as $menu_key => $menu_item ) {
			if ( isset( $menu_item[2] ) && $settings_slug == $menu_item[2] ) {
				$menu[ $menu_key ][0] = $menu_item[0] . $bubble_count;
			}
		}

	}

	/**
	 * Moderation Page
	 * @since  1.0.0
	 */
	public function photo_moderation_page() {
		require_once( self::$plugin_dir .'inc/moderation.php' );
	}

	/**
	 * Build moderation model JSON objects
	 * @since  1.0.3
	 */
	public function moderationJSON( $attachments ) {
		if ( empty( $attachments ) )
			return array();

		$json = array();
		foreach ( $attachments as $key => $attachment_id ) {
			// Get attachment
			$attachmemnt = get_post( $attachment_id );
			$json[] = array(
				// Build our model data
				'id' => $attachment_id,
				'url' => wp_get_attachment_url( $attachment_id ),
				'date' => mysql2date( __( 'Y/m/d', 'apppresser-camera' ), $attachmemnt->post_date ),
				'mime' => preg_match( '/^.*?\.(\w+)$/', get_attached_file( $attachment_id ), $matches )
					? esc_html( strtoupper( $matches[1] ) )
					: strtoupper( str_replace( 'image/', '', get_post_mime_type() ) ),
				'title' => $title = get_the_title( $attachment_id ),
				'thumb' => wp_get_attachment_image( $attachment_id, 'thumbnail', false, array( 'title' => $title ) ),
				'editLink' => get_edit_post_link( $attachment_id ),
				'authorURL' => esc_url( add_query_arg( array( 'author' => $attachmemnt->post_author ), 'upload.php' ) ),
				'authorName' => get_the_author_meta( 'user_nicename' , $attachmemnt->post_author ),
				'parentTitle' => get_the_title( $attachmemnt->post_parent ),
				'parentEditURL' => get_edit_post_link( $attachmemnt->post_parent ),
			);
		}
		// Send our models data
		return $json;
	}

	/**
	 * Ajax handling of photo approval
	 * @since  1.0.3
	 */
	public function image_approval_handler() {

		$request = $_REQUEST;

		if ( isset( $request['action'] ) ) {
			unset( $request['action'] );
		}

		if ( 'DELETE' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_REQUEST['id'], $_REQUEST['approved'] ) ) {
			wp_send_json_error( $request );
		}

		$attachment_id = absint( $_REQUEST['id'] );
		$approval      = in_array( $_REQUEST['approved'], array( 'deny', 'approve' ) ) ? $_REQUEST['approved'] : false;

		// Check for proper data, deny or approve
		if ( $approval ) {
			// wp_send_json_success( $request );

			// Update moderation queue option
			$this->update_moderation_queue( $attachment_id );

			// Handle the moderation and report back
			$success = $this->handle_moderation( $attachment_id, 'approve' == $approval );

			// If all went well, report success
			if ( $success ) {
				wp_send_json_success( $attachment_id );
			}
		}

		wp_send_json_error( $request );

	}

	/**
	 * Moderation handling via email and form buttons
	 * @since  1.0.0
	 */
	public function handle_photo_moderation() {

		$is_admin = ( isset( $_POST['appp_handle_photos'] ) && is_admin() );
		$is_email = ( isset( $_GET['appp_approve'] ) || isset( $_GET['appp_deny'] ) );
		if ( ! $is_admin && ! $is_email )
			return;

		$attachments = self::get_photos();
		$attachments = is_array( $attachments ) ? $attachments : array();

		$key = $email_action = false;
		if ( isset( $_POST['appp_handle_photos'] ) ) {
			if ( ! isset( $_POST['appp_photos'] ) )
				return;
			$post_attachments = $_POST['appp_photos'];
		} elseif ( isset( $_GET['appp_approve'] ) ) {
			$key = $_GET['appp_approve'];
			$email_action = 'approved';
			$email_action_text = __( 'approved', 'apppresser-camera' );
		} elseif ( isset( $_GET['appp_deny'] ) ) {
			$key = $_GET['appp_deny'];
			$email_action = 'denied';
			$email_action_text = __( 'denied', 'apppresser-camera' );
		}

		// if key then get attachment_id in array
		if ( $key && isset( $attachments[ $key ] ) && $attachments[ $key ] )
			$post_attachments = array( $attachments[ $key ] );

		// if no attachments to process, bail
		if ( ! is_array( $post_attachments ) )
			return;

		// delete attachment/denied?
		$do_delete = ! ( ! isset( $_POST['appp_handle_photos'] ) || $_POST['appp_handle_photos'] != 'Deny' && $email_action != 'denied' );

		foreach ( $post_attachments as $attachment_id ) {
			$this->handle_moderation( $attachment_id, ! $do_delete );
		}

		// update moderation attachments
		$attachments = array_diff( $attachments, $post_attachments );
		update_option( 'appp_moderation_photos', $attachments );
		self::$photos = $attachments;

		if ( $key ) {
			// display message if email approved/denied
			echo '<h3>'. sprintf( __( 'You %s the following photo:', 'apppresser-camera' ), $email_action_text ) .'</h3>' . wp_get_attachment_image( $attachment_id, 'thumbnail' );
			exit();
		}

		wp_redirect( $_SERVER['REQUEST_URI'] );

	}

	/**
	 * Handle the moderation and report back
	 * @since  1.0.3
	 * @param  int   $attachment_id ID of attachment to moderate
	 * @param  bool  $approve       Approve or Deny
	 * @return bool                 Success or fail
	 */
	public function handle_moderation( $attachment_id, $approve = true ) {

		if ( $approve ) {
			// Change post status to publish for parent
			return $this->maybe_publish_parent( $attachment_id );
		}

		// Update woo gallery if AppWoo installed
		$this->update_woo_gallery( $attachment_id );
		// Delete parent post
		$this->maybe_delete_parent( $attachment_id );
		// Delete the attachment
		return wp_delete_post( $attachment_id, 1 );
	}

	/**
	 * Delete parent post when photo denied
	 * @since  1.0.1
	 * @param  int   $attachment_id ID of attachment to moderate
	 * @return bool                 Success or fail
	 */
	public function maybe_delete_parent( $attachment_id ) {
		$parent = get_post_ancestors( $attachment_id );

		if ( ! isset( $parent[0] ) )
			return false;
		
		// Check if post is a draft, that means we used the camera to create a new post
		$status = get_post_status( $parent[0] );
		
		if ( $status == 'draft' ) {
			// if post is a draft, delete it
			return wp_delete_post( absint( $parent[0] ), 1 );
		} else {
			// otherwise don't delete, because we'd delete the page with the camera button on it
			return false;
		}
	}

	/**
	 * Publish parent post when photo approved
	 * @since  1.0.1
	 * @param  int   $attachment_id ID of attachment to moderate
	 * @return bool                 Success or fail
	 */
	public function maybe_publish_parent( $attachment_id ) {
		$parent = get_post_ancestors( $attachment_id );
		if ( ! isset( $parent[0] ) )
			return false;

		return wp_update_post( apply_filters( 'appp_moderate_maybe_publish', array(
			'ID' => absint( $parent[0] ),
			'post_status' => 'publish',
		) ) );
	}

	/**
	 * Updates moderation queue option array
	 * @since  1.0.3
	 * @param  int   $attachment_id ID of attachment to moderate
	 * @return bool                 Success or fail
	 */
	public function update_moderation_queue( $attachment_id ) {
		// Get the queue
		$queue = get_option( 'appp_moderation_photos', array() );
		// Get the key of the ID to remove
		$this_id = array_search( $attachment_id, $queue );

		if ( false !== $this_id ) {
			// Remove this ID from the queue
			unset( $queue[ $this_id ] );
			// and update the option
			return update_option( 'appp_moderation_photos', $queue );
		}

		return false;
	}

	/**
	 * Updates moderated woocommerce display meta
	 * @since  1.0.0
	 * @param  int   $attachment_id ID of attachment to moderate
	 * @return bool                 Success or fail
	 */
	public function update_woo_gallery( $attachment_id ) {

		// check if AppWoo is installed & remove from product gallery
		if ( ! class_exists( 'AppPresser_WooCommerce' ) )
			return;

		$parent = get_post_ancestors( $attachment_id );
		if ( ! isset( $parent[0] ) || get_post_type( $parent[0] ) != 'product' )
			return;

		$post_id = $parent[0];
		$attachment_ids = get_post_meta( $post_id, '_product_image_gallery', 1 );
		if ( ! $attachment_ids )
			return;

		$attachment_ids = explode( ',', $attachment_ids );
		if ( ! is_array( $attachment_ids ) || ! in_array( $attachment_id, $attachment_ids ) )
			return;

		$attachment_ids = array_diff( $attachment_ids, array( $attachment_id ) );
		$attachment_ids = implode( ',', $attachment_ids );
		return update_post_meta( $post_id, '_product_image_gallery', $attachment_ids  );
	}

	/**
	 * Checks moderated woocommerce display and modfies display of gallery
	 * @since  1.0.0
	 * @param  array $ids IDs of product gallery attachments
	 * @return array      Modified IDs of product gallery attachments
	 */
	public function moderate_gallery_images( $ids ) {
		// if moderation turned on, moderate
		if ( appp_get_setting( 'photo_upload_moderation' ) == 'on' ) {

			$attachments = self::get_photos();
			// Only include $attachment ids that aren't being moderated.
			if ( is_array( $ids ) && is_array( $attachments ) )
				return array_diff( $ids, $attachments );
		}
		return $ids;
	}

	/**
	 * Add attachment attributes when moderated
	 * @since  1.0.0
	 * @param  array  $attr       Attachment attributes
	 * @param  object $attachment Attachment object
	 * @return array              Modified attributes
	 */
	public function moderate_gallery_images_admin( $attr, $attachment ) {
		// if moderation turned on, moderate
		if ( appp_get_setting( 'photo_upload_moderation' ) == 'on' ) {

			$attachments = self::get_photos();
			// Only include $attachment ids that aren't being moderated.
			if ( is_array( $attachments ) && in_array( $attachment->ID, $attachments ) ) {
				// If in moderation, give them a moderation class
				$attr['class'] = $attr['class'] . ' needs-moderating';
				$attr['title'] = __( 'Image is in moderation queue', 'apppresser-camera' );
				add_action( 'admin_footer', array( $this, 'footer_style_script' ) );
			}
		}

		return $attr;
	}

	/**
	 * Custom styling if Woo images are in moderation
	 * @since  1.0.0
	 */
	public function footer_style_script() {
		?>
		<style type="text/css">
		#product_images_container .needs-moderating { opacity: .4; }
		#product_images_container .needs-moderating:hover { opacity: 1; }
		.moderation-link { padding: 0 9px; }
		</style>
		<script type="text/javascript">
		(function(window, document, $, undefined){

			var $context = $( '#product_images_container' );
			var $needsmod = $( '.needs-moderating', $context );
			var html = '<p class="moderation-link"><a href="<?php echo $this->photo_moderation_url(); ?>">'+ $needsmod.length +' <?php _e( "photos are awaiting moderation", "apppresser-camera" ); ?></a></p>';
			$context.parent().append( html );

		})(window, document, jQuery);
		</script>
		<?php
	}

	/**
	 * get_option wrapper that stores data per session
	 * @since  1.0.0
	 * @return mixed  Array of attachments or null
	 */
	public static function get_photos() {
		// if ( 'valid' !== appp_get_setting( self::APPP_KEY .'_status' ) )
		// 	return false;
		if ( 'on' !== appp_get_setting( 'photo_upload_moderation' ) )
			return false;
		if ( self::$photos === null ) {
			self::$photos = get_option( 'appp_moderation_photos' );
		}
		return self::$photos;
	}

}
