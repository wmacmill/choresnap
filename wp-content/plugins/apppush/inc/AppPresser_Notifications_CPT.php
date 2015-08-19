<?php

class AppPresser_Notifications_CPT {

	public function __construct() {

		$this->singular  = __( 'Notification', 'apppresser-push' );
		$this->plural    = __( 'Notifications', 'apppresser-push' );
		$this->post_type = AppPresser_Notifications::$cpt;

		$this->labels = array(
			'name'               => $this->plural,
			'singular_name'      => $this->singular,
			'add_new'            => sprintf( __( 'Add New %s' ), $this->singular ),
			'add_new_item'       => sprintf( __( 'Add New %s' ), $this->singular ),
			'edit_item'          => sprintf( __( 'Edit %s' ), $this->singular ),
			'new_item'           => sprintf( __( 'New %s' ), $this->singular ),
			'all_items'          => $this->plural,
			'view_item'          => sprintf( __( 'View %s' ), $this->singular ),
			'search_items'       => sprintf( __( 'Search %s' ), $this->plural ),
			'not_found'          => sprintf( __( 'No %s' ), $this->plural ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash' ), $this->plural ),
			'parent_item_colon'  => null,
			'menu_name'          => $this->plural,
		);

		$this->args = array(
			'labels'             => $this->labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => 'apppresser_settings',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'notification' ),
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'menu_position'      => 1,
			'supports'           => array( 'title', 'excerpt' ),
		);

	}

	public function hooks() {

		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'admin_head', array( $this, 'conditional_hooks' ) );
		add_filter( 'post_updated_messages', array( $this, 'messages' ) );
		add_filter( 'manage_edit-'. $this->post_type .'_columns', array( $this, 'columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'columns_display' ) );
		add_action( 'add_meta_boxes_' . $this->post_type, array( $this, 'metabox_replace' ) );

	}

	/**
	 * Conditional Hooks for this CPT
	 * @since  1.0.0
	 */
	public function conditional_hooks() {
		$screen = get_current_screen();
		if ( isset( $screen->post_type ) && $screen->post_type == $this->post_type ) {
			add_filter( 'enter_title_here', array( $this, 'title' ) );
			add_filter( 'gettext', array( $this, 'modify_text' ) );
			$this->excerpt_css();
		}
	}

	/**
	 * Register notifications Custom Post Type
	 * @since  1.0.0
	 */
	public function register_cpt() {
		register_post_type( $this->post_type, apply_filters( 'appp_push_cpt_args', $this->args ) );
	}

	/**
	 * Modies CPT based messages to include our CPT labels
	 * @since  0.1.0
	 * @param  array  $messages Array of messages
	 * @return array            Modied messages array
	 */
	public function messages( $messages ) {
		global $post, $post_ID;

		$messages[$this->singular] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( '%1$s updated. <a href="%2$s">View %1$s</a>' ), $this->singular, esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.' ),
			3 => __( 'Custom field deleted.' ),
			4 => sprintf( __( '%1$s updated.' ), $this->singular ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s' ), $this->singular , wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( '%1$s published. <a href="%2$s">View %1$s</a>' ), $this->singular, esc_url( get_permalink( $post_ID ) ) ),
			7 => sprintf( __( '%1$s saved.' ), $this->singular ),
			8 => sprintf( __( '%1$s submitted. <a target="_blank" href="%2$s">Preview %1$s</a>' ), $this->singular, esc_url( add_query_arg( 'preview', 'true', esc_url( get_permalink( $post_ID ) ) ) ) ),
			9 => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %1$s</a>' ), $this->singular,
					// translators: Publish box date format, see http://php.net/date
					date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( '%1$s draft updated. <a target="_blank" href="%2$s">Preview %1$s</a>' ), $this->singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		return $messages;

	}

	/**
	 * Filter CPT title entry placeholder text
	 * @since  1.0.0
	 * @param  string $title Original placeholder text
	 * @return string        Modifed placeholder text
	 */
	public function title( $title ){
		return sprintf( __( '%s Title' ), $this->singular );
	}

	/**
	 * Change text for certain customizer strings four our custom version.
	 * @since  1.0.7
	 * @param  string  $translated_text Input
	 * @return string                   Maybe modified text
	 */
	public function modify_text( $translated_text ) {
		switch ( $translated_text ) {
			case 'Excerpt':
				return sprintf( __( '%s Text' ), $this->singular );
		}
		return $translated_text;
	}

	/**
	 * Registers admin columns to display.
	 * @since  0.1.0
	 * @param  array  $columns Array of registered column names/labels
	 * @return array           Modified array
	 */
	public function columns( $columns ) {

		$date = $columns['date'];
		unset( $columns['date'] );
		$columns[ $this->post_type .'_excerpt' ] = __( 'Excerpt' );
		$columns['date'] = $date;

		return $columns;
	}

	/**
	 * Handles admin column excerpt display.
	 * @since  0.1.0
	 * @param  array  $column Array of registered column names
	 */
	public function columns_display( $column ) {
		global $post;

		if ( $this->post_type .'_excerpt' === $column && ! empty( $post->post_excerpt ) ) {
			echo wpautop( $post->post_excerpt );
		}
	}

	/**
	 * Make excerpt column wider
	 * @since  1.0.0
	 */
	public function excerpt_css() {
		?>
		<style type="text/css"> .column-<?php echo $this->post_type; ?>_excerpt { width: 65%; } </style>
		<?php
	}

	/**
	 * Replace excerpt metabox
	 * @since  1.0.0
	 * @param  object  $post Post object
	 */
	public function metabox_replace() {
		remove_meta_box( 'postexcerpt', $this->post_type, 'normal' );
		add_meta_box( 'notificationexcerpt', sprintf( __( '%s Text' ), $this->singular ), array( $this, 'post_excerpt_meta_box' ), $this->post_type, 'normal', 'core' );
	}

	/**
	 * Display post excerpt textarea, w/o helper text
	 * @since 1.0.0
	 * @param object $post
	 */
	function post_excerpt_meta_box( $post ) {
		?>
		<label class="screen-reader-text" for="excerpt"><?php printf( __( '%s Text' ), $this->singular ) ?></label><textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
		<?php
	}

}
