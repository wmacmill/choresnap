(function($) {
	'use strict';

	var listifyWooCommerce = {
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
				self.initRatings();
				self.initSocialLogin(false);
				self.initPackageSelection();

				$( 'body' ).on( 'popup-trigger-ajax', function() {
					self.initSocialLogin( $( '.popup' ) );
				});
			});
		},

		initRatings: function() {
			$( '.comment-form-rating' ).on( 'hover click', '.stars span a', function() {
				$(this)
					.siblings()
						.removeClass( 'hover' )
						.end()
					.prevAll()
						.addClass( 'hover' );
			});
		},

		initSocialLogin: function(search) {
			if ( ! search ) {
				search = $( 'body' );
			}

			var $social = search.find( $( '.woocommerce:not(body) .wc-social-login' ) );

			if ( ! $social.length ) {
				return true;
			}

			if ( $social.hasClass( 'wc-social-login-link-account' ) ) {
				return true;
			}

			var $clone = $social.clone();
			var $container = $social.parents( '.woocommerce:not(body)' );

			$social.remove();
			$container.append( $clone );
		},

		initPackageSelection: function() {
			var selectedPackage = $( '#listify_selected_package' );
			
			if ( selectedPackage.length == 0 ) {
				return;
			}
			
			var value = selectedPackage.val();

			$( '.job_listing_packages' ).find( '#package-' + value ).attr( 'checked', 'checked' );
		}
	};

	listifyWooCommerce.init();

})(jQuery);
