<?php
/**
 * The template for displaying when no job listings are found (in a loop).
 *
 * @package Listify
 */
?>

<li class="no_job_listings_found col-xs-12">
	<div class="content-box">
		<?php printf( __( 'Perhaps try revising your search or <a href="%s">create a listing</a> instead!', 'listify' ), job_manager_get_permalink( 'submit_job_form' ) ); ?>
	</div>
</li>
