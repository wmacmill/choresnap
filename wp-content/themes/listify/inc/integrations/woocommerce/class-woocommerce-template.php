<?php

class Listify_WooCommerce_Template {

	public function __construct() {
		$this->css = new Listify_Customizer_CSS();
				
		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_filter( 'woocommerce_show_page_title', '__return_false' );
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 8 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_styles' ), 11 );

		add_filter( 'wp_nav_menu_items', array( $this, 'cart_icon' ), 0, 2 );

		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_show_product_loop_sale_flash' );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'before_shop_loop_item_title' ), 3 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'before_shop_loop_item_title_title' ), 10 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'after_shop_loop_item_title' ), 10 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'after_shop_loop_item_title' ), 20 );

		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item' ) );
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 11 );


		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

		add_filter( 'woocommerce_product_review_list_args', array( $this, 'product_review_list_args' ) );

		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'woocommerce_cross_sells_columns' ) );
		add_filter( 'woocommerce_cross_sells_total', array( $this, 'woocommerce_cross_sells_columns' ) );
		add_filter( 'loop_shop_columns', array( $this, 'loop_shop_columns' ) );

		add_filter( 'get_comment_text', array( $this, 'review_comment_text' ), 11, 3 );
	}

	public function body_class( $classes ) {
		if ( class_exists( 'WC_Social_Login' ) ) {
			$classes[] = 'woocommerce-social-login';
		}

		return $classes;
	}

	public function enqueue_styles($enqueue_styles) {
		unset( $enqueue_styles[ 'woocommerce-general' ] );

		return $enqueue_styles;
	}

	public function wp_enqueue_scripts() {
		add_action( 'listify_output_customizer_css', array( $this, 'primary' ) );
		add_action( 'listify_output_customizer_css', array( $this, 'accent' ) );

		if ( class_exists( 'WC_Social_Login' ) ) {
			global $wc_social_login;

			$wc_social_login->frontend->load_styles_scripts();
		}
	}

	public function wp_enqueue_styles() {
		wp_dequeue_style( 'woocommerce_chosen_styles' );
		wp_dequeue_style( 'select2' );
	}

	public function cart_icon( $items, $args ) {
		if ( 'primary' != $args->theme_location || ! listify_theme_mod( 'nav-cart' ) ) {
			return $items;
		}

		global $woocommerce;

		$before = '<li class="menu-item menu-type-link">';
		$after  = '</li>';

		$link = sprintf(
			'<a href="%s" class="current-cart"><span class="current-cart-count">%d</span> %s</a>',
			esc_url( $woocommerce->cart->get_cart_url() ),
			$woocommerce->cart->cart_contents_count,
			_n( 'Item', 'Items', $woocommerce->cart->cart_contents_count, 'listify' )
		);

		return $before . $link . $after . $items;
	}

	public function primary() {
		$this->css->add( array(
			'selectors' => array(
				'.woocommerce .quantity input[type="button"]'
			),
			'declarations' => array(
				'color' => listify_theme_mod( 'color-primary' ) 
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.woocommerce-message',
				'.job-manager-message'
			),
			'declarations' => array(
				'border-color' => listify_theme_mod( 'color-primary' ) 
			)
		) );
	}

	public function accent() {
		$this->css->add( array(
			'selectors' => array(
				'.type-product .onsale',
				'.type-product .price ins',
				'.job-package-tag'
			),
			'declarations' => array(
				'background-color' => listify_theme_mod( 'color-accent' )
			)
		) );

		$this->css->add( array(
			'selectors' => array(
				'.woocommerce-tabs .tabs .active a'
			),
			'declarations' => array(
				'color' => listify_theme_mod( 'color-accent' ) 
			)
		) );
	}

	public function before_shop_loop_item_title() {
		echo '<span class="product-overlay">';
	}

	public function after_shop_loop_item_title() {
		echo '</span>';
	}

	public function before_shop_loop_item_title_title() {
		echo '<span class="title-price">';
	}

	public function after_shop_loop_item() {
		echo '<a href="' . get_the_permalink() . '" class="product-image">';
		echo woocommerce_get_product_thumbnail( 'shop_catalog' );
		echo '</a>';
	}

	public function product_review_list_args( $args ) {
		$args[ 'callback' ] = 'listify_comment';

		return $args;
	}

	public function woocommerce_cross_sells_columns() {
		return 1;
	}

	public function loop_shop_columns( $columns ) {
		if ( ! is_active_sidebar( 'widget-area-sidebar-shop' ) ) {
			return 3;
		}

		return $columns;
	}

	public function review_comment_text( $content, $comment, $args ) {
		if ( 0 != $comment->comment_parent || ! is_singular( 'product' ) ) {
			return $content;
		}

		$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

		ob_start();
	?>
		<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="comment-rating">
			<span itemprop="ratingValue"><?php echo number_format( $rating, 1, '.', ',' ); ?></span>
		</div>
	<?php
		$average = ob_get_clean();

		return $average . $content;
	}

}

new Listify_WooCommerce_Template;
