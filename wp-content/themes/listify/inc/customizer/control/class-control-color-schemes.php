<?php
/**
 * Color Schemes
 *
 * @since Listify 1.0.0
 */
class Listify_Customize_Color_Schemes_Control extends WP_Customize_Control {
	public $type = 'radio';
	public $schemes = array();

	public function render_content() {
		global $listify_customizer_colors;

		$name = '_customize-radio-' . $this->id;
		$schemes = $this->schemes;
?>
	<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

	<?php foreach ( $schemes as $scheme => $colors ) : ?>

		<p><label>
			<input <?php $this->link(); ?> name="<?php echo esc_attr( $name ); ?>" value="<?php echo $scheme; ?>" type="radio" <?php echo $this->generate_scheme_data( $colors ); ?> <?php checked($scheme, $this->value()); ?> />
			<?php echo $this->generate_scheme_preview( $colors ); ?>
			<?php echo $scheme; ?>
		</label></p>

	<?php endforeach; ?>

	<?php if ( $this->description ) : ?>
		<p><?php echo $this->description; ?></p>
	<?php endif; ?>

	<style>
		.color-scheme {
			display: inline-block;
			height: 24px;
			vertical-align: middle;
			padding: 2px;
			border: 1px solid #ddd;
			margin-right: 4px;
			margin-top: -3px;
		}

		.color-scheme-color {
			display: inline-block;
			width: 10px;
			height: 24px;
		}
	</style>
<?php
	}

	public function generate_scheme_preview( $colors ) {
		echo '<span class="color-scheme">';

		foreach ( $colors as $color ) {
			echo '<span class="color-scheme-color" style="background-color: ' . $color . '"></span>';
		}

		echo '</span>';
	}

	public function generate_scheme_data( $colors ) {
		$output = array();

		foreach ( $colors as $key => $color ) {
			$output[] = sprintf( 'data-%s="%s"', $key, $color );
		}

		return implode( ' ', $output );
	}
}
