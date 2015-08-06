<?php
/**
 * Handles all "template" related items for Job Manager.
 *
 * This includes outputting information in certain template files,
 * registering new widget areas, custom menus, etc.
 *
 * @package Listify
 */
class Listify_WP_Job_Manager_Template extends listify_Integration {

	public function __construct() {
		$this->includes = array();
		$this->integration = 'wp-job-manager';

		$this->is_home = false;

		parent::__construct();
	}

	/**
	 * This is quite large because the majorify of the templates
	 * are built through actions. This allows them to be unhooked
	 * or rearranged farily easily.
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function setup_actions() {
		/**
		 * Global
		 */

		// template loader
		add_filter( 'template_include', array( $this, 'template_include' ) );

		// job manager template loader
		add_filter( 'job_manager_locate_template', array( $this, 'locate_template' ), 10, 3 );

		// body/post class suppliments
		add_filter( 'post_class', array( $this, 'post_class' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		// register new widgets and widget areas
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// megamenu
		add_filter( 'wp_nav_menu_items', array( $this, 'taxonomy_mega_menu' ), 0, 2 );

		/**
		 * Single Listing Item
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// attach custom data attributes
		add_filter( 'listify_job_listing_data', array( $this, 'job_listing_data' ), 10, 2 );

		// output job manager hooks
		add_action( 'listify_single_job_listing_meta', array( $this, 'single_job_listing_meta' ) );

		// output company information
		add_action( 'single_job_listing_meta_start', array( $this, 'the_title' ), 10 );
		add_action( 'single_job_listing_meta_start', array( $this, 'the_location_formatted' ), 20 );
		add_action( 'single_job_listing_meta_start', array( $this, 'the_category' ), 30 );

		// output listing actions
		add_action( 'listify_single_job_listing_actions', array( $this, 'the_actions' ) );
		add_action( 'listify_single_job_listing_actions_after', array( $this, 'submit_review_link' ) );

		// breadcrum links
		add_filter( 'term_links-job_listing_category', array( $this, 'term_links' ) );
		add_filter( 'term_links-job_listing_type', array( $this, 'term_links' ) );

		// output information on the grid/list items
		add_action( 'listify_content_job_listing_meta', array( $this, 'the_title' ), 15 );
		add_action( 'listify_content_job_listing_meta', array( $this, 'the_location_formatted' ), 20 );
		add_action( 'listify_content_job_listing_meta', array( $this, 'the_phone' ), 25 );

		remove_action( 'single_job_listing_start', 'job_listing_meta_display', 20 );
		remove_action( 'single_job_listing_start', 'job_listing_company_display', 30 );

		/**
		 * Results
		 */

		// output the results
		add_action( 'listify_output_results', array( $this, 'output_results' ) );

		// add results found to the ajax response to be used elsewhere
		add_filter( 'job_manager_get_listings_result', array( $this, 'job_manager_get_listings_result' ), 10, 2 );

		// add a label for job types
		add_action( 'job_manager_job_filters_end', array( $this, 'job_types_label' ), 9 );

		// add a submit button
		add_action( 'job_manager_job_filters_end', array( $this, 'add_submit_button' ), 25 );

		// add form context
		add_action( 'job_manager_job_filters_end', array( $this, 'add_form_context' ) );

		// if we are showing more than just the map (i.e results)
		$display = listify_theme_mod( 'listing-archive-output' );

		// if the map + results are showing
		if ( $display != 'map' ) {
			add_action( 'job_manager_job_filters_after', array( $this, 'style_switcher' ) );
			add_action( 'job_manager_job_filters_before', array( $this, 'toggle_filters' ) );
			add_action( 'listify_facetwp_sort', array( $this, 'style_switcher' ) );
		}

		// monitor the style switcher
		add_action( 'wp_ajax_listify_save_archive_style', array( $this, 'save_style' ) );
		add_action( 'wp_ajax_nopriv_listify_save_archive_style', array( $this, 'save_style' ) );

		add_filter( 'job_manager_get_listings_result', array( $this, 'homepage_grid_columns' ) );


	}

	public function temp_remove_ajax_filters() {
		wp_dequeue_script( 'wp-job-manager-ajax-filters' );
	}

	public function temp_add_ajax_filters() {
		wp_enqueue_script( 'wp-job-manager-ajax-filters' );
	}

	/** Global --------------------------------------------------------------- */

	/**
	 * Check if we are on a Job Manager-related taxonomy. If so, load
	 * the standard job listing archive which will handle it all.
	 *
	 * @since Listify 1.0.0
	 *
	 * @param string $template
	 * @return string $template
	 */
	public function template_include( $template ) {
		$this->is_home = listify_is_widgetized_page();

		if ( is_tax( array( 'job_listing_category', 'job_listing_type', 'job_listing_tag', 'job_listing_region' ) ) ) {
			$template = locate_template( array( 'archive-job_listing.php' ) );

			if ( '' != $template ) {
				return $template;
			}
		}

		return $template;
	}

	/**
	 * Job Manager template loader suppliment. Any time Job Manager looks for
	 * a template file it will also check the /templates/ directory in this
	 * integration directory
	 *
	 * @since Listify 1.0.0
	 *
	 * @param string $template
	 * @param string $template_name
	 * @param string $template_path
	 * @return string $template
	 */
	public function locate_template( $template, $template_name, $template_path ) {
		global $job_manager;

		if ( ! file_exists( $template ) ) {
			$default_path = listify_Integration::get_dir() . 'templates/';

			$template = $default_path . $template_name;
		}

		return $template;
	}

	/**
	 * Add supplimentary body classes so we can target certain things.
	 *
	 * @since Listify 1.0.0
	 *
	 * @param array $classes
	 * @return array $classes
	 */
	public function body_class( $classes ) {
		global $wp_query;

		$categories = true;

		$cats = get_terms( 'job_listing_category', array( 'hide_empty' => 0 ) );

		$categories = is_wp_error( $cats ) || empty( $cats ) ? false : true;
		$categories = $categories && get_option( 'job_manager_enable_categories' );
		$categories = $categories && ! is_tax( 'job_listing_category' );
		$categories = $categories && ! ( isset( $_GET[ 'search_categories' ] ) && 0 != $_GET[ 'search_categories' ] );

		if ( $categories ) {
			$classes[] = 'wp-job-manager-categories-enabled';

			if ( get_option( 'job_manager_enable_default_category_multiselect' ) && ! listify_is_widgetized_page() ) {
				$classes[] = 'wp-job-manager-categories-multi-enabled';
			}
		}

		if ( 'map' == listify_theme_mod( 'listing-archive-output' ) ) {
			$classes[] = 'listing-archive-display-map-only';
		}

		if ( isset( $wp_query->query_vars[ 'gallery' ] ) ) {
			$classes[] = 'single-job_listing-gallery';
		}

		if ( listify_is_job_manager_archive() ) {
			$classes[] = 'job-manager-archive';
		}

		return $classes;
	}

	/**
	 * Add supplimentary post classes so we can target certain things.
	 *
	 * @since Listify 1.0.0
	 *
	 * @param array $classes
	 * @return array $classes
	 */
	public function post_class( $classes ) {
		global $wp_query, $post, $listify_job_manager;

		$home = listify_is_widgetized_page();

		$style = $this->get_archive_display_style();

		if ( is_author() || isset( $wp_query->query[ 'is_author' ] ) ) {
			$style = 'list';
		}

		if ( $home ) {
			$style = 'grid';
		}

		if ( 'job_listing' == $post->post_type ) {
			$classes[] = $this->get_grid_columns($style);

			$classes[] = 'style-' . $style;
		}

		if ( 'attachment' == $post->post_type ) {
			unset( $classes[ array_search( 'attachment', $classes ) ] );
		}

		return $classes;
	}

	public function widgets_init() {
		global $listify_strings;

		$widgets = array(
			'job_listing-content.php',
			'job_listing-comments.php',
			'job_listing-gallery.php',
			'job_listing-gallery-slider.php',
			'job_listing-tags.php',
			'job_listing-map.php',
			'job_listing-business-hours.php',
			'job_listing-author.php',
			'job_listing-video.php',
			'home-recent-listings.php',
			'home-search-listings.php',
			'home-term-lists.php',
			'home-tabbed-listings.php',
			'home-taxonomy-image-grid.php',
			'home-map-listings.php'
		);

		foreach ( $widgets as $widget ) {
			include_once( listify_Integration::get_dir() . 'widgets/class-widget-' . $widget );
		}

		register_widget( 'Listify_Widget_Listing_Content' );
		register_widget( 'Listify_Widget_Listing_Comments' );
		register_widget( 'Listify_Widget_Listing_Gallery' );
		register_widget( 'Listify_Widget_Listing_Gallery_Slider' );
		register_widget( 'Listify_Widget_Listing_Tags' );
		register_widget( 'Listify_Widget_Listing_Map' );
		register_widget( 'Listify_Widget_Listing_Business_Hours' );
		register_widget( 'Listify_Widget_Listing_Author' );
		register_widget( 'Listify_Widget_Listing_Video' );
		register_widget( 'Listify_Widget_Recent_Listings' );
		register_widget( 'Listify_Widget_Search_Listings' );
		register_widget( 'Listify_Widget_Tabbed_Listings' );
		register_widget( 'Listify_Widget_Term_Lists' );
		register_widget( 'Listify_Widget_Taxonomy_Image_Grid' );
		register_widget( 'Listify_Widget_Map_Listings' );

		unregister_widget( 'WP_Job_Manager_Widget_Recent_Jobs' );
		unregister_widget( 'WP_Job_Manager_Widget_Featured_Jobs' );

		register_sidebar( array(
			'name'          => sprintf( __( '%s Archives - Sidebar', 'listify' ), $listify_strings->label( 'singular' ) ),
			'id'            => 'archive-job_listing',
			'before_widget' => '<aside id="%1$s" class="widget widget-job_listing-archive %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title widget-title-job_listing %s">',
			'after_title'   => '</h1>',
		) );

		register_sidebar( array(
			'name'          => sprintf( __( 'Single %s - Main Content', 'listify' ), $listify_strings->label( 'singular' ) ),
			'id'            => 'single-job_listing-widget-area',
			'before_widget' => '<aside id="%1$s" class="widget widget-job_listing %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title widget-title-job_listing %s">',
			'after_title'   => '</h1>',
		) );

		register_sidebar( array(
			'name'          => sprintf( __( 'Single %s - Sidebar', 'listify' ), $listify_strings->label( 'singular' ) ),
			'id'            => 'single-job_listing',
			'before_widget' => '<aside id="%1$s" class="widget widget-job_listing %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title widget-title-job_listing %s">',
			'after_title'   => '</h1>',
		) );
	}

	public function taxonomy_mega_menu( $items, $args ) {
		if ( 'none' == listify_theme_mod( 'nav-megamenu' ) ) {
			return $items;
		}

		if ( 'secondary' != $args->theme_location ) {
			return $items;
		}

		$taxonomy = get_taxonomy( listify_theme_mod( 'nav-megamenu' ) );

		if ( is_wp_error( $taxonomy ) ) {
			return $items;
		}

		global $listify_strings;

		$link = sprintf( 
			'<a href="%s">' . __( 'Browse %s', 'listify' ) . '</a>',
			get_post_type_archive_link( 'job_listing' ), 
			str_replace( $listify_strings->label( 'singular' ) . ' ', '', $taxonomy->labels->name )
		);

		$submenu = wp_list_categories( apply_filters( 'listify_mega_menu_list', array(
			'taxonomy' => $taxonomy->name,
			'hide_empty' => 0,
			'pad_counts' => true,
			'show_count' => true,
			'echo' => false,
			'title_li' => false,
			'depth' => 1,
			'walker' => new Listify_Walker_Category
		) ) );

		$dropdown = wp_dropdown_categories( array(
			'show_option_all' => sprintf( __( 'Select a %s', 'listify' ), str_replace( $listify_strings->label( 'singular' ) . ' ', '', $taxonomy->labels->singular_name ) ),
			'taxonomy' => $taxonomy->name,
			'hide_empty' => 0,
			'pad_counts' => false,
			'show_count' => true,
			'echo' => false,
			'title_li' => false,
			'depth' => 1,
			'hierarchical' => true,
			'name' => $taxonomy->name,
			'walker' => new Listify_Walker_Category_Dropdown
		) );

		$submenu =
			'<ul class="sub-menu category-list">' .
				'<form id="job_listing_tax_mobile" action="' . home_url() . '" method="get">' . $dropdown . '</form>
				<div class="container">
					<div class="mega-category-list-wrapper">' . $submenu . '</div>
					<!--<a href="' . get_post_type_archive_link( 'job_listing' ) . '">' . __( 'View All', 'listify' ) . '</a>-->
				</div>
			</ul>';

		return '<li id="categories-mega-menu" class="ion-navicon-round menu-item menu-type-link">'. $link . $submenu . '</li>' . $items;
	}

	/** Single Listing Item ---------------------------------------------------- */

	public function enqueue_scripts() {
		$preview = is_page( get_option( 'job_manager_submit_job_form_page_id', false ) );

		if ( ! ( is_singular( 'job_listing' ) || $preview ) ) {
			return;
		}

        global $listify_job_manager;

        $listify_job_manager->map->template->enqueue_scripts(true);

		$job_id = isset( $_REQUEST[ 'job_id' ] ) ? esc_attr( $_REQUEST[ 'job_id' ] ) : 0;
		$listing = get_post( $job_id );

		$terms = get_the_terms( $listing->ID, listify_get_top_level_taxonomy() );
		$term_id = null;
        $default_icon = apply_filters( 'listify_default_marker_icon', 'ion-information-circled' );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            $term = current( $terms )->term_id;
		    $icon = listify_theme_mod( 'marker-icon-' . current( $terms )->term_id );
            $icon = $icon ? $icon : $default_icon;
        } else {
            $term = '';
            $icon = $default_icon;
        }

        $vars = array(
            'lat' => $listing->geolocation_lat,
            'lng' => $listing->geolocation_long,
            'term' => $term,
            'icon' => $icon,
            'mapOptions' => array(
				'zoom' => apply_filters( 'listify_single_listing_map_zoom', 15 )
			)
        );

        if ( $preview && isset( $_POST[ 'geo_lat' ] ) ) {
			$lat = isset( $_POST[ 'geo_lat' ] ) ? esc_attr( $_POST[ 'geo_lat' ] ) : '';
			$lng = isset( $_POST[ 'geo_lng' ] ) ? esc_attr( $_POST[ 'geo_lng' ] ) : '';

			$vars[ 'lat' ] = $lat;
			$vars[ 'lng' ] = $lng;
		}

		$vars = apply_filters( 'listify_single_map_settings', $vars );

		wp_enqueue_script( 'listify-app-listing', get_template_directory_uri() . '/inc/integrations/wp-job-manager/js/listing/app.min.js', array( 'listify-app-map' ) );
        wp_localize_script( 'listify-app-listing', 'listifySingleMap', $vars );
	}

