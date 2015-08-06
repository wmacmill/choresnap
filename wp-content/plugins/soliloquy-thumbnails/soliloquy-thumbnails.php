<?php
/**
 * Plugin Name: Soliloquy - Thumbnails Addon
 * Plugin URI:  http://soliloquywp.com
 * Description: Enables thumbnails display for Soliloquy sliders.
 * Author:      Thomas Griffin
 * Author URI:  http://thomasgriffinmedia.com
 * Version:     2.2.1
 * Text Domain: soliloquy-thumbnails
 * Domain Path: languages
 *
 * Soliloquy is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Soliloquy is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Soliloquy. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define necessary addon constants.
define( 'SOLILOQUY_THUMBNAILS_PLUGIN_NAME', 'Soliloquy - Thumbnails Addon' );
define( 'SOLILOQUY_THUMBNAILS_PLUGIN_VERSION', '2.2.1' );
define( 'SOLILOQUY_THUMBNAILS_PLUGIN_SLUG', 'soliloquy-thumbnails' );

add_action( 'plugins_loaded', 'soliloquy_thumbnails_plugins_loaded' );
/**
 * Ensures the full Soliloquy plugin is active before proceeding.
 *
 * @since 1.0.0
 *
 * @return null Return early if Soliloquy is not active.
 */
