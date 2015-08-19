
<?php if ( current_user_can( 'candidate' || 'administrator' ) ) : ?>
<?php global $post; ?>
<form class="job-manager-application-form job-manager-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( get_permalink() );?>">
	<?php do_action( 'job_application_form_fields_start' ); ?>
	<?php 
		//this checks for the balance of the user & outputs an error if they don't have enough credits
		//get all your variables
		$user_ID = get_current_user_id();
		$post_ID = get_the_ID();
		$cred_value	= -1 * abs(get_post_meta( $post_ID, '_cred_field', true));//get_post_meta( $post_ID, 'cred_field', true);
		$current_balance = mycred_get_users_cred( $user_ID );
		$resulting_balance = $current_balance + $cred_value;

		//check the result of this transaction on the user's balance
		
		//if balance is zero - decline
		if( $cred_value < 0 && $current_balance <= 0 ){
			echo '<div id="credit-error">You currently have no credits and this chore requires ' . abs($cred_value) . ' credits to apply.</div>'  ;
			echo '<a href="/my-account/credit-balance/" class="button">Buy Credits</a>';
			echo '</form>'; //closes the form
			return;
		}
		
		//If we are deducting points, make sure the amount will not take us below zero
		elseif( $cred_value < 0 && $resulting_balance < 0 ) {
			echo '<div id="credit-error">This chore requires ' . abs($cred_value) . ' credits to apply. You need ' . abs($resulting_balance) . ' more credits.</div>' ;
			echo '<a href="/my-account/credit-balance/" class="button">Click here</a>';
			echo '</form>'; //closes the form
			return;
		}

		else {
			//we're going to count how many published resumes the user has & if they don't have any print the message below. If they have an active resume let them apply. 
			$active_resumes = count (get_posts ( array(
				'post_type' => 'resume',
				'post_status' => 'publish',
				'author' => get_current_user_id(),
				'nopaging' => true
				)));

			if ( $active_resumes < 1 ) {
				echo '<div id="active-resumes">It appears you do not have an active profile. To check the status of your profile visit <a href="/my-account/my-resumes/" target="_blank">My Resumes</a>. If you recently created your profile we may still be reviewing it. If you think this is an error please <a href="/contact-us/" target="_blank">Contact Us</a> and let us know.</div></form>';
				return;	
			}
			
			echo '<div id="credit-apply-message">You have ' . $current_balance . ' credits. ' . 'Send for ' . abs($cred_value) . ' credits.</div>';
		}
	?>
	
	<?php foreach ( $application_fields as $key => $field ) : //checks $valid_transaction is set as 0 stil if so it's a valid transaction ?>
		<fieldset class="fieldset-<?php esc_attr_e( $key ); ?>">
			<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . apply_filters( 'submit_job_form_required_label', $field['required'] ? '' : ' <small>' . __( '(optional)', 'wp-job-manager' ) . '</small>', $field ); ?></label>
			<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
				<?php get_job_manager_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
			</div>
		</fieldset>
	<?php endforeach; ?>
	
	<?php do_action( 'job_application_form_fields_end' ); ?>

	     <p>
		<input type="submit" name="wp_job_manager_send_application" value="<?php esc_attr_e( 'Send application', 'wp-job-manager-applications' ); ?>" />
		<input type="hidden" name="job_id" value="<?php echo absint( $post->ID ); ?>" />
		</p>
	

</form>
<?php else : ?>
	<h2 class="modal-title">So close...</h2>
	<div class="enter-an-application">
		<?php echo ('<div id="not-a-business">Submit your application today by creating a profile <a href="http://choresnap.com/company-profile/">here</a>.</div>');?>
	</div>
<?php endif; ?>
