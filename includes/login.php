<?php
/**
 *	Add login with Facebook button to the login page.
 *
 *	@since	1.0.0
 */
function bppc_login_page_content( $content ) {
	global $post;
	$login_page = intval( get_option( 'bppc_login_page', 0 ) );
	
	// Return if the current page is not the login page.
	if( $login_page && $post->ID == $login_page ) {
		// Get the login url.
		$redirect_url = ( ! empty( $_GET['ref'] ) ) ? esc_url_raw( $_GET['ref'] ) : home_url( '/' );
		
		$login_url   = add_query_arg( array(
			'loginFacebook'	=> 1,
			'redirect'		=> $redirect_url,
		), wp_login_url() );
		
		// Output the button.
		$content .= '<p style="text-align:center;">' . '<a href="' . esc_url( $login_url ) . '" class="button-alt-big"><i class="fa fa-facebook-square"></i>&nbsp;&nbsp;Login With Facebook</a>' . '</p>';
	}
	
	return $content;
}

add_filter( 'the_content', 'bppc_login_page_content' );