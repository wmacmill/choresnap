<?php

/**
 * AppBuddy_Notifications class.
 */
class AppBuddy_Notifications {


	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		@$this->is_loggedin = is_user_logged_in();
		$this->hooks();
	}


	/**
	 * hooks function.
	 *
	 * @access public
	 * @return void
	 */
	public function hooks() {
		
		if ( isset( $this->is_loggedin) && class_exists( 'AppPresser_Notifications' ) && class_exists( 'AppPresser_Notifications_Update' ) && appp_get_setting( 'apppush_appbuddy' ) ) {
			add_action( 'messages_message_sent', array( $this, 'send_notification_message' ),999 ,1 );
			add_action( 'bp_friends_sent_request_email', array( $this, 'send_friend_request' ),999 ,5 );
			add_action( 'bp_activity_sent_mention_email', array( $this, 'send_mention' ),1 ,5 );
			
		}

	}


	/**
	 * send_notification_message function.
	 *
	 * Sends a push notification for any email sent via messages
	 *
	 * @todo turn this into toolbar button api
	 * @access public
	 * @return array
	 */
	public function send_notification_message( $message ) {
		$push = new AppPresser_Notifications_Update;
		$devices = $push->get_devices_by_user_id( $message->recipients );
		$push->notification_send( 'now', $message->subject, 1, $devices );
	}

	/**
	 * send_friend_request function.
	 *
	 * Sends a push notification for any friend request
	 *
	 * @todo turn this into toolbar button api
	 * @access public
	 * @return array
	 */
	public function send_friend_request( $friend_id, $subject, $message, $friendship_id, $initiator_id ) {
		$push = new AppPresser_Notifications_Update;
		$devices = $push->get_devices_by_user_id( array( $friend_id ) );
		$push->notification_send( 'now', $subject, 1, $devices);
	}

	/**
	 * send_mention function.
	 *
	 * Sends a push notification for any @mention
	 *
	 * @todo turn this into toolbar button api
	 * @access public
	 * @return array
	 */
	public function send_mention( $activity, $subject, $message, $content, $receiver_user_id ) {
		$push = new AppPresser_Notifications_Update;
		$devices = $push->get_devices_by_user_id( array( $receiver_user_id ) );
		$push->notification_send( 'now', $subject, 1, $devices );
	}


	/**
	 * status_modal_button function.
	 *
	 * Adds button right toolbar button hook.
	 *
	 * @access public
	 * @param array $args (default: array())
	 * @return void
	 */
	public function status_modal_button() {
	?>
		<nav id="top-menu3" class="top-menu pull-right" role="navigation">
			<?php do_action('toolbar_button_right'); ?>
		</nav>
	<?php
	}

	public function attach_image_input() {
	?>
		<input type="hidden" id="attach-image" name="attach-image" value="">
	<?php
	}

	/**
	 * get_status_modal_button function.
	 *
	 * @access public
	 * @param array $args (default: array())
	 * @return void
	 */
	public function get_status_modal_button( $args = array() ) {

		// need defaults here
		$this->args = wp_parse_args( $args, array(
				'button_class' => '',
				'icon_class'   => '',
				'button_text'  => '',
				'url'          => '',
			) );

		wp_enqueue_script( 'appbuddy' );
		//wp_enqueue_script( 'heartbeat' );

		$button = apply_filters( 'appbuddy_modal_button', sprintf( '<a class="%s" href="%s"><i class="%s"></i> %s</a>', $this->args['button_class'], $this->args['url'], $this->args['icon_class'], $this->args['button_text'] ) );

		return $button;
	}


}
$AppBuddy_Notifications = new AppBuddy_Notifications();