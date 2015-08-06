<?php
/*  Copyright 2012-2013  Frank Staude  (email : frank@staude.net)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class disable_title {

    /**
     * constructor
     */
    function __construct() {
        add_filter( 'the_title',                array( 'disable_title', 'the_title' ), 10, 2 );
        add_action( 'admin_menu',               array( 'disable_title', 'add_meta_box' ) );
        add_action( 'save_post',                array( 'disable_title', 'save_meta' ) );
        add_action( 'plugins_loaded',           array( 'disable_title', 'load_translations' ) );
        add_filter( 'wp_get_nav_menu_items',    array( 'disable_title', 'filter_menu' ), 10, 3 );
        add_filter( 'widget_posts_args',        array( 'disable_title', 'filter_widget' ) );
        add_filter( 'wp_list_pages_excludes',   array( 'disable_title', 'filter_default_menu' ), 10, 1 );
        
    }

    static public function filter_widget( $args ) {
        global $wpdb;
        
        $idArray = array();
        $ids = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_z8n-fs-disable-title-widget' AND meta_value = 1" );
        foreach ( $ids as $idObj ) {
            $idArray[] = $idObj->post_id;
        }
        if ( count( $idArray ) > 0 ) {
            $args[ 'post__not_in' ] = $idArray;
        }
        
        return ($args);
    }
    
    /**
     * filter to filter the wordpress menu
     * 
     * @param type $items
     * @param type $menu
     * @param type $args
     * @return type
     */
    static public function filter_menu( $items, $menu, $args ) {
        foreach ( $items as $key => $item ) {
            $id = $item->object_id;
            $unvisible = get_post_meta( $id, '_z8n-fs-disable-title-menu', true );
            if ( $unvisible == 1 ) {
                unset( $items[ $key ] );
            }
        }
        return ( $items );
    }

    /**
     * filter to filter th wordpress default menu
     * 
     * @global object $wpdb
     * @param array $args
     * @return array
     */
    static public function filter_default_menu( $args ) {
        global $wpdb;
        
        $idArray = array();
        $ids = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_z8n-fs-disable-title-menu' AND meta_value = 1" );
        foreach ( $ids as $idObj ) {
            $idArray[] = $idObj->post_id;
        }
        return ($idArray);
    }    
    
    /**
     * load texttranslations for plugin
     */
    static public function load_translations() {
        load_plugin_textdomain( 'disable_title', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * filter the title
     * 
     * @param string $title
     * @param integer $id
     * @return string
     */
    static public function the_title( $title, $id = false ) {
        if ( ! $id || ! in_the_loop() ) {
            return $title;
        }
        if ( ! is_admin() ) { // no title replacement in backend
            $page = get_post_meta( $id, '_z8n-fs-disable-title-detail', true );
            $category = get_post_meta( $id, '_z8n-fs-disable-title-category', true );
            $archive = get_post_meta( $id, '_z8n-fs-disable-title-archive', true );
            $home = get_post_meta( $id, '_z8n-fs-disable-title-home', true );

            if ( is_page() || is_single() ) {
                if ( $page == 1 ) {
                    return '';
                } else {
                    return $title;
                }
            }
            if ( is_category() ) {
                if ( $category == 1 ) {
                    return '';
                } else {
                    return $title;
                }
            }
            if ( is_archive() ) {
                if ( $archive == 1 ) {
                    return '';
                } else {
                    return $title;
                }
            }
            if ( is_home() ) {
                if ( $home == 1 ) {
                    return '';
                } else {
                    return $title;
                }
            }
        }
        return $title;
    }

    /**
     * add a metabox to page/post/cpt
     */
    static public function add_meta_box() {
        add_meta_box( 'disable_title', __( 'Title settings', 'disable_title' ), array( 'disable_title', 'disable_title_metabox' ), 'post' );
        add_meta_box( 'disable_title', __( 'Title settings', 'disable_title' ), array( 'disable_title', 'disable_title_metabox' ), 'page' );
        // now register CPT 
        $builtInPostTypes = array(
            'post' => 'post',
            'page' => 'page',
            'attachment' => 'attachment',
            'revision' => 'revision',
            'nav_menu_item' => 'nav_menu_item'
        );
        $post_types = get_post_types( '', 'names' );
        foreach ( $post_types as $post_type ) {
            if ( ! in_array( $post_type, $builtInPostTypes ) ) {
                add_meta_box( 'disable_title', __( 'Title settings', 'disable_title' ), array( 'disable_title', 'disable_title_metabox' ), $post_type );
            }
        }
    }

    /**
     * Content from the metabox
     * 
     * @param object $post
     */
    static public function disable_title_metabox( $post ) {
        $detail =   get_post_meta($post->ID, '_z8n-fs-disable-title-detail',    true );
        $category = get_post_meta($post->ID, '_z8n-fs-disable-title-category',  true );
        $archive =  get_post_meta($post->ID, '_z8n-fs-disable-title-archive',   true );
        $home =     get_post_meta($post->ID, '_z8n-fs-disable-title-home',      true );
        $menu =     get_post_meta($post->ID, '_z8n-fs-disable-title-menu',      true );
        $widget =   get_post_meta($post->ID, '_z8n-fs-disable-title-widget',    true );
        ?>
        <input type="hidden" name="z8n-fs-disable-title-posts" value="1">
        <input id="z8n-fs-disable-title-home" type="checkbox" <?php if ( $home == 1 ) echo 'checked="checked"'; ?> name="z8n-fs-disable-title-home" value="1">
        <?php _e('Disable title on homepage', 'disable_title'); ?><br />
        <input id="z8n-fs-disable-title-detail" type="checkbox" <?php if ( $detail == 1 ) echo 'checked="checked"'; ?> name="z8n-fs-disable-title-detail" value="1">
        <?php _e('Disable title on page/post', 'disable_title'); ?><br />
        <input id="z8n-fs-disable-title-category" type="checkbox" <?php if ( $category == 1 ) echo 'checked="checked"'; ?> name="z8n-fs-disable-title-category" value="1">
        <?php _e('Disable title on category page', 'disable_title'); ?><br />
        <input id="z8n-fs-disable-title-archive" type="checkbox" <?php if ( $archive == 1 ) echo 'checked="checked"'; ?> name="z8n-fs-disable-title-archive" value="1">
        <?php _e('Disable title on archive page', 'disable_title'); ?><br />
        <input id="z8n-fs-disable-title-menu" type="checkbox" <?php if ( $menu == 1 ) echo 'checked="checked"'; ?> name="z8n-fs-disable-title-menu" value="1">
        <?php _e('Disable title on menu', 'disable_title'); ?><br />
        <input id="z8n-fs-disable-title-widget" type="checkbox" <?php if ( $widget == 1 ) echo 'checked="checked"'; ?> name="z8n-fs-disable-title-widget" value="1">
        <?php _e('Disable title on widgets', 'disable_title'); ?><br />
        <?php
    }

    /**
     * Saves the post metadata
     * 
     * @param integer $post_id
     */
    static public function save_meta( $post_id ) {
        if ( isset( $_POST[ 'z8n-fs-disable-title-posts' ] ) ) {
            $detail =   isset( $_POST[ 'z8n-fs-disable-title-detail' ] ) ? 1 : 0;
            $category = isset( $_POST[ 'z8n-fs-disable-title-category' ] ) ? 1 : 0;
            $archive =  isset( $_POST[ 'z8n-fs-disable-title-archive' ] ) ? 1 : 0;
            $home =     isset( $_POST[ 'z8n-fs-disable-title-home' ] ) ? 1 : 0;
            $menu =     isset( $_POST[ 'z8n-fs-disable-title-menu' ] ) ? 1 : 0;
            $widget =   isset( $_POST[ 'z8n-fs-disable-title-widget' ] ) ? 1 : 0;
            // Update values
            update_post_meta( $post_id, '_z8n-fs-disable-title-home', $home );
            update_post_meta( $post_id, '_z8n-fs-disable-title-detail', $detail );
            update_post_meta( $post_id, '_z8n-fs-disable-title-category', $category );
            update_post_meta( $post_id, '_z8n-fs-disable-title-archive', $archive );
            update_post_meta( $post_id, '_z8n-fs-disable-title-menu', $menu );
            update_post_meta( $post_id, '_z8n-fs-disable-title-widget', $widget );
        }
    }

}
