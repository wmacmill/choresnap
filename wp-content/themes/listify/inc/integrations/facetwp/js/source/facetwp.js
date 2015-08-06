(function($) {
	'use strict';

	var listifyFacetWP = {
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
				self.megamenu();
				self.sorting();
			});
		},

		megamenu: function() {
			$( 'body.facetwp .category-list a' ).click(function() {
				window.location.hash = '';
				window.location.href = $(this).attr( 'href' );
				window.location.reload();
			});
		},

		sorting: function() {
			var self = this;

			this.cache.$document.on( 'facetwp-loaded facetwp-refresh', function() {
				$( 'select' ).wrap( '<span class="select"></span>' );
			});
		}
	};

	listifyFacetWP.init();

})(jQuery);
