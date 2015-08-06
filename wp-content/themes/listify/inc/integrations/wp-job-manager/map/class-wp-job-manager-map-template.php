<?php

class Listify_WP_Job_Manager_Map_Template extends listify_Integration {

	public function __construct() {
		$this->includes = array();
		$this->integration = 'wp-job-manager';

		parent::__construct();
	}

	public function setup_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		
		// send back info we can actually use to backbone
		add_action( 'wp_footer', array( $this, 'pin_template' ) );
		add_action( 'wp_footer', array( $this, 'infobubble_template' ) );

		// if we are not sorting by a region we need to do more things
		if ( ! get_option( 'job_manager_regions_filter' ) ) {
			// output the labels of how we are sorting
			add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
			add_filter( 'job_manager_get_listings_custom_filter_text', array( $this, 'job_manager_get_listings_custom_filter_text' ) );

			// add the hidden fields to send to send over
			add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_manager_job_filters_distance' ), 0 );
		}

		// add the view switcher
		add_action( 'listify_map_before', array( $this, 'view_switcher' ) );

		// output the map
		add_action( 'listify_output_map', array( $this, 'output_map' ) );

		// add map specific listing data
		add_filter( 'listify_listing_data', array( $this, 'listing_data' ) );
	}

	/**
	 * Does the map need to appear on this page?
	 */
	public function display() {
		$display = listify_theme_mod( 'listing-archive-output');

		return in_array( $display, array( 'map', 'map-results' ) );
	}

	/**
	 * Where is the map appearing on this page?
	 */
	public function position() {
		return listify_theme_mod( 'listing-archive-map-position' );
	}

	/**
	 * Set the default region bias
	 */
	public function region_bias() {
		return listify_theme_mod( 'region-bias' );
	}

	/**
	 * Get the unit
	 */
	public function unit() {
		return $this->is_english() ? 'mi' : 'km';
	}

	/**
	 * Get location
	 */
	public function is_english() {
		$english = apply_filters( 'listify_map_english_units', array( 'US', 'GB', 'LR', 'MM' ) );

		if ( in_array( $this->region_bias(), $english ) ) {
			return true;
		}

		return false;
	}
	
	private function get_average_radius() {
		$default = listify_theme_mod( 'map-behavior-search-default' );
		return $default;
	}

	public function listing_data( $data ) {
		global $post;

		$data[ 'id' ] = $post->ID;
		$data[ 'link' ] = get_permalink( $post->ID );

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );

		if ( ! is_array( $image ) ) {
			$image = listify_get_cover_from_group( array( 'size' => 'thumbnail' ) );
		}

		if ( is_array( $image ) && isset( $image[0] ) ) {
			$data[ 'thumb' ] = $image[0];
		}

		$data[ 'title' ] = the_title_attribute( array( 'post' => $post, 'echo' => false ) );
		$data[ 'address' ] = get_the_job_location( $post->ID );

		/** Longitude */
		$long = esc_attr( $post->geolocation_long );

		if ( $long ) {
			$data[ 'longitude' ] = $long;
		}

		/** Latitude */
		$lat = esc_attr( $post->geolocation_lat );

		if ( $lat ) {
			$data[ 'latitude' ] = $lat;
		}

		/** Type (default) */
		$terms = get_the_terms( get_the_ID(), listify_get_top_level_taxonomy() );
		$term_id = null;

		/** Color (default) */
		$default_color = apply_filters( 'listify_default_marker_color', listify_theme_mod( 'color-primary' ) );

		/** Icon (default) */
		$default_icon = apply_filters( 'listify_default_marker_icon', 'ion-information-circled' );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			/** Color */
			$color = listify_theme_mod( 'marker-color-' . current( $terms )->term_id );
			$data[ 'color' ] = $color ? $color : $default_color;

			/** Icon */
			$icon = listify_theme_mod( 'marker-icon-' . current( $terms )->term_id );
			$data[ 'icon' ] = $icon ? $icon : $default_icon;

			/** Term */
			$term_id = current( $terms )->term_id;
			$data[ 'term' ] = $term_id;
		} else {
			$data[ 'icon' ] = $default_icon;
			$data[ 'color' ] = $default_color;
			$data[ 'term' ] = 0;
		}

		foreach ( $data as $key => $value ) {
			$data[ $key ] = esc_attr( strip_tags( $value ) );
		}

		return $data;
	}

	public function pin_template() {
		locate_template( array( 'templates/tmpl-map-pin.php' ), true );
	}

	public function infobubble_template() {
		locate_template( array( 'templates/tmpl-map-popup.php' ), true );
	}

	/**
	 * Figure out if this page needs a map or not.
	 * 
	 * This is based on output, widgets, if the scripts are needed, etc.
	 */
	public function page_needs_map( $force = false ) {
		if ( $force ) {
			return $force;
		}

		$needs = false;

		if ( listify_is_job_manager_archive() ) {
			$needs = true;
		}

		if ( is_singular( 'job_listing' ) ) {
			$needs = true;
		}
		
		// always load when relisting/previewing just in case
		if ( ( isset( $_GET[ 'step' ] ) && 'preview' == $_GET[ 'step' ] ) || isset( $_REQUEST[ 'job_manager_form' ] ) ) {
			$needs = true;
		}

		if ( listify_is_widgetized_page() ) {
			$needs = true;
		}

		if ( apply_filters( 'listify_page_needs_map', false ) ) {
			$needs = true;
		}

		if ( ! $this->display() ) {
			$needs = false;
		}

		return $needs;
	}
	
	public function enqueue_scripts( $force = false ) {
		$deps = array(
			'jquery',
			'jquery-ui-slider',
			'google-maps',
			'wp-backbone',
			'wp-job-manager-ajax-filters',
		);

		if ( class_exists( 'WP_Job_Manager_Extended_Location' ) ) {
			$deps[] = 'wpjm-extended-location';
		}

		$deps[] = 'listify';

		$bias = strtolower( listify_theme_mod( 'region-bias' ) );
		$base = '//maps.googleapis.com/maps/api/js'; 
		$args = array(
			'v' => 3,
			'libraries' => 'geometry,places',
			'key' => listify_theme_mod( 'map-behavior-api-key' ),
			'language' => get_locale() ? substr( get_locale(), 0, 2 ) : ''
		);

		if ( '' != $bias ) {
			$args[ 'region' ] = $bias;
		}

		wp_enqueue_script( 'google-maps', esc_url_raw( add_query_arg( $args, $base ) ) );
		wp_enqueue_script( 'listify-app-map', Listify_Integration::get_url() . 'js/map/app.min.js', $deps, '20150213', true );

		$settings = array(
			'displayMap' => (bool) $this->display(),
			'facetwp' => listify_has_integration( 'facetwp' ),
			'useClusters' => (bool) listify_theme_mod( 'map-behavior-clusters' ),
			'autoFit' => (bool) listify_theme_mod( 'map-behavior-autofit' ),
			'trigger' => listify_theme_mod( 'map-behavior-trigger' ),
			'mapOptions' => array(
				'zoom' => listify_theme_mod( 'map-behavior-zoom' ),
				'maxZoom' => listify_theme_mod( 'map-behavior-max-zoom' ),
				'gridSize' => listify_theme_mod( 'map-behavior-grid-size' ),
			),
			'searchRadius' => array(
				'min' => listify_theme_mod( 'map-behavior-search-min' ),
				'max' => listify_theme_mod( 'map-behavior-search-max' ),
				'default' => listify_theme_mod( 'map-behavior-search-default' )
			)
		);

		if ( '' != ( $center = listify_theme_mod( 'map-behavior-center' ) ) ) {
			$settings[ 'mapOptions'][ 'center' ] = array_map( 'trim', explode( ',', $center ) );
		}

		if ( has_filter( 'job_manager_geolocation_region_cctld' ) ) {
			$settings[ 'autoComplete' ][ 'componentRestrictions' ] = array(
				'country' => $bias
			);
		}

		$settings = apply_filters( 'listify_map_settings', $settings );

		wp_localize_script( 'listify-app-map', 'listifyMapSettings', apply_filters( 'listify_map_settings', $settings ) );
	}
	
	/**
	 * Set the body class based on how the map is being output
	 */
	public function body_class( $classes ) {
		global $post;

		if (
			listify_is_job_manager_archive() &&
			'side' == $this->position() &&
			$this->display() &&
			! ( listify_is_widgetized_page() )
		) {
			$classes[] = 'fixed-map';
		}

		return $classes;
	}

	/**
	 * Display the map
	 */
	public function output_map() {
		if ( ! $this->page_needs_map() ) {
			return;
		}

		locate_template( array( 'content-job_listing-map.php' ), true );
	}
	
	/**
	 * Display the grid/list switcher
	 */
	public function view_switcher() {
	?>
		<div class="archive-job_listing-toggle-wrapper container">
			<div class="archive-job_listing-toggle-inner views">
				<a href="#" class="archive-job_listing-toggle active" data-toggle=".content-area"><?php _e( 'Results', 'listify' ); ?></a><a href="#" class="archive-job_listing-toggle" data-toggle=".job_listings-map-wrapper"><?php _e( 'Map', 'listify' ); ?></a>
			</div>
		</div>
	<?php
	}

	
	/**
	 * Add the hidden fields and radius slider
	 */
	public function job_manager_job_filters_distance() {
		$checked = true;

		if ( is_tax( 'job_listing_region' ) ) {
			$checked = false;
		}
	?>
		<div class="search-radius-wrapper in-use">
			<div class="search-radius-label">
				<label for="use_search_radius">
					<input type="checkbox" name="use_search_radius" id="use_search_radius" <?php checked( true, $checked ); ?>/>
					<?php printf( __( 'Radius: <span class="radi">%s</span> %s', 'listify' ), $this->get_average_radius(), $this->unit() ); ?>
				</label>
			</div>
			<div class="search-radius-slider">
				<div id="search-radius"></div>
			</div>

			<input type="hidden" id="search_radius" name="search_radius" value="<?php echo isset( $_GET[ 'search_radius'
			] ) ? absint( $_GET[ 'search_radius' ] ) : $this->get_average_radius(); ?>" />
		</div>

		<input type="hidden" id="search_lat" name="search_lat" value="<?php echo isset( $_GET[ 'search_lat' ] ) ? esc_attr(
		$_GET[ 'search_lat' ] ) : 0; ?>" />
  		<input type="hidden" id="search_lng" name="search_lng" value="<?php echo isset( $_GET[ 'search_lng' ] ) ?
  		esc_attr( $_GET[ 'search_lng' ] ) : 0; ?>" />
	<?php
	}

	/**
	 * Add some text so we know what we are searching for
	 */
	public function job_manager_get_listings_custom_filter_text( $text ) {
		$params = array();

		parse_str( $_REQUEST[ 'form_data' ], $params );

		$use_radius = isset( $params[ 'use_search_radius' ] ) && 'on' == $params[ 'use_search_radius' ];

		if ( ! $use_radius ) {
			return $text;
		}

		if ( 
			! isset( $params[ 'search_lat' ] ) ||
			'' == $params[ 'search_lat' ] || 
			'' == $params[ 'search_location' ]
		) {
			return $text;
		}

		$text .= ' ' . sprintf( __( 'within a %d %s radius', 'listify' ), $params[ 'search_radius' ], $this->unit() );

		return $text;
	}

}
