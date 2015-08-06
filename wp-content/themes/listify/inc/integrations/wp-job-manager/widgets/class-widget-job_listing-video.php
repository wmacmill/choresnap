<?php
/**
 * Job Listing: Video
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Video extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing video.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_video';
		$this->widget_name        = __( 'Listify - Listing: Video', 'listify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Video',
				'label' => __( 'Title:', 'listify' )
			),
			'icon' => array(
				'type'    => 'select',
				'std'     => 'ios-videocam',
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

		global $job_manager, $post;

		extract( $args );

		if ( '' == get_the_company_video() ) {
			return;
		}

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;
        
        do_action( 'listify_widget_job_listing_video_before' );

		the_company_video();

        do_action( 'listify_widget_job_listing_video_after' );

		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
