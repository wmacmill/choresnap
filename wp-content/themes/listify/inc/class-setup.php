<?php

class Listify_Setup {

    public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		$menus = get_theme_mod( 'nav_menu_locations' );
		$this->theme = wp_get_theme();

		$has_listings = new WP_Query( array( 'post_type' => 'job_listing', 'fields' => 'ids', 'posts_per_page' => 1 ) );

		$this->steps = array(
			'install-plugins' => array(
				'title' => __( 'Install Required &amp; Recommended Plugins', 'listify' ),
				'completed' => class_exists( 'WP_Job_Manager' ) && class_exists( 'WooCommerce' ),
				'description' => 
					'<p>
						Before you can use Listify you must first install WP Job Manager and WooCommerce. 
						You can read about <a
						href="http://listify.astoundify.com/article/260-why-does-this-theme-require-plugins">why this theme
						requires plugins</a> in our documentation. 
					</p> 
					<p><strong>Note:</strong></strong>
					<ul>
						<li>When installing WP Job Manager and WooCommerce <strong>do not</strong> use the automatic setup and
						install the recommended pages. This will be done automatically when you import the demo XML.</li>
						<li>Only free plugins/add-ons can be installed automatically. You will need to install any premium
						plugins/add-ons separately.</li>
						<li>It is recommended you install and activate any additional plugins you plan on using before importing
						any XML content.</li>
						<li><strong>Once your plugins are installed</strong> and content is imported please review all plugin
						settings pages to make sure everything has been properly set up.</li>
					</ul>' . 
					sprintf( '<a href="%1$s/images/setup/setup-plugins.gif"><img
					src="%1$s/images/setup/setup-plugins.gif" width="430" alt=""
					/></a>', get_template_directory_uri() ) . 
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url( 'themes.php?page=tgmpa-install-plugins' ), __( 'Install Plugins', 'listify' ) ) . '</p>',
				'documentation' => array(
					'WP Job Manager' => 'http://listify.astoundify.com/article/228-wp-job-manager',
					'WooCommerce' => 'http://listify.astoundify.com/article/229-woocommerce',
					'Jetpack' => 'http://listify.astoundify.com/article/230-jetpack',
					'Bulk Install' => 'http://listify.astoundify.com/article/320-bulk-install-required-and-recommended-plugins'
				)
			),
			'import-content' => array(
				'title' => __( 'Import Demo Content', 'listify' ),
				'completed' => $has_listings->have_posts(),
				'description' => 
					'<p>' . __( 'Installing the demo content is not required to use this theme. It is simply meant to provide a way to get a feel for the theme without having to manually set up all of your own content. <strong>If you choose not to import the demo content you need to make sure you manually create all necessary page templates for your website.</strong>', 'listify' ). '</p>' .
					'<p>' . __( 'The Listify theme package includes multiple demo content .XML files. This is what you will
					upload to the WordPress importer. Depending on the plugins you have activated or the intended use of your
					website you may not need to upload all .XML files.', 'listify' ) . '</p>' . 
					sprintf( '<a href="%1$s/images/setup/setup-content.gif"><img src="%1$s/images/setup/setup-content.gif" width="430" alt=""

					/></a>', get_template_directory_uri() ) . 
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url( 'import.php' ), __( 'Begin Importing Content', 'listify' ) ) . '</p>',
				'documentation' => array(
					'Install Demo Content' => 'http://listify.astoundify.com/article/236-installing-demo-content',
					'Manually Add a Listing' => 'http://listify.astoundify.com/article/245-adding-a-listing',
					'Importing Content (Codex)' => 'http://codex.wordpress.org/Importing_Content',
					'WordPress Importer' => 'https://wordpress.org/plugins/wordpress-importer/'
				)
			),
			'import-widgets' => array(
				'title' => __( 'Import Widgets', 'listify' ),
				'completed' => is_active_sidebar( 'widget-area-home' ),
				'description' => 
					'<p>' . __( 'Importing the demo widgets is not required to use this theme. It simply allows you to quickly match the same settings found on our theme demo. If you do not import the widgets you can manually manage your widgets just like a standard WordPress widget area.', 'listify' ). '</p>' .
					sprintf( '<a href="%1$s/images/setup/setup-widgets.gif"><img src="%1$s/images/setup/setup-widgets.gif" width="430" alt="" /></a>', get_template_directory_uri() ) . 
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url(
					'tools.php?page=widget-importer-exporter' ), __( 'Begin Importing Widgets', 'listify' ) ) . '</p>',
				'documentation' => array(
					'Widget Areas' => 'http://listify.astoundify.com/category/352-widget-areas',
					'Widgets' => 'http://listify.astoundify.com/category/199-widgets' 
				)
			),
			'setup-menus' => array(
				'title' => __( 'Setup Menus', 'listify' ),
				'description' => 
					'<p>' . __( 'Make sure you create and assign your menus to the menu locations found in the theme. This is required to use the custom mega menu dropdown, account item, and more.', 'listify' ) . '</p>' .
					sprintf( '<a href="%1$s/images/setup/setup-menus.gif"><img src="%1$s/images/setup/setup-menus.gif" width="430" alt="" /></a>', get_template_directory_uri() ) . 
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url( 'nav-menus.php' ), __( 'Manage Menus', 'listify' ) ) . '</p>' ,
				'completed' => isset( $menus[ 'primary' ] ),
				'documentation' => array(
					'Primary Menu' => 'http://listify.astoundify.com/article/250-manage-the-primary-menu',
					'Secondary Menu' => 'http://listify.astoundify.com/article/253-manage-the-secondary-menu',
					'Tertiary Menu' => 'http://listify.astoundify.com/article/254-enable-the-tertiary-navigation',
					'Add a Dropdown' => 'http://listify.astoundify.com/article/252-add-a-dropdown-menu',
					'Add an Avatar' => 'http://listify.astoundify.com/article/251-add-the-avatar-menu-item',
					'Adding Icons' => 'http://listify.astoundify.com/article/257-adding-icons-to-menu-items',
					'Create a Popup' => 'http://listify.astoundify.com/article/255-creating-a-popup-menu-item',
					'Show/Hide Items' => 'http://listify.astoundify.com/article/256-show-different-menus-for-logged-in-or-logged-out',
				)
			),
			'setup-homepage' => array(
				'title' => __( 'Setup Static Homepage', 'listify' ),
				'description' => 
					'<p>' . __( 'In order to display custom widgets on your homepage you must first assign your static
					page in the WordPress settings. You can also set which page will display your blog posts. If you have
					imported the theme demo content you&#39;ll want to set the page called "Search Your City" as your homepage.', 'listify' ) . '</p>' .
					sprintf( '<a href="%1$s/images/setup/setup-reading.gif"><img src="%1$s/images/setup/setup-reading.gif" width="430" alt="" /></a>', get_template_directory_uri() ) . 
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url( 'options-reading.php' ), __( 'Reading Settings', 'listify' ) ) . '</p>', 
				'completed' => get_option( 'page_on_front' ),
				'documentation' => array(
					'Create Your Homepage' => 'http://listify.astoundify.com/article/261-creating-your-homepage',
					'Reading Settings (codex)' => 'http://codex.wordpress.org/Settings_Reading_Screen'
				)
			),
			'setup-widgets' => array(
				'title' => __( 'Setup Widgets', 'listify' ),
				'completed' => is_active_sidebar( 'widget-area-home' ),
				'description' => 
					'<p>' . __( 'Manage your widgets to control what displays on your homepage, listing pages, standard pages, and more.', 'listify' ). '</p>' .
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url( 'widgets.php' ), __( 'Manage Widgets', 'listify' ) ) . '</p>',
				'documentation' => array(
					'Widget Areas' => 'http://listify.astoundify.com/category/352-widget-areas',
					'Widgets' => 'http://listify.astoundify.com/category/199-widgets' 
				)
			),
			'customize-theme' => array(
				'title' => __( 'Customize', 'listify' ),
				'description' => 
					'<p>' . __( 'Manage the appearance and behavior of various theme components with the live customizer.', 'listify' ) . '</p>' .
					'<p>' . sprintf( '<a href="%s" class="button button-primary button-large">%s</a>', admin_url( 'customize.php' ), __( 'Customize', 'listify' ) ) . '</p>',
				'completed' => get_option( 'theme_mods_listify' ),
				'documentation' => array(
					'Appearance' => 'http://listify.astoundify.com/category/334-appearance',
					'Booking Services' => 'http://listify.astoundify.com/category/455-booking-service-integration',
					'Child Themes' => 'http://listify.astoundify.com/category/209-child-themes',
					'Translations' => 'http://listify.astoundify.com/category/210-translations'
				)
			),
			'support-us' => array(
				'title' => __( 'Get Involved', 'listify' ),
				'description' => __( 'Help improve Listify by submitting a rating and helping to translate the theme to as many languages as possible!', 'listify' ),
				'completed' => 'n/a',
				'documentation' => array(
					'Leave a Positive Review' => 'http://bit.ly/rate-listify',
					'Contribute Your Translation' => 'http://bit.ly/translate-listify'
				)
			)
		);

		add_action( 'admin_menu', array( $this, 'add_page' ), 100 );
		add_action( 'admin_menu', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_head', array( $this, 'admin_css' ) );
    }

    public function add_page() {
		add_submenu_page( 'themes.php', __( 'Listify Setup', 'listify' ), __( 'Setup Guide', 'listify' ), 'manage_options', 'listify-setup', array( $this, 'output' ) );
    }

    public function admin_css() {
		$screen = get_current_screen();

		if ( 'appearance_page_listify-setup' != $screen->id ) {
			return;
		}
		?>
		<style>
		.accordion-container {
			border: 1px solid #dfdfdf;
			border-width: 1px 1px 0;
		}

		.accordion-section-title:before {
			font-family: 'dashicons';
			display: inline-block;
			font-size: 18px;
			vertical-align: middle;
			margin-top: -1px;
		}

		.install-plugins .accordion-section-title:before {
			content: "\f106";
		}

		.import-content .accordion-section-title:before {
			content: "\f109";
		}

		.import-widgets .accordion-section-title:before {
			content: "\f116";
		}

		.setup-menus .accordion-section-title:before {
			content: "\f333";
		}

		.setup-homepage .accordion-section-title:before {
			content: "\f102";
		}

		.setup-widgets .accordion-section-title:before {
			content: "\f180";
		}

		.customize-theme .accordion-section-title:before {
			content: "\f108";
		}

		.support-us .accordion-section-title:before {
			content: "\f155";
		}

		.is-completed,
		.not-completed {
			color: green;
			font-size: 24px;
			margin: 0.5em 0 1em;
		}

		.is-completed:before,
		.not-completed:before {
			margin: -4px 10px 0 0;
			font-family: 'dashicons';
			content: "\f328";
			border: 3px solid green;
			width: 30px;
			height: 30px;
			border-radius: 50%;
			text-align: center;
			line-height: 30px;
			display: inline-block;
			vertical-align: middle;`
		}

		.not-completed {
			color: red;
		}

		.not-completed:before {
			content: "\f158";
			border-color: red;
		}

		.listify-badge {
			position: absolute;
			top: 0;
			right: 0;
			border-radius: 4px;
			box-shadow: 0 1px 3px rgba(0,0,0,.2);
			overflow: hidden;
		}
		
		.accordion-section-content ul {
			margin-bottom: 20px;
		}

		.accordion-section-content li {
			list-style: disc;
			list-style-position: inside;
			margin-left: 15px;
		}
		</style>
    <?php
    }

    public function add_meta_boxes() {
		foreach ( $this->steps as $step => $info ) {
			add_meta_box( $step , $info[ 'title' ], array( $this, 'step_box' ), 'listify_setup_steps', 'normal', 'high', $info );
		}
    }

    public function step_box( $object, $metabox ) {
	$args = $metabox[ 'args' ];
    ?>
	<?php if ( $args[ 'completed' ] == true  ) { ?>
	    <div class="is-completed"><?php _e( 'Completed!', 'listify' ); ?></div>
	<?php } elseif ( $args[ 'completed' ] == false && 'n/a' != $args[ 'completed' ] ) { ?>
	    <div class="not-completed"><?php _e( 'Incomplete', 'listify' ); ?></div>
	<?php } ?>

	<p><?php echo $args[ 'description' ]; ?></p>

	<?php if ( 'Get Involved' != $args[ 'title' ] ) : ?> 
	<hr />
	<p><?php _e( 'You can read more and watch helpful video tutorials below:', 'listify' ); ?></p>
	<?php endif; ?>

	<p>
	    <?php foreach ( $args[ 'documentation' ] as $title => $url ) { ?>
		<a href="<?php echo esc_url( $url ); ?>" class="button button-secondary"><?php echo esc_attr( $title ); ?></a>&nbsp;
	    <?php } ?>
	</p>
    <?php
    }

    public function output() {
    ?>
	<div class="wrap about-wrap listify-setup">
	    <?php $this->welcome(); ?>
	    <?php $this->links(); ?>
	</div>

	<div id="poststuff" class="wrap listify-steps" style="margin: 25px 40px 0 20px">
	    <?php $this->steps(); ?>
	</div>
    <?php  
    }

    public function welcome() {
    ?>
	<h1><?php printf( __( 'Welcome to Listify %s', 'listify' ), $this->theme->Version ); ?></h1>
	<p class="about-text"><?php printf( __( 'The last directory you will ever buy.
	Use the steps below to finish setting up your new website. If you have more questions
	please <a href="%s">review the documentation</a>.', 'listify' ),
	'http://listify.astoundify.com' ); ?></p>
	<div class="listify-badge"><img src="<?php echo get_template_directory_uri(); ?>/images/listify-banner-welcome.jpg" width="140" alt="" /></div>
    <?php
    }

    public function links() {
    ?>
	<p class="helpful-links">
	    <a href="http://listify.astoundify.com" class="button button-primary"><?php _e( 'Documentation', 'listify' ); ?></a>&nbsp;
	    <a href="http://support.astoundify.com" class="button button-secondary"><?php _e( 'Submit a Support Ticket', 'listify' ); ?></a>&nbsp;
	</p>
    <?php
    }

    public function steps() {
    ?>
	<?php do_accordion_sections('listify_setup_steps', 'normal', null ); ?>
    <?php
    }
}

$GLOBALS[ 'listify_setup' ] = new Listify_Setup;
