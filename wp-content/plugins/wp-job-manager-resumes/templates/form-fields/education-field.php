<?php if ( ! empty( $field['value'] ) && is_array( $field['value'] ) ) : ?>
	<?php foreach ( $field['value'] as $education ) : ?>
		<div class="resume-manager-data-row">
			<a href="#" class="resume-manager-remove-row"><?php _e( 'Remove', 'wp-job-manager-resumes' ); ?></a>
			<?php foreach ( $field['fields'] as $subkey => $subfield ) : ?>
				<fieldset class="fieldset-<?php esc_attr_e( $subkey ); ?>">
					<label for="<?php esc_attr_e( $subkey ); ?>"><?php echo $subfield['label'] . ( $subfield['required'] ? '' : ' <small>' . __( '(optional)', 'wp-job-manager-resumes' ) . '</small>' ); ?></label>
					<div class="field">
						<?php
							// Get name and value
							$subfield['name']  = 'candidate_education_' . $subkey . '[]';
							$subfield['value'] = $education[ $subkey ];
							$class->get_field_template( $subkey, $subfield );
						?>
					</div>
				</fieldset>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<a href="#" class="resume-manager-add-row" data-row="<?php

	ob_start();
	?>
		<div class="resume-manager-data-row">
			<a href="#" class="resume-manager-remove-row"><?php _e( 'Remove', 'wp-job-manager-resumes' ); ?></a>
			<?php foreach ( $field['fields'] as $subkey => $subfield ) : ?>
				<fieldset class="fieldset-<?php esc_attr_e( $subkey ); ?>">
					<label for="<?php esc_attr_e( $subkey ); ?>"><?php echo $subfield['label'] . ( $subfield['required'] ? '' : ' <small>' . __( '(optional)', 'wp-job-manager-resumes' ) . '</small>' ); ?></label>
					<div class="field">
						<?php
							// Get name and value
							$subfield['name']  = 'candidate_education_' . $subkey . '[]';
							$class->get_field_template( $subkey, $subfield );
						?>
					</div>
				</fieldset>
			<?php endforeach; ?>
		</div>
	<?php
	echo esc_attr( ob_get_clean() );

?>">+ <?php _e( 'Add Education', 'wp-job-manager-resumes' ); ?></a>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>