function soliloquy_thumbnails_plugins_loaded() {

    // Bail if the main class does not exist.
    if ( ! class_exists( 'Soliloquy' ) ) {
        return;
    }

    // Fire up the addon.
    add_action( 'soliloquy_init', 'soliloquy_thumbnails_plugin_init' );

	// Loads the plugin textdomain for translation
    load_plugin_textdomain( SOLILOQUY_THUMBNAILS_PLUGIN_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}

/**
 * Loads all of the addon hooks and filters.
 *
 * @since 1.0.0
 */
function soliloquy_thumbnails_plugin_init() {

    add_action( 'soliloquy_updater', 'soliloquy_thumbnails_updater' );
    add_filter( 'soliloquy_defaults', 'soliloquy_thumbnails_defaults', 10, 2 );
    add_filter( 'soliloquy_get_config_mobile_keys', 'soliloquy_thumbnails_mobile_keys' );
    add_filter( 'soliloquy_tab_nav', 'soliloquy_thumbnails_tab_nav' );
    add_action( 'soliloquy_mobile_box', 'soliloquy_mobile_tab_thumbnails' );
    add_action( 'soliloquy_tab_thumbnails', 'soliloquy_thumbnails_tab_thumbnails' );
    add_filter( 'soliloquy_save_settings', 'soliloquy_thumbnails_save', 10, 2 );
    add_action( 'soliloquy_before_output', 'soliloquy_thumbnails_init' );
 
    // Edit Metadata
	add_action( 'soliloquy_after_preview', 'soliloquy_thumbnails_preview', 10, 3 ); // Preview Thumbnail
	add_action( 'soliloquy_after_image_meta_settings', 'soliloquy_thumbnails_meta', 10, 3 ); // Thumbnail URL Input Field
    add_action( 'soliloquy_after_video_meta_settings', 'soliloquy_thumbnails_meta', 10, 3 ); // Thumbnail URL Input Field
    add_action( 'soliloquy_after_html_meta_settings', 'soliloquy_thumbnails_meta', 10, 3 ); // Thumbnail URL Input Field
    add_filter( 'soliloquy_ajax_save_meta', 'soliloquy_thumbnails_save_meta', 10, 4 ); // Save Thumbnail metadata

}

/**
 * Initializes the addon updater.
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function soliloquy_thumbnails_updater( $key ) {

    $args = array(
        'plugin_name' => SOLILOQUY_THUMBNAILS_PLUGIN_NAME,
        'plugin_slug' => SOLILOQUY_THUMBNAILS_PLUGIN_SLUG,
        'plugin_path' => plugin_basename( __FILE__ ),
        'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . SOLILOQUY_THUMBNAILS_PLUGIN_SLUG,
        'remote_url'  => 'http://soliloquywp.com/',
        'version'     => SOLILOQUY_THUMBNAILS_PLUGIN_VERSION,
        'key'         => $key
    );
    $soliloquy_thumbnails_updater = new Soliloquy_Updater( $args );

}

/**
 * Applies a default to the addon setting.
 *
 * @since 1.0.0
 *
 * @param array $defaults  Array of default config values.
 * @param int $post_id     The current post ID.
 * @return array $defaults Amended array of default config values.
 */
function soliloquy_thumbnails_defaults( $defaults, $post_id ) {

    $defaults['thumbnails']          = 0;
    $defaults['thumbnails_width']    = 75;
    $defaults['thumbnails_height']   = 50;
    $defaults['thumbnails_position'] = 'bottom';
    $defaults['thumbnails_margin']   = 10;
    $defaults['thumbnails_num']      = 3;
    $defaults['thumbnails_crop']     = 1;

    // Mobile
    $defaults['mobile_thumbnails']   = 0;

    return $defaults;

}

/**
 * Adds mappings for configuration keys that have a mobile equivalent, allowing Soliloquy to
 * use these mobile keys when reading in configuration values on mobile devices
 *
 * @since 2.1.7
 *
 * @param array $mobile_keys Mobile Keys
 * @return array Mobile Keys
 */
function soliloquy_thumbnails_mobile_keys( $mobile_keys ) {

    $mobile_keys['thumbnails'] = 'mobile_thumbnails';
    return $mobile_keys;

}

/**
 * Filters in a new tab for the addon.
 *
 * @since 1.0.0
 *
 * @param array $tabs  Array of default tab values.
 * @return array $tabs Amended array of default tab values.
 */
function soliloquy_thumbnails_tab_nav( $tabs ) {

    $tabs['thumbnails'] = __( 'Thumbnails', 'soliloquy-thumbnails' );
    return $tabs;

}

/**
 * Callback for displaying the UI for mobile thumbnails options.
 *
 * @since 2.1.7
 *
 * @param object $post The current post object.
 */
function soliloquy_mobile_tab_thumbnails( $post ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <tr id="soliloquy-config-mobile-thumbnails-box">
        <th scope="row">
            <label for="soliloquy-config-mobile-thumbnails"><?php _e( 'Enable Slider Thumbnails?', 'soliloquy-thumbnails' ); ?></label>
        </th>
        <td>
            <input id="soliloquy-config-mobile-thumbnails" type="checkbox" name="_soliloquy[mobile_thumbnails]" value="<?php echo $instance->get_config( 'mobile_thumbnails', $instance->get_config_default( 'mobile_thumbnails' ) ); ?>" <?php checked( $instance->get_config( 'mobile_thumbnails', $instance->get_config_default( 'mobile_thumbnails' ) ), 1 ); ?> />
            <span class="description"><?php _e( 'Enables or disables the slider thumbnails feature on mobile devices.', 'soliloquy-thumbnails' ); ?></span>
        </td>
    </tr>
    <?php

}

/**
 * Callback for displaying the UI for setting thumbnails options.
 *
 * @since 1.0.0
 *
 * @param object $post The current post object.
 */
function soliloquy_thumbnails_tab_thumbnails( $post ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <div id="soliloquy-thumbnails">
        <p class="soliloquy-intro"><?php _e( 'The settings below adjust the thumbnails settings for the slider.', 'soliloquy-thumbnails' ); ?></p>
        <table class="form-table">
            <tbody>
                <tr id="soliloquy-config-thumbnails-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails"><?php _e( 'Enable Slider Thumbnails?', 'soliloquy-thumbnails' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-thumbnails" type="checkbox" name="_soliloquy[thumbnails]" value="<?php echo $instance->get_config( 'thumbnails', $instance->get_config_default( 'thumbnails' ) ); ?>" <?php checked( $instance->get_config( 'thumbnails', $instance->get_config_default( 'thumbnails' ) ), 1 ); ?> data-conditional="soliloquy-config-thumbnails-width-box,soliloquy-config-thumbnails-height-box,soliloquy-config-thumbnails-position-box,soliloquy-config-thumbnails-margin-box,soliloquy-config-thumbnails-num-box,soliloquy-config-thumbnails-crop-box" />
                        <span class="description"><?php _e( 'Enables or disables the slider thumbnails feature.', 'soliloquy-thumbnails' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-thumbnails-width-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails-width"><?php _e( 'Thumbnails Width', 'soliloquy-thumbnails' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-thumbnails-width" type="number" name="_soliloquy[thumbnails_width]" value="<?php echo $instance->get_config( 'thumbnails_width', $instance->get_config_default( 'thumbnails_width' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'The width of each slide thumbnail (acts a max width and adjusts dynamically).', 'soliloquy-thumbnails' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-thumbnails-height-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails-height"><?php _e( 'Thumbnails Height', 'soliloquy-thumbnails' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-thumbnails-height" type="number" name="_soliloquy[thumbnails_height]" value="<?php echo $instance->get_config( 'thumbnails_height', $instance->get_config_default( 'thumbnails_height' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'The height of each slide thumbnail (acts a max height and adjusts dynamically).', 'soliloquy-thumbnails' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-thumbnails-position-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails-position"><?php _e( 'Thumbnails Position', 'soliloquy' ); ?></label>
                    </th>
                    <td>
                        <select id="soliloquy-config-thumbnails-position" name="_soliloquy[thumbnails_position]">
                            <?php foreach ( (array) soliloquy_thumbnails_positions() as $i => $data ) : ?>
                                <option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $instance->get_config( 'thumbnails_position', $instance->get_config_default( 'thumbnails_position' ) ) ); ?>><?php echo $data['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e( 'Sets the thumbnail position relative to the slider.', 'soliloquy' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-thumbnails-margin-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails-margin"><?php _e( 'Thumbnails Margin', 'soliloquy-thumbnails' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-thumbnails-margin" type="number" name="_soliloquy[thumbnails_margin]" value="<?php echo $instance->get_config( 'thumbnails_margin', $instance->get_config_default( 'thumbnails_margin' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'The margin between each thumbnail within the slider.', 'soliloquy-thumbnails' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-thumbnails-num-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails-num"><?php _e( 'Number of Visible Thumbnails', 'soliloquy-thumbnails' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-thumbnails-num" type="number" name="_soliloquy[thumbnails_num]" value="<?php echo $instance->get_config( 'thumbnails_num', $instance->get_config_default( 'thumbnails_num' ) ); ?>" />
                        <p class="description"><?php _e( 'The number of thumbnails visible in the thumbnail container.', 'soliloquy-thumbnails' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-thumbnails-crop-box">
                    <th scope="row">
                        <label for="soliloquy-config-thumbnails-crop"><?php _e( 'Crop Slider Thumbnails?', 'soliloquy-thumbnails' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-thumbnails-crop" type="checkbox" name="_soliloquy[thumbnails_crop]" value="<?php echo $instance->get_config( 'thumbnails_crop', $instance->get_config_default( 'thumbnails_crop' ) ); ?>" <?php checked( $instance->get_config( 'thumbnails_crop', $instance->get_config_default( 'thumbnails_crop' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables thumbnail cropping.', 'soliloquy-thumbnails' ); ?></span>
                    </td>
                </tr>
                <?php do_action( 'soliloquy_thumbnails_box', $post ); ?>
            </tbody>
        </table>
    </div>
    <?php

}

/**
 * Saves the addon settings.
 *
 * @since 1.0.0
 *
 * @param array $settings  Array of settings to be saved.
 * @param int $post_id     The current post ID.
 * @return array $settings Amended array of settings to be saved.
 */
function soliloquy_thumbnails_save( $settings, $post_id ) {

    $settings['config']['thumbnails']          = isset( $_POST['_soliloquy']['thumbnails'] ) ? 1 : 0;
    $settings['config']['thumbnails_width']    = absint( $_POST['_soliloquy']['thumbnails_width'] );
    $settings['config']['thumbnails_height']   = absint( $_POST['_soliloquy']['thumbnails_height'] );
    $settings['config']['thumbnails_position'] = preg_replace( '#[^a-z0-9-_]#', '', $_POST['_soliloquy']['thumbnails_position'] );
    $settings['config']['thumbnails_margin']   = absint( $_POST['_soliloquy']['thumbnails_margin'] );
    $settings['config']['thumbnails_num']      = absint( $_POST['_soliloquy']['thumbnails_num'] );
    $settings['config']['thumbnails_crop']     = isset( $_POST['_soliloquy']['thumbnails_crop'] ) ? 1 : 0;

    // Mobile
    $settings['config']['mobile_thumbnails']   = isset( $_POST['_soliloquy']['mobile_thumbnails'] ) ? 1 : 0;

    return $settings;

}

/**
* Outputs the thumbnail preview area on the Edit Metadata modal
*
* @since 2.1.4
* 
* @param int $attach_id The current attachment ID.
* @param array $data    Array of attachment data.
* @param int $post_id   The current post ID.
*/
function soliloquy_thumbnails_preview( $attach_id, $data, $post_id ) {
	
	?>
	<hr />
	<h2><?php _e( 'Thumbnail', 'soliloquy-thumbnails' ); ?></h2>
	<img class="details-image thumb" src="<?php echo ( isset( $data['thumb'] ) ? $data['thumb'] : ''); ?>" draggable="false" />
	<div class="clear">
    	<a href="#" class="soliloquy-thumbnail button button-primary" data-field="soliloquy-thumb" title="<?php _e( 'Choose Thumbnail Image', 'soliloquy' ); ?>"><?php _e( 'Choose Thumbnail Image', 'soliloquy' ); ?></a>
		<a href="#" class="soliloquy-thumbnail-delete button button-secondary" data-field="soliloquy-thumb" title="<?php _e( 'Remove Thumbnail Image', 'soliloquy' ); ?>"><?php _e( 'Remove Thumbnail Image', 'soliloquy' ); ?></a>
	</div>
	
	<?php 
	// Output message to state whether the thumbnail is optional or not
	switch( $data['type'] ) {
		case 'image':
		case 'video':
			?>
			<span class="description"><?php _e( 'Optionally choose a specific thumbnail for this image. If none is chosen, one will be automatically generated from the image above.', 'soliloquy-thumbnails' ); ?></span>
			<?php
			break;
		case 'html':
		?>
			<span class="description"><?php _e( 'Specify a thumbnail image for this HTML slider.', 'soliloquy-thumbnails' ); ?></span>
			<?php
			break;
	}
		
}

/**
 * Outputs the thumbnail meta fields.
 *
 * @since 1.0.0
 *
 * @param int $attach_id The current attachment ID.
 * @param array $data    Array of attachment data.
 * @param int $post_id   The current post ID.
 */
function soliloquy_thumbnails_meta( $attach_id, $data, $post_id ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <label class="setting">
        <span class="name"><?php _e( 'Thumbnail', 'soliloquy-thumbnails' ); ?></span>
		<input id="soliloquy-thumb-<?php echo $attach_id; ?>" class="soliloquy-thumb" type="text" name="_soliloquy[meta_thumb]" value="<?php echo ( ! empty( $data['thumb'] ) ? esc_url( $data['thumb'] ) : '' ); ?>" data-soliloquy-meta="thumb" />
		<span class="description"><?php _e( 'Specify the URL of the image to display as a thumbnail.', 'soliloquy-thumbnails' ); ?></span>
	</label>
    <?php

}

/**
* Saves thumbnail meta fields
*
* @since 2.1.4
*
* @param array $slider_data	Array of slider data
* @param array $meta		Array of meta data
* @param int $attach_id		The current attachment ID
* @param int $post_id 		The current post ID
*/
function soliloquy_thumbnails_save_meta( $slider_data, $meta, $attach_id, $post_id ) {
	
	if ( isset( $meta['thumb'] ) ) {
        $slider_data['slider'][$attach_id]['thumb'] = esc_url( $meta['thumb'] );
    }
	
	return $slider_data;
}

/**
 * Prepares all of the contextual hooks and filters for running thumbnails.
 *
 * @since 1.0.0
 *
 * @param array $data Data for the slider.
 */
function soliloquy_thumbnails_init( $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return;
    }

    // Add classes to outer container
    add_filter( 'soliloquy_output_container_classes', 'soliloquy_thumbnails_outer_container_classes', 10, 2 );

    // Determine slider content filter based on thumbnail position.
    switch ( $instance->get_config( 'thumbnails_position', $data ) ) {
        case 'top':
            add_filter( 'soliloquy_output_start', 'soliloquy_thumbnails_output', 10, 2 );
            break;

        case 'left':
            add_filter( 'soliloquy_output_start', 'soliloquy_thumbnails_output', 10, 2 );
            add_filter( 'soliloquy_output_container_style', 'soliloquy_thumbnails_container_style', 10, 2 );
            break;

        case 'right':
            add_filter( 'soliloquy_output_start', 'soliloquy_thumbnails_output', 10, 2 );
            add_filter( 'soliloquy_output_container_style', 'soliloquy_thumbnails_container_style', 10, 2 );
            break;
        
        case 'bottom':
            add_filter( 'soliloquy_output', 'soliloquy_thumbnails_output', 10, 2 );
            break;
    }

    // Add the rest of the contextual hooks/filters.
    add_filter( 'soliloquy_cropped_image', 'soliloquy_thumbnails_get_thumbnail', 10, 5 );
    add_filter( 'soliloquy_output_start', 'soliloquy_thumbnails_styles', 0, 2 );
    add_filter( 'soliloquy_thumbnails_output_container_style', array( $instance, 'position_slider' ), 999, 2 );
    add_action( 'soliloquy_api_start_global', 'soliloquy_thumbnails_reveal' );
    add_action( 'soliloquy_api_preload', 'soliloquy_thumbnails_preload', 0 );
    add_action( 'soliloquy_api_slider', 'soliloquy_thumbnails_js' );
    add_action( 'soliloquy_api_before_transition', 'soliloquy_thumbnails_sync' );

}

/**
 * Adds CSS classes to the Soliloquy outer container
 *
 * @since 2.2.0
 *
 * @param array $classes CSS Classes
 * @param array $data Slider Data
 * @return array CSS Classes
 */
function soliloquy_thumbnails_outer_container_classes( $classes, $data ) {

    // Add position
    $instance  = Soliloquy_Shortcode::get_instance();
    $classes[] = 'soliloquy-thumbnails-outer-container';
    $classes[] = 'soliloquy-thumbnails-position-' . $instance->get_config( 'thumbnails_position', $data );

    return $classes;

}

/**
 * Adds inline styles to the Soliloquy inner container
 *
 * Used to apply left/right padding to the Soliloquy inner container if thumbnails are
 * left/right aligned, so the thumbnails + slideshow display side by side
 *
 * @since 2.2.0
 *
 * @param string $styles Inline CSS Styles
 * @param array $data Slider Data
 * @return string Inline CSS Styles
 */
function soliloquy_thumbnails_container_style( $styles, $data ) {

    // Check if thumbnails are left/right positioned
    $instance  = Soliloquy_Shortcode::get_instance();
    $position = $instance->get_config( 'thumbnails_position', $data );
    
    // Get width and margin
    $width = $instance->get_config( 'thumbnails_width', $data );
    $margin = $instance->get_config( 'thumbnails_margin', $data );
    $spacing = $width + $margin;

    switch ( $position ) {
        case 'left':
            // Add left padding to slider
            $styles .= 'padding-left:' . $spacing . 'px;';
            break;

        case'right':
            // Add right padding to slider
            $styles .= 'padding-right:' . $spacing . 'px;';
            break;
    }

    return $styles;

}

/**
 * Outputs the thumbnails HTML markup.
 *
 * @since 1.0.0
 *
 * @param string $slider  The HTML markup of the slider.
 * @param array $data     Data for the slider.
 * @return string $slider Amended HTML markup of the slider.
 */
function soliloquy_thumbnails_output( $slider, $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return $slider;
    }
    
    $i        = 0;
    $width    = 'left' == $instance->get_config( 'thumbnails_position', $data ) || 'right' == $instance->get_config( 'thumbnails_position', $data ) ? $instance->get_config( 'thumbnails_width', $data ) : $instance->get_config( 'slider_width', $data );
    $height   = 'left' == $instance->get_config( 'thumbnails_position', $data ) || 'right' == $instance->get_config( 'thumbnails_position', $data ) ? $instance->get_config( 'slider_height', $data ) : $instance->get_config( 'thumbnails_height', $data );
    
    $thumbs   = apply_filters( 'soliloquy_thumbnails_output_start', $slider, $data );

    $thumbs  .= '<div id="soliloquy-thumbnails-container-' . sanitize_html_class( $data['id'] ) . '" class="' . soliloquy_thumbnails_classes( $data ) . '" style="max-width:' . $width . 'px;max-height:' . $height . 'px;' . apply_filters( 'soliloquy_thumbnails_output_container_style', '', $data ) . '"' . apply_filters( 'soliloquy_thumbnails_output_container_attr', '', $data ) . '>';
        $thumbs .= '<ul id="soliloquy-thumbnails-' . sanitize_html_class( $data['id'] ) . '" class="soliloquy-thumbnails soliloquy-wrap soliloquy-clear">';
            $thumbs = apply_filters( 'soliloquy_thumbnails_output_before_container', $thumbs, $data );

            foreach ( $data['slider'] as $id => $item ) {
                // Skip over images that are pending (ignore if in Preview mode).
                if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
                    continue;
                }

                $thumbs   = apply_filters( 'soliloquy_thumbnails_output_before_item', $thumbs, $id, $item, $data, $i );
                $output   = '<li class="' . soliloquy_thumbnails_item_classes( $item, $i, $data ) . '"' . apply_filters( 'soliloquy_thumbnails_output_item_attr', '', $id, $item, $data, $i ) . ' draggable="false" style="list-style:none">';
                    $output .= soliloquy_thumbnails_get_slide( $id, $item, $data, $i );
                $output .= '</li>';
                $output  = apply_filters( 'soliloquy_thumbnails_output_single_item', $output, $id, $item, $data, $i );
                $thumbs .= $output;
                $thumbs  = apply_filters( 'soliloquy_thumbnails_output_after_item', $thumbs, $id, $item, $data, $i );

                // Increment the iterator.
                $i++;
            }

            $thumbs = apply_filters( 'soliloquy_thumbnails_output_after_container', $thumbs, $data );
        $thumbs .= '</ul>';
        $thumbs  = apply_filters( 'soliloquy_thumbnails_output_end', $thumbs, $data );
    $thumbs .= '</div>';

    // Remove contextual filters.
    remove_filter( 'soliloquy_thumbnails_output_container_style', array( $instance, 'position_slider' ), 999, 2 );
    remove_filter( 'soliloquy_output', 'soliloquy_thumbnails_output', 10, 2 );
    remove_filter( 'soliloquy_output_start', 'soliloquy_thumbnails_output', 10, 2 );

    return apply_filters( 'soliloquy_thumbnails_output', $thumbs, $data );

}

/**
 * Outputs the thumbnails styles.
 *
 * @since 1.0.0
 *
 * @param string $slider  The HTML markup of the slider.
 * @param array $data     Data for the slider.
 * @return string $slider Amended HTML markup of the slider.
 */
function soliloquy_thumbnails_styles( $slider, $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return $slider;
    }

    // Since this CSS only needs to be defined once on a page, use static flag to help keep track.
    static $soliloquy_thumbnails_css_flag = false;

    // If the flag has been set to true, return the default code.
    if ( $soliloquy_thumbnails_css_flag ) {
        return $slider;
    }

    $styles   = '<style type="text/css">';
        $styles .= '.soliloquy-thumbnails-container .soliloquy-item { opacity: .5; }';
        $styles .= '.soliloquy-thumbnails-container .soliloquy-active-slide, .soliloquy-thumbnails-container .soliloquy-item:hover { opacity: 1; }';
    $styles  .= '</style>';
    $styles   = apply_filters( 'soliloquy_thumbnails_styles', $styles, $data );

    // Set our flag to true.
    $soliloquy_thumbnails_css_flag = true;

    return Soliloquy_Shortcode::get_instance()->minify( $styles ) . $slider;

}

/**
 * Reveals the thumbnails with elegance.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 */
function soliloquy_thumbnails_reveal( $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return;
    }

    ob_start();
    ?>
    jQuery('#soliloquy-thumbnails-container-<?php echo $data['id']; ?>').css('height', Math.round(jQuery('#soliloquy-thumbnails-container-<?php echo $data['id']; ?>').width()/(<?php echo $instance->get_config( 'slider_width', $data ); ?>/<?php echo $instance->get_config( 'thumbnails_height', $data ); ?>))).fadeTo(300, 1);
    <?php
    echo ob_get_clean();

}

/**
 * Preloads the thumbnail output at lightening fast speeds.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 */
function soliloquy_thumbnails_preload( $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return;
    }

    ob_start();
    ?>
    if ( typeof soliloquy_thumbnails_slider === 'undefined' || false === soliloquy_thumbnails_slider ) {
        soliloquy_thumbnails_slider = {};
    }

    var soliloquy_thumbnails_container_<?php echo $data['id']; ?> = $('#soliloquy-thumbnails-container-<?php echo $data['id']; ?>'),
        soliloquy_thumbnails_<?php echo $data['id']; ?> = $('#soliloquy-thumbnails-<?php echo $data['id']; ?>'),
        soliloquy_thumbnails_holder_<?php echo $data['id']; ?> = $('#soliloquy-thumbnails-<?php echo $data['id']; ?>').find('.soliloquy-preload');

    if ( 0 !== soliloquy_thumbnails_holder_<?php echo $data['id']; ?>.length ) {
        var soliloquy_thumbnails_src_attr = 'data-soliloquy-src';
        $.each(soliloquy_thumbnails_holder_<?php echo $data['id']; ?>, function(i, el){
            var soliloquy_src = $(this).attr(soliloquy_thumbnails_src_attr);
            if ( typeof soliloquy_src === 'undefined' || false === soliloquy_src ) {
                return;
            }

            var soliloquy_image = new Image();
            soliloquy_image.src = soliloquy_src;
            $(this).attr('src', soliloquy_src).removeAttr(soliloquy_thumbnails_src_attr);
        });
    }
    <?php
    echo ob_get_clean();

}

/**
 * Outputs the thumbnails JS init code to initialize the thumbnails.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 */
function soliloquy_thumbnails_js( $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return;
    }

    // Get mode (horizontal or vertical)
    $position = $instance->get_config( 'thumbnails_position', $data );
    switch ( $position ) {
        case 'top':
        case 'bottom':
            $mode = 'horizontal';
            break;

        case 'left':
        case 'right':
            $mode = 'vertical';
            break;
    }

    ob_start();
    ?>
    soliloquy_thumbnails_slider['<?php echo $data['id']; ?>'] = soliloquy_thumbnails_<?php echo $data['id']; ?>.soliloquy({
        <?php do_action( 'soliloquy_thumbnails_api_config_start', $data ); ?>
        slideWidth: <?php echo $instance->get_config( 'thumbnails_width', $data ); ?>,
        slideMargin: <?php echo $instance->get_config( 'thumbnails_margin', $data ); ?>,
        minSlides: <?php echo $instance->get_config( 'thumbnails_num', $data ); ?>,
        maxSlides: <?php echo $instance->get_config( 'thumbnails_num', $data ); ?>,
        moveSlides: 1,
        slideSelector: '.soliloquy-item',
        auto: 0,
        useCSS: 0,
        adaptiveHeight: 1,
        adaptiveHeightSpeed: <?php echo apply_filters( 'soliloquy_adaptive_height_speed', 400, $data ); ?>,
        infiniteLoop: 0,
        hideControlOnEnd: 1,
        mode: '<?php echo $mode; ?>',
        pager: 0,
        controls: <?php echo $instance->get_config( 'arrows', $data ); ?>,
        nextText: '',
        prevText: '',
        startText: '',
        stopText: '',
        <?php do_action( 'soliloquy_thumbnails_api_config_callback', $data ); ?>
        onSliderLoad: function(currentIndex){
            soliloquy_thumbnails_container_<?php echo $data['id']; ?>.find('.soliloquy-active-slide').removeClass('soliloquy-active-slide');
            soliloquy_thumbnails_container_<?php echo $data['id']; ?>.css({'height':'auto','background-image':'none'}).find('.soliloquy-controls').fadeTo(300, 1);
            $('#soliloquy-thumbnails-<?php echo $data['id']; ?> .soliloquy-item').eq(soliloquy_slider['<?php echo $data['id']; ?>'].getCurrentSlide()).addClass('soliloquy-active-slide');
            $(document).on('click.SoliloquyThumbnails', '#soliloquy-thumbnails-<?php echo $data['id']; ?> .soliloquy-thumbnails-item', function(e){
                e.preventDefault();
                var $this = $(this);
                if ( $this.hasClass('soliloquy-active-slide') ) {
                    return;
                }
                soliloquy_slider['<?php echo $data['id']; ?>'].stopAuto();
                soliloquy_slider['<?php echo $data['id']; ?>'].goToSlide($this.index());
            });
            <?php do_action( 'soliloquy_thumbnails_api_on_load', $data ); ?>
        },
        onSlideBefore: function(element, oldIndex, newIndex){
            <?php do_action( 'soliloquy_thumbnails_api_before_transition', $data ); ?>
        },
        onSlideAfter: function(element, oldIndex, newIndex){
            <?php do_action( 'soliloquy_thumbnails_api_after_transition', $data ); ?>
        },
        <?php do_action( 'soliloquy_thumbnails_api_config_end', $data ); ?>
    });
    <?php
    echo ob_get_clean();

}

/**
 * Syncs the thumbnails to the main slider.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 */
function soliloquy_thumbnails_sync( $data ) {

    // If there are no thumbnails, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'thumbnails', $data ) ) {
        return;
    }

    ob_start();
    ?>
    soliloquy_thumbnails_<?php echo $data['id']; ?>.find('.soliloquy-active-slide').removeClass('soliloquy-active-slide');
    soliloquy_thumbnails_<?php echo $data['id']; ?>.find('.soliloquy-item:not(.soliloquy-clone):eq(' + newIndex + ')').addClass('soliloquy-active-slide');
    if ( soliloquy_thumbnails_slider['<?php echo $data['id']; ?>'].getSlideCount() - newIndex >= parseInt(<?php echo $instance->get_config( 'thumbnails_num', $data ); ?>) ) {
        soliloquy_thumbnails_slider['<?php echo $data['id']; ?>'].goToSlide(newIndex);
    } else {
        soliloquy_thumbnails_slider['<?php echo $data['id']; ?>'].goToSlide(soliloquy_thumbnails_slider['<?php echo $data['id']; ?>'].getSlideCount() - parseInt(<?php echo $instance->get_config( 'thumbnails_num', $data ); ?>));
    }
    <?php
    echo ob_get_clean();

}

