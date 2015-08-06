<?php

class Listify_Customizer_Output_Colors {
	
	public function __construct() {
		$this->css = new Listify_Customizer_CSS;
		$this->scheme = listify_theme_mod( 'color-scheme', 'Default' );

		add_action( 'listify_output_customizer_css', array( $this, 'background' ), 0 );
		add_action( 'listify_output_customizer_css', array( $this, 'link' ), 10 );
		add_action( 'listify_output_customizer_css', array( $this, 'body_text' ), 20 );
		add_action( 'listify_output_customizer_css', array( $this, 'header_background' ), 30 );
		add_action( 'listify_output_customizer_css', array( $this, 'primary' ), 40 );
		add_action( 'listify_output_customizer_css', array( $this, 'accent' ), 50 );
		add_action( 'listify_output_customizer_css', array( $this, 'footer' ), 60 );
	}

	public function background() {
		$background = '#' . get_background_color();

		$this->css->add( array(
			'selectors' => array(
				'.nav-menu .sub-menu.category-list',
				'ul.nav-menu .sub-menu.category-list',
				'input',
				'textarea',
				'.site select',
				'.facetwp-facet .facetwp-checkbox:before',
				'.widget_layered_nav li a:before',
				'.site-main .content-box select',
				'.site-main .job_listings select',
				'body .chosen-container-single .chosen-single',
				'body .chosen-container-multi .chosen-choices li.search-field input[type=text]',
				'.select2-container .select2-choice',
				'.entry-content div.mce-toolbar-grp',
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $background, 3 )
			)
		) );
		
