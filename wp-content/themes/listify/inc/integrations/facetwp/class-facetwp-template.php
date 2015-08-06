<?php

class Listify_FacetWP_Template extends Listify_FacetWP {

	public function __construct() {
		global $listify_job_manager;
		
		// Global template override
		add_filter( 'template_include', array( $this, 'template_include' ) );

		// Archive Listings
		remove_all_actions( 'listify_output_results' );
		add_action( 'listify_output_results', array( $this, 'output_results' ), 20 );
		add_action( 'listify_output_results', array( $this, 'output_filters' ) );

		add_action( 'archive_job_listing_layout_before', array( $this, 'archive_job_listing_layout_before' ) );

		if ( 'side' == $this->position() ) {
			add_action( 'listify_sidebar_archive_job_listing_after', array( $this, 'output_filters' ) );
		} else {
			add_action( 'listify_output_results', array( $this, 'output_filters' ), 10 );
		}

		add_filter( 'term_link', array( $this, 'term_link' ), 10, 3 );
	}

	public function term_link( $link, $term, $taxonomy ) {
        $facets = FWP()->helper->get_facets();
        $taxes  = $sources = array();
        
        $permalink = FWP()->helper->get_setting( 'term_permalink', 'term_id' );
        $permalink_type = FWP()->helper->get_setting( 'permalink_type', 'hash' );

		$term = 'term_id' == $permalink ? $term->term_id : $term->slug;

        if ( empty( $facets ) ) {
			return $link;
		}

		foreach ( $facets as $facet ) {
			if ( isset( $facet[ 'source' ] ) ) {
				$sources[ $facet[ 'name' ] ] = $facet[ 'source' ];
			}
		}

		if ( empty( $sources ) ) {
			return $link;
		}

		foreach ( $sources as $name => $source ) {
			$source = str_replace( 'tax/', '', $source );

			if ( $taxonomy == $source ) {
				$post_type = get_post_type_archive_link( 'job_listing' );

				if ( 'get' == $permalink_type ) {
					$url = add_query_arg( 'fwp_' . $name, $term, $post_type );
				} else {
					$url = $post_type . '#!/' . $name . '=' . $term;
				}

				return esc_url( $url );
			}
		}

		return $link;
	}

	public function position() {
		global $listify_job_manager;

		$position = listify_theme_mod( 'listing-archive-facetwp-position' );

		// Force if the map is already on the side
		if ( ( 'side' == $listify_job_manager->map->template->position() && $listify_job_manager->map->template->display() ) || listify_is_widgetized_page() ) {
			$position = 'top';
		}

		return $position;
	}

	public function template_include( $template ) {
		$path = 'inc/integrations/facetwp/templates';

		if ( is_post_type_archive( 'job_listing' ) ) {
			$new_template = locate_template( array( $path . '/archive-job_listing.php' ) );

			if ( '' != $new_template ) {
				return $new_template;
			}

			return $template;
		}

		return $template;
	}

	public function after_setup_theme() {

	}

	public function output_results() {
		do_action( 'listify_facetwp_sort' );

		echo '<div class="facetwp_job_listings"><ul class="job_listings">';
			echo facetwp_display( 'template', 'listings' );
		echo '</ul></div>';

		echo facetwp_display( 'pager' );
	}

	public function archive_job_listing_layout_before() {
		echo facetwp_display( 'sort' );
	}

	public function output_filters() {
		global $listify_facetwp;

    	if ( did_action( 'listify_output_results' ) && 'side' == $this->position() )	{
			return;
		}

		if ( 'side' == $this->position() ) {
			$after = $before = '';
		} else {
			$before = '<div class="row">';
			$after = '</div>';
		}

		echo '<a href="#" data-toggle=".job_filters" class="js-toggle-area-trigger">' . __( 'Toggle Filters', 'listify' ) . '</a>';

		echo '<div class="facets job_filters content-box ' . $this->position() . '">';

		echo $before;

			echo $this->output_facets();

		echo $after;

		echo '</div>';
	}

	public function output_facets() {
		global $listify_facetwp;

		$facets  = $listify_facetwp->get_facets();

		$count   = count( $facets );
		$column  = ( 0 == $count ) ? 12 : round( 12 / $count );
		$done    = 0;

		if ( $count > 3 ) {
			$column = 4;
		}

		$class = 'col-xs-12 col-sm-6 col-md-' . $column;

		if ( 'side' == $this->position() ) {
			$column = null;
			$class   = 'widget';
		}

		$output = array();

		foreach ( $facets as $key => $facet ) {
			if ( 'side' != $this->position() && ( $count > 3 && $done == 3 ) ) {
				$output[] = '</div><div class="row">';
			}

			$output[] = '<aside class="facetwp-filter ' . $class . ' widget-job_listing-archive"><h2
			class="widget-title">' . esc_attr( $facet[ 'label' ] ) . '</h2>' . facetwp_display( 'facet', $facet['name'] ) . '</aside>';

			$done++;
		}

		return implode( '', $output );
	}

}
