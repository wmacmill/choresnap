<?php
/**
 * FacetWP
 */

class Listify_FacetWP extends listify_Integration {

	public $facets;
	public $template;

	public function __construct() {
		$this->includes = array(
			'class-facetwp-template.php',
			'class-facetwp-proximity.php'
		);

		$this->integration = 'facetwp';

		parent::__construct();
	}

	public function setup_actions() {
		add_action( 'init', array( $this, 'init' ), 12 );

		add_filter( 'listify_pre_controls_listing-archive', array( $this, 'add_customizer_controls' ), 10, 3 );
		add_filter( 'listify_theme_mod_defaults', array( $this, 'add_customizer_defaults' ) );
		
		add_filter( 'facetwp_index_row', array( $this, 'index_listify_latlng' ), 10, 2 );
	}

	public function init() {
		$this->template = new Listify_FacetWP_Template;

		add_filter( 'facetwp_query_args', array( $this, 'facetwp_query_args' ), 10, 2 );
		add_filter( 'facetwp_template_html', array( $this, 'facetwp_template_html' ), 10, 2 );
	}

    function index_listify_latlng( $params, $class ) {
        if ( 'cf/geolocation_lat' == $params['facet_source'] ) {
            $lat = $params['facet_value'];

            if ( !empty( $lat ) ) {
                $lat = get_post( $params[ 'post_id' ] )->geolocation_lat;
                $lng = get_post( $params[ 'post_id' ] )->geolocation_long;

                $params['facet_value'] = $lat;
                $params['facet_display_value'] = $lng;

                $class->insert( $params );
            }

            return false;
        }

        return $params;
    }

	public function facetwp_template_html( $output, $class ) {
		if ( 'listings' != $class->template[ 'name' ] ) {
			return $output;
		}

		$query = new WP_Query( $class->query_args );

		ob_start();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				get_template_part( 'content', 'job_listing' );
			}
		} else {
			echo '<li class="col-xs-12">';
			get_template_part( 'content', 'none' );
			echo '</li>';
		}

		$output = ob_get_clean();

		return $output;
	}

	public function facetwp_query_args( $query_args, $facet ) {
		if ( 'listings' != $facet->template[ 'name' ] ) {
			return $query_args;
		}

		if ( '' == $query_args ) {
			$query_args = array();
		}

		$defaults = array(
			'post_type' => 'job_listing',
			's' => isset( $facet->http_params[ 'get' ][ 's' ] ) ? $facet->http_params[ 'get' ][ 's' ] : ''
		);

		$query_args = wp_parse_args( $query_args, $defaults );

		return $query_args;
	}

	public function add_customizer_controls( $controls, $section, $wp_customize ) {
		$controls[ 'listing-archive-facetwp-position' ] = array(
			'label' => __( 'FacetWP Filter Position', 'listify' ),
			'type' => 'select',
			'choices' => array(
				'side' => __( 'Side', 'listify' ),
				'top' => __( 'Top', 'listify' )
			)
		);

		$controls[ 'listing-archive-facetwp-defaults' ] = array(
			'label' => __( 'FacetWP Filters', 'listify' ),
			'description' => __( 'A comma (,) separated list of FacetWP facet slugs. The order they appear here will be the order they appear on your website.', 'listify' )
		);

		return $controls;
	}

	public function add_customizer_defaults( $defaults ) {
		$defaults[ 'listing-archive-facetwp-position' ] = 'side';
		$defaults[ 'listing-archive-facetwp-defaults' ] = 'keyword, location, category';

		return $defaults;
	}

	public function get_facets( $facets = false ) {
		$facets  = $facets ? $facets : listify_theme_mod( 'listing-archive-facetwp-defaults' );
		$_facets = array();

		if ( ! is_array( $facets ) ) {
			$facets = array_map( 'trim', explode( ',', $facets ) );
		}

		$facetwp = FWP();
		
		foreach ( $facets as $key => $facet_name ) {
			$facet = $facetwp->helper->get_facet_by_name( $facet_name );

			if ( ! $facet ) {
				continue;
			}

			$_facets[] = $facet;
		}

		return $_facets;
	}

	public function get_homepage_facets( $facets ) {
		$facets  = $this->get_facets( $facets );
		$_facets = array();
		$facetwp = FWP();

		$blacklist = apply_filters( 'listify_facetwp_homepage_blacklist', array( 'checkboxes', 'slider', 'date_range', 'number_range' ) );

		foreach ( $facets as $key => $facet ) {
			if ( in_array( $facet[ 'type' ], $blacklist ) ) {
				continue;
			}

			$_facets[] = $facet;
		}

		return $_facets;
	}

}

$GLOBALS[ 'listify_facetwp' ] = new Listify_FacetWP();
