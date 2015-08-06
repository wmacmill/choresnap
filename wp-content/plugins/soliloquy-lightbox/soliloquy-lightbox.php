<?php
/**
 * Plugin Name: Soliloquy - Lightbox Addon
 * Plugin URI:  http://soliloquywp.com
 * Description: Enables responsive lightbox support for Soliloquy sliders.
 * Author:      Thomas Griffin
 * Author URI:  http://thomasgriffinmedia.com
 * Version:     2.2.6
 * Text Domain: soliloquy-lightbox
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
define( 'SOLILOQUY_LIGHTBOX_PLUGIN_NAME', 'Soliloquy - Lightbox Addon' );
define( 'SOLILOQUY_LIGHTBOX_PLUGIN_VERSION', '2.2.6' );
define( 'SOLILOQUY_LIGHTBOX_PLUGIN_SLUG', 'soliloquy-lightbox' );

add_action( 'plugins_loaded', 'soliloquy_lightbox_plugins_loaded' );
/**
 * Ensures the full Soliloquy plugin is active before proceeding.
 *
 * @since 1.0.0
 *
 * @return null Return early if Soliloquy is not active.
 */
function soliloquy_lightbox_plugins_loaded() {

    // Bail if the main class does not exist.
    if ( ! class_exists( 'Soliloquy' ) ) {
        return;
    }

    // Fire up the addon.
    add_action( 'soliloquy_init', 'soliloquy_lightbox_plugin_init' );
    
    // Loads the plugin textdomain for translation
    load_plugin_textdomain( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}

/**
 * Loads all of the addon hooks and filters.
 *
 * @since 1.0.0
 */
function soliloquy_lightbox_plugin_init() {

    add_action( 'soliloquy_updater', 'soliloquy_lightbox_updater' );
    add_filter( 'soliloquy_defaults', 'soliloquy_lightbox_defaults', 10, 2 );
    add_filter( 'soliloquy_meta_defaults', 'soliloquy_lightbox_meta_defaults', 10, 3 );
    add_filter( 'soliloquy_tab_nav', 'soliloquy_lightbox_tab_nav' );
    add_action( 'soliloquy_tab_lightbox', 'soliloquy_lightbox_tab_lightbox' );
    add_action( 'soliloquy_mobile_box', 'soliloquy_lightbox_tab_mobile' );
    add_action( 'soliloquy_after_image_meta_settings', 'soliloquy_lightbox_meta', 10, 3 );
    add_action( 'soliloquy_after_video_meta_settings', 'soliloquy_lightbox_meta', 10, 3 );
    add_filter( 'soliloquy_ajax_save_meta', 'soliloquy_lightbox_save_meta', 10, 4 );
    add_filter( 'soliloquy_save_settings', 'soliloquy_lightbox_save', 10, 2 );
    add_action( 'soliloquy_saved_settings', 'soliloquy_lightbox_crop', 10, 3 );
    add_action( 'soliloquy_before_output', 'soliloquy_lightbox_init' );
    add_filter( 'soliloquy_css', 'soliloquy_lightbox_theme', 10, 2 );

}

/**
 * Initializes the addon updater.
 *
 * @since 1.0.0
 *
 * @param string $key The user license key.
 */
function soliloquy_lightbox_updater( $key ) {

    $args = array(
        'plugin_name' => SOLILOQUY_LIGHTBOX_PLUGIN_NAME,
        'plugin_slug' => SOLILOQUY_LIGHTBOX_PLUGIN_SLUG,
        'plugin_path' => plugin_basename( __FILE__ ),
        'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . SOLILOQUY_LIGHTBOX_PLUGIN_SLUG,
        'remote_url'  => 'http://soliloquywp.com/',
        'version'     => SOLILOQUY_LIGHTBOX_PLUGIN_VERSION,
        'key'         => $key
    );
    $soliloquy_lightbox_updater = new Soliloquy_Updater( $args );

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
function soliloquy_lightbox_defaults( $defaults, $post_id ) {

    $defaults['lightbox']            = 0;
    $defaults['lightbox_theme']      = 'base';
    $defaults['lightbox_title']      = 'inside';
    $defaults['lightbox_arrows']     = 1;
    $defaults['lightbox_keyboard']   = 1;
    $defaults['lightbox_mousewheel'] = 0;
    $defaults['lightbox_aspect']     = 1;
    $defaults['lightbox_loop']       = 1;
    $defaults['lightbox_transition'] = 'fade';
    $defaults['lightbox_videos']     = 1;
    $defaults['lightbox_thumbs']     = 1;
    $defaults['lightbox_twidth']     = 75;
    $defaults['lightbox_theight']    = 50;
    $defaults['lightbox_tposition']  = 'bottom';

    // Mobile
    $defaults['mobile_lightbox']     = 0;
    return $defaults;

}

/**
 * Applies defaults to attachment meta settings.
 *
 * @since 1.0.0
 *
 * @param array $defaults  Array of default config values.
 * @param int $post_id     The current post ID.
 * @param int $attach_ud   The current attachment ID.
 * @return array $defaults Amended array of default config values.
 */
function soliloquy_lightbox_meta_defaults( $defaults, $post_id, $attach_id ) {

    $defaults['lightbox_enable'] = 1;
    return $defaults;

}

/**
 * Filters in a new tab for the addon.
 *
 * @since 1.0.0
 *
 * @param array $tabs  Array of default tab values.
 * @return array $tabs Amended array of default tab values.
 */
function soliloquy_lightbox_tab_nav( $tabs ) {

    $tabs['lightbox'] = __( 'Lightbox', 'soliloquy-lightbox' );
    return $tabs;

}

/**
 * Callback for displaying the UI for setting lightbox options.
 *
 * @since 1.0.0
 *
 * @param object $post The current post object.
 */
function soliloquy_lightbox_tab_lightbox( $post ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <div id="soliloquy-lightbox">
        <p class="soliloquy-intro"><?php _e( 'The settings below adjust the lightbox settings for the slider.', 'soliloquy-lightbox' ); ?></p>
        <table class="form-table">
            <tbody>
                <tr id="soliloquy-config-lightbox-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox"><?php _e( 'Enable Lightbox?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox" type="checkbox" name="_soliloquy[lightbox]" value="<?php echo $instance->get_config( 'lightbox', $instance->get_config_default( 'lightbox' ) ); ?>" <?php checked( $instance->get_config( 'lightbox', $instance->get_config_default( 'lightbox' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables the slider lightbox.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-theme-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-theme"><?php _e( 'Lightbox Theme', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <select id="soliloquy-config-lightbox-theme" name="_soliloquy[lightbox_theme]">
                            <?php foreach ( (array) soliloquy_lightbox_themes() as $i => $data ) : ?>
                                <option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $instance->get_config( 'lightbox_theme', $instance->get_config_default( 'lightbox_theme' ) ) ); ?>><?php echo $data['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e( 'Sets the theme for the lightbox display.', 'soliloquy-lightbox' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-title-display-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-title-display"><?php _e( 'Lightbox Title Position', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <select id="soliloquy-config-lightbox-title-display" name="_soliloquy[lightbox_title]">
                            <?php foreach ( (array) soliloquy_lightbox_titles() as $i => $data ) : ?>
                                <option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $instance->get_config( 'lightbox_title', $instance->get_config_default( 'lightbox_title' ) ) ); ?>><?php echo $data['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e( 'Sets the display of the titles inside of the lightbox.', 'soliloquy-lightbox' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-arrows-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-arrows"><?php _e( 'Enable Lightbox Arrows?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-arrows" type="checkbox" name="_soliloquy[lightbox_arrows]" value="<?php echo $instance->get_config( 'lightbox_arrows', $instance->get_config_default( 'lightbox_arrows' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_arrows', $instance->get_config_default( 'lightbox_arrows' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables the lightbox navigation arrows.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-keyboard-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-keyboard"><?php _e( 'Enable Keyboard Navigation?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-keyboard" type="checkbox" name="_soliloquy[lightbox_keyboard]" value="<?php echo $instance->get_config( 'lightbox_keyboard', $instance->get_config_default( 'lightbox_keyboard' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_keyboard', $instance->get_config_default( 'lightbox_keyboard' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables keyboard navigation in the lightbox.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-mousewheel-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-mousewheel"><?php _e( 'Enable Mousewheel Navigation?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-mousewheel" type="checkbox" name="_soliloquy[lightbox_mousewheel]" value="<?php echo $instance->get_config( 'lightbox_mousewheel', $instance->get_config_default( 'lightbox_mousewheel' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_mousewheel', $instance->get_config_default( 'lightbox_mousewheel' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables mousewheel navigation in the lightbox.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-aspect-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-aspect"><?php _e( 'Keep Aspect Ratio?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-aspect" type="checkbox" name="_soliloquy[lightbox_aspect]" value="<?php echo $instance->get_config( 'lightbox_aspect', $instance->get_config_default( 'lightbox_aspect' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_aspect', $instance->get_config_default( 'lightbox_aspect' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'If enabled, images will always resize based on the original aspect ratio.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-loop-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-loop"><?php _e( 'Loop Lightbox Navigation?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-loop" type="checkbox" name="_soliloquy[lightbox_loop]" value="<?php echo $instance->get_config( 'lightbox_loop', $instance->get_config_default( 'lightbox_loop' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_loop', $instance->get_config_default( 'lightbox_loop' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables infinite navigation cycling of the lightbox.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-effect-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-effect"><?php _e( 'Lightbox Transition Effect', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <select id="soliloquy-config-lightbox-effect" name="_soliloquy[lightbox_transition]">
                            <?php foreach ( (array) soliloquy_lightbox_transition_effects() as $i => $data ) : ?>
                                <option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $instance->get_config( 'lightbox_transition', $instance->get_config_default( 'lightbox_transition' ) ) ); ?>><?php echo $data['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e( 'Type of transition between images in the lightbox view.', 'soliloquy-lightbox' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-videos-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-videos"><?php _e( 'Load Videos in Lightbox?', 'soliloquy-lightbox' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-videos" type="checkbox" name="_soliloquy[lightbox_videos]" value="<?php echo $instance->get_config( 'lightbox_videos', $instance->get_config_default( 'lightbox_videos' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_videos', $instance->get_config_default( 'lightbox_videos' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Loads video slides in the lightbox on click instead of inside the slider itself.', 'soliloquy-lightbox' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-thumbnails-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-thumbnails"><?php _e( 'Enable Lightbox Thumbnails?', 'soliloquy' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-thumbnails" type="checkbox" name="_soliloquy[lightbox_thumbs]" value="<?php echo $instance->get_config( 'lightbox_thumbs', $instance->get_config_default( 'lightbox_thumbs' ) ); ?>" <?php checked( $instance->get_config( 'lightbox_thumbs', $instance->get_config_default( 'lightbox_thumbs' ) ), 1 ); ?> />
                        <span class="description"><?php _e( 'Enables or disables lightbox thumbnails.', 'soliloquy' ); ?></span>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-thumbnails-width-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-thumbnails-width"><?php _e( 'Lightbox Thumbnails Width', 'soliloquy' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-thumbnails-width" type="number" name="_soliloquy[lightbox_twidth]" value="<?php echo $instance->get_config( 'lightbox_twidth', $instance->get_config_default( 'lightbox_twidth' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'Sets the width of each lightbox thumbnail.', 'soliloquy' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-thumbnails-height-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-thumbnails-height"><?php _e( 'Lightbox Thumbnails Height', 'soliloquy' ); ?></label>
                    </th>
                    <td>
                        <input id="soliloquy-config-lightbox-thumbnails-height" type="number" name="_soliloquy[lightbox_theight]" value="<?php echo $instance->get_config( 'lightbox_theight', $instance->get_config_default( 'lightbox_theight' ) ); ?>" /> <span class="soliloquy-unit"><?php _e( 'px', 'soliloquy' ); ?></span>
                        <p class="description"><?php _e( 'Sets the height of each lightbox thumbnail.', 'soliloquy' ); ?></p>
                    </td>
                </tr>
                <tr id="soliloquy-config-lightbox-thumbnails-position-box">
                    <th scope="row">
                        <label for="soliloquy-config-lightbox-thumbnails-position"><?php _e( 'Lightbox Thumbnails Position', 'soliloquy' ); ?></label>
                    </th>
                    <td>
                        <select id="soliloquy-config-lightbox-thumbnails-position" name="_soliloquy[lightbox_tposition]">
                            <?php foreach ( (array) soliloquy_lightbox_thumbnail_positions() as $i => $data ) : ?>
                                <option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $instance->get_config( 'lightbox_tposition', $instance->get_config_default( 'lightbox_tposition' ) ) ); ?>><?php echo $data['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e( 'Sets the position of the lightbox thumbnails.', 'soliloquy' ); ?></p>
                    </td>
                </tr>
                <?php do_action( 'soliloquy_lightbox_box', $post ); ?>
            </tbody>
        </table>
    </div>
    <?php

}

/**
 * Callback for displaying the UI for setting mobile lightbox options.
 *
 * @since 1.0.0
 *
 * @param object $post The current post object.
 */
function soliloquy_lightbox_tab_mobile( $post ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <tr id="soliloquy-config-mobile-lightbox-box">
        <th scope="row">
            <label for="soliloquy-config-mobile-lightbox"><?php _e( 'Enable Lightbox on Mobile?', 'soliloquy-lightbox' ); ?></label>
        </th>
        <td>
            <input id="soliloquy-config-mobile-lightbox" type="checkbox" name="_soliloquy[mobile_lightbox]" value="<?php echo $instance->get_config( 'mobile_lightbox', $instance->get_config_default( 'mobile_lightbox' ) ); ?>" <?php checked( $instance->get_config( 'mobile_lightbox', $instance->get_config_default( 'mobile_lightbox' ) ), 1 ); ?> />
            <span class="description"><?php _e( 'Enables or disables the slider lightbox on mobile devices.', 'soliloquy-lightbox' ); ?></span>
        </td>
    </tr>
    <?php

}

/**
 * Outputs the lightbox meta fields.
 *
 * @since 1.0.0
 *
 * @param int $attach_id The current attachment ID.
 * @param array $data    Array of attachment data.
 * @param int $post_id   The current post ID.
 */
function soliloquy_lightbox_meta( $attach_id, $data, $post_id ) {

    $instance = Soliloquy_Metaboxes::get_instance();
    ?>
    <label class="setting">
        <span class="name"><?php _e( 'Load in Lightbox?', 'soliloquy' ); ?></span>
		<input id="soliloquy-lightbox-enable-<?php echo $attach_id; ?>" class="soliloquy-lightbox-enable" type="checkbox" name="_soliloquy[lightbox_enable]" data-soliloquy-meta="lightbox_enable" value="<?php echo $instance->get_meta( 'lightbox_enable', $attach_id, $instance->get_meta_default( 'lightbox_enable', $attach_id ) ); ?>"<?php checked( $instance->get_meta( 'lightbox_enable', $attach_id, $instance->get_meta_default( 'lightbox_enable', $attach_id ) ), 1 ); ?> />
	</label>
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
function soliloquy_lightbox_save( $settings, $post_id ) {

    $settings['config']['lightbox']            = isset( $_POST['_soliloquy']['lightbox'] ) ? 1 : 0;
    $settings['config']['lightbox_theme']      = preg_replace( '#[^a-z0-9-_]#', '', $_POST['_soliloquy']['lightbox_theme'] );
    $settings['config']['lightbox_title']      = preg_replace( '#[^a-z0-9-_]#', '', $_POST['_soliloquy']['lightbox_title'] );
    $settings['config']['lightbox_arrows']     = isset( $_POST['_soliloquy']['lightbox_arrows'] ) ? 1 : 0;
    $settings['config']['lightbox_keyboard']   = isset( $_POST['_soliloquy']['lightbox_keyboard'] ) ? 1 : 0;
    $settings['config']['lightbox_mousewheel'] = isset( $_POST['_soliloquy']['lightbox_mousewheel'] ) ? 1 : 0;
    $settings['config']['lightbox_aspect']     = isset( $_POST['_soliloquy']['lightbox_aspect'] ) ? 1 : 0;
    $settings['config']['lightbox_loop']       = isset( $_POST['_soliloquy']['lightbox_loop'] ) ? 1 : 0;
    $settings['config']['lightbox_transition'] = preg_replace( '#[^a-z0-9-_]#', '', $_POST['_soliloquy']['lightbox_transition'] );
    $settings['config']['lightbox_videos']     = isset( $_POST['_soliloquy']['lightbox_videos'] ) ? 1 : 0;
    $settings['config']['lightbox_thumbs']     = isset( $_POST['_soliloquy']['lightbox_thumbs'] ) ? 1 : 0;
    $settings['config']['lightbox_twidth']     = absint( $_POST['_soliloquy']['lightbox_twidth'] );
    $settings['config']['lightbox_theight']    = absint( $_POST['_soliloquy']['lightbox_theight'] );
    $settings['config']['lightbox_tposition']  = preg_replace( '#[^a-z0-9-_]#', '', $_POST['_soliloquy']['lightbox_tposition'] );

    // Mobile
    $settings['config']['mobile_lightbox']     = isset( $_POST['_soliloquy']['mobile_lightbox'] ) ? 1 : 0;

    return $settings;

}

/**
 * Saves the addon meta settings.
 *
 * @since 1.0.0
 *
 * @param array $settings  Array of settings to be saved.
 * @param array $meta      Array of slide meta to use for saving.
 * @param int $attach_id   The current attachment ID.
 * @param int $post_id     The current post ID.
 * @return array $settings Amended array of settings to be saved.
 */
function soliloquy_lightbox_save_meta( $settings, $meta, $attach_id, $post_id ) {

    $settings['slider'][$attach_id]['lightbox_enable'] = isset( $meta['lightbox_enable'] ) && $meta['lightbox_enable'] ? 1 : 0;
    return $settings;

}

/**
 * Crops images based on lightbox settings for the slider.
 *
 * @since 1.0.0
 *
 * @param array $settings  Array of settings to be saved.
 * @param int $post_id     The current post ID.
 * @param object $post     The current post object.
 */
function soliloquy_lightbox_crop( $settings, $post_id, $post ) {

    // If the lightbox option and crop option are checked, crop images accordingly.
    if ( isset( $settings['config']['lightbox_thumbs'] ) && $settings['config']['lightbox_thumbs'] ) {
        $instance = Soliloquy_Metaboxes::get_instance();
        $args     = apply_filters( 'soliloquy_crop_image_args',
            array(
                'position' => 'c',
                'width'    => $instance->get_config( 'lightbox_twidth', $instance->get_config_default( 'lightbox_twidth' ) ),
                'height'   => $instance->get_config( 'lightbox_theight', $instance->get_config_default( 'lightbox_theight' ) ),
                'quality'  => 100,
                'retina'   => false
            )
        );
        soliloquy_lightbox_crop_images( $args, $post_id );
    }

}

/**
 * Callback function for cropping lightbox thumbs.
 *
 * @since 1.0.0
 *
 * @param array $settings  Array of settings to be saved.
 * @param int $post_id     The current post ID.
 * @param object $post     The current post object.
 */
function soliloquy_lightbox_crop_images( $args, $post_id ) {

    // Gather all available images to crop.
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
    $images      = ! empty( $slider_data['slider'] ) ? $slider_data['slider'] : false;
    $common      = Soliloquy_Common::get_instance();

    // Loop through the images and crop them.
    if ( $images ) {
        // Increase the time limit to account for large image sets and suspend cache invalidations.
        set_time_limit( 0 );
        wp_suspend_cache_invalidation( true );

        foreach ( $images as $id => $item ) {
            // Get the full image attachment. If it does not return the data we need, skip over it.
            $image = wp_get_attachment_image_src( $id, 'full' );
            if ( ! is_array( $image ) ) {
                // Check for video/HTML slide and possibly use a thumbnail instead.
                if ( ( isset( $item['type'] ) && 'video' == $item['type'] || isset( $item['type'] ) && 'html' == $item['type'] ) && ! empty( $item['thumb'] ) ) {
                    $image = $item['thumb'];
                } else {
                    continue;
                }
            } else {
                $image = $image[0];
            }

            // Allow image to be filtered to use a different thumbnail than the main image.
            $image = apply_filters( 'soliloquy_cropped_image', $image, $id, $item, $args, $post_id );

            // Generate the cropped image.
            $cropped_image = $common->resize_image( $image, $args['width'], $args['height'], true, $args['position'], $args['quality'], $args['retina'] );

            // If there is an error, possibly output error message, otherwise woot!
            if ( is_wp_error( $cropped_image ) ) {
                // If debugging is defined, print out the error.
                if ( defined( 'SOLILOQUY_CROP_DEBUG' ) && SOLILOQUY_CROP_DEBUG ) {
                    echo '<pre>' . var_export( $cropped_image->get_error_message(), true ) . '</pre>';
                }
            } else {
                $slider_data['slider'][$id]['lb_thumb'] = $cropped_image;
            }
        }

        // Turn off cache suspension and flush the cache to remove any cache inconsistencies.
        wp_suspend_cache_invalidation( false );
        wp_cache_flush();

        // Update the slider data.
        update_post_meta( $post_id, '_sol_slider_data', $slider_data );
    }

}

/**
 * Prepares all of the contextual hooks and filters for running lightbox.
 *
 * @since 1.0.0
 *
 * @param array $data Data for the slider.
 */
function soliloquy_lightbox_init( $data ) {

    // If there is no lightbox, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    $key = ( wp_is_mobile() ? 'mobile_lightbox' : 'lightbox' );
    if ( ! $instance->get_config( $key, $data ) ) {
        return;
    }

    // Load the lightbox scripts, styles and theme.
    soliloquy_lightbox_load( $data );

    // Add lightbox contextual hooks and filters.
    add_filter( 'soliloquy_output_item_data', 'soliloquy_lightbox_infuse', 10, 4 );
    add_filter( 'soliloquy_output_link_attr', 'soliloquy_lightbox_attr', 10, 5 );
    add_action( 'soliloquy_api_slider', 'soliloquy_lightbox_js' );
    add_action( 'soliloquy_api_on_load', 'soliloquy_lightbox_js_clone' );

}

/**
 * Loads all of the necessary assets for the lightbox.
 *
 * @since 1.0.0
 *
 * @param array $data Data for the slider.
 */
function soliloquy_lightbox_load( $data ) {

    // Register and enqueue styles.
    wp_register_style( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . '-style', plugins_url( 'css/lightbox.css', __FILE__ ), array(), SOLILOQUY_LIGHTBOX_PLUGIN_VERSION );
    wp_enqueue_style( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . '-style' );

    // Register and enqueue scripts.
    wp_register_script( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . '-script', plugins_url( 'js/min/lightbox-min.js', __FILE__ ), array( 'jquery' ), SOLILOQUY_LIGHTBOX_PLUGIN_VERSION, true );
    wp_enqueue_script( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . '-script' );

    // Load the lightbox theme.
    $instance = Soliloquy_Shortcode::get_instance();
    foreach ( (array) soliloquy_lightbox_themes() as $array => $data ) {
        if ( $instance->get_config( 'lightbox_theme', $data ) !== $data['value'] || 'base' == $data['value'] ) {
            continue;
        }

        wp_enqueue_style( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . $theme . '-theme', plugins_url( 'themes/' . $theme . '/style.css', $data['file'] ), array( SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . '-style' ) );
        break;
    }

}

/**
 * Infuses a link to the image itself if the lightbox option is checked
 * but no image link is found.
 *
 * @since 1.0.0
 *
 * @param array $item  Array of slide data.
 * @param int $id      The current slider ID.
 * @param array $data  Array of slider data.
 * @param int $i       The current position in the slider.
 * @return array $item Amended array of slide data.
 */
function soliloquy_lightbox_infuse( $item, $id, $data, $i ) {
	
    // If there is no lightbox, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'lightbox', $data ) ) {
        return $item;
    }

    // If the item has chosen not to enable the lightbox, pass over it.
    if ( isset( $item['lightbox_enable'] ) && ! $item['lightbox_enable'] ) {
        return $item;
    }

    // If no link is set and the user has not chosen to disable the lightbox, set it to the image itself.
    if ( empty( $item['link'] ) ) {
        if ( ! empty( $item['src'] ) ) {
            $item['link'] = esc_url( $item['src'] );
        } elseif ( isset( $item['thumb'] ) ) {
            $item['link'] = esc_url( $item['thumb'] );
        }
    }
    
    // If no thumbnail has been set but thumbnails are active, make sure to generate the thumbnail.
    if ( $instance->get_config( 'lightbox_thumbs', $data ) && empty( $item['lb_thumb'] ) ) {
	    // Get item id - attachments using the dynamic shortcode won't populate $item['id']
	    $itemID = ( isset( $item['id'] ) ? $item['id'] : $id );
	    
        $item['lb_thumb'] = $instance->get_image_src( $itemID, $item, $data, 'lightbox' );
    }

    return apply_filters( 'soliloquy_lightbox_item_data', $item, $id, $data, $i );

}

/**
 * Adds the proper attributes to images so they can be opened in the lightbox.
 *
 * @since 1.0.0
 *
 * @param string $attr  String of link attributes.
 * @param int $id       The current slider ID.
 * @param array $item   Array of slide data.
 * @param array $data   Array of slider data.
 * @param int $i        The current position in the slider.
 * @return string $attr Amended string of link attributes.
 */
function soliloquy_lightbox_attr( $attr, $id, $item, $data, $i ) {

    // If there is no lightbox, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'lightbox', $data ) ) {
        return $attr;
    }

    // If the item has chosen not to enable the lightbox, pass over it.
    if ( isset( $item['lightbox_enable'] ) && ! $item['lightbox_enable'] ) {
        return $attr;
    }

    // Add in the rel attribute for the lightbox.
    $attr .= ' rel="soliloquybox' . sanitize_html_class( $data['id'] ) . '"';

    // If we have a title, add in the caption for the lightbox.
    if ( ! empty( $item['caption'] ) ) {
        $attr .= ' data-soliloquy-lightbox-caption="' . esc_html( do_shortcode( $item['caption'] ) ) . '"';
    }

    // If we have thumbnails, add in the thumbnail attribute as well.
    if ( $instance->get_config( 'lightbox_thumbs', $data ) ) {
        if ( ! empty( $item['lb_thumb'] ) ) {
            $attr .= ' data-thumbnail="' . esc_url( $item['lb_thumb'] ) . '"';
        }
    }

    // If a video, make the helper an iframe for third party videos, and html for local videos
    if ( isset( $item['type'] ) && 'video' == $item['type'] ) {
        $url   = $instance->get_video_data( $id, $item, $data, 'url' );
        $vid_type = $instance->get_video_data( $id, $item, $data, 'type' );
        switch ( $vid_type ) {
            case 'local':
                $attr .= ' data-soliloquybox-type="html" data-soliloquybox-href="' . esc_url( $url ) . '"';
                break;

            default:
                $attr .= ' data-soliloquybox-type="iframe" data-soliloquybox-href="' . esc_url( $url ) . '"';
                break;
        }
    }

    // If an HTML slide, make the helper inline.
    if ( isset( $item['type'] ) && 'html' == $item['type'] ) {
        $attr .= ' data-soliloquybox-type="inline"';
    }

    return apply_filters( 'soliloquy_lightbox_attr', $attr, $id, $item, $data, $i );

}

/**
 * Outputs the lightbox JS init code to initialize the lightbox.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 */
function soliloquy_lightbox_js( $data ) {

    // If there is no lightbox, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'lightbox', $data ) ) {
        return;
    }

    ob_start();
    ?>
    // Unload video triggers inside the slider if videos should be loaded in lightbox only, otherwise the opposite.
    <?php if ( $instance->get_config( 'lightbox_videos', $data ) ) : ?>
    $(document).off('click.soliloquyYouTube<?php echo $data['id']; ?> click.soliloquyVimeo<?php echo $data['id']; ?> click.soliloquyWistia<?php echo $data['id']; ?> click.soliloquyLocal<?php echo $data['id']; ?>');
    <?php endif; ?>

    if ( typeof soliloquy_lightbox === 'undefined' || false === soliloquy_lightbox ) {
        soliloquy_lightbox = {};
    }

    soliloquy_lightbox['<?php echo $data['id']; ?>'] = $('#soliloquy-<?php echo $data['id']; ?> a[rel="soliloquybox<?php echo $data['id']; ?>"]').soliloquybox({
        <?php do_action( 'soliloquy_lightbox_api_config_start', $data ); ?>
        <?php if ( ! $instance->get_config( 'lightbox_keyboard', $data ) ) : ?>
        keys: 0,
        <?php endif; ?>
        scrolling: 'no',
        arrows: <?php echo $instance->get_config( 'lightbox_arrows', $data ); ?>,
        aspectRatio: <?php echo $instance->get_config( 'lightbox_aspect', $data ); ?>,
        loop: <?php echo $instance->get_config( 'lightbox_loop', $data ); ?>,
        mouseWheel: <?php echo $instance->get_config( 'lightbox_mousewheel', $data ); ?>,
        preload: 1,
        nextEffect: '<?php echo $instance->get_config( 'lightbox_transition', $data ); ?>',
        prevEffect: '<?php echo $instance->get_config( 'lightbox_transition', $data ); ?>',
        tpl: {
            wrap     : '<div class="soliloquybox-wrap" tabIndex="-1"><div class="soliloquybox-skin soliloquybox-theme-<?php echo $instance->get_config( 'lightbox_theme', $data ); ?>"><div class="soliloquybox-outer"><div class="soliloquybox-inner"></div></div></div></div>',
            image    : '<img class="soliloquybox-image" src="{href}" alt="" />',
            iframe   : '<iframe id="soliloquybox-frame{rnd}" name="soliloquybox-frame{rnd}" class="soliloquybox-iframe" frameborder="0" vspace="0" hspace="0" allowtransparency="true"\></iframe>',
            error    : '<p class="soliloquybox-error"><?php echo __( 'The requested content cannot be loaded.<br/>Please try again later.</p>', 'soliloquy' ); ?>',
            closeBtn : '<a title="<?php echo __( 'Close', 'soliloquy' ); ?>" class="soliloquybox-item soliloquybox-close" href="javascript:;"></a>',
            next     : '<a title="<?php echo __( 'Next', 'soliloquy' ); ?>" class="soliloquybox-nav soliloquybox-next" href="javascript:;"><span></span></a>',
            prev     : '<a title="<?php echo __( 'Previous', 'soliloquy' ); ?>" class="soliloquybox-nav soliloquybox-prev" href="javascript:;"><span></span></a>'
        },
        helpers: {
            <?php do_action( 'soliloquy_lightbox_api_helper_config', $data ); ?>
            media: true,
            title: {
                <?php do_action( 'soliloquy_lightbox_api_title_config', $data ); ?>
                type: '<?php echo $instance->get_config( 'lightbox_title', $data ); ?>'
            },
            video: {
                autoplay: 1,
                playpause: 1,
                progress: 1,
                current: 1,
                duration: 1,
                volume: 1
            },
            <?php if ( $instance->get_config( 'lightbox_thumbs', $data ) ) : ?>
            thumbs: {
                width: <?php echo $instance->get_config( 'lightbox_twidth', $data ); ?>,
                height: <?php echo $instance->get_config( 'lightbox_theight', $data ); ?>,
                source: function(current) {
                    return $(current.element).data('thumbnail');
                },
                position: '<?php echo $instance->get_config( 'lightbox_tposition', $data ); ?>'
            }
            <?php endif; ?>
        },
        <?php do_action( 'soliloquy_lightbox_api_config_callback', $data ); ?>
        beforeLoad: function(){
            <?php if ( ! $instance->get_config( 'lightbox_videos', $data ) ) : ?>
            if ( $(this.element).hasClass('soliloquy-video-link') && ! $.soliloquybox.isActive ) {
                return false;
            }
            <?php endif; ?>

            this.title = $(this.element).data('soliloquy-lightbox-caption');

            <?php do_action( 'soliloquy_lightbox_api_before_load', $data ); ?>
        },
        afterLoad: function(){
            <?php do_action( 'soliloquy_lightbox_api_after_load', $data ); ?>
        },
        beforeShow: function(){
            $(window).on({
                'resize.soliloquybox' : function(){
                    $.soliloquybox.update();
                }
            });
            soliloquy_slider['<?php echo $data['id']; ?>'].stopAuto();
            <?php do_action( 'soliloquy_lightbox_api_before_show', $data ); ?>
        },
        afterShow: function(){
            $('.soliloquybox-inner').swipe( {
                swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
                    if (direction === 'left') {
                        $.soliloquybox.next(direction);
                    } else if (direction === 'right') {
                        $.soliloquybox.prev(direction);
                    }
                }
            } );

            <?php do_action( 'soliloquy_lightbox_api_after_show', $data ); ?>
        },
        beforeClose: function(){
            <?php do_action( 'soliloquy_lightbox_api_before_close', $data ); ?>
        },
        afterClose: function(){
            $(window).off('resize.soliloquybox');
            <?php do_action( 'soliloquy_lightbox_api_after_close', $data ); ?>
        },
        onUpdate: function(){
            <?php do_action( 'soliloquy_lightbox_api_on_update', $data ); ?>
        },
        onCancel: function(){
            <?php do_action( 'soliloquy_lightbox_api_on_cancel', $data ); ?>
        },
        onPlayStart: function(){
            <?php do_action( 'soliloquy_lightbox_api_on_play_start', $data ); ?>
        },
        onPlayEnd: function(){
            <?php do_action( 'soliloquy_lightbox_api_on_play_end', $data ); ?>
        }
        <?php do_action( 'soliloquy_lightbox_api_config_end', $data ); ?>
    });
    <?php
    echo ob_get_clean();

}

/**
 * Removes lightbox attributes from cloned slides.
 *
 * @since 1.0.0
 *
 * @param array $data Array of slider data.
 */
function soliloquy_lightbox_js_clone( $data ) {

    // If there is no lightbox, don't output anything.
    $instance = Soliloquy_Shortcode::get_instance();
    if ( ! $instance->get_config( 'lightbox', $data ) ) {
        return;
    }

    ob_start();
    ?>
    // Remove any rel attributes from cloned slides.
    $('#soliloquy-container-<?php echo $data['id']; ?>').find('.soliloquy-clone > a').removeAttr('rel');
    <?php
    echo ob_get_clean();

}

/**
 * Returns the available lightbox themes.
 *
 * @since 1.0.0
 *
 * @return array Array of lightbox theme data.
 */
function soliloquy_lightbox_themes() {

    $themes = array(
        array(
            'value' => 'base',
            'name'  => __( 'Base', 'soliloquy' )
        ),
        array(
            'value' => 'classic',
            'name'  => __( 'Classic', 'soliloquy' ),
            'file'  => __FILE__,
        ),
        array(
            'value' => 'karisma',
            'name'  => __( 'Karisma', 'soliloquy' ),
            'file'  => __FILE__,
        ),
        array(
            'value' => 'metro',
            'name'  => __( 'Metro', 'soliloquy' ),
            'file'  => __FILE__,
        ),

    );

    return apply_filters( 'soliloquy_lightbox_themes', $themes );

}

/**
 * Returns the available lightbox title positions.
 *
 * @since 1.0.0
 *
 * @return array Array of lightbox title data.
 */
function soliloquy_lightbox_titles() {

    $titles = array(
        array(
            'value' => 'float',
            'name'  => __( 'Float', 'soliloquy' )
        ),
        array(
            'value' => 'inside',
            'name'  => __( 'Inside', 'soliloquy' )
        ),
        array(
            'value' => 'outside',
            'name'  => __( 'Outside', 'soliloquy' )
        ),
        array(
            'value' => 'over',
            'name'  => __( 'Over', 'soliloquy' )
        )
    );

    return apply_filters( 'soliloquy_lightbox_titles', $titles );

}

/**
 * Returns the available lightbox transition effects.
 *
 * @since 1.0.0
 *
 * @return array Array of lightbox transition effects.
 */
function soliloquy_lightbox_transition_effects() {

    $transitions = array(
        array(
            'value' => 'fade',
            'name'  => __( 'Fade', 'soliloquy' )
        ),
        array(
            'value' => 'elastic',
            'name'  => __( 'Elastic', 'soliloquy' )
        ),
        array(
            'value' => 'none',
            'name'  => __( 'No Effect', 'soliloquy' )
        )
    );

    return apply_filters( 'soliloquy_lightbox_transition_effects', $transitions );

}

/**
 * Returns the available lightbox thumbnail positions.
 *
 * @since 1.0.0
 *
 * @return array Array of lightbox thumbnail data.
 */
function soliloquy_lightbox_thumbnail_positions() {

    $positions = array(
        array(
            'value' => 'bottom',
            'name'  => __( 'Bottom', 'soliloquy' )
        ),
        array(
            'value' => 'top',
            'name'  => __( 'Top', 'soliloquy' )
        )
    );

    return apply_filters( 'soliloquy_lightbox_thumbnail_positions', $positions );

}

/**
 * Adds a Lightbox Theme CSS file to the array of stylesheets to be loaded, if
 * the given slider data's config specifies a non-base theme
 *
 * @since 2.2.3
 *
 * @param array $stylesheets Stylesheets
 * @param array $data Slider Data
 * @return array Stylesheets
 */
function soliloquy_lightbox_theme( $stylesheets, $data ) {

    // Get instance
    $instance = Soliloquy_Shortcode::get_instance();

    // Get theme
    $theme = $instance->get_config( 'lightbox_theme', $data );

    // Check theme isn't base
    if ( 'base' == $theme ) {
        return $stylesheets;
    }

    // Add stylesheet to array for loading
    $stylesheets[] = array(
        'id'    => SOLILOQUY_LIGHTBOX_PLUGIN_SLUG . $theme . '-theme-style-css',
        'href'  => esc_url( add_query_arg( 'ver', SOLILOQUY_LIGHTBOX_PLUGIN_VERSION, plugins_url( 'themes/' . $theme . '/style.css', __FILE__ ) ) ),
    );

    // Return
    return $stylesheets;

}