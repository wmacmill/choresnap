<?php
class Listify_Widget_Term_Lists extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		if ( ! $this->widget_id ) {
			$this->widget_description = __( 'Display lists of listings associated with terms of a given taxonomy.', 'listify' );
			$this->widget_id          = 'listify_widget_taxonomy_term_lists';
			$this->widget_name        = __( 'Listify - Page: Term Lists', 'listify' );

			$this->settings = array(
				'title' => array(
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Title:', 'listify' )
				),
				'description' => array(
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Description:', 'listify' )
				),
				'limit' => array(
					'type'  => 'number',
					'std'   => 5,
					'min'   => 1,
					'max'   => 30,
					'step'  => 1,
					'label' => __( 'Number per list', 'listify' )
				),
				'orderby' => array(
					'label' => __( 'Order By:', 'listify' ),
					'type' => 'select',
					'std'  => 'date',
					'options' => array(
						'date' => __( 'Date', 'listify' ),
						'featured' => __( 'Featured', 'listify' ),
						'title' => __( 'Title', 'listify' ),
						'ID' => __( 'ID', 'listify' )
					)
				),
				'taxonomy' => array(
					'label' => __( 'Taxonomy:', 'listify' ),
					'type' => 'select',
					'std'  => '',
					'options' => $this->get_taxonomies_simple()
				),
				'featured' => array(
					'type' => 'checkbox',
					'std'  => 0,
					'label' => __( 'Use only featured listings', 'listify' )
				)
			);
		}

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
		$description = isset( $instance[ 'description' ] ) ? esc_attr( $instance[ 'description' ] ) : false;

		$after_title = '<h2 class="home-widget-description">' . $description . '</h2>' . $after_title;

		$listings_by_term = $this->get_listings_by_term();

		global $listify_job_manager;

		ob_start();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;
		?>

		<div class="listing-by-term-wrapper row" data-columns>

		<?php foreach ( $listings_by_term as $term_id => $listings ) : if ( ! $listings->have_posts() ) continue; ?>

		<?php $term = get_term_by( 'ID', $term_id, $this->taxonomy ); ?>

			<div id="term-<?php echo $term->term_id; ?>" class="listings-by-term">
				<div class="listing-by-term-inner">
					<h2 class="listing-by-term-title"><a href="<?php echo get_term_link( $term, $this->taxonomy ); ?>"><?php echo $term->name; ?></a></h2>

					<ul>
					<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

						<li>
							<a href="<?php the_permalink(); ?>" class="job_listing-clickbox"></a>

							<div class="listings-by-term-preview">
								<?php the_post_thumbnail(); ?>
							</div>

							<div class="listings-by-term-content">
								<?php the_title(); ?>
								<?php do_action( 'listify_listings_by_term_after' ); ?>
							</div>
						</li>

					<?php endwhile; ?>
					</ul>

					<div class="listings-by-term-more">
						<a href="<?php echo get_term_link( $term, $this->taxonomy ); ?>"><?php _e( 'More', 'listify' ); ?></a>
					</div>
				</div>
			</div>

		<?php endforeach; ?>

		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}

	public function get_listings_by_term() {
		$limit = isset( $this->instance[ 'limit' ] ) ? absint( $this->instance[ 'limit' ] ) : 3;
		$featured = isset( $this->instance[ 'featured' ] ) && 1 == $this->instance[ 'featured' ] ? true : null;
		$orderby = isset( $this->instance[ 'orderby' ] ) ? $this->instance[ 'orderby' ] : 'date';

		if ( ! isset( $this->taxonomy ) ) {
			$this->taxonomy = isset( $this->instance[ 'taxonomy' ] ) ? esc_attr( $this->instance[ 'taxonomy' ] ) : 'job_listing_type';
		}

		$this->get_terms();

		foreach ( $this->terms as $term_id ) {
			$objects = get_objects_in_term( $term_id, $this->taxonomy, array( 'orderby' => $orderby ) );

			if ( empty( $objects ) ) {
				$objects = array(-1);
			}

			$_output[ $term_id ] = get_job_listings( array(
				'posts_per_page' => $limit,
				'featured' => $featured,
				'orderby' => $orderby,
				'no_found_rows' => true,
				'post__in' => $objects 
			) );

		}

		return $_output;
	}

	public function get_terms() {
		if ( isset( $this->instance[ 'terms' ] ) ) {
			$terms = maybe_unserialize( $this->instance[ 'terms' ] );
			$this->terms = array();

			foreach ( $terms as $term_slug ) {
				$term = get_term_by( 'slug', $term_slug, $this->taxonomy );
				$this->terms[] = $term->term_id;
			}
		} else {
			$this->terms = get_terms( $this->taxonomy, array( 'hide_empty' => 0, 'fields' => 'ids' ) );
		}

		return $this->terms;
	}

	public function get_terms_simple() {
		$_terms = array();

		if ( empty( $this->terms ) || is_wp_error( $this->terms ) ) {
			return array();
		}

		foreach ( $this->terms as $term ) {
			$term = get_term_by( 'ID', $term, $this->taxonomy );

			$_terms[ $term->slug ] = $term->name;
		}

		return $_terms;
	}

	private function get_taxonomies_simple() {
		$taxonomies = get_taxonomies( array( 'public' => true ), array( 'output' => 'objects' ) );
		$_taxonomies = array();

		foreach ( $taxonomies as $taxonomy ) {
			$_taxonomies[ $taxonomy->name ] = $taxonomy->labels->name;
		}

		return $_taxonomies;
	}
}
