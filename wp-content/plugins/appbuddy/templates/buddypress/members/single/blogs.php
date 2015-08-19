<?php

/**
 * BuddyPress - Users Blogs
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<div class="item-list-tabs" id="subnav" role="navigation">
	<ul>

		<?php bp_get_options_nav(); ?>

		<!--li id="blogs-order-select" class="last filter">

			<label for="blogs-all"><?php _e( 'Order By:', 'appbuddy' ); ?></label>
			<select id="blogs-all">
				<option value="active"><?php _e( 'Last Active', 'appbuddy' ); ?></option>
				<option value="newest"><?php _e( 'Newest', 'appbuddy' ); ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'appbuddy' ); ?></option>

				<?php do_action( 'bp_member_blog_order_options' ); ?>

			</select>
		</li-->
	</ul>
</div><!-- .item-list-tabs -->

<?php
switch ( bp_current_action() ) :

	// Home/My Blogs
	case 'my-sites' :
		do_action( 'bp_before_member_blogs_content' ); ?>

		<div class="blogs myblogs" role="main">

			<?php bp_get_template_part( 'blogs/blogs-loop' ) ?>

		</div><!-- .blogs.myblogs -->

		<?php do_action( 'bp_after_member_blogs_content' );
		break;

	// Any other
	default :
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
