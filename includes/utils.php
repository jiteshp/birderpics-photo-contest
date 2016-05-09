<?php
/**
 *	Cleanup the admin bar.
 *
 *	@since	1.0.0
 */
function bppc_cleanup_admin_bar() {
	if( is_admin() ) {
		return;
	}
	
	global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('about');
    $wp_admin_bar->remove_menu('wporg');
    $wp_admin_bar->remove_menu('documentation');
    $wp_admin_bar->remove_menu('support-forums');
    $wp_admin_bar->remove_menu('feedback');
    $wp_admin_bar->remove_menu('site-name');
    $wp_admin_bar->remove_menu('view-site');
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('w3tc');
    $wp_admin_bar->remove_menu('edit-profile');  
    $wp_admin_bar->remove_menu('search');  
}

add_action( 'wp_before_admin_bar_render', 'bppc_cleanup_admin_bar' );

/**
 *	Redirect to home page after logout
 *
 *	@since	1.0.0
 */
function bppc_logout() {
	wp_redirect( home_url( '/' ) );
	exit();
}

add_action( 'wp_logout', 'bppc_logout' );

/**
 *	Custom archive title
 *
 *	@since	1.0.0
 */
function bppc_archive_title() {
	return __( 'Vote for your favorite birder photo' );
}

add_filter( 'get_the_archive_title', 'bppc_archive_title' );

/**
 *	Logging utility function.
 *
 *	@since	1.0.0
 */
function bppc_log( $message ) {
	if( WP_DEBUG === true ){
		if( is_array( $message ) || is_object( $message ) ){
			error_log( print_r( $message, true ) );
		} 
		else {
			error_log( $message );
		}
    }
}