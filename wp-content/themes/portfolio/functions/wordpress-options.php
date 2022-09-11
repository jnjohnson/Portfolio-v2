<?php

function wrap_embed_with_div($html, $url, $attr) {
	return '<div class="iframe-container">' . $html . '</div>';
}

add_filter('embed_oembed_html', 'wrap_embed_with_div', 10, 3);

function remove_comments(){
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'remove_comments' );

function wpse200296_before_admin_bar_render()
{
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('customize');
}
add_action( 'wp_before_admin_bar_render', 'wpse200296_before_admin_bar_render' );

function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

add_filter( 'img_caption_shortcode_width', '__return_false' );

function km_add_unfiltered_html_capability_to_editors( $caps, $cap, $user_id ) {
if ( 'unfiltered_html' === $cap) {
	$caps = array( 'unfiltered_html' );
}
return $caps;
}
add_filter( 'map_meta_cap', 'km_add_unfiltered_html_capability_to_editors', 1, 3 );

function prefix_remove_php_test( $tests ) {
	unset( $tests['direct']['php_version'] );
	return $tests;
}
add_filter( 'site_status_tests', 'prefix_remove_php_test' );


add_filter( 'tablepress_use_default_css', '__return_false' );


function fld_alter_table_output($output) {
	return '<div class="table-wrap">' . $output . '</div>';
}
add_filter('tablepress_table_output', 'fld_alter_table_output');