/**
 * Returns the available thumbnail positions for the thumbnails container.
 *
 * @since 1.0.0
 *
 * @return array Array of thumbnail position data.
 */
function soliloquy_thumbnails_positions() {

    $positions = array(
        array(
            'value' => 'top',
            'name'  => __( 'Top', 'soliloquy' )
        ),
        array(
            'value' => 'bottom',
            'name'  => __( 'Bottom', 'soliloquy' )
        ),
        array(
            'value' => 'left',
            'name'  => __( 'Left', 'soliloquy' )
        ),
        array(
            'value' => 'right',
            'name'  => __( 'Right', 'soliloquy' )
        ),
    );

    return apply_filters( 'soliloquy_thumbnails_positions', $positions );

}

/**
 * Returns the thumbnails slider classes.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 * @return string     String of space separated thumbnail slider classes.
 */
function soliloquy_thumbnails_classes( $data ) {

    // Set default class.
    $instance  = Soliloquy_Shortcode::get_instance();
    $classes   = array();
    $classes[] = 'soliloquy-container';
    $classes[] = 'soliloquy-thumbnails-container';

    // Allow filtering of classes and then return what's left.
    $classes = apply_filters( 'soliloquy_thumbnails_output_classes', $classes, $data );

    // Add custom class based on the theme.
    $classes[] = 'soliloquy-theme-' . $instance->get_config( 'slider_theme', $data );
    
    // If the slider has RTL support, add a class for it.
    if ( $instance->get_config( 'rtl', $data ) ) {
        $classes[] = 'soliloquy-rtl';
    }

    return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );

}

