<?php
/**
 *	Register a CMB2 meta box for voting form.
 *
 *	@since 1.0.0
 */
function bppc_register_voting_form() {
	$cmb = new_cmb2_box( array(
		'id'			=> 'bppc-voting-form',
		'object_types'	=> array( 'bppc_photo_entry' ),
		'hookup'		=> false,
		'save_fields'	=> true,
		'cmb_styles'	=> false,
	) );
	
	$cmb->add_field( array(
		'id'			=> 'submitted_form',
		'type'			=> 'hidden',
		'default'		=> 'bppc-voting-form'
	) );
}

add_action( 'cmb2_init', 'bppc_register_voting_form' );

/**
 *	Prepend the voting form to the photo entry content.
 *
 *	@since 1.0.0
 */
function bppc_voting_form_shortcode( $atts ) {
	global $post;
	$output = '';
	
	if( 'bppc_photo_entry' == get_post_type( $post->ID ) ) {
		// Check if voting is open for this photo entry.
		if( date( 'm' ) == date( 'm', strtotime( $post->post_date ) ) &&
			date( 'Y' ) == date( 'Y', strtotime( $post->post_date ) ) ) {
			
			// Check is user has already voted for this entry.
			$current_user_id = get_current_user_id();
			$voters = get_post_meta( $post->ID, 'bppc_voters' );
			
			if( $current_user_id && in_array( $current_user_id, $voters ) ) {
				$output .= '<p class="highlight-block">' . 
					__( 'You have already voted for this photo entry. Thank you.' ) . 
				'</p>';
			}
			else {
				$cmb = cmb2_get_metabox( 'bppc-voting-form', 'fake-object-id' );
				
				$cmb->add_hidden_field( array(
					'field_args' => array(
						'id'		=> 'submitted_post_id',
						'type'		=> 'hidden',
						'default'		=> $post->ID,
					)
				) );
			
				$output .= cmb2_get_metabox_form( $cmb, 'fake-object-id', array(
					'save_button'	=> __( 'Vote For This Photo' ),
				) );
			}
		}
		else {
			$output .= '<p class="highlight-block">' . 
					__( 'Voting is closed for this photo entry.' ) . 
				'</p>';
		}
	}
	
	return $output;
}

add_shortcode( 'voting_form', 'bppc_voting_form_shortcode' );

/**
 *	Handle the vote submit logic.
 *
 *	@since 1.0.0
 */
function bppc_voting_form_submit() {
	$cmb = cmb2_get_metabox( 'bppc-voting-form', 'fake-object-id' );
	
	// Return if nothing was posted.
	if( empty( $_POST ) || 
		! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return;
	}
	
	// Return if this form was not posted
	if( empty( $_POST['submitted_form'] ) || 'bppc-voting-form' != $_POST['submitted_form'] ) {
		return;
	}
	
	// Redirect to login page if the user is not logged in for some reason.
	if( ! is_user_logged_in() ) {
		bppc_login_before_voting( $_POST['submitted_post_id'] );
	}
	
	// Get the vote count and voters list from the post meta.
	$voters 	= get_post_meta( $_POST['submitted_post_id'], 'bppc_voters' );
	$current_user_id = get_current_user_id();
	
	// Check if the user has already voted?
	if( ! $voters || ! in_array( $current_user_id, $voters ) ) {
		update_post_meta( $_POST['submitted_post_id'], 'bppc_voters', $current_user_id );
		
		if( ! $voters ) {
			update_post_meta( $_POST['submitted_post_id'], 'bppc_votes', 1 );
		}
		else {
			update_post_meta( $_POST['submitted_post_id'], 'bppc_votes', count( $voters ) + 1, count( $voters ) );
		}
	}
	
	// Redirect to the photo page to prevent re-submit of vote.
	wp_redirect( esc_url_raw( get_permalink( $_POST['submitted_post_id'] ) ) );
	
	exit;
}

add_action( 'cmb2_after_init', 'bppc_voting_form_submit' );

/**
 *	Log the user in before voting.
 *
 *	@since 1.0.0
 */
function bppc_login_before_voting( $post_id ) {
	$login_url = add_query_arg( 'ref', get_permalink( $post_id ),
		get_permalink( get_option( 'bppc_login_page' ) ) );
	
	wp_redirect( esc_url_raw( $login_url ) );
	
	exit;
}