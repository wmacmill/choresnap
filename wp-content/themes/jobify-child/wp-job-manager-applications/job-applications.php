<div id="job-manager-job-applications">
	<a href="<?php// echo esc_url( add_query_arg( 'download-csv', true ) ); ?>" class="job-applications-download-csv"><?php// _e( 'Download CSV', 'wp-job-manager-applications' ); ?></a>
	<p><?php printf( __( 'The job applications for "%s" are listed below.', 'wp-job-manager-applications' ), '<a href="' . get_permalink( $job_id ) . '">' . get_the_title( $job_id ) . '</a>' ); ?></p>
	<div class="job-applications">
		<form class="filter-job-applications" method="GET">
			<p>
				<select name="application_status">
					<option value=""><?php _e( 'Filter by status', 'wp-job-manager-applications' ); ?>...</option>
					<?php foreach ( get_job_application_statuses() as $name => $label ) : ?>
						<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $application_status, $name ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<select name="application_orderby">
					<option value=""><?php _e( 'Newest first', 'wp-job-manager-applications' ); ?></option>
					<option value="name" <?php selected( $application_orderby, 'name' ); ?>><?php _e( 'Sort by name', 'wp-job-manager-applications' ); ?></option>
					<option value="rating" <?php selected( $application_orderby, 'rating' ); ?>><?php _e( 'Sort by rating', 'wp-job-manager-applications' ); ?></option>
				</select>
				<input type="hidden" name="action" value="show_applications" />
				<input type="hidden" name="job_id" value="<?php echo absint( $_GET['job_id'] ); ?>" />
				<?php if ( ! empty( $_GET['page_id'] ) ) : ?>
					<input type="hidden" name="page_id" value="<?php echo absint( $_GET['page_id'] ); ?>" />
				<?php endif; ?>
			</p>
		</form>
		<ul class="job-applications">
			<?php foreach ( $applications as $application ) : ?>
				<li class="job-application" id="application-<?php esc_attr_e( $application->ID ); ?>">
					<header>
						<?php job_application_header( $application ); ?>
					</header>
					<section class="job-application-content">
						<?php $applicant_id = get_post_meta( $application->ID, '_resume_id', true ); $application_email = get_post_meta( $application->ID, '_candidate_email', true ); $applicant_phone = get_post_meta( $applicant_id, '_candidate_phone', true );
							echo '<div class="job-application-meta">' . '<h3>Quick Details</h3>' ;
							$date_founded = get_post_meta ( $applicant_id, '_date_founded', single);
							$bonded = get_post_meta ( $applicant_id , '_resume_bonded', single );
							$employee_number = get_post_meta ( $applicant_id, '_resume_employees', single);
							$payment_methods = strip_tags( get_the_term_list ( $applicant_id, 'paymentmethod', '', ', ', '') );
							$workers_comp = get_post_meta ( $applicant_id, '_resume_workers_comp', single );
							$liability_insurance = get_post_meta ( $applicant_id, '_resume_liability_insurance', single );
							$written_contracts = get_post_meta ( $applicant_id, '_resume_written_contracts', single );
							$warranty = get_post_meta ( $applicant_id, '_resume_warranty_terms', single );
						?>
						<dl class="application-defined-list">
							<dt>Company Contact</dt>
							<dd><?php echo $application->post_title; ?></dd>
							<dt>Email</dt>
							<dd><?php echo '<a href="mailto:' . $application_email. '?subject=Your Estimate on Chore Snap">' . $application_email . '</a>'; ?></dd>
							<dt>Phone</dt>
							<dd><?php echo '<a href="tel:' . $applicant_phone . '">' . $applicant_phone . '</a>' ;?></dd>
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
						<?php job_application_content( $application ); ?>
						<?php $application_includes = get_post_meta( $application->ID, 'What is included?', true );
						echo '<b>What is included:</b><br>' . $application_includes; ?>
					</section>
					<section class="job-application-edit">
						<?php job_application_edit( $application ); ?>
					</section>
					<section class="job-application-notes">
						<?php job_application_notes( $application ); ?>
					</section>
					<footer>
						<?php job_application_footer( $application ); ?>
					</footer>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>
	</div>
</div>