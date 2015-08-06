<?php
/*
Plugin Name: FacetWP - Proximity
Plugin URI: https://facetwp.com/
Description: A FacetWP facet to filter posts by proximity
Version: 1.2.1
Author: Matt Gibbs
Author URI: https://facetwp.com/
GitHub Plugin URI: https://github.com/FacetWP/facetwp-proximity

Copyright 2014 Matt Gibbs

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


class FWP_Proximity
{

    function __construct() {
        add_action( 'init' , array( $this, 'init' ), 0 );
    }


    /**
     * Intialize
     */
    function init() {
        add_filter( 'facetwp_facet_types', array( $this, 'register_facet_type' ) );
        add_filter( 'facetwp_sort_options', array( $this, 'sort_options' ), 1, 2 );
        add_filter( 'facetwp_filtered_post_ids', array( $this, 'sort_by_distance' ), 10, 2 );
    }


    /**
     * Add "Distance" to the sort box
     */
    function sort_options( $options, $params ) {

        $options['distance'] = array(
            'label' => __( 'Distance', 'fwp' ),
            'query_args' => array(
                'orderby' => 'post__in',
                'order' => 'ASC',
            ),
        );
        return $options;
    }


    /**
     * After the final list of post IDs has been produced,
     * sort them by distance if needed
     */
    function sort_by_distance( $post_ids, $class ) {

        $ordered_posts = FWP()->helper->facet_types['proximity']->ordered_posts;

        if ( !empty( $ordered_posts ) ) {

            // Sort the post IDs according to distance
            $intersected_ids = array();

            foreach ( $ordered_posts as $p ) {
                if ( in_array( $p, $post_ids ) ) {
                    $intersected_ids[] = $p;
                }
            }
            $post_ids = $intersected_ids;
        }
        return $post_ids;
    }


    /**
     * Register the "proximity" facet type
     */
    function register_facet_type( $facet_types ) {

        include( dirname( __FILE__ ) . '/includes/proximity.php' );
        $facet_types['proximity'] = new FacetWP_Facet_Proximity();
        return $facet_types;
    }
}


$fwp_proximity = new FWP_Proximity();
