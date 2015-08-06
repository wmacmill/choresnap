<form method="post" class="job-manager-form">
	<fieldset>
		<label for="alert_name"><?php _e( 'Alert Name', 'wp-job-manager-alerts' ); ?></label>
		<div class="field">
			<input type="text" name="alert_name" value="<?php echo esc_attr( $alert_name ); ?>" id="alert_name" class="input-text" placeholder="<?php _e( 'Enter a name for your alert', 'wp-job-manager-alerts' ); ?>" />
		</div>
	</fieldset>
	<!--<fieldset>
		<label for="alert_keyword"><?php _e( 'Keyword', 'wp-job-manager-alerts' ); ?></label>
		<div class="field">
			<input type="text" name="alert_keyword" value="<?php echo esc_attr( $alert_keyword ); ?>" id="alert_keyword" class="input-text" placeholder="<?php _e( 'Optionally add a keyword to match jobs against', 'wp-job-manager-alerts' ); ?>" />
		</div>
	</fieldset>-->
	<?php if ( taxonomy_exists( 'job_listing_region' ) && wp_count_terms( 'job_listing_region' ) > 0 ) : ?>
		<fieldset>
			<label for="alert_regions"><?php _e( 'Job Region', 'wp-job-manager-alerts' ); ?></label>
			<div class="field">
				<select name="alert_regions[]" data-placeholder="<?php _e( 'Any region', 'wp-job-manager-alerts' ); ?>" id="alert_regions" multiple="multiple" class="job-manager-chosen-select">
					<?php
						$terms    = get_terms( 'job_listing_region', array( 'hide_empty' => true ) );
						$selected = $alert_id ? wp_get_post_terms( $alert_id, 'job_listing_region', array( 'fields' => 'ids' ) ) : $alert_region;
						foreach ( $terms as $term ) {
							echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( in_array( $term->term_id, $selected ), true, false ) . '>' . esc_html( $term->name ) . '</option>';
						}
					?>
				</select>
			</div>
		</fieldset>
	<?php endif; ?>
	<?php if ( get_option( 'job_manager_enable_categories' ) && wp_count_terms( 'job_listing_category' ) > 0 ) : ?>
		<fieldset>
			<label for="alert_cats"><?php _e( 'Categories', 'wp-job-manager-alerts' ); ?></label>
			<div class="field">
				<?php
					wp_enqueue_script( 'wp-job-manager-term-multiselect' );

					job_manager_dropdown_categories( array(
						'taxonomy'     => 'job_listing_category',
						'hierarchical' => 1,
						'name'         => 'alert_cats',
						'orderby'      => 'name',
						'selected'     => $alert_cats,
						'hide_empty'   => false,
						'placeholder'  => __( 'Any category', 'wp-job-manager' )
					) );
				?>
			</div>
		</fieldset>
	<?php endif; ?>
	<fieldset>
		<label for="alert_job_type"><?php _e( 'Job Type', 'wp-job-manager-alerts' ); ?></label>
		<div class="field">
			<select name="alert_job_type[]" data-placeholder="<?php _e( 'Any job type', 'wp-job-manager-alerts' ); ?>" id="alert_job_type" multiple="multiple" class="job-manager-chosen-select">
				<?php
					$terms = get_job_listing_types();
					foreach ( $terms as $term )
						echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( in_array( $term->slug, $alert_job_type ), true, false ) . '>' . esc_html( $term->name ) . '</option>';
				?>
			</select>
		</div>
	</fieldset>
	<fieldset>
		<label for="alert_frequency"><?php _e( 'Email Frequency', 'wp-job-manager-alerts' ); ?></label>
		<div class="field">
			<select name="alert_frequency" id="alert_frequency">
				<option value="daily" <?php selected( $alert_frequency, 'daily' ); ?>><?php _e( 'Daily', 'wp-job-manager-alerts' ); ?></option>
				<!--<option value="weekly" <?php selected( $alert_frequency, 'weekly' ); ?>><?php _e( 'Weekly', 'wp-job-manager-alerts' ); ?></option>
				<option value="fortnightly" <?php selected( $alert_frequency, 'fortnightly' ); ?>><?php _e( 'Fortnightly', 'wp-job-manager-alerts' ); ?></option>-->
			</select>
		</div>
		If no matches are found an email will not be sent. 
	</fieldset>
	<p>
		<?php wp_nonce_field( 'job_manager_alert_actions' ); ?>
		<input type="hidden" name="alert_id" value="<?php echo absint( $alert_id ); ?>" />
		<input type="submit" name="submit-job-alert" value="<?php _e( 'Save alert', 'wp-job-manager-alerts' ); ?>" />
	</p>
</form>