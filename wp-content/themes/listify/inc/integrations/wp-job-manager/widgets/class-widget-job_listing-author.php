<?php
/**
 * Job Listing: Author
 *
 * @since Listify 1.0.0
 */
class Listify_Widget_Listing_Author extends Listify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display the listing\'s author', 'listify' );
		$this->widget_id          = 'listify_widget_panel_listing_auhtor';
		$this->widget_name        = __( 'Listify - Listing: Author', 'listify' );
		$this->settings           = array(
			'descriptor' => array(
				'type'  => 'text',
				'std'   => 'Listing Owner',
				'label' => __( 'Descriptor:', 'listify' )
			),
			'biography' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Show biography', 'listify' )
			),
		);

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		$descriptor = isset( $instance[ 'descriptor' ] ) ? esc_attr( $instance[ 'descriptor' ] ) : false;
		$biography = isset( $instance[ 'biography' ] ) && 1 == $instance[ 'biography' ] ? true : false;

		global $post;

		extract( $args );

		ob_start();

		echo $before_widget;
		?>

		<div class="job_listing-author">
			<div class="job_listing-author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 210 ); ?>
			</div>

			<div class="job_listing-author-info">
				<?php the_author(); ?>

				<small class="job_listing-author-descriptor"><?php echo $descriptor; ?></small>

				<?php if ( 'preview' != $post->post_status ) : ?>
				<div class="job_listing-author-info-more">
					<a href="#job_listing-author-apply" data-mfp-src=".job_application" class="popup-trigger"><span class="ion-email"></span></a>

					<?php if ( ! is_position_filled() && $post->post_status == 'publish' ) get_job_manager_template( 'job-application.php' ); ?>

					<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><span class="ion-information-circled"></span></a>
				</div>
				<?php endif; ?>
			</div>

			<?php if ( $biography && $bio = get_the_author_meta( 'description', get_the_author_meta( 'ID' ) ) ) : ?>
				<div class="job_listing-author-biography">
					<?php echo $bio; ?>
				</div>
			<?php endif; ?>

            <?php do_action( 'listify_widget_job_listing_author_after' ); ?>
		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );

		$this->cache_widget( $args, $content );
	}
}
