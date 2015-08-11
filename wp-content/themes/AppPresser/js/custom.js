(function(window, document, $, undefined){
	'use strict';

	// Initiate our object and vars
	var app = {
		// make sure localize_script is called (or bail)
		appp               : typeof window.appp !== 'undefined' ? window.appp : false,
		// Check for woocommerce plugin
		woo                : typeof window.apppwoo !== 'undefined' ? window.apppwoo : false,
		// Initialize snap.js left panel menu
		snapper            : new Snap({
			element         : document.getElementById('page'),
			disable         : 'right',
			hyperextensible : false,
			touchToDrag     : false // animation is too choppy
		}),
		spinner            : null,
		scriptsLoaded      : {},
		stylesLoaded       : {},
		xhr                : [],
		scriptsLoadedCount : 0,
		stylesLoadedCount  : 0,
		backhref           : '',
		backLoad		   : false,
		history			   : [],
		laststate          : window.location.href,
		timeout            : false,
		isWidth600         : true,
		$                  : {},
		modalID			   : ''
	};

	app.cacheSelectors = function() {
		app.$.body        = $('body');
		app.$.main        = $('#main');
		app.$.ajaxModal   = $('#ajaxModal');
		app.$.modalInside = $('.modal-inside');
		app.$.ioModal     = $('.io-modal');
	}

	app.init = function() {

		app.cacheSelectors();

		var isWidth600Check;

		if ( ! app.appp )
			return;

		app.logGroup( 'apppresser.init()' );

		app.log( 'window.appp', app.appp );
		app.log( 'window.apppwoo', app.woo );

		// Check for loaded scripts/styles
		setTimeout( function() {
			app.scriptLoader();
			app.styleLoader();
			app.setCurrentNav();
		}, 1000);

		// load with a fresh pushstate
		window.history.replaceState({}, '', window.location.href);

		app.history.unshift({
            url: window.location.href
        });
        if( !localStorage['urlHistory'] )
        localStorage['urlHistory'] = JSON.stringify( app.history ) ;

		// Load spinner
		app.$.body.append('<div class="ajax-spinner"><div class="spinner"></div></div>');
		app.$.spinner = $('.ajax-spinner');


		$('.site-header').on('touchmove',function(e){ e.preventDefault(); });
		$('.site-footer').on('touchmove',function(e){ e.preventDefault(); });


		var hammertime = new Hammer( $('.site-title-wrap').get(0) );

		hammertime.on('doubletap', function(ev) {
		    app.$.main.animate({scrollTop:0}, 'fast');
		});

		// force external links to inappbrowser
		$('body').on( 'click', '#main a', function(e) {
		   var a = new RegExp('/' + window.location.host + '/');
		   if( !a.test(e.target.href) ) {
	       		if ( !$(this).is('[href*="#"]') && e.target.tagName == 'A' ) {
		   			e.preventDefault();
		   			e.stopPropagation();
		   			window.open(e.target.href, '_blank');
		   		}
		   }
   		});


		// Check if width is > 600px
		if ( window.matchMedia ) {
			// Establishing media check
			isWidth600Check = window.matchMedia( '(min-width: 600px)' );
			// Add listener for detecting changes
			isWidth600Check.addListener( function( mediaQueryList ) {
				app.isWidth600 = mediaQueryList.matches;
				app.log( 'Width ' + ( app.isWidth600 ? '>' : '<' ) +' 600' );
			});
		}

		app.logGroup( true );

		// Setting initial values
		app.isWidth600 = isWidth600Check && isWidth600Check.matches;

		/**
		 * Multi-level left panel menu
		 */

		app.$.subMenu = $('.snap-drawer li').has('ul.sub-menu, ul.children').children('a').addClass('has-sub-menu'); // For sub-menu
		// Add back button
		app.$.subMenu.next('ul').prepend('<a href="#" class="menu-back"><i class="fa fa-angle-left fa-lg"></i> '+l10n.back+'</a>' );
		// Add right arrow to links with a sub menu
		app.$.subMenu.append('<i class="fa fa-angle-right fa-lg pull-right"></i>');


		/**
		 * Ajax panel
		 */
		/* This needs to be more flexible to allow users to use the panel in custom ways. Instead of listing selectors, we could add a class of .panel-toggle to the element, then just say anything with .panel-toggle will open the panel.  We can add a theme option so people can add any selector to use the panel. For example:
		var ajaxitems = 'ul.products li a, a.module, a.setting, a.cart-contents, .panel-toggle a, a.panel-toggle';
		var ajaxitems = [get the setting - array of selectors]; // retrieve selectors from theme option, store them in a variable
		$(ajaxitems).addClass('panel-toggle'); // add .panel-toggle to any selectors the user enters. Must be an <a> tag.
		*/
		app.backhref = app.woo && app.woo.is_shop ? app.woo.shop_url : app.appp.home_url;

		app.$.body
			// @TODO make right panel work for version 1.x
			// .on( 'click', '.app-panel .back-btn', function(event) {
			// 	event.preventDefault();
			// 	$('html').removeClass('open-panel');

			// 	app.log('goback', app.backhref );

			// 	app.change_url();

			// 	// Delay to keep panel from going blank while sliding
			// 	setTimeout( function(){
			// 		$('.item-content #main').remove();
			// 		// Hide app panel to prevent transition bug
			// 		$('.app-panel').hide();
			// 	}, 500);

			// })
			.on('click', '.ajaxify, .ajaxify a, .blog .post a, li.previous a, li.next a, .entry-meta a, .entry-title a, .page-links a, .comment-author a, .woocommerce-pagination a, a.wc-forward', function(event) {
				var $self = $(this);

				if ( app.canAjax( $self ) )
					app.loadAjaxContent( $self.attr('href'), false, event );

			})
			// Slide open drawer submenus while ajax-loading the main menu item's page
			.on('click', '.snap-drawer li a', function(event) {
				var $self      = $(this);
				var hasSubMenu = $self.hasClass( 'has-sub-menu' );
				var hasExternal = $self.parent().hasClass( 'external' );

				if ( hasSubMenu ) {
					$( event.target ).next('ul').addClass('open-sub-menu');
					// event.preventDefault();
				}

				if ( hasExternal ) {
					window.open( $(this).attr( 'href' ), '_blank' );
				}

				if ( app.canAjax( $self ) ) {
					app.loadAjaxContent( $self.attr('href'), false, event );

					// If smaller screen, hide the menu onclick
					if ( ! app.isWidth600 && app.snapper.state().state == 'left' && ! hasSubMenu )
						app.snapper.close();
				}


			})
			// ajax load pages on footer menu tab clicks
			.on('click', '.footer-menu a', function(event) {
				var $self = $(this);

				if ( app.canAjax( $self ) ) {
					app.loadAjaxContent( $self.attr('href'), false, event );

					// If smaller screen, hide the menu onclick
					if ( ! app.isWidth600 && app.snapper.state().state == 'left' )
						app.snapper.close();
				}


			})
			// ajax load previously visited pages
			.on('click', 'a.back', function(event) {

				event.preventDefault();

				app.backLoad = true;

				var prevUrl = JSON.parse( localStorage['urlHistory'] );

				if( prevUrl.length <= 1 ) return;

				// remove the current url from array
				prevUrl.shift();

				setTimeout( function() {
					app.loadAjaxContent( prevUrl[0]['url'], false, event );
				}, 0);

				localStorage['urlHistory'] = JSON.stringify( prevUrl ) ;

			})
			.on( 'click', '.menu-back', function(event) {
				event.preventDefault();
				// Close sub menu if back button is clicked
				$(this).closest('ul').removeClass('open-sub-menu');
			})
			// Bootstrap Modal
			.on( 'click', 'a.modal-toggle, .modal-toggle a', function(event) {
				if ( ! apppresser.canModal( $(this) ) )
					return;
				event.preventDefault();
				var content = this.href+' #content';
				app.$.ajaxModal.modal();
				app.$.modalInside.load( content );
			})
			// Load login screen in modal.
			//.on( 'click', '[href*="wp-login.php"]', function(event) {
				//event.preventDefault();
				//var content = this.href+' #login';
				//app.$.ajaxModal.modal();
				//app.$.modalInside.load( content );
			//})
			// Panel open
			.on( 'click', '#nav-left-open', function(){
				// Close left panel menu if it's open
				if( app.snapper.state().state == 'left' )
					app.snapper.close();
				else
					app.snapper.open( 'left' );
			})
			/*
			* modal window a work in progress...
			*/
			.on( 'click', '.io-modal-open, .io-modal-close', function(event) {
				event.preventDefault();

				var UpClasses   = 'slide-in-up-add ng-animate slide-in-up slide-in-up-add-active';
				var downClasses = 'slide-in-up-remove slide-in-up-remove-active';

				if ( $(this).hasClass( 'io-modal-open' ) ) {

					//get href of button that matches id of modal div
					app.modalID = $(this).attr('href');

					if( app.modalID == '#loginModal') {
						$('input[name=redirect_to]').val(window.location);
					}

					// need to move .css to css file
					$(app.modalID).css('display', 'block').removeClass(downClasses).addClass(UpClasses);


					// focus on textarea needs delay for modal slide up
					setTimeout(function() {
						$('textarea#whats-new').focus();
					}, 1000);

				} else {

					// slide down modal and put it back in the content area.
					$('.io-modal').removeClass(UpClasses).addClass(downClasses).css('display', 'none');
					$('form').trigger("reset");

				}
			});

		// Close ioModal when opening snap drawer
		app.snapper.on( 'open', function(){
			if ( app.$.ioModal.data( 'isOpen' ) ) {
				app.ioModal.close();
			}
		});

		// Instantiate fastclick
		FastClick.attach(document.body);

		// Display alert when device goes offline
		document.addEventListener("offline", function () {
			alert( l10n.offline );
		}, false);

		// For iscroll - fixes scrolling problem on Android 2.3
		// var myScroll;
		// function loaded() {
		// 	setTimeout(function () {
		// 		myScroll = new iScroll('main');
		// 	}, 100);
		// }
		// window.addEventListener('load', loaded, false);

		// var addEvent = function addEvent(element, eventName, func) {
		// 	if (element.addEventListener) {
		// 		return element.addEventListener(eventName, func, false);
		// 	} else if (element.attachEvent) {
		// 		return element.attachEvent("on" + eventName, func);
		// 	}
		// };
		// addEvent(document.getElementById('nav-left-open'), 'click', function(){
		// 	snapper.open('left');
		// });

	}

	app.ioModal = (function(){
		var UpClasses   = 'slide-in-up-add ng-animate slide-in-up slide-in-up-add-active';
		var downClasses = 'slide-in-up-remove slide-in-up-remove-active';

		return {
			open: function() {
				app.$.ioModal
					.removeClass( downClasses )
					.addClass( UpClasses )
					.data( 'isOpen', true )
					.trigger('isOpen');
			},
			close: function() {
				app.$.ioModal
					.removeClass( UpClasses )
					.addClass( downClasses )
					.data( 'isOpen', false )
					.trigger('isClosed');

				// iOS scroll fix
				setTimeout( function() { app.$.ioModal.removeClass( downClasses ); }, 150 );
			}
		}
	})();

	app.setCurrentNav = function() {
		// get current page's corresponding nav item
		var $current = $('.has-sub-menu[href="'+ window.location.href +'"]');

		if ( $current.length ) {
			// Open its corresponding submenu
			$current.next('ul').addClass('open-sub-menu');
			var $parent = $current.parents( '.sub-menu' );
			while ( $parent.length ) {
				// And any submenu's above it
				$parent.addClass('open-sub-menu');
				$parent = $parent.parents( '.sub-menu' );
			}
		}
	}

	app.scriptLoader = function( $scripts ) {
		'use strict';

		$scripts = $scripts || $( 'script[src]' );
		var addedscripts = {}, filename, src, count = 0, counted = ( 'length' in app.scriptsLoaded );

		app.scriptsLoaded['length'] = counted ? app.scriptsLoaded['length'] : 0;

		$scripts.each(function () {
			var $self = $(this);

			if ( $self.data('loaded') === true )
				return true;

			src      = $self.attr('src');
			filename = src.replace(/^.*[\\\/]/, '').replace(/(\?.*)|(#.*)/g, '');

			// if ( $.inArray( filename, app.scriptsLoaded ) !== -1 )
			if ( filename in app.scriptsLoaded )
				return true;

			app.scriptsLoaded['length']++;
			count++;
			app.scriptsLoaded[filename] = src;
			addedscripts[filename] = src;
			// addedscripts.push( filename );
			$self.data('loaded', true);
		});

		app.log( 'scriptLoader' );

		if ( ! count ) {
			app.log( 'No new scripts to load.' );
			return addedscripts;
		}

		if ( counted )
			app.log( 'addedscripts', count, addedscripts );
		app.log( 'app.scriptsLoaded', app.scriptsLoaded );

		app.log( 'END scriptLoader' );

		return addedscripts;

	}

	app.styleLoader = function( $styles ) {
		'use strict';

		$styles   = $styles || $( 'link[type="text/css"]' );

		var addedStyles = {}, filename, src, count = 0, counted = ( 'length' in app.stylesLoaded );

		app.stylesLoaded['length'] = counted ? app.stylesLoaded['length'] : 0;

		$styles.each(function () {
			var $self = $(this);

			if ( $self.data('loaded') === true )
				return true;

			src      = $self.attr('href');
			filename = src.replace(/^.*[\\\/]/, '').replace(/(\?.*)|(#.*)/g, '');

			// if ( $.inArray( filename, app.stylesLoaded ) !== -1 )
			if ( filename in app.stylesLoaded )
				return true;

			app.stylesLoaded['length']++;
			count++;
			app.stylesLoaded[filename] = src;
			addedStyles[filename] = src;
			// addedStyles.push( filename );
			$self.data('loaded', true);
		});

		app.log( 'styleLoader' );

		if ( ! count ) {
			app.log( 'No new styles to load.' );
			return addedStyles;
		}

		if ( counted )
			app.log( 'addedStyles', count, addedStyles );
		app.log( 'app.stylesLoaded', app.stylesLoaded );

		app.log( 'END styleLoader' );

		return addedStyles;

	}

	app.loadAjaxContent = function( href, $selector, event) {
		'use strict';

		// var for passed event target
		var that = event.target;

		var fragments = href.split( '/' );
		// Don't ajax page fragments
		if ( fragments[fragments.length-1].charAt(0) === '#' && !$(that).parent().hasClass('back') )
			return;


		if ( event )
			event.preventDefault();

		// Don't bother re-fetching this page's content
		if ( app.untrailingslashit( href ) === app.untrailingslashit( window.location.href ) )
			return;

		// @TODO get ajax working on main nav items
		// (localized data not working)

		var titles = {
			'title'    : $('title'),
			'navtitle' : $('.site-title-wrap h1')
		};
		$selector  = $selector || app.$.main;
		href       = href || this.href;

		// Cancel pending timeout actions
		if (app.timeout)
			clearTimeout(app.timeout);

		// Cancel pending xhr requests
		$.each(app.xhr, function( index, request ) {
			// app.log( 'aborting', index, request.requestURL );
			request.abort();
		});

		if ( app.doingAjax === true )
			return;
		app.doingAjax = true;

		// Show ajax spinner
		app.$.spinner.show();
		
		setTimeout(function() {
			//app.$.spinner.hide();
		}, 60000);

		// Do our ajax
		var status = $.ajax({
			url: href,
			type: 'GET',
			dataType: 'html',
			cache: false
		}).done(function( responseText ) {

		//console.log(responseText);

			var html       = $("<div>").append( $.parseHTML( responseText, document, true ) );
			var $main      = html.find( '#main' );
			var newtitles    = {
				'title'    : html.find( 'title' ),
				'navtitle' : html.find( '.site-title-wrap h1' )
			};
			var newclasses = $main.attr( 'class' ).replace( 'site-main', '' );
			var content    = $main.children().unwrap();
			var appp_header_right = html.find( '#top-menu3' );
			var appp_pull_left = html.find( '.pull-left' );
			var appp_modal = html.find( '.io-modal' );
			var appp_activity_modal = html.find( '#activity-post-form' );
			// Get scripts on new page and filter out any that have been loaded on the page already
			var scripts    = app.scriptLoader( html.find( 'script[src]' ) );
			var styles     = app.styleLoader( html.find( 'link[type="text/css"]' ) );
			// @TODO figure out how to re-load localized data
			// app.loadL10n( html );
			// Replace existing page body classes with new
			app.$.body.attr( 'class', newclasses );
			app.$.main.attr( 'class', newclasses );
			// Replace existing page <title> with new
			titles.title.text( newtitles.title.text() );
			// Replace existing page nav title with new
			titles.navtitle.html( newtitles.navtitle.html() );

			// Replace existing page content with new
			$( '#top-menu3' ).replaceWith( appp_header_right );
			$( '.pull-left' ).replaceWith( appp_pull_left );
			$( '#activity-post-form' ).replaceWith( appp_activity_modal );

			$selector.html( content );

			// Change url to reflect new page
			app.change_url( href );

			app.timeout = setTimeout(function (){
				// Loop through our new scripts
				$.each( scripts, function( filename, url ) {

					app.log( '$.each(scripts)', filename, url );

					var response = app.loadScript( url, true, function() {
						// Increase our scripts loaded count
						app.scriptsLoadedCount++;

						app.log( 'script retrieved', url );
						app.log( 'scripts retrieved', app.scriptsLoadedCount );
					});
					if ( response ) {

						response
							.done(function( script, textStatus, jqXHR ) {
								// jqXHR.requestURL = url;
								// app.log( 'rescript: Status', url );
							})
							.fail(function( jqXHR, settings, exception ) {
								app.log( 'loadScript: Triggered ajaxError handler for: '+ url, exception );
							});

						// Add this script loading ajax request to our pending xhr requests
						app.xhr.push( response );
					}
				});
				$.each( styles, function( filename, url ) {

					app.log( '$.each(styles)', filename, url );
					app.loadCSS( url );
					app.stylesLoadedCount++;
					app.log( 'style retrieved', url );
					app.log( 'styles retrieved', app.stylesLoadedCount );

				});

			}, 30);

			// A hook for other JS functions to run
			// Passes in jQuery, our $selector and the href
			$(document).trigger( 'load_ajax_content_done', $, $selector, href );
			app.$.body.trigger( 'post-load', $, $selector, href );


		}).complete( function( jqXHR, status ) {
			// jqXHR.requestURL = href;
			// app.log( 'selector load was performed.' );

			// Hide spinner
			app.$.spinner.fadeOut('fast');

			if ( status !== 'success' )
				return;

			// app.log( 'selector load was successful.' );

			app.$.main.animate({scrollTop:0}, 'fast');

			// add current_page_item class the clicked tab or drawer item
			if ( $(that).parents().hasClass('footer-menu') ) {
				var href = $( that ).attr('href');
				$( '.footer-menu li' ).removeClass('current_page_item');
				$( that ).parent().addClass('current_page_item');
				$( '#site-navigation ul.menu li' ).removeClass('current_page_item');
				$('#site-navigation ul.menu a[href="' + href + '"]').parent().addClass('current_page_item');

			}

			if ( $(that).parents().hasClass('menu') ) {
				var href = $( that ).attr('href');
				$( '#site-navigation ul.menu li' ).removeClass('current_page_item');
				$( that ).parent().addClass('current_page_item');
				$( '.footer-menu li' ).removeClass('current_page_item');
				$('.footer-menu a[href="' + href + '"]').parent().addClass('current_page_item');
			}

		});

		// Add this ajax request to our pending xhr requests
		app.xhr.push( status );
		app.doingAjax = false;

	}

	app.canAjax = function( $element ) {
		return ( apppresser.appp.can_ajax && ! $element.is('.menu-back, .external, .no-ajax, .menu .no-ajax > a, .nav-divider, .modal-toggle, .modal-toggle a') || $element.is('.back')  );
	}

	app.canModal = function( $element ) {
		return ( apppresser.appp.can_ajax && ! $element.is('a.no-modal, .no-modal a') );
	}

	app.change_url = function( newurl ) {
		'use strict';
		newurl = newurl || apppresser.backhref;

		// Change url to reflect new page
		window.history.pushState({},'', newurl);

		var prevUrl = JSON.parse(localStorage['urlHistory']);

		if( !app.backLoad ) {
			//place newurl on top of url history array if moving forward in navigation
			prevUrl.unshift({
	            url: newurl
	        });
        }
        //save adjusted url history array to local storage incase browser gets refreshed
        localStorage['urlHistory'] = JSON.stringify( prevUrl ) ;

        app.backLoad = false;

	}

	app.loadL10n = function( html ) {
		var inlineScripts = html.find( "script[type='text/javascript']" ).text();
		var pattern = /\/\* <!\[CDATA\[ \*\/([\s\S]*)\/\* \]\]> \*\//;
		// var test = inlineScripts.search(pattern);
		var matches = inlineScripts.match(pattern);
		var script = matches[1];
		eval( script );
	}

	app.loadScript = function(url, arg1, arg2) {
		'use strict';
		var cache = false, callback = null;
		// arg1 and arg2 can be interchangable as either the callback function or the cache bool
		if ($.isFunction(arg1)){
			callback = arg1;
			cache = arg2 || cache;
		} else {
			cache = arg1 || cache;
			callback = arg2 || callback;
		}
		// equivalent to a $.getScript but with control over cacheing
		return $.ajax({
			url: url,
			type: 'GET',
			dataType: 'script',
			cache: cache,
			success: callback
		});
	}

	app.loadCSS = function( href ) {

		var cssLink = $('<link>');
		$("head").append(cssLink); // IE hack: append before setting href

		cssLink.attr({
			rel:  "stylesheet",
			type: "text/css",
			href: href
		});

	}

	app.untrailingslashit = function(str) {
		if ( str.substr(-1) == '/' ) {
			return str.substr(0, str.length - 1);
		}
		return str;
	}

	/**
	 * Safely log things if query var is set
	 * @since  1.0.0
	 */
	app.log = function() {
		'use strict';
		if ( this.appp.debug && console && typeof console.log === 'function' ) {
			console.log.apply(console, arguments);
		}
	}

	/**
	 * Group logged items
	 * @since  1.0.0
	 */
	app.logGroup = function( groupName, expanded ) {
		'use strict';

		if ( this.appp.debug && console && typeof console.group === 'function' ) {
			if ( groupName === true ) {
				console.groupEnd();
			} else if ( typeof groupName === 'undefined' ) {
				if ( expanded )
					console.group();
				else
					console.groupCollapsed();
			} else {
				if ( expanded )
					console.group( groupName );
				else
					console.groupCollapsed( groupName );
			}
		}
	}
	
	// do not submit comment if no value
	$( 'body' ).on( 'click', '#respond #submit', function() {
	
		// comment check
		var $comment = $( this ).closest( '#respond' ).find( '#comment' ),
			comment  = $.trim( $comment.val() );

	    if ( comment === '' ) {
	        alert( appp.i18n_required_comment_text );
			return false;
		}
		
		// rating check
		var $rating = $( this ).closest( '#respond' ).find( '#rating' ),
		rating  = $rating.val();

		if ( $rating.size() > 0 && ! rating && wc_single_product_params.review_rating_required === 'yes' && ! comment === '' ) {
			alert( wc_single_product_params.i18n_required_rating_text );
			return false;
		}
			
	});
	

	app.init();

	window.apppresser = app;

})(window, document, jQuery);
