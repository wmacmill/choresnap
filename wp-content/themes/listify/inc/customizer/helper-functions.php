<?php

function listify_get_theme_mod_defaults() {
	$scheme = get_theme_mod( 'color-scheme', 'Default' );
	$schemes = listify_get_color_schemes();
	$scheme = $schemes[ $scheme ];

	$mods = array(
		// Navigation
		'nav-cart' => 1,
		'nav-search' => 1,
		'nav-megamenu' => 'job_listing_category',

		// Header Image
		'fixed-header' => 0,

		// Colors
		'color-scheme' => 'Default',
		'color-header-background' => $scheme[ 'color-header-background' ],
		'color-navigation-text' => $scheme[ 'color-navigation-text' ],
		'color-link' => $scheme[ 'color-link' ],
		'color-body-text' => $scheme[ 'color-body-text' ],
		'color-primary' => $scheme[ 'color-primary' ],
		'color-accent' => $scheme[ 'color-accent' ],
		'color-as-seen-on-background' => $scheme[ 'color-accent' ],
		'color-footer-widgets-background' => $scheme[ 'color-footer-widgets-background' ],
		'color-footer-background' => $scheme[ 'color-footer-background' ],
		
		// Header & Logo
		'fixed-header' => 0,

		// Labels & Behavior
		'label-singular' => __( 'Listing', 'listify' ),
		'label-plural' => __( 'Listings', 'listify' ),
		'region-bias' => 'US',
		'custom-submission' => true, 
		'social-association' => 'user',
		'categories-only' => true,

		// Listing Archive
		'listing-archive-output' => 'map-results',
		'listing-archive-map-position' => 'side',
		'listing-archive-display-style' => 'grid',

		// Marker Appearance

		// Map Appearance
		'map-appearance-scheme' => 'Default',

		// Map Behavior
		'map-behavior-trigger' => 'mouseover',
		'map-behavior-clusters' => 1,
		'map-behavior-grid-size' => 60,
		'map-behavior-autofit' => 1,
		'map-behavior-center' => '',
		'map-behavior-zoom' => 3,
		'map-behavior-max-zoom' => 17,
		'map-behavior-search-min' => 0,
		'map-behavior-search-max' => 100,
		'map-behavior-search-default' => 50,

		// Copyright
		'footer-style' => 'dark',
		'copyright-text' => sprintf( __( 'Copyright %s &copy; %s. All Rights Reserved', 'listify' ),
		get_bloginfo( 'name' ), date( 'Y' ) ),

		// Call to Action
		'call-to-action-display' => 1,
		'call-to-action-title' => sprintf( '%s is the best way to find & discover great local
		businesses', get_bloginfo( 'name' ) ),
		'call-to-action-description' => 'It just gets better and better', 
		'call-to-action-button-text' => 'Create Your Account',
		'call-to-action-button-href' => '',
		'call-to-action-button-subtext' => 'and get started in minutes',

		// As Seen On
		'as-seen-on-title' => '',
		'as-seen-on-logos' => '',
	);

	return apply_filters( 'listify_theme_mod_defaults', $mods );
}

function listify_theme_mod( $key ) {
	$mods = listify_get_theme_mod_defaults();
	
	$default = isset( $mods[ $key ] ) ? $mods[ $key ] : '';

	return get_theme_mod( $key, $default );
}

function listify_get_color_scheme() {
	$scheme = listify_theme_mod( 'color-scheme' );
	$schemes = listify_get_color_schemes();

	return $schemes[ $scheme ];
}

function listify_get_color_schemes() {
	$schemes = apply_filters( 'listify_color_schemes', array(
		'Default' => array(
			'background_color' => '#f0f3f6', // background
			'header_textcolor' => '#ffffff', // text
			'color-navigation-text' => '#ffffff',
			'color-header-background' => '#3396d1', // background
			'color-body-text' => '#717a8f', // text
			'color-link' => '#3396d1', // text
			'color-primary' => '#77c04b', // mixed
			'color-accent' => '#3396d1', // mixed
			'color-footer-widgets-background' => '#2f3339', // background
			'color-footer-background' => '#22262c' // mixed
		),
		'Green' => array(
			'background_color' => '#f8f8f8',
			'header_textcolor' => '#ffffff',
			'color-navigation-text' => '#ffffff',
			'color-header-background' => '#42c25e',
			'color-body-text' => '#88998a',
			'color-link' => '#42c25e',
			'color-primary' => '#42c25e',
			'color-accent' => '#2baddd',
			'color-footer-widgets-background' => '#333943',
			'color-footer-background' => '#333943'
		),
		'Dark' => array(
			'background_color' => '#f8f8f8',
			'header_textcolor' => '#ffffff',
			'color-navigation-text' => '#ffffff',
			'color-header-background' => '#333943',
			'color-body-text' => '#666666',
			'color-link' => '#2fdab8',
			'color-primary' => '#2fdab8',
			'color-accent' => '#f27935',
			'color-footer-widgets-background' => '#333943',
			'color-footer-background' => '#23272d'
		),
		'Light Gray' => array(
			'background_color' => '#f6f6f6',
			'header_textcolor' => '#818080',
			'color-navigation-text' => '#818080',
			'color-header-background' => '#ffffff',
			'color-body-text' => '#818080',
			'color-link' => '#42c25e',
			'color-primary' => '#33adc5',
			'color-accent' => '#818080',
			'color-footer-widgets-background' => '#303c40',
			'color-footer-background' => '#283235'
		)
	) );

	return $schemes;
}