		$this->css->add( array(
			'selectors' => array(
				'.listing-cover',
				'.entry-cover',
				'.homepage-cover.page-cover',
				'.list-cover',
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $background, -10 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.nav-menu .sub-menu.category-list'
			),
			'declarations' => array(
				'background-color' => $background
			),
			'media' => 'screen and (min-width: 768px)'
		) );
		$this->css->add( array(
			'selectors' => array(
				'input',
				'textarea',
				'input[type="checkbox"]',
				'input[type="radio"]',
				'.site select',
				'.facetwp-facet .facetwp-checkbox:before',
				'.widget_layered_nav li a:before',
				'.site-main .content-box select',
				'.site-main .job_listings select',
				'.content-pagination .page-numbers',
				'.facetwp-pager .facetwp-page',
				'.js-toggle-area-trigger',
				'.chosen-container-multi .chosen-choices',
				'.wp-editor-wrap',
				'.account-sign-in',
				'.filter_by_tag',
				'.job-manager-form fieldset.fieldset-job_hours',
				'.ninja-forms-required-items',
				'.showing_jobs',
				'.summary .stock',
				'.woocommerce-tabs .woocommerce-noreviews',
				'.entry-content .rcp_form input[type="text"]:focus',
				'.entry-content .rcp_form input[type="password"]:focus',
				'.entry-content .rcp_form input[type="email"]:focus',
				'.entry-content div.mce-toolbar-grp',
				'body .chosen-container-single .chosen-single',
				'body .chosen-container-multi .chosen-choices',
				'body .chosen-container-multi .chosen-choices li.search-field input[type=text]',
				'.payment_methods li .payment_box',
				'.search-choice-close',
				'.filter_by_tag a:before',
				'.woocommerce .quantity input[type="button"]'
			),
			'declarations' => array(
				'border-color' => $this->css->darken( $background, -5 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.payment_methods li'
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $background, -5 )
			)
		) );
	}

	public function body_text() {
		$body = listify_theme_mod( 'color-body-text' );

		$this->css->add( array(
			'selectors' => array(
				'body',
				'button',
				'input',
				'select',
				'textarea',
				'.current-account-user-info',
				'.listify_widget_panel_listing_tags .tag',
				'.entry-cover.no-image',
				'.entry-cover.no-image a',
				'.listing-cover.no-image',
				'.listing-cover.no-image a:not(.button)',
				'.entry-footer .button.button-small',
				'.button[name="apply_coupon"]',
				'.button[name="apply_coupon"]:hover',
				'.widget a',
				'.content-pagination .page-numbers',
				'.facetwp-pager .facetwp-page',
				'.type-job_listing.style-list .job_listing-entry-header',
				'.type-job_listing.style-list .job_listing-entry-header a',
				'.js-toggle-area-trigger',
				'.job-dashboard-actions a',
				'body.fixed-map .site-footer',
				'body.fixed-map .site-footer a',
				'.homepage-cover .job_search_form .select:after',
				'.tabbed-listings-tabs a',
				'.archive-job_listing-toggle',
				'.map-marker-info a',
				'.map-marker-info a:hover',
				'.job-manager-form fieldset.fieldset-job_hours',
				'.listing-by-term-title a',
				'.listings-by-term-more a:hover',
				'.search_location .locate-me:hover:before',
				'.no-image .ion-ios-star:before',
				'.no-image .ion-ios-star-half:before',
				'.back-to-listing a',
				'body .chosen-container-single .chosen-single',
				'.select2-default',
				'.select2-container .select2-choice',
				'.select2-container-multi .select2-choices .select2-search-choice',
				'body .homepage-cover .chosen-container .chosen-results li',
				'.filter_by_tag a',
				'a.upload-images',
				'a.upload-images span',
				'.nav-menu .sub-menu.category-list a',
				'.woocommerce-tabs .tabs a',
				'.job-manager-bookmark-actions a'
			),
			'declarations' => array(
				'color' => $body
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.comment-meta a',
				'.commentlist a.comment-ago',
				'div:not(.no-image) .star-rating:before',
				'div:not(.no-image) .stars span a:before',
				'.cta-subtext',
				'.cta-description p',
				'.job_listing-author-descriptor',
				'.entry-meta',
				'.entry-meta a',
				'.home-widget-description',
				'.listings-by-term-content .job_listing-rating-count',
				'.listings-by-term-more a',
				'.search-form .search-submit:before',
				'.mfp-content .mfp-close:before',
				'div:not(.job-package-price) .woocommerce .amount',
				'.woocommerce .quantity',
				'.showing_jobs',
			),
			'declarations' => array(
				'color' => $this->css->darken( $body, 35 )
			)
		) );
		$this->css->add( array(
			'selectors' => array(
				'.social-profiles a',
				'.listing-gallery-nav .slick-dots li button:before'
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $body, 35 )
			)
		) );
	}

	public function link() {
		$link = listify_theme_mod( 'color-link' );

		$this->css->add( array(
			'selectors' => array(
				'a',
				'.content-pagination .page-numbers.current'
			),
			'declarations' => array(
				'color' => $link
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'a:active',
				'a:hover',
				'.primary-header .current-account-toggle .sub-menu a'
			),
			'declarations' => array(
				'color' => $this->css->darken( $link, -25 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.job_position_featured .content-box'
			),
			'declarations' => array(
				'box-shadow' => '0 0 0 3px ' . $link
			)
		) );
	}

	public function header_background() {
		$header_background = listify_theme_mod( 'color-header-background' );
		$navigation = listify_theme_mod( 'color-navigation-text' );

		$this->css->add( array(
			'selectors' => array(
				'.search-overlay',
				'.primary-header'
			),
			'declarations' => array(
				'background-color' => $header_background 
			)
		) );
		
		$this->css->add( array(
			'selectors' => array(
				'.nav-menu a',
				'.nav-menu li:before',
				'.nav-menu li:after',
				'.nav-menu a:before',
				'.nav-menu a:after',
				'.nav-menu ul a',
				'.nav-menu.primary ul ul a',
				'.nav-menu.primary ul ul li:before',
				'.nav-menu.primary ul ul li:after',
			),
			'declarations' => array(
				'color' => $header_background 
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.nav-menu.primary a',
				'.nav-menu.primary li:before',
				'.nav-menu.primary li:after',
				'.nav-menu.primary a:before',
				'.nav-menu.primary a:after',
			),
			'declarations' => array(
				'color' => $navigation
			),
			'media' => 'screen and (min-width: 992px)'
		) );

		$this->css->add( array(
			'selectors' => array(
				'.search-overlay a.search-overlay-toggle',
			),
			'declarations' => array(
				'color' => $navigation
			)
		) );

		if ( 'Dark' == $this->scheme ) {
			/* on the non-mobile menu set the top level link items to the set color */
			$this->css->add( array(
				'selectors' => array(
					'.site-header .nav-menu a',
					'.site-header .nav-menu li:before',
					'.site-header .nav-menu li:after',
					'.site-header .nav-menu a:before',
					'.site-header .nav-menu a:after',
					'.site-header .nav-menu ul ul.category-list a',
					'.nav-menu.tertiary a',
					'.nav-menu.tertiary li:before',
					'.nav-menu.tertiary li:after'
				),
				'declarations' => array(
					'color' => $navigation
				),
				'media' => 'screen and (min-width: 992px)'
			) );

			/* on the mobile menu dropdown set the links to the header color */
			$this->css->add( array(
				'selectors' => array(
					'.site-header .nav-menu ul ul a',
					'.site-header .nav-menu ul ul li:before',
					'.site-header .nav-menu ul ul li:after',
					'.nav-menu.tertiary ul a',
					'.nav-menu.tertiary ul li:before',
				),
				'declarations' => array(
					'color' => $header_background
				)
			) );

			$this->css->add( array(
				'selectors' => array(
					'ul.nav-menu .sub-menu.category-list',
					'.tertiary-navigation',
				),
				'declarations' => array(
					'background-color' => $header_background
				)
			) );

			$this->css->add( array(
				'selectors' => array(
					'.main-navigation',
					'ul.nav-menu .sub-menu.category-list .category-count',
					'.call-to-action'
				),
				'declarations' => array(
					'background-color' => $this->css->darken( $header_background, -15 )
				)
			) );

			$this->css->add( array(
				'selectors' => array(
					'ul.nav-menu .sub-menu.category-list .container:before'
				),
				'declarations' => array(
					'border-top-color' => $this->css->darken( $header_background, -15 )
				)
			) );
		} elseif ( 'Light Gray' == $this->scheme ) {
			$this->css->add( array(
				'selectors' => array(
					'.site-header .nav-menu a',
					'.site-header .nav-menu.secondary a',
					'.tertiary-navigation .nav-menu.tertiary a',
					'.tertiary-navigation .nav-menu.tertiary li:before',
					'.tertiary-navigation .nav-menu.tertiary li:after',
					'.tertiary-navigation .nav-menu.tertiary ul ul a',
					'.site-header .nav-menu li:before',
					'.site-header .nav-menu li:after',
					'.site-header .nav-menu a:before',
					'.site-header .nav-menu a:after',
					'.site-header .nav-menu ul ul a',
					'.site-header .nav-menu.primary ul ul a',
					'.site-header .nav-menu ul ul.category-list a',
					'.search-overlay a.search-overlay-toggle'
				),
				'declarations' => array(
					'color' => $navigation
				)
			) );

			$this->css->add( array(
				'selectors' => array(
					'.main-navigation',
					'.category-list .category-count'
				),
				'declarations' => array(
					'background-color' => '#' . get_background_color()
				)
			) );

			$this->css->add( array(
				'selectors' => array(
					'ul.nav-menu .sub-menu.category-list .container:before'
				),
				'declarations' => array(
					'border-top-color' => '#' . get_background_color()
				)
			) );
		}

		if ( in_array( $this->scheme, array( 'Light Gray', 'Dark' ) ) ) {
			$this->css->add( array(
				'selectors' => array(
					'.nav-menu .sub-menu.category-list'
				),
				'declarations' => array(
					'background-color' => $header_background
				),
				'media' => 'screen and (min-width: 768px)'
			) );

			/* on the mobile menu set the toggle items to the navigation color */
			$this->css->add( array(
				'selectors' => array(
					'.navigation-bar-toggle',
					'.main-navigation .search-overlay-toggle',
					'.main-navigation .search-overlay .search-overlay-toggle'
				),
				'declarations' => array(
					'color' => $navigation
				)
			) );
		}
	}

	public function primary() {
		$primary = listify_theme_mod( 'color-primary' );
		$header = listify_theme_mod( 'color-header-background' );

		$this->css->add( array(
			'selectors' => array(
				'.listify_widget_panel_listing_tags .tag.active:before',
				'.job-package-includes li:before',
				'.woocommerce-tabs .tabs .active a'
			),
			'declarations' => array(
				'color' => $primary
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.button-secondary:hover',
				'.button-secondary:focus',
				'input[type="button"].facetwp-reset:hover',
				'input[type="button"].facetwp-reset:focus',
				'.star-rating-wrapper a:hover ~ a:before',
				'.star-rating-wrapper a:hover:before',
				'.star-rating-wrapper a.active ~ a:before',
				'.star-rating-wrapper a.active:before',
				'.woocommerce-tabs .stars span a:hover:before',
				'.woocommerce-tabs .stars span a.active:before',
				'.woocommerce-tabs .stars span a.hover:before',
				'.tabbed-listings-tabs a:hover',
				'.tabbed-listings-tabs a.active',
				'.archive-job_listing-toggle.active'
			),
			'declarations' => array(
				'color' => $this->css->darken( $primary, -35 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'button:not([role="presentation"])',
				'input[type="button"]',
				'input[type="reset"]',
				'input[type="submit"]',
				'.button',
				'.facetwp-type-slider .noUi-connect',
				'.ui-slider .ui-slider-range',
				'.listing-owner',
				'.comment-rating',
				'.job_listing-rating-average',
				'.map-marker.active:after',
				'.cluster',
				'.widget_calendar tbody a',
				'.job_listing-author-info-more a:first-child',
                '.load_more_jobs',
			),
			'declarations' => array(
				'background-color' => $primary

			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.primary.nav-menu .current-cart .current-cart-count'
			),
			'declarations' => array(
				'background-color' => $primary,
				'border-color' => $header
			),
			'media' => 'screen and (min-width: 992px)'
		) );
    
		if ( 'Green' == $this->scheme ) {
			$this->css->add( array(
				'selectors' => array(
					'.primary.nav-menu .current-cart .current-cart-count'
				),
				'declarations' => array(
					'color' => $primary,
					'background-color' => '#fff'
				),
				'media' => 'screen and (min-width: 992px)'
			) );
		} elseif ( 'Light Gray' == $this->scheme ) {
			$this->css->add( array(
				'selectors' => array(
					'.primary.nav-menu .current-cart .current-cart-count'
				),
				'declarations' => array(
					'color' => listify_theme_mod( 'color-header-background' )
				),
				'media' => 'screen and (min-width: 992px)'
			) );
		}

		$this->css->add( array(
			'selectors' => array(
				'button:not([role="presentation"]):hover',
				'button:not([role="presentation"]):focus',
				'input[type="button"]:hover',
				'input[type="button"]:focus',

				'input[type="reset"]:hover',
				'input[type="reset"]:focus',
				'input[type="submit"]:hover',
				'input[type="submit"]:focus',
				'.button:hover',
				'.button:focus',
				'::selection',
				'.load_more_jobs:hover',
				'.update_results.refreshing'
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $primary, -5 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'::-moz-selection'
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $primary, -5 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.facetwp-type-slider .noUi-horizontal .noUi-handle',
				'.ui-slider .ui-slider-handle',
				'.tabbed-listings-tabs a:hover',
				'.tabbed-listings-tabs a.active',
				'.archive-job_listing-toggle.active',
				'li.job-package:hover',
				'.job_listing_packages ul.job_packages li:hover',
				'.woocommerce-info'
			),
			'declarations' => array(
				'border-color' => $primary
			)
		) );
	}

	public function accent() {
		$accent = listify_theme_mod( 'color-accent' );

		$this->css->add( array(
			'selectors' => array(
				'input[type=checkbox]:checked:before',
				'.facetwp-facet .facetwp-checkbox.checked:after',
				'.facetwp-facet .facetwp-link.checked',
				'.widget_layered_nav li.chosen a:after',
				'.widget_layered_nav li.chosen a',
				'.ion-ios-star:before',
				'.ion-ios-star-half:before',
				'.upload-images:hover .upload-area',
				'.comment-author .rating-stars .ion-ios-star',
				'.archive-job_listing-layout.button.active',
				'.job_listing_packages ul.job_packages li label',
				'.upload-images:hover',
				'.filter_by_tag a:after',
				'.search-choice-close:after',
				'.claimed-ribbon span:before'
			),
			'declarations' => array(
				'color' => $accent
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.button-secondary',
				'input[type="button"].facetwp-reset',
				'.job_listing-author-info-more a:last-child'
			),
			'declarations' => array(
				'background-color' => $accent
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.button-secondary:hover',
				'.button-secondary:focus',
				'input[type="button"].facetwp-reset:hover',
				'input[type="button"].facetwp-reset:focus'
			),
			'declarations' => array(
				'background-color' => $this->css->darken( $accent, -5 )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.upload-images:hover'
			),
			'declarations' => array(
				'border-color' => $accent
			)
		) );
	}

	public function footer() {
		$widgets = listify_theme_mod( 'color-footer-widgets-background' );
		$copy = listify_theme_mod( 'color-footer-background' );

		$this->css->add( array(
			'selectors' => array(
				'.site-footer-widgets'
			),
			'declarations' => array(
				'background-color' => $widgets
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.site-footer'
			),
			'declarations' => array(
				'background-color' => $copy
			)
		) );
	}

}

new Listify_Customizer_Output_Colors();
