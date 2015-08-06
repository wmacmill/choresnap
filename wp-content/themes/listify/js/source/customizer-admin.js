window.cCustomizer = window.cCustomizer || {};

(function( window, $, wp ) {

	var listifyCustomizer = listifyCustomizer || {};

	cCustomizer.Schemes = function() {
		this.container = $( '#customize-control-color-scheme' );
		this.controls = [
			'background_color',
			'header_textcolor',
			'color-navigation-text',
			'color-header-background',
			'color-body',
			'color-link',
			'color-primary',
			'color-accent',
			'color-footer-widget-background',
			'color-footer-background'
		];

		this.bindEvents();
	}

	cCustomizer.Schemes.prototype.setActive = function() {
		this.active = this.container.find( 'input:checked' );
	}

	cCustomizer.Schemes.prototype.getActive = function() {
		return this.active;
	}

	cCustomizer.Schemes.prototype.bindEvents = function() {
		var self = this;

		this.container.find( 'input' ).on( 'change', function() {
			self.setActive();
			self.updateControls();
		});
	}

	cCustomizer.Schemes.prototype.updateControls = function() {
		var self = this;

		$.each( this.controls, function(i, control) {
			var schemeColor = self.getActive().data( control );
			var $el = $( '#customize-control-' + control ).find( '.color-picker-hex' );

			$el.wpColorPicker( 'color', schemeColor );
		});
	}

	$(document).on( 'ready', function() {
		listifyCustomizer.scehemes = new cCustomizer.Schemes();
	});

})( this, jQuery, wp );
