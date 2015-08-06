<?php
/**
 * Job Listing: Map
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Map extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing location and contact details.', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_map';
		$this->widget_name        = __( 'Listify - Listing: Map & Contact Details', 'listify' );
		$this->settings           = array(
			'map' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display map', 'listify' )
			),
			'address' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display address', 'listify' )
			),
			'phone' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display phone number', 'listify' )
			),
			'web' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display website', 'listify' )
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

		global $job_manager, $post, $listify_job_manager;

		extract( $args );

		$icon = isset( $instance[ 'icon' ] ) ? $instance[ 'icon' ] : null;
		$fields = array( 'map', 'address', 'phone', 'web' );
		$location = $listify_job_manager->template->get_the_location_formatted();

		foreach ( $fields as $field ) {
			$$field = isset( $instance[ $field ] ) && 1 == $instance[ $field ] ? true : false;
		}

		if ( $icon ) {
			$before_title = sprintf( $before_title, 'ion-' . $icon );
		}

		ob_start();

		echo $before_widget;
		?>

		<div class="row">
			<?php if ( $map && $post->geolocation_lat ) : ?>
				<div class="<?php if ( $phone || $web || $address ) : ?>col-md-6<?php endif; ?> col-sm-12">
					<a href="<?php echo $listify_job_manager->template->google_maps_url(); ?>" class="listing-contact-map-clickbox"></a>
					<div id="listing-contact-map"></div>
				</div>
			<?php endif; ?>

			<?php if ( $phone || $web || $address ) : ?>
			<div class="col-md-<?php echo $map ? 6 : 12; ?> col-sm-12">
				<div class="listing-contact-overview">
					<div class="listing-contact-overview-inner">
					<?php
                        do_action( 'listify_widget_job_listing_map_before' );

						if ( $address ) :
							$listify_job_manager->template->the_location_formatted();
						endif;

						if ( $phone ) :
							$listify_job_manager->template->the_phone();
						endif;

						if ( $web ) :
							$listify_job_manager->template->the_url();
						endif;

                        do_action( 'listify_widget_job_listing_map_after' );
					?>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		add_filter( 'listify_page_needs_map', '__return_false' );

		$this->cache_widget( $args, $content );
	}
}
