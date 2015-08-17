<?php

class AppBuddy_Blogs {

	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		/**
		* Include Phonegap inappbrowser plugins
		* All bundled plugins can be found in apppresser/inc/phonegap-plugins
		* @since  1.0.0
		*/
		add_filter( 'apppresser_phonegap_plugin_packages', array( $this, 'appp_inappbrowser' ) );

		add_action( 'wp_footer', array( $this, 'appp_redirect_links_inside_app' ), 9999 );
	}

	public function appp_inappbrowser( $plugins ) {
		return array_merge( $plugins, array( 'inappbrowser' ) );
	}

	public function appp_redirect_links_inside_app(){
		?>
		<script>
			(function(window, document, $, undefined){
				var doBrowser = function(){

					$('#blogs-list').on( 'click', 'a', function( event ) {
						event.preventDefault();
					});

					document.addEventListener( 'deviceready', function(){
						$( function($){
							console.log( 'deviceready' );
							$('#blogs-list').on( 'click', 'a', function( event ) {
								var browserInstance = window.open( $(this).attr( 'href' ), '_blank' );
							});
						});
					}, false);

				}
				$(document).on( 'ready', doBrowser ).on( 'load_ajax_content_done', doBrowser );

			})(window, document, jQuery);
		</script>
		<?php
	}

}
$AppBuddy_Blogs = new AppBuddy_Blogs();