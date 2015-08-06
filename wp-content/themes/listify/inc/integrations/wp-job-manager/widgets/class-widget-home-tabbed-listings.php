<?php
/**
 * Home: Tabbed Listings
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Tabbed_Listings extends Listify_Widget_Term_Lists {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( listify_theme_mod( 'categories-only' ) ) {
			$this->taxonomy = 'job_listing_category';
		} else {
			$this->taxonomy = 'job_listing_type';
		}

		$this->get_terms();

		$this->widget_description = __( 'Display a tabbed layout of listing types', 'listify' );
		$this->widget_id          = 'listify_widget_tabbed_listings';
		$this->widget_name        = __( 'Listify - Page: Tabbed Listings', 'listify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'What\'s New',
				'label' => __( 'Title:', 'listify' )
			),
			'limit' => array(
				'type'  => 'number',
				'std'   => 3,
				'min'   => 3,
				'max'   => 30,
				'step'  => 3,
				'label' => __( 'Number per tab:', 'listify' )
			),
			'featured' => array(
				'type' => 'checkbox',
				'std'  => 0,
				'label' => __( 'Use Featured listings', 'listify' )
			),
			'terms' => array(
				'label' => __( 'Types to Feature:', 'listify' ),
				'type' => 'multicheck',
				'std'  => '',
				'options' => $this->get_terms_simple()
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

		$this->instance = $instance;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );

		$listings_by_term = $this->get_listings_by_term();

		ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;
		?>

		<ul class="tabbed-listings-tabs">
			<?php foreach ( $listings_by_term as $term_id => $listings ) : ?>
				<?php $term = get_term_by( 'ID', $term_id, $this->taxonomy ); ?>
				<li><a href="#tab-<?php echo $term->term_id; ?>"><?php echo $term->name; ?></a></li>
			<?php endforeach; ?>

			<li><a href="<?php echo get_post_type_archive_link( 'job_listing' ); ?>"><?php _e( 'See More', 'listify' ); ?></a></li>
		</ul>

		<div class="tabbed-listings-tabs-wrapper">

			<?php foreach ( $listings_by_term as $term_id => $listings ) : ?>

			<div id="tab-<?php echo $term_id; ?>" class="listings-tab">

				<ul class="job_listings">
					<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

						<?php get_template_part( 'content', 'job_listing' ); ?>

					<?php endwhile; ?>
				</ul>

			</div>

			<?php endforeach; ?>

		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
