<?php
/**
 * Job Listing: Social Profiles
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Social_Profiles extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the social profiles of the listing author', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_social_profiles';
		$this->widget_name        = __( 'Listify - Listing: Social Profiles', 'listify' );
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

		extract( $args );

		global $post;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		$methods = wp_get_user_contact_methods();
		$output = array();

		foreach ( $methods as $method => $label ) {
			if ( 'user' == listify_theme_mod( 'social-association' ) ) {
				$value = get_the_author_meta( $method, $post->post_author );
			} else {
				$value = get_post_meta( $post->ID, '_company_' . $method, true );
			}

			if ( '' == $value ) {
				continue;
			}
			
			if ( $value && ! strstr( $value, 'http:' ) && ! strstr( $value, 'https:' ) ) {
				$value = 'http://' . $value;
			}

			$output[] = sprintf( '<a href="%s" target="_blank" class="ion-social-%s">%s</a>', $value, $method, $label );
		}

		if ( empty( $methods ) || empty( $output ) ) {
			return;
		}

		ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

        do_action( 'listify_widget_job_listing_social_before' );

		echo '<ul class="social-profiles"><li>' . implode( '</li><li>', $output ) . '</li></ul>';

        do_action( 'listify_widget_job_listing_social_after' );

		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
