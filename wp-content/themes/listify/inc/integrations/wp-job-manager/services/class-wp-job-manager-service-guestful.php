<?php

class Listify_WP_Job_Manager_Service_Guestful extends Listify_WP_Job_Manager_Service {

	public function __construct() {
		$this->meta_key = 'guestful';
		$this->label    = __( 'Book with Guestful', 'listify' );

		parent::__construct();
	}

	public function get_content() {
		$args = array(
			'rid' => $this->get_value()
		);

		$url = esc_url( add_query_arg( $args, 'https://www.guestful.com/widgets/responsive/js/script-loader.js' ) );

		ob_start();
	?>
		<div style="text-align:center;width: 100%; height: 300px;">
			<script type="text/javascript" class="guestful-widget-loader" src="<?php echo $url; ?>"></script>
		</div>

	<?php
		return ob_get_clean();
	}

}

new Listify_WP_Job_Manager_Service_Guestful;