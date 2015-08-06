<?php
/**
 *
 */

class Listify_WP_Job_Manager_Claim extends Listify_WP_Job_Manager {

	public function __construct() {
		if ( class_exists( 'WP_Job_Manager_Claim_Listing' ) ) {
			return;
		}

		$this->option = 'job_manager_form_claim';

		add_filter( 'job_manager_contact_listing_forms', array( $this, 'contact_listing_forms' ) );

		if ( get_option( $this->option, false ) && class_exists( 'Astoundify_Job_Manager_Contact_Listing' ) ) {
			add_action( 'listify_single_job_listing_actions_start', array( $this, 'claim_button' ) );
		}

		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'admin_claim' ) );

		add_filter( 'job_manager_contact_listing_gravityforms_apply_form_args', array( $this, 'form_args_gravity' ) );
		add_filter( 'job_manager_contact_listing_cf7_apply_form_args', array( $this, 'form_args_cf7' ) );

		add_action( 'single_job_listing_meta_start', array( $this, 'the_badge' ), 11 );
		add_action( 'listify_content_job_listing_meta', array( $this, 'the_badge' ), 18 );
	}

    public function the_badge() {
		global $post;

		if ( ! $post->_claimed ) {
			return;
		}

		get_template_part( 'content-badge-claimed' );
    }

	public function contact_listing_forms( $forms ) {
		$forms[ 'job_listing' ][ 'claim' ] = get_option( $this->option, false );

		return $forms;
	}

	public function claim_button() {
		global $post;

		if ( 'publish' != $post->post_status || $post->_claimed ) {
			return;
		}
	?>
		<a href="#claim-listing-<?php echo $post->ID; ?>" class="popup-trigger"><i class="ion-thumbsup"></i> <?php _e( 'Claim Listing', 'listify' ); ?></a>

		<div id="claim-listing-<?php echo $post->ID; ?>" class="popup">
			<h2 class="popup-title"><?php printf( __( 'Claim "%s"', 'listify' ), get_the_title( $post->ID ) ); ?></h2>

			<?php
				$plugin = Astoundify_Job_Manager_Contact_Listing::$active_plugin;
				$form = get_option( $this->option );

				do_action( 'job_manager_contact_listing_form_' . $plugin, $form );
			?>
		</div>
	<?php
	}

	public function admin_claim( $fields ) {
		$fields[ '_claimed' ] = array(
			'type' => 'checkbox',
			'label' => __( 'Claimed:', 'listify' ),
			'placeholder' => '',
			'description' => __( 'The owner has been verified', 'listify' )
		);

		return $fields;
	}

	public function form_args_gravity( $args ) {
		$args = str_replace( 'title="false"', '', $args );

		return $args;
	}

	public function form_args_cf7( $args ) {
		$args = ' title="Contact Business"';

		return $args;
	}

}
