<?php
/**
 * Job Listing: Content
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Content extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing description.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_content';
		$this->widget_name        = __( 'Listify - Listing: Description', 'listify' );
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

		global $job_manager;

		extract( $args );

		if ( '' == get_the_content() ) {
			return;
		}

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		ob_start();

		echo $before_widget;

		remove_filter( 'the_content', array( $job_manager->post_types, 'job_content' ) );

		if ( $title ) echo $before_title . $title . $after_title;

        do_action( 'listify_widget_job_listing_content_before' );

		the_content();

        do_action( 'listify_widget_job_listing_content_after' );

		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
