<?php
/**
 * Plugin Name: Soliloquy - Carousel Addon
 * Plugin URI:  http://soliloquywp.com
 * Description: Enables carousel display for Soliloquy sliders.
 * Author:      Thomas Griffin
 * Author URI:  http://thomasgriffinmedia.com
 * Version:     2.1.4
 * Text Domain: soliloquy-carousel
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
define( 'SOLILOQUY_CAROUSEL_PLUGIN_NAME', 'Soliloquy - Carousel Addon' );
define( 'SOLILOQUY_CAROUSEL_PLUGIN_VERSION', '2.1.4' );
define( 'SOLILOQUY_CAROUSEL_PLUGIN_SLUG', 'soliloquy-carousel' );

add_action( 'plugins_loaded', 'soliloquy_carousel_plugins_loaded' );
/**
 * Ensures the full Soliloquy plugin is active before proceeding.
 *
 * @since 1.0.0
 *
 * @return null Return early if Soliloquy is not active.
 */
function soliloquy_carousel_plugins_loaded() {

    // Bail if the main class does not exist.
    if ( ! class_exists( 'Soliloquy' ) ) {
        return;
    }

    // Fire up the addon.
    add_action( 'soliloquy_init', 'soliloquy_carousel_plugin_init' );
    
    // Loads the plugin textdomain for translation
    load_plugin_textdomain( SOLILOQUY_CAROUSEL_PLUGIN_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}

/**
 * Loads all of the addon hooks and filters.
 *
 * @since 1.0.0
 */
function soliloquy_carousel_plugin_init() {

    add_action( 'soliloquy_updater', 'soliloquy_carousel_updater' );
    add_filter( 'soliloquy_defaults', 'soliloquy_carousel_defaults', 10, 2 );
    add_filter( 'soliloquy_get_config_mobile_keys', 'soliloquy_carousel_mobile_keys' );
    add_filter( 'soliloquy_tab_nav', 'soliloquy_carousel_tab_nav' );
    add_action( 'soliloquy_mobile_box', 'soliloquy_mobile_tab_carousel' );
    add_action( 'soliloquy_tab_carousel', 'soliloquy_carousel_tab_carousel' );
    add_filter( 'soliloquy_save_settings', 'soliloquy_carousel_save', 10, 2 );
    add_action( 'soliloquy_saved_settings', 'soliloquy_carousel_crop', 10, 3 );
    add_filter( 'soliloquy_pre_data', 'soliloquy_carousel_set_transition' );
    add_filter( 'soliloquy_disable_preloading', 'soliloquy_carousel_disable_preloading', 10, 2 );
    add_filter( 'soliloquy_crop_type', 'soliloquy_carousel_crop_output', 10, 4 );
    add_filter( 'soliloquy_api_config_callback', 'soliloquy_carousel_output' );
    add_filter( 'soliloquy_output_image_slide_dimensions', 'soliloquy_carousel_image_dimensions', 10, 2 );

}

/**
 * Initializes the addon updater.
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function soliloquy_carousel_updater( $key ) {

    $args = array(
        'plugin_name' => SOLILOQUY_CAROUSEL_PLUGIN_NAME,
        'plugin_slug' => SOLILOQUY_CAROUSEL_PLUGIN_SLUG,
        'plugin_path' => plugin_basename( __FILE__ ),
        'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . SOLILOQUY_CAROUSEL_PLUGIN_SLUG,
        'remote_url'  => 'http://soliloquywp.com/',
        'version'     => SOLILOQUY_CAROUSEL_PLUGIN_VERSION,
        'key'         => $key
    );
    $soliloquy_carousel_updater = new Soliloquy_Updater( $args );

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
function soliloquy_carousel_defaults( $defaults, $post_id ) {

    $defaults['carousel']        = 0;
    $defaults['carousel_width']  = 200;
    $defaults['carousel_height'] = 125;
    $defaults['carousel_margin'] = 10;
    $defaults['carousel_min']    = 3;
    $defaults['carousel_max']    = 3;
    $defaults['carousel_move']   = 1;

    // Mobile
    $defaults['mobile_carousel'] = 0;

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
function soliloquy_carousel_mobile_keys( $mobile_keys ) {

    $mobile_keys['carousel'] = 'mobile_carousel';
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
function soliloquy_carousel_tab_nav( $tabs ) {

    $tabs['carousel'] = __( 'Carousel', 'soliloquy-carousel' );
    return $tabs;

}

/**
 * Callback for displaying the UI for setting mobile options.
 *
 * @since 1.0.0
 *
 * @param object $post The current post object.
 */
function soliloquy_mobile_tab_carousel( $post ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <tr id="soliloquy-config-mobile-carousel-box">
        <th scope="row">
            <label for="soliloquy-config-mobile-carousel"><?php _e( 'Enable Slider Carousel?', 'soliloquy-carousel' ); ?></label>
        </th>
        <td>
            <input id="soliloquy-config-mobile-carousel" type="checkbox" name="_soliloquy[mobile_carousel]" value="<?php echo $instance->get_config( 'mobile_carousel', $instance->get_config_default( 'mobile_carousel' ) ); ?>" <?php checked( $instance->get_config( 'mobile_carousel', $instance->get_config_default( 'mobile_carousel' ) ), 1 ); ?> />
            <span class="description"><?php _e( 'Enables or disables the slider carousel feature on mobile devices.', 'soliloquy-carousel' ); ?></span>
        </td>
    </tr>
    <?php

}

/**
 * Callback for displaying the UI for setting carousel options.
 *
 * @since 1.0.0
 *
 * @param object $post The current post object.
 */
function soliloquy_carousel_tab_carousel( $post ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <div id="soliloquy-carousel">
        <p class="soliloquy-intro"><?php _e( 'The settings below adjust the carousel settings for the slider.', 'soliloquy-carousel' ); ?></p>
        <table class="form-table">
            <tbody>
                <tr id="soliloquy-config-carousel-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel"><?php _e( 'Enable Slider Carousel?', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel" type="checkbox" name="_soliloquy[carousel]" value="<?php echo $instance->get_config( 'carousel', $instance->get_config_default( 'carousel' ) ); ?>" <?php checked( $instance->get_config( 'carousel', $instance->get_config_default( 'carousel' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables the slider carousel feature.', 'soliloquy-carousel' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-carousel-width-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel-width"><?php _e( 'Carousel Slide Width', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel-width" type="number" name="_soliloquy[carousel_width]" value="<?php echo $instance->get_config( 'carousel_width', $instance->get_config_default( 'carousel_width' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'The width of each slide inside of the carousel (acts a max width and adjusts dynamically).', 'soliloquy-carousel' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-carousel-height-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel-height"><?php _e( 'Carousel Slide Height', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel-height" type="number" name="_soliloquy[carousel_height]" value="<?php echo $instance->get_config( 'carousel_height', $instance->get_config_default( 'carousel_height' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'The height of each slide inside of the carousel (acts a max height and adjusts dynamically).', 'soliloquy-carousel' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-carousel-margin-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel-margin"><?php _e( 'Carousel Slide Margin', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel-margin" type="number" name="_soliloquy[carousel_margin]" value="<?php echo $instance->get_config( 'carousel_margin', $instance->get_config_default( 'carousel_margin' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'The margin between each carousel slide within the slider.', 'soliloquy-carousel' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-carousel-min-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel-min"><?php _e( 'Carousel Slides Minimum', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel-min" type="number" name="_soliloquy[carousel_min]" value="<?php echo $instance->get_config( 'carousel_min', $instance->get_config_default( 'carousel_min' ) ); ?>" />
                        <p class="description"><?php _e( 'The minimum number of slides that should be visible within the carousel.', 'soliloquy-carousel' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-carousel-max-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel-max"><?php _e( 'Carousel Slides Maximum', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel-max" type="number" name="_soliloquy[carousel_max]" value="<?php echo $instance->get_config( 'carousel_max', $instance->get_config_default( 'carousel_max' ) ); ?>" />
                        <p class="description"><?php _e( 'The maximum number of slides that should be visible within the carousel.', 'soliloquy-carousel' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-carousel-move-box">
                    <th scope="row">
                        <label for="soliloquy-config-carousel-move"><?php _e( 'Number of Slides to Move', 'soliloquy-carousel' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-carousel-move" type="number" name="_soliloquy[carousel_move]" value="<?php echo $instance->get_config( 'carousel_move', $instance->get_config_default( 'carousel_move' ) ); ?>" />
                        <p class="description"><?php _e( 'The number of slides to move when navigating through the carousel.', 'soliloquy-carousel' ); ?></p>
                    </td>
                </tr>
                <?php do_action( 'soliloquy_carousel_box', $post ); ?>
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
function soliloquy_carousel_save( $settings, $post_id ) {

    $settings['config']['carousel']        = isset( $_POST['_soliloquy']['carousel'] ) ? 1 : 0;
    $settings['config']['carousel_width']  = absint( $_POST['_soliloquy']['carousel_width'] );
    $settings['config']['carousel_height'] = absint( $_POST['_soliloquy']['carousel_height'] );
    $settings['config']['carousel_margin'] = absint( $_POST['_soliloquy']['carousel_margin'] );
    $settings['config']['carousel_min']    = absint( $_POST['_soliloquy']['carousel_min'] );
    $settings['config']['carousel_max']    = absint( $_POST['_soliloquy']['carousel_max'] );
    $settings['config']['carousel_move']   = absint( $_POST['_soliloquy']['carousel_move'] );

    // Mobile
    $settings['config']['mobile_carousel'] = isset( $_POST['_soliloquy']['mobile_carousel'] ) ? 1 : 0;

    return $settings;

}

/**
 * Crops images based on carousel settings for the slider.
 *
 * @since 1.0.0
 *
 * @param array $settings  Array of settings to be saved.
 * @param int $post_id     The current post ID.
 * @param object $post     The current post object.
 */
function soliloquy_carousel_crop( $settings, $post_id, $post ) {

    // If the carousel option and crop option are checked, crop images accordingly.
    if ( isset( $settings['config']['slider'] ) && $settings['config']['slider'] ) {
        if ( isset( $settings['config']['carousel'] ) && $settings['config']['carousel'] ) {
            $instance = Soliloquy_Metaboxes::get_instance();
            $args     = apply_filters( 'soliloquy_crop_image_args',
                array(
                    'position' => 'c',
                    'width'    => $instance->get_config( 'carousel_width', $instance->get_config_default( 'carousel_width' ) ),
                    'height'   => $instance->get_config( 'carousel_height', $instance->get_config_default( 'carousel_height' ) ),
                    'quality'  => 100,
                    'retina'   => false
                )
            );
            $instance->crop_images( $args, $post_id );
        }
    }

}

/**
 * Ensures that the transition for the slider is proper for a carousel.
 *
 * @since 1.0.0
 *
 * @param array $data  Array of slider data.
 * @return array $data Amended array of slider data.
 */
function soliloquy_carousel_set_transition( $data ) {

    // If there is no carousel, don't modify any of the data.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'carousel', $data ) ) {
        return $data;
    }

    // Check the transition type. If it is set to 'fade', adjust it to scroll horizontally.
    if ( 'fade' == $instance->get_config( 'transition', $data ) ) {
        $data['config']['transition'] = 'horizontal';
    }

    // Return the data.
    return apply_filters( 'soliloquy_carousel_transition', $data );

}

/**
 * Disable image preloading for Carousels; ensures they load in IE without issue.
 * Temporary until there is a better solution for getting carousels to work with .soliloquy-preload in IE10+11
 *
 * @since 2.1.3
 *
 * @param bool $disabled Disable Image Preloading
 * @return bool Disable Image Preloading
 */
function soliloquy_carousel_disable_preloading( $disabled, $data ) {

    // Check slider is a carousel before we disable preloading
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'carousel', $data ) ) {
        return $disabled;
    }

    // Carousel, so disable preloading
    return true;

}

/**
 * Sets the image src to return the cropped image size for the carousel.
 *
 * @since 1.0.0
 *
 * @param string $type  The type of crop to perform.
 * @param int $id       The current slider ID.
 * @param array $item   Array of data about the current slide item.
 * @param array $data   Array of slider data.
 * @return string $type Amended type of crop to perform.
 */
function soliloquy_carousel_crop_output( $type, $id, $item, $data ) {

    // If there is no carousel, don't crop anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'carousel', $data ) ) {
        return $type;
    }

    // If the slider is not set to be cropped, don't crop anything.
    if ( ! $instance->get_config( 'slider', $data ) ) {
        return $type;
    }

    // Change the crop type for our carousel.
    return apply_filters( 'soliloquy_carousel_crop_type', 'carousel', $type, $id, $item, $data );

}

/**
 * Outputs the carousel settings to the specific slider.
 *
 * @since 1.0.0
 *
 * @param array $data Data for the slider.
 * @return null       Return early if a carousel is not enabled.
 */
function soliloquy_carousel_output( $data ) {

    // If there is no carousel, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'carousel', $data ) ) {
        return;
    }

    // Output the carousel settings.
    ob_start();
    ?>
    slideWidth: <?php echo $instance->get_config( 'carousel_width', $data ); ?>,
    slideMargin: <?php echo $instance->get_config( 'carousel_margin', $data ); ?>,
    minSlides: <?php echo $instance->get_config( 'carousel_min', $data ); ?>,
    maxSlides: <?php echo $instance->get_config( 'carousel_max', $data ); ?>,
    moveSlides: <?php echo $instance->get_config( 'carousel_move', $data ); ?>,
    <?php
    echo ob_get_clean();

}

/**
 * Amends the image dimensions = carousel width and height
 *
 * Called when "Set Dimensions on Images" is enabled
 *
 * @since 2.1.4
 *
 * @param array $dimensions Width and Height Dimensions
 * @param array $data Slider Data
 * @return array Dimensions
 */
function soliloquy_carousel_image_dimensions( $dimensions, $data ) {

    // If there is no carousel, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'carousel', $data ) ) {
        return $dimensions;
    }

    // Change dimensions = carousel
    $dimensions = array(
        'width' => $instance->get_config( 'carousel_width', $data ),
        'height'=> $instance->get_config( 'carousel_height', $data ),
    );

    return $dimensions;

}