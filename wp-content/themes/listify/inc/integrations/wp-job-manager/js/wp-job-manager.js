(function($) {
	'use strict';

	var listifyWPJobManager = {
		cache: {
			$document: $(document),
			$window: $(window)
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.cache.$target = $( '.job_listings' );
				
				self.initHeader();
				self.initFilters();
				self.initSearch();
				self.submitButton();
				self.initTimePickers();
				self.initTabbedListings();
				self.initBusinessHours();
				self.initApply();
				self.previewListing();
				self.filterTags();
			});

			this.cache.$window.on( 'resize', function() {
				self.initHeader();
			});
		},

		initHeader: function() {
			var fixedHeader = $( 'body' ).hasClass( 'fixed-header' );
			var fixedMap    = $( 'body' ).hasClass( 'fixed-map' );
			var adminBar    = $( 'body' ).hasClass( 'admin-bar' );

			var $body = $( 'body' );
			var headerHeight = parseInt( $( '.primary-header' ).outerHeight() );
			var navigationHeight = parseInt( $( '.main-navigation' ).outerHeight() );
			var adminBarHeight = parseInt( $( '#wpadminbar' ).outerHeight() );

			if ( $( 'html' ).outerWidth() < 992 ) {
				$( 'body' ).css( 'padding-top', 0 );
				$( '.site-content' ).css( 'margin-top', 0 );
				$( 'body.fixed-map.admin-bar .job_listings-map-wrapper' ).css( 'top', 0 );

				return;
			}

			if ( fixedHeader && ! fixedMap ) {
				$( '.fixed-header' ).css( 'padding-top', headerHeight );
			}

			if ( fixedHeader && fixedMap && adminBar ) {
				$( '.site-header' ).css( 'top', parseInt( headerHeight ) + parseInt( adminBarHeight ) );
			} else if ( fixedHeader && fixedMap ) {
				$( '.site-header' ).css( 'top', headerHeight );
			}

			if ( fixedMap && ( $( 'body' ).hasClass( 'archive' ) || $( 'body' ).hasClass( 'job-manager-archive' ) ) ) {
				var offset = headerHeight + navigationHeight;

				$( 'body.fixed-map.admin-bar .job_listings-map-wrapper' ).css( 'top', offset );
				$( '.site-content' ).css( 'margin-top', offset );

				if ( adminBar ) {
					$( 'body.fixed-map.admin-bar .job_listings-map-wrapper' ).css( 'top', offset + adminBarHeight
					);
				}
			}
		},

		initSearch: function() {
			$( '.search-overlay-toggle[data-toggle]' ).click(function(e) {
				e.preventDefault();

				$( $(this).data( 'toggle' ) )
					.toggleClass( 'active' );
			});

			$( '.listify_widget_search_listings form.job_filters' )
				.removeClass( 'job_filters' )
				.addClass( 'job_search_form' )
				.prop( 'action', listifySettings.archiveurl );

			$( 'button.update_results' ).on( 'click', function() {
				$(this).parent( 'form' ).submit();
			});

			$( 'form.job_search_form input' ).keypress(function(event) {
				if ( event.which == 13 ) {
					event.preventDefault();
					$( 'form.job_search_form' ).submit();
				}
			});

			$( 'form.job_search_form' ).on( 'submit', function(e) {
				var info = $(this).serialize();

				window.location.href = listifySettings.archiveurl + '?' + info;
			});
		},

		initFilters: function() {
			var filters = [ $( 'ul.job_types' ), $( '.filter_by_tag' ) ];

			$.each(filters, function(i, el) {
				if ( el.outerHeight() > 140 ) {
					el.addClass( 'too-tall' );
				}
			});

			if ( $( '.home' ).find( '.job_types' ) && ! $( '.home .job_types' ).is( ':visible' ) ) {
				$( '.home input[name="filter_job_type[]"]' ).remove();
			}
		},

		submitButton: function() {
			$( '.update_results' ).on( 'click', function(e) {
				e.preventDefault();

				$( 'div.job_listings' ).trigger( 'update_results', [1, false] );
			});
		},

		initTimePickers: function() {
			$( '.timepicker' ).timepicker({
				timeFormat: listifySettings.l10n.timeFormat,
				noneOption: {
					label: listifySettings.l10n.closed,
					value: listifySettings.l10n.closed
				}
			});
		},

		initTabbedListings: function() {
			var $tabWrapper = $( '.tabbed-listings-tabs-wrapper' );
			var $buttonsWrapper = $( '.tabbed-listings-tabs' );

			$tabWrapper.find( '> div' ).hide().filter( ':first-child' ).show();
			$buttonsWrapper.find( 'li:first-child a' ).addClass( 'active' );

			$buttonsWrapper.on( 'click', 'li:not(:last-child) a', function(e) {
				e.preventDefault();

				$buttonsWrapper.find( 'li a' ).removeClass( 'active' );
				$(this).addClass( 'active' );

				var activeTab = $(this).attr( 'href' );

				$tabWrapper.find( '> div' ).hide().filter( activeTab ).show();
			});
		},

		initBusinessHours: function() {
			$( '.fieldset-job_hours label' ).click(function(e) {
				e.preventDefault();

				$(this)
					.parent()
					.toggleClass( 'open' )
					.end()
					.next()
					.toggle();
			});
		},

		initApply: function() {
			$( '.job_application.application' ).addClass( 'popup' );
		},

		previewListing: function() {
			if ( $( '.job_listing_preview' ).length ) {
				$( '#main' ).addClass( 'preview-listing' );

				$( '.job_listing_preview.single_job_listing' )
					.removeClass( 'single_job_listing' )
					.addClass( 'single-job_listing' );
			}
		},

		filterTags: function() {
			$('.filter_by_tag').contents().filter(function() {
				return this.nodeType === 3;
			}).each(function() {
				this.nodeValue = $.trim(this.nodeValue);
			}).wrap('<span class="filter-label"></span>');
		}
	};

	listifyWPJobManager.init();

	window.cSwitcher = window.cSwitcher || {};

	var switcher = switcher || {};

	cSwitcher.Buttons = function() {
		this.bindEvents();
	}

	cSwitcher.Buttons.prototype.bindEvents = function() {
		var self = this;

		$( '.archive-job_listing-layout' ).click(function(e) {
			e.preventDefault();

			self.toggle($(this));

			switcher.user.savePreference();

			return switcher.results.updateStyle();
		});

		switcher.dom.target.on( 'updated_results', function(event, result) {
			return switcher.results.updateStyle();
		});
	}

	cSwitcher.Buttons.prototype.toggle = function(clicked) {
		var $el = clicked,
		    style = $el.data( 'style' );

		switcher.dom.buttons.find( 'a').removeClass( 'active' );
		$el.addClass( 'active' );

		switcher.results.setStyle(style);
	}

	cSwitcher.Buttons.prototype.getActiveStyle = function() {
		return switcher.dom.buttons.find( '.active' ).data( 'style' )
	}

	cSwitcher.Results = function(style) {
		this.setStyle(style);
	}

	cSwitcher.Results.prototype.setStyle = function(style) {
		if ( ! style ) {
			this.style = switcher.buttons.getActiveStyle()
		} else {
			this.style = style;
		}
	}

	cSwitcher.Results.prototype.updateStyle = function() {
		var columns = $( '.type-job_listing' ).data( 'grid-columns' );
		var $items = $( '.type-job_listing' );

		$items
			.removeClass( 'style-grid style-list' )
			.addClass( 'style-' + this.style )
			.removeClass (function (index, css) {
				return (css.match (/\bcol-\S+/g) || []).join(' ');
			});

		if ( 'grid' == this.style ) {
			$items.addClass( columns );
		} else {
			$items.addClass( 'col-xs-12' );
		}
	}

	cSwitcher.User = function() {

	}

	cSwitcher.User.prototype.savePreference = function() {
		var data = {
			action: 'listify_save_archive_style',
			style: switcher.results.style
		}

		$.ajax({
			url: listifySettings.ajaxurl,
			data: data,
			dataType: 'json',
			type: 'POST'
		}).done(function(response) {

		});
	}

	$(document).on( 'ready', function() {
		switcher.dom = {
			target: $( '.job_listings' ),
			buttons: $( '.archive-job_listing-layout-wrapper' )
		}

		if ( ! $( 'body' ).hasClass( 'job-manager-archive' ) ) { 
			return;
		}

		switcher.buttons = new cSwitcher.Buttons();
		switcher.results = new cSwitcher.Results();
		switcher.user    = new cSwitcher.User();

		$(window).on( 'resize', function() {
			if ( $(window).width() < 786 ) {
				switcher.dom.buttons
					.find( 'a' )
					.filter( ':first-child' )
					.trigger( 'click' )
					.end()
					.hide();
			} else {
				switcher.dom.buttons.show();
			}
		});
	});

})(jQuery);