	/**
	 * Add supplimentary data to individual listings so we can plot
	 * and other things with it.
	 *
	 * @since Listify 1.0.0
	 *
	 * @param array $data
	 * @return array $data
	 */
	public function job_listing_data( $data, $json = false ) {
		global $post, $listify_job_manager;

		$data = $output = array();

		/** Cols */
		$data[ 'grid-columns' ] = $this->get_grid_columns();

		$data = apply_filters( 'listify_listing_data', $data );

		if ( $json ) {
			return $data;
		}

		foreach ( $data as $key => $value ) {
			$output[] .= sprintf( 'data-%s="%s"', $key, $value );
		}

		return implode( ' ', $output );
	}

	/**
	 * Output Job Manger's custom template hooks that we use
	 * to attach the rest of the listing information to.
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function single_job_listing_meta() {
		do_action( 'single_job_listing_meta_start' );

		do_action( 'single_job_listing_meta_end' );

		do_action( 'single_job_listing_meta_after' );
	}

	/**
	 * Listing Title
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function the_title() {
		if ( ! get_the_title() ) {
			return;
		}
	?>
		<h1 itemprop="name" class="job_listing-title">
			<?php the_title(); ?>
		</h1>
	<?php
	}

	/**
	 * Listing Location
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function the_location() {
		echo $this->get_the_location();
	}

	public function get_the_location( $flat = false ) {
		if ( ! get_the_job_location() ) {
			return;
		}

		if ( $flat ) {
			$flat  = '<div class="job_listing-location" itemprop="address" Itemscope itemtype="http://schema.org/PostalAddress">';
			$flat .= get_the_job_location(false);
			$flat .= '</div>';

			return $flat;
		}

		ob_start();
	?>
		<div class="job_listing-location" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<?php the_job_location(); ?>
		</div>
	<?php
		$location = ob_get_clean();

		return $location;
	}

	/**
	 * Listing Location (formatted)
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function the_location_formatted() {
		echo $this->get_the_location_formatted();
	}

	public function get_the_location_formatted() {
		global $post;

		if ( true == apply_filters( 'listify_force_skip_formatted_address', false ) ) {
			return $this->get_the_location( true );
		}

		$location = get_the_job_location();

		$address = apply_filters( 'listify_formatted_address', array(
			'first_name'    => '',
			'last_name'     => '',
			'company'       => '',
			'address_1'     => $post->geolocation_street,
			'address_2'     => '',
			'street_number' => $post->geolocation_street_number,
			'city'          => $post->geolocation_city,
			'state'         => $post->geolocation_state_short,
			'full_state'    => $post->geolocation_state_long,
			'postcode'      => $post->geolocation_postcode,
			'country'       => $post->geolocation_country_short,
			'full_country'  => $post->geolocation_country_long
		), $location, $post );

		$output[ 'start' ] = '<div class="job_listing-location job_listing-location-formatted">';

		$output[ 'map-link-start' ] = sprintf( '<a class="google_map_link" href="%s" target="_blank">', $this->google_maps_url() );

		$output[ 'address' ] = '<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' . WC()->countries->get_formatted_address( $address ) . '</span>';
		$output[ 'address' ] = html_entity_decode( $output[ 'address' ] );

		$output[ 'map-link-end' ] = '</a>';
		$output[ 'end' ] = '</div>';

		$output = apply_filters( 'listify_the_location_formatted_parts', $output, $location, $post );
		$output = apply_filters( 'listify_the_location_formatted', implode( '', $output ) );

		return $output;
	}

	public function google_maps_url() {
		global $post;

		$base = 'http://maps.google.com/maps';
		$args = array(
			'saddr' => 'Current+Location',
			'daddr' => urlencode( $post->geolocation_lat . ',' . $post->geolocation_long )
		);

		return esc_url( add_query_arg( $args, $base ) );
	}

	/**
	 * Listing Phone Number
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public static function the_phone() {
		global $post;

		$phone = $post->_phone;

		if ( ! $phone ) {
			return;
		}
	?>
		<div class="job_listing-phone">
			<span itemprop="telephone"><a href="tel:<?php echo esc_attr( preg_replace( "/[^0-9,.]/", '', $phone ) ); ?>"><?php echo
			esc_attr( $phone ); ?></a></span>
		</div>
	<?php
	}

	/**
	 * Listing URL
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public static function the_url() {
		global $post;

		$url = get_the_company_website( $post->ID );

		if ( ! $url ) {
			return;
		}

		$url = esc_url( $url );
		$base = parse_url( $url );
		$base = $base[ 'host' ];
	?>
		<div class="job_listing-url">
			<span itemprop="url"><a href="<?php echo $url; ?>" rel="nofollow" target="_blank"><?php echo esc_attr( $base ); ?></a></span>
		</div>
	<?php
	}

	/**
	 * Listing Category
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function the_category() {
		$types = false;

		if ( ! listify_theme_mod( 'categories-only' ) ) {
			$types = get_the_term_list(
				get_post()->ID,
				'job_listing_type',
				'<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">',
				'<span class="ion-chevron-right"></span>',
				'</span>'
			);
		}

		$terms = false;

		if ( get_option( 'job_manager_enable_categories' ) ) {
			$crumbs = new Listify_Taxonomy_Breadcrumbs( array(
				'taxonomy' => 'job_listing_category',
				'sep' => '<span class="ion-chevron-right"></span>'
			) );
		}
	?>
		<div class="content-single-job_listing-title-category">
			<?php if ( $types ) : ?>
				<?php echo $types; ?>
				<span class="ion-chevron-right"></span>
			<?php endif; ?>

			<?php if ( ! empty( $crumbs->crumbs ) ) : ?>
				<?php $crumbs->output(); ?>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * Listing Actions
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function the_actions() {
	?>
		<div class="content-single-job_listing-actions-start">
			<?php do_action( 'listify_single_job_listing_actions_start' ); ?>
		</div>

		<?php do_action( 'listify_single_job_listing_actions_after' ); ?>
	<?php
	}
	
	public function submit_review_link() {
		global $post;

		if ( ! comments_open() || ! ( is_active_widget( false, false, 'listify_widget_panel_listing_comments', true ) || !
		is_active_sidebar( 'single-job_listing-widget-area' ) ) || 'preview' == $post->post_type ) {
			return;
		}

		if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
			$url = get_permalink( wc_get_page_id( 'myaccount' ) );
		} else {
			$url = '#respond';
		}
	?>
		<a href="<?php echo esc_url( $url ); ?>" class="single-job_listing-respond button button-secondary"><?php _e( 'Submit a Review', 'listify' ); ?></a>
	<?php
	}

	public function term_links( $term_links ) {
		$links = array();

		foreach ( $term_links as $link ) {
			$link = str_replace( 'rel="tag">', 'rel="tag" itemprop="url"><span itemprop="title">', $link );
			$link = str_replace( '</a>', '</span></a>', $link );

			$links[] = $link;
		}

		return $links;
	}

	/** Archive Page ---------------------------------------------------- */

