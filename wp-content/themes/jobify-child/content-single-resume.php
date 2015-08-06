<?php
/**
 *
 */

global $post;

$skills     = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) );
$education  = get_post_meta( $post->ID, '_candidate_education', true );
$experience = get_post_meta( $post->ID, '_candidate_experience', true );

$info            = jobify_theme_mod( 'jobify_listings', 'jobify_listings_display_area' );

$has_local_info  = is_array( $skills ) || $education || $experience;

$col_description = 'top' == $info ? '12' : ( $has_local_info ? '6' : '10' );
$col_info        = 'top' == $info ? '12' : ( 'side' == $info ? '4' : '6' );
?>

<div class="single-resume-content row">

	<?php if ( resume_manager_user_can_view_resume( $post->ID ) ) : ?>

		<?php do_action( 'single_resume_start' ); ?>

		<?php locate_template( array( 'sidebar-single-resume-top.php' ), true, false ); ?>

		<div class="resume_description col-md-<?php echo $col_description; ?> col-sm-12">
			<div class="resume-main-content">
			<h2 class="job-overview-title"><?php echo 'About ' . get_the_title ( $ID );/*This is the original template info _e( 'Description', 'jobify' ); */?></h2>
			<?php echo apply_filters( 'the_resume_description', get_the_content() ); ?>
			</div>
			<div class="resume-company-details">
				<h2 class="job-overview-title">Credentials & Information</h2>
				<div class="resume-company-details-list">
					<?php 
						$date_founded = get_post_meta ( get_the_ID(), '_date_founded', single);
						$bonded = get_post_meta ( get_the_ID() , '_resume_bonded', single );
						$employee_number = get_post_meta ( get_the_ID(), '_resume_employees', single);
						$payment_methods = strip_tags( get_the_term_list ( get_the_ID(), 'paymentmethod', '', ', ', '') );
						$workers_comp = get_post_meta ( get_the_ID(), '_resume_workers_comp', single );
						$liability_insurance = get_post_meta ( get_the_ID(), '_resume_liability_insurance', single );
						$written_contracts = get_post_meta ( get_the_ID(), '_resume_written_contracts', single );
						$warranty = get_post_meta ( get_the_ID(), '_resume_warranty_terms', single );
					?>

					<dl class="resume-def-list">
						<dt>Company Founded</dt>
						<dd><?php echo ($date_founded == "" ? "Unknown" : $date_founded) ;?></dd>
						<dt>Number of Employees<dt>
						<dd><?php echo ($employee_number == "" ? "Unknown" : $employee_number) ;?></dt>
						<dt>Payment Methods</dt>
						<dd><?php echo $payment_methods ; ?></dd>
						<dt>Bonded</dt>
						<dd><?php echo ($bonded == 1 ? "Yes" : "No") ;?></dd>
						<dt>Workers Compensation</dt>
						<dd><?php echo ($workers_comp == 1 ? "Yes" : "No") ;?></dd>
						<dt>Liability Insurance</dt>
						<dd><?php echo ($liability_insurance == 1 ? "Yes" : "No") ;?></dd>
						<dt>Written Contracts Provided</dt>
						<dd><?php echo ($written_contracts == 1 ? "Yes" : "No") ;?></dd>
						<dt>Warranties or Guarantees</dt>
						<dd><?php echo ($warranty == "" ? "None" : $warranty ) ;?></dd>
					</dl>
				</div>
			</div>
			<?php if($attached_images = get_attached_media( 'image' )) echo '<h2 class="job-overview-title">' . get_the_title ( $ID ) . ' Photo Gallery</h2>'; ?>
			<?php echo do_shortcode('[gallery soliloquy="true"]'); ?>
		</div>

		<?php if ( $has_local_info ) : ?>

		<div class="resume-info col-md-<?php echo $col_info; ?> col-sm-8 col-xs-12">

			<?php if ( $skills && is_array( $skills ) && 'side' == $info ) : ?>
				<h2 class="job-overview-title"><?php _e( 'Skills', 'jobify' ); ?></h2>

				<ul class="resume-manager-skills">
					<?php echo '<li>' . implode( '</li><li>', $skills ) . '</li>'; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $education ) : ?>
				<h2 class="job-overview-title"><?php _e( 'Education', 'jobify' ); ?></h2>

				<dl class="resume-manager-education">
				<?php
					foreach( $education as $item ) : ?>

						<dt>
							<h3><?php echo esc_html( $item['location'] ); ?></h3>
						</dt>
						<dd>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<strong class="qualification"><?php echo esc_html( $item['qualification'] ); ?></strong>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>

			<?php if ( $experience ) : ?>
				<h2 class="job-overview-title"><?php _e( 'Experience', 'jobify' ); ?></h2>

				<dl class="resume-manager-experience">
				<?php
					foreach( $experience as $item ) : ?>

						<dt>
							<h3><?php echo esc_html( $item['employer'] ); ?></h3>
						</dt>
						<dd>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<strong class="job_title"><?php echo esc_html( $item['job_title'] ); ?></strong>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>
		</div>

		<?php endif; ?>

		<?php locate_template( array( 'sidebar-single-resume.php' ), true, false ); ?>

		<?php do_action( 'single_resume_end' ); ?>

	<?php else : ?>

		<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'resume_manager', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

	<?php endif; ?>

</div>