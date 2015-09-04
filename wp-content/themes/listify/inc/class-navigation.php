<?php

class Listify_Navigation {

	public function __construct() {
		add_filter( 'wp_page_menu_args', array( $this, 'always_show_home' ) );

		add_filter( 'walker_nav_menu_start_el', array( $this, 'avatar_item' ), 10, 4 );
		add_filter( 'nav_menu_css_class', array( $this, 'avatar_item_class' ), 10, 3 );

		add_filter( 'nav_menu_css_class', array( $this, 'popup_trigger_class' ), 10, 3 );

		add_filter( 'wp_nav_menu_items', array( $this, 'search_icon' ), 1, 2 );
	}

	/**
	 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
	 */
	public function always_show_home( $args ) {
		$args['show_home'] = true;

		return $args;
	}

	/**
	 * Custom Account menu item.
	 *
	 * Look for a menu item with a title of `{{account}}` and replace the
	 * content with information about the current account.
	 *
	 * @since Listify 1.0.0
	 *
	 * @param string $item_output
	 * @param object $item
	 * @param int $depth
	 * @param array $args
	 * @return string $item_output
	 */
	public function avatar_item( $item_output, $item, $depth, $args ) {
		if ( '{{account}}' != $item->title ) {
			return $item_output;
		}

		$user = wp_get_current_user();

		if ( ! is_user_logged_in() ) {
			$display_name = apply_filters( 'listify_account_menu_guest_label', __( 'Guest', 'listify' ) );

			$avatar = '';
		} else {
			if ( $user->first_name ) {
				$display_name = $user->first_name;
			} else {
				$display_name = $user->display_name;
			}

			$display_name = apply_filters( 'listify_acount_menu_user_label', $display_name, $user );

			$avatar =
			'<div class="current-account-avatar" data-href="' . get_author_posts_url( $user->ID, $user->user_nicename ) .
			'">' .
					get_avatar( $user->ID, 90 )
			. '</div>';
		}

		$item_output = str_replace( '{{account}}', $avatar . $display_name, $item_output );

		return $item_output;
	}

	/**
	 * If the menu item has the `{{account}}` tag add a custom class to the item.
	 *
	 * @see listify_account_walker_nav_menu_start_el()
	 *
	 * @since Listify 1.0.0
	 *
	 * @param array $classes
	 * @param object $item
	 * @param array $args
	 * @return array $classes
	 */
	public function avatar_item_class( $classes, $item, $args ) {
		if ( 'primary' != $args->theme_location ) {
			return $classes;
		}

		if ( '{{account}}' != $item->title || ! is_user_logged_in() ) {
			return $classes;
		}

		$classes[] = 'account-avatar';

		return $classes;
	}

	public function popup_trigger_class( $classes, $item, $args ) {
		$popup = array_search( 'popup', $classes );

		if ( false === $popup ) {
			remove_filter( 'nav_menu_link_attributes', array( $this, 'popup_trigger_attributes' ), 10, 3 );

			return $classes;
		} else {
			unset( $classes[ $popup ] );

			add_filter( 'nav_menu_link_attributes', array( $this, 'popup_trigger_attributes' ), 10, 3 );
		}

		return $classes;
	}

	public function popup_trigger_attributes( $atts, $item, $args ) {
		$atts[ 'class' ] = 'popup-trigger-ajax';

		if ( in_array( 'popup-wide', $item->classes ) ) {
			$atts[ 'class' ] .= ' popup-wide';
		}

		return $atts;
	}

	public function search_icon( $items, $args ) {
		if ( 'primary' != $args->theme_location || ! listify_theme_mod( 'nav-search' ) ) {
			return $items;
		}

        if ( listify_has_integration( 'facetwp' ) ) {
    		return '<li class="menu-item menu-type-link"><a href="' . get_post_type_archive_link( 'job_listing' ) . '" class="search-overlay-toggle"></a></li>' . $items;
        } else {
    		return '<li class="menu-item menu-type-link"><a href="#search-header" data-toggle="#search-header" class="search-overlay-toggle"></a></li>' . $items;
        }
	}
}

$GLOBALS[ 'listify_navigation' ] = new Listify_Navigation();
