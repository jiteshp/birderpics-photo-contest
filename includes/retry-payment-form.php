<?php
/**
 *	Retry payment form.
 *
 *	@since 1.0.0
 */
function bppc_retry_payment_form() {
	$cmb = new_cmb2_box( array(
		'id'			=> 'bppc-retry-payment-form',
		'object_types'	=> array( 'bppc_photo_entry' ),
		'hookup'		=> false,
		'save_fields'	=> false,
		'cmb_styles'	=> false,
	) );
	
	$cmb->add_field( array(
		'id'			=> 'submitted_form',
		'type'			=> 'hidden',
		'default'		=> 'bppc-retry-payment-form',
	) );
}

add_action( 'cmb2_init', 'bppc_retry_payment_form' );
 
/**
 *	Handle payment failure.
 *
 *	@since	1.0.0
 */
function bppc_handle_payment_failure( $content ) {
	global $post;
	$payment_failure_page = get_option( 'bppc_payment_failure_page', 0 );
	
	// Check if we are on the payment failure page?
	if( $post->ID != $payment_failure_page ) {
		return $content;
	}
	
	// Check if the request is coming from payumoney?
	if( empty( $_GET ) || ! isset( $_GET['pucb'] ) ) {
		return $content;
	}
	
	// Check if the post data is available?
	if( empty( $_POST ) || ! isset( $_POST['productinfo'] ) ) {
		return $content;
	}
	
	// Get the re-try payment form.
	$cmb = cmb2_get_metabox( 'bppc-retry-payment-form', 'fake-object-id' );
	
	foreach( $_POST as $key => $value ) {
		if( in_array( $key, array( 'txnid', 'amount', 'productinfo', 'firstname', 'email', 'phone' ) ) ) {
			$cmb->add_hidden_field( array(
				'field_args'	=> array(
					'id'		=> $key,
					'type'		=> 'hidden',
					'default'	=> $value,
				)
			) );
		}
	}
	
	$output = cmb2_get_metabox_form( $cmb, 'fake-object-id', array(
		'save_button'		=> __( 'Re-try Payment' ),
	) );
	
	return $content . '<div style="text-align: center; padding-bottom: 10em;">' . $output . '</div>';
}

add_filter( 'the_content', 'bppc_handle_payment_failure' );
 
/**
 *	Handle retry payment form submit.
 *
 *	@since	1.0.0
 */
function bppc_handle_retry_payment_form_submit() {
	$cmb = cmb2_get_metabox( 'bppc-retry-payment-form', 'fake-object-id' );
	
	// Return if nothing was posted.
	if( empty( $_POST ) || 
		! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return;
	}
	
	// Return if this form was not posted
	if( empty( $_POST['submitted_form'] ) || 'bppc-retry-payment-form' != $_POST['submitted_form'] ) {
		return;
	}
	
	// Check if the form security is ok.
	if( ! isset( $_POST[ $cmb->nonce() ] ) || 
		! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Security check failed. Please try again.' ) )
		);
	}
	
	// Send to the payment gateway.
	$payment_gateway = new PayUMoney_Payment_Gateway( 
		get_option( 'bppc_payu_merchant_key' ),
		get_option( 'bppc_payu_salt' ),
		get_permalink( get_option( 'bppc_payment_success_page' ) ),
		get_permalink( get_option( 'bppc_payment_failure_page' ) ),
		( 'on' == get_option( 'bppc_payu_mode' ) )
	);
	
	$payment_gateway->request_payment( $_POST );
}

add_action( 'cmb2_after_init', 'bppc_handle_retry_payment_form_submit' );