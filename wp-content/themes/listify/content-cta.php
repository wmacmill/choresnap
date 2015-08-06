<?php
/**
 * The template for displaying the call to action
 */

if ( ! listify_theme_mod( 'call-to-action-display' ) ) {
  return;
}

$title = listify_theme_mod( 'call-to-action-title' );
$description = listify_theme_mod( 'call-to-action-description' );
$button_text = listify_theme_mod( 'call-to-action-button-text' );
$button_href = listify_theme_mod( 'call-to-action-button-href' );
$button_subtext = listify_theme_mod( 'call-to-action-button-subtext' );
?>

<div class="call-to-action">

	<div class="container">
		<div class="row">

			<div class="col-sm-12 col-md-8 col-lg-9">
				<h1 class="cta-title"><?php echo esc_attr( $title ); ?></h1>

				<div class="cta-description"><?php echo wpautop( esc_attr( $description ) ); ?></div>
			</div>

			<div class="cta-button-wrapper col-sm-12 col-md-4 col-lg-3">
				<a class="cta-button button" href="<?php echo esc_url( $button_href ); ?>"><?php echo esc_attr( $button_text ); ?></a>
				<small class="cta-subtext"><?php echo esc_attr( $button_subtext ); ?></small>
			</div>

		</div>
	</div>

</div>
