<?php

class Listify_WP_Job_Manager_Service_Resurva extends Listify_WP_Job_Manager_Service {

	public function __construct() {
		$this->meta_key = 'resurva';
		$this->label    = __( 'Book with Resurva', 'listify' );

		parent::__construct();
	}

	public function get_content() {
		$url = $this->get_value( 'resurva_url' );
		$resurva = $this->get_value();

		ob_start();
	?>
		<script id="resurva-embed" type="text/javascript">
		   // <![CDATA[
		  (function(d, s, id) {
		 var js, rjs = d.getElementById('resurva-embed');
		 if (d.getElementById(id)) return;
		 js = d.createElement(s); js.id = id;
		 js.src  = "<?php echo esc_url( $url ); ?>";
		 js.src += "?key=<?php echo esc_attr( $resurva ); ?>";
		rjs.parentNode.insertBefore(js, rjs);
		}(document, 'script', 'resurva-js'));
		// ]]>
		</script>
	<?php
		$content = ob_get_clean();

		return $content;
	}

}

new Listify_WP_Job_Manager_Service_Resurva;
