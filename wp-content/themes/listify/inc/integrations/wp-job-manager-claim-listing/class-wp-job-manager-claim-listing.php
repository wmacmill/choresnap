<?php
/**
 * WP Job Manager - Claim Listing
 */

class Listify_WP_Job_Manager_Claim_Listing extends Listify_Integration {

	public function __construct() {
		$this->includes = array();
		$this->integration = 'wp-job-manager-claim-listing';
		$this->claim = WP_Job_Manager_Claim_Listing();

		parent::__construct();
	}

	public function setup_actions() {
		remove_action( 'single_job_listing_start', array( $this->claim->listing, 'claim_listing_link' ) );
		add_action( 'listify_single_job_listing_actions_start', array( $this, 'claim_button' ) );

		add_action( 'single_job_listing_meta_start', array( $this, 'the_badge' ), 11 );
		add_action( 'listify_content_job_listing_meta', array( $this, 'the_badge' ), 18 );
	}

    public function the_badge() {
		if ( $this->claim->listing->is_claimable() ) {
            return;
        }

		get_template_part( 'content-badge-claimed', 'claim-listing' );
    }

	public function claim_button() {
		global $post;

		if ( ! $this->claim->listing->is_claimable() ) {
			return;
		}

		$href = wp_nonce_url( add_query_arg( array( 
			'action' => 'claim_listing', 
			'listing_id' => $post->ID 
		) ), 'claim_listing', 'claim_listing_nonce' );
	?>
		<a href="<?php echo esc_url( $href ); ?>" class="claim-listing"><i class="ion-thumbsup"></i> <?php _e( 'Claim Listing', 'listify' ); ?></a>
	<?php
	}

}

$GLOBALS[ 'listify_job_manager_claim_listing' ] = new Listify_WP_Job_Manager_Claim_Listing();
