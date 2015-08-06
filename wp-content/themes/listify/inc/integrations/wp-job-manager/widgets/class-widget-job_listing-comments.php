<?php
/**
 * Job Listing: Comments
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Comments extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing comments.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_comments';
		$this->widget_name        = __( 'Listify - Listing: Reviews', 'listify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'listify' )
			),
			'icon' => array(
				'type'    => 'select',
				'std'     => '',
				'label'   => __( 'Icon:', 'listify' ),
				'options' => $this->get_icon_list()
			)
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		global $post;

		if ( 'publish' != $post->post_status ) {
			return;
		}

		global $job_manager, $comments_widget_title, $comments_widget_icon, $comments_widget_before_title, $comments_widget_after_title;

		extract( $args );

		$comments_widget_title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$comments_widget_icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;

		ob_start();

		comments_template();

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