/**
 * Returns the thumbnails slider item classes.
 *
 * @since 1.0.0
 *
 * @param array $item Array of item data.
 * @param int $i      The current position in the slider.
 * @param array $data The slider data to use for retrieval.
 * @return string     String of space separated slider item classes.
 */
function soliloquy_thumbnails_item_classes( $item, $i, $data ) {

    // Set default class.
    $classes   = array();
    $classes[] = 'soliloquy-item';
    $classes[] = 'soliloquy-item-' . $i;
    $classes[] = 'soliloquy-thumbnails-item';

    // Set the type of slide as a class.
    $classes[] = ! empty( $item['type'] ) ? 'soliloquy-' . $item['type'] . '-slide' : 'soliloquy-image-slide';

    // Allow filtering of classes and then return what's left.
    $classes = apply_filters( 'soliloquy_thumbnails_output_item_classes', $classes, $item, $i, $data );
    return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );

}

/**
* When soliloquy_thumbnails_get_slide calls Soliloquy_Shortcode::get_image_src(),
* get_image_src fires the filter that calls this function so we generate a thumbnail
* based from the 'full' thumbnail, if specified.
*
* Otherwise fallback to the image and use that as a thumbnail
*
* @since 2.1.8
*
* @param string $image Image
* @param int $id Image ID
* @param array $item Slide
* @param array $data Slider Config
* @param int $slider_id Slider ID
* @return string Image to use as Thumbnail
*/
function soliloquy_thumbnails_get_thumbnail( $image, $id, $item, $data, $slider_id ) {

    if ( isset( $item['thumb'] ) && ! empty( $item['thumb'] ) ) {
        return $item['thumb'];
    }

    return $image;

}