	/**
	 * When viewing an archive output the Job Manager-specific
	 * archive results. In this case, we load the job shortcode.
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function output_results( $content ) {
		if ( '' != $content ) {
			echo do_shortcode( $content );
		} else {
			echo do_shortcode( apply_filters( 'listify_default_jobs_shortcode', '[jobs show_pagination=true]' ) );
		}
	}

	/**
	 * Add the number of found posts to the AJAX response
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function job_manager_get_listings_result( $result, $jobs ) {
		$result[ 'found_posts' ] = $jobs->found_posts;

		return $result;
	}

	/**
	 * Add a label to the job types
	 *
	 * @since Listify 1.0.0
	 *
	 * return void
	 */
	public function job_types_label() {
		if ( is_tax( 'job_listing_type' ) ) {
			return;
		}

		echo '<p class="filter-by-type-label">';
		_e( 'Filter by type:', 'listify' );
		echo '</p>';
	}

	/**
	 * Add a submit button to the bottom of the Job Manager filters
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function add_submit_button() {
		if ( listify_has_integration( 'facetwp' ) ) {
			return;
		}

		$label = sprintf( _x( 'Update %s', 'search filters submit', 'listify' ), listify_theme_mod( 'label-plural' ) );

		if ( is_front_page() ) {
			$label = sprintf( _x( 'Search %s', 'search filters submit', 'listify' ), listify_theme_mod( 'label-plural' ) );
		}

		$refreshing = __( 'Loading...', 'listify' );

		echo '<button type="submit" data-refresh="' . $refreshing . '" data-label="' . $label . '" name="update_results" class="update_results">' . $label . '</button>';
	}

	/**
	 * Add context to the form so we know where it's being submitted from.
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function add_form_context() {
		if ( is_tax() ) {
			$object = get_queried_object();
			$context = 'archive-' . $object->slug;
		} else {
			$context = get_queried_object_id();
		}

		echo '<input type="hidden" id="search_context" name="search_context" value="' . $context . '" />';
	}

	public function get_grid_columns( $style = false ) {
		global $listify_job_manager, $wp_query;

		if ( 'list' == $style ) {
			return apply_filters( 'listify_list_columns', 'col-xs-12' );
		}

		if ( 
			( listify_job_listing_archive_has_sidebar() || 'side' == $listify_job_manager->map->template->position() ) && 
			! ( listify_is_widgetized_page() || is_page_template( 'page-templates/template-full-width-blank.php' ) ) ||
			is_singular( 'job_listing' )
		) {
			$cols = apply_filters( 'listify_grid_columns_sidebar', 'col-xs-12 col-sm-6' );
		} else {
			$cols = apply_filters( 'listify_grid_columns_no_sidebar', 'col-xs-12 col-sm-6 col-md-4' );
		}

		return $cols;
	}

	/**
	 * Add a title and display switcher below the filters and above the listings.
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function style_switcher() {
		global $wp_query;

		$style = $this->get_archive_display_style();
	?>
		<div class="archive-job_listing-filter-title">
			<div class="archive-job_listing-layout-wrapper">
				<?php do_action( 'archive_job_listing_layout_before' ); ?>

				<a href="#" data-style="grid" class="archive-job_listing-layout button<?php echo 'grid' == $style ? ' active' : ''; ?>"><span class="ion-grid"></span></a>
				<a href="#" data-style="list" class="archive-job_listing-layout button<?php echo 'list' == $style ? ' active' : ''; ?>"><span class="ion-navicon-round"></span></a>

				<?php do_action( 'archive_job_listing_layout_after' ); ?>
			</div>

			<h3 class="archive-job_listing-found">
				<?php printf( __( '<span class="results-found">%s</span> Results Found', 'listify' ),
				listify_has_integration( 'facetwp' ) ? do_shortcode( '[facetwp counts="true"]' ) : 0 ); ?>
			</h3>
		</div>
	<?php
	}

	/**
	 * Add a toggle above the filters for mobile devices.
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function toggle_filters() {
		global $wp_query;
	?>
		<a href="#" data-toggle=".job_filters" class="js-toggle-area-trigger"><?php _e( 'Toggle Filters', 'listify' ); ?></a>
	<?php
	}

	/**
	 * Get the active style switcher state
	 *
	 * @since Listify 1.0.0
	 *
	 * @return string $style
	 */
	public function get_archive_display_style() {
		$default = listify_theme_mod( 'listing-archive-display-style' );

		if ( is_user_logged_in() ) {
			$style = get_user_meta( get_current_user_id(), 'listify_archive_style', true );
		} else {
			$style = isset( $_COOKIE[ 'listify_archive_style' ] ) ? $_COOKIE[ 'listify_archive_style' ] : false;
		}

		return apply_filters( 'listify_archive_display_style', $style ? $style : $default );
	}

	/**
	 * Save the style switcher style on AJAX request
	 *
	 * @since Listify 1.0.0
	 *
	 * @return void
	 */
	public function save_style() {
		$style = isset( $_POST[ 'style' ] ) ? esc_attr( $_POST[ 'style' ] ) : false;

		if ( ! $style ) {
			wp_send_json_error();
		}

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'listify_archive_style', $style );
		} else {
			$expire = time() + ( 14 * DAY_IN_SECONDS );

			setcookie( 'listify_archive_style', $style, $expire, COOKIEPATH, COOKIE_DOMAIN, false, true);
		}

		wp_send_json_success();
	}

	public function homepage_grid_columns( $result ) {
		if ( ! DOING_AJAX ) {
			return $result;
		}

		$params = array();

		parse_str( $_REQUEST[ 'form_data' ], $params );

		if ( ! isset( $params[ 'search_context' ] ) ) {
			return $result;
		}

		$context = $params[ 'search_context' ];

		if ( $context === get_option( 'page_on_front' ) && 0 != $context ) {
			$result[ 'html' ] = str_replace( 'col-xs-12 col-sm-6', 'col-xs-12 col-sm-6 col-md-4', $result[ 'html' ] );
		}

		return $result;
	}

}
