<?php
/**
 * Metabox class.
 *
 * @since 2.1.9
 *
 * @package Soliloquy_Dynamic
 * @author  Tim Carr
 */
class Soliloquy_Dynamic_Metaboxes {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;
    
    /**
     * Holds the Soliloquy Dynamic ID.
     *
     * @since 1.0.0
     *
     * @var int
     */
    public $dynamic_id;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Soliloquy_Dynamic::get_instance();
        
        // Get Dynamic ID
        $this->dynamic_id = get_option( 'soliloquy_dynamic' );

        // Actions and Filters
        add_filter( 'soliloquy_slider_types', array( $this, 'add_dynamic_type' ), 9999, 2 );
        add_action( 'soliloquy_display_dynamic', array( $this, 'images_display' ) );
        
    }

    /**
     * Changes the available Slider Type to Dynamic if the user is editing
     * the Soliloquy Dynamic Post
     *
     * @since 1.0.0
     *
     * @param array $types Slider Types
     * @param WP_Post $post WordPress Post
     * @return array Slider Types
     */
    public function add_dynamic_type( $types, $post ) {
        
        // Check Post = Dynamic
        switch ( get_post_type( $post ) ) {
            case 'soliloquy':
                if ( $post->ID != $this->dynamic_id) {
                    return $types;
                }
                break;
            default:
                // Not a Soliloquy CPT
                return $types;
                break;
        }
        
        // Change Types = Dynamic only
        $types = array(
            'dynamic' => __( 'Dynamic', 'soliloquy-dynamic' ),
        );
        
        return $types;
        
    }
    
    /**
     * Display output for the Images Tab
     *
     * @since 1.0.0
     * @param WP_Post $post WordPress Post
     */
    public function images_display( $post ) {
        
        ?>
        <div id="soliloquy-dynamic">
            <p class="soliloquy-intro"><?php printf( __( 'This slider and its settings will be used as defaults for any dynamic slider you create on this site. Any of these settings can be overwritten on an individual slider basis via template tag arguments or shortcode parameters. <a href="%s" title="Click here for Dynamic Addon documentation." target="_blank">Click here for Dynamic Addon documentation.</a>', 'soliloquy-dynamic' ), 'http://soliloquywp.com/docs/dynamic-addon/' ); ?></p>
        </div>
        <?php
            
    }
    
    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Soliloquy_Dynamic_Metaboxes object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Dynamic_Metaboxes ) ) {
            self::$instance = new Soliloquy_Dynamic_Metaboxes();
        }

        return self::$instance;

    }

}

// Load the metaboxes class.
$soliloquy_dynamic_metaboxes = Soliloquy_Dynamic_Metaboxes::get_instance();