/**
 * Returns the slide markup for the thumbnails slider.
 *
 * @since 1.0.0
 *
 * @param mixed $id   The slider ID.
 * @param array $item Array of item data.
 * @param array $data The slider data to use for retrieval.
 * @param int $i      The current position in the slider.
 * @return string     String of space separated slider item classes.
 */
function soliloquy_thumbnails_get_slide( $id, $item, $data, $i ) {
	
	$instance = Soliloquy_Shortcode::get_instance();
	$mobile = $instance->is_mobile();
    $imagesrc = $instance->get_image_src( $id, $item, $data, 'thumbnails' );

    $output   = apply_filters( 'soliloquy_thumbnails_output_before_item', '', $id, $item, $data, $i );
        $output  = apply_filters( 'soliloquy_thumbnails_output_before_link', $output, $id, $item, $data, $i );
        $output .= '<a href="" class="soliloquy-link soliloquy-thumbnails-link" title="' . esc_attr( $item['title'] ) . '"' . apply_filters( 'soliloquy_thumbnails_output_link_attr', '', $id, $item, $data, $i ) . '>';
            $output  = apply_filters( 'soliloquy_thumbnails_output_before_image', $output, $id, $item, $data, $i );
            
            if ( $mobile ) {
	            $output .= '<img id="soliloquy-image-' . sanitize_html_class( $id ) . '" class="soliloquy-image soliloquy-thumbnails-image soliloquy-preload soliloquy-image-' . $i . '" src="' . esc_url( plugins_url( 'assets/css/images/holder.gif', Soliloquy::get_instance()->file ) ) . '" data-soliloquy-src="' . esc_url( $imagesrc ) . '" alt="' . trim( esc_html( $item['title'] ) ) . '"' . apply_filters( 'soliloquy_thumbnails_output_image_attr', '', $id, $item, $data, $i ) . ' />';
            } else {
	            $output .= '<img id="soliloquy-image-' . sanitize_html_class( $id ) . '" class="soliloquy-image soliloquy-thumbnails-image soliloquy-image-' . $i . '" src="' . esc_url( $imagesrc ) . '" alt="' . trim( esc_html( $item['title'] ) ) . '"' . ( $instance->get_config( 'dimensions', $data ) ? ' width="' . $instance->get_config( 'thumbnails_width', $data ) . '" height="' . $instance->get_config( 'thumbnails_height', $data ) . '"' : '' ) . apply_filters( 'soliloquy_thumbnails_output_image_attr', '', $id, $item, $data, $i ) . ' />';
            }
            
            $output  = apply_filters( 'soliloquy_thumbnails_output_after_image', $output, $id, $item, $data, $i );
        $output .= '</a>';
        $output  = apply_filters( 'soliloquy_thumbnails_output_after_link', $output, $id, $item, $data, $i );
    $output   = apply_filters( 'soliloquy_thumbnails_output_after_item', $output, $id, $item, $data, $i );

    return apply_filters( 'soliloquy_thumbnails_output_single_item', $output, $id, $item, $data, $i );

}