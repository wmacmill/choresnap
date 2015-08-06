<?php
if ( ! function_exists( 'wp_terms_checklist' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/admin.php' );
}

/**
 * Job Listing: Tags
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Tags extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing tags.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_tags';
		$this->widget_name        = __( 'Listify - Listing: Tags', 'listify' );
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

		if ( ! class_exists( 'WP_Job_Manager_Job_Tags' ) ) {
			return;
		}

		global $job_manager, $post;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		$tags = wp_get_object_terms( $post->ID, 'job_listing_tag' );

		if ( is_wp_error( $tags ) || empty( $tags ) ) {
			return;
		}

		ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

        do_action( 'listify_widget_job_listing_tags_before' );

		wp_terms_checklist( $post->ID, array(
			'taxonomy' => 'job_listing_tag',
			'checked_ontop' => false,
			'walker' => new Listify_Walker_Tags_Checklist
		) );

        do_action( 'listify_widget_job_listing_tags_after' );

		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}

class Listify_Walker_Tags_Checklist extends Walker_Category_Checklist {
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);

		$output .= sprintf(
			'<span class="tag %s">%s</span>',
			in_array( $category->term_id, $selected_cats ) ? 'active' : 'inactive',
			esc_html( apply_filters( 'the_category', $category->name ) )
		);
	}

	function end_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		return '';
	}
}
