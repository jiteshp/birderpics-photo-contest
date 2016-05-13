<?php
/**
 *	Register the CMB2 meta box.
 *
 *	@since	1.0.0
 */
function bppc_register_photo_submission_form() {
	$cmb = new_cmb2_box( array(
		'id'			=> 'bppc-photo-submission-form',
		'object_types'	=> array( 'bppc_photo_entry' ),
		'hookup'		=> false,
		'save_fields'	=> false,
		'cmb_styles'	=> false,
	) );
	
	$cmb->add_field( array(
		'id'			=> 'submitted_form',
		'type'			=> 'hidden',
		'default'		=> 'bppc-photo-submission-form'
	) );
	
	$cmb->add_field( array(
		'name'			=> __( 'Photo (required, maximum 2mb)' ),
		'id'			=> 'submitted_photo',
		'type'			=> 'text',
		'attributes'	=> array(
			'type'		=> 'file',
			'accept'	=> '.jpg',
		),
	) );
	
	$cmb->add_field( array(
		'name'			=> __( 'Photo title (required)' ),
		'id'			=> 'submitted_photo_title',
		'type'			=> 'text',
	) );
	
	$cmb->add_field( array(
		'name'			=> __( 'Photo description (required)' ),
		'id'			=> 'submitted_photo_description',
		'type'			=> 'textarea',
	) );
	
	$cmb->add_field( array(
		'name'			=> __( 'Your phone number (required)' ),
		'id'			=> 'submitted_phone_number',
		'type'			=> 'text',
	) );
	
	$cmb->add_field( array(
		'description'	=> __( 'I agree to the <a target="_blank" href="http://birderpics.com/photo-contest-rules">terms &amp; conditions</a>.' ),
		'id'			=> 'submitted_agreement',
		'type'			=> 'checkbox',
	) );
}

add_action( 'cmb2_init', 'bppc_register_photo_submission_form' );

/**
 *	Append form to the photo submission page content.
 *
 *	@since	1.0.0
 */
function bppc_photo_submission_page_content( $content ) {
	global $post;
	$cmb = cmb2_get_metabox( 'bppc-photo-submission-form', 'fake-object-id' );
	$photo_submission_page = get_option( 'bppc_photo_submission_page', 0 );
	$entry_fee = get_option( 'bppc_entry_fee', 0 );
	$output = '';
	
	// Return if the current page is not the photo submission page.
	if( ! $photo_submission_page || ( $photo_submission_page != $post->ID )  ) {
		return $content;
	}
	
	// Redirect to login page if the user is not logged in for some reason.
	if( ! is_user_logged_in() ) {
		bppc_login_before_photo_submission();
	}
	
	// Check for form errors & display them.
	if( ( $error = $cmb->prop( 'submission_error' ) ) && is_wp_error( $error ) ) {
		$output .= '<p class="error">' . $error->get_error_message() . '</p>';
		
		// Repopulate the form fields.
		if( ! empty( $_POST['submitted_photo_title'] ) ) {
			$cmb->get_field( 'submitted_photo_title' )->args['default'] = 
				trim( $_POST['submitted_photo_title'] );
		}
		
		if( ! empty( $_POST['submitted_photo_description'] ) ) {
			$cmb->get_field( 'submitted_photo_description' )->args['default'] = 
				trim( $_POST['submitted_photo_description'] );
		}
		
		if( ! empty( $_POST['submitted_agreement'] ) ) {
			$cmb->get_field( 'submitted_agreement' )->args['default'] = 
				$_POST['submitted_agreement'];
		}
	}
	
	$save_button_text = ( $entry_fee ) ? 
		__( 'Submit Photo &amp; Make Payment' ) : __( 'Submit Photo' );
	
	// Output the form html.
	$output .= cmb2_get_metabox_form( $cmb, 'fake-object-id', array(
		'save_button'	=> $save_button_text,
	) );
	
	return $content . '<div class="bppc-form">' . $output . '</div>';
}

add_filter( 'the_content', 'bppc_photo_submission_page_content' );

/**
 *	Handle form submission, check if user is logged in, check for errors, create
 *	media photo, inset photo entry and set the photo entry's post thumbnail.
 *
 *	@since	1.0.0
 */
function bppc_photo_submission_form_submit() {
	$cmb = cmb2_get_metabox( 'bppc-photo-submission-form', 'fake-object-id' );
	
	// Return if nothing was posted.
	if( empty( $_POST ) || 
		! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) {
		return;
	}
	
	// Return if this form was not posted
	if( empty( $_POST['submitted_form'] ) || 'bppc-photo-submission-form' != $_POST['submitted_form'] ) {
		return;
	}
	
	// Redirect to login page if the user is not logged in for some reason.
	if( ! is_user_logged_in() ) {
		bppc_login_before_photo_submission();
	}
	
	// Check if the form security is ok.
	if( ! isset( $_POST[ $cmb->nonce() ] ) || 
		! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Security check failed. Please try again.' ) )
		);
	}
	
	// Validate the submitted photo.
	if( empty( $_FILES ) || 
		! isset( $_FILES['submitted_photo'] ) ||
		isset( $_FILES['submitted_photo']['error'] ) &&
		0 != $_FILES['submitted_photo']['error'] ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Photo is required.' ) )
		);
	}
	
	$submitted_photo = array_filter( $_FILES['submitted_photo'] );
	if( empty( $submitted_photo ) ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Photo is required.' ) )
		);
	}
	
	if( 'image/jpeg' != $submitted_photo['type'] ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Photo must be a .jpg file.' ) )
		);
	}
	
	if( 2097152 < $submitted_photo['size'] ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Photo can be of maximum 2 MB size.' ) )
		);
	}
	
	// Validate the submitted photo title.
	if( empty( $_POST['submitted_photo_title'] ) ||
		'' == trim( $_POST['submitted_photo_title'] ) ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Photo title is required.' ) )
		);	
	}
	
	// Validate the submitted photo description.
	if( empty( $_POST['submitted_photo_description'] ) ||
		'' == trim( $_POST['submitted_photo_description'] ) ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Photo description is required.' ) )
		);	
	}
	
	// Validate the submitted phone number.
	if( empty( $_POST['submitted_phone_number'] ) ||
		'' == trim( $_POST['submitted_phone_number'] ) ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'Phone number is required.' ) )
		);	
	}
	
	// Validate the agreement to terms & conditions.
	if( empty( $_POST['submitted_agreement'] ) ||
		'on' != $_POST['submitted_agreement'] ) {
		return $cmb->prop( 'submission_error', 
			new WP_Error( 'post_data_missing', __( 'You must agree to the terms &amp; conditions.' ) )
		);	
	}
	
	// Insert the post.
	$entry_fee = get_option( 'bppc_entry_fee', 0 );
	$sanitized_values = $cmb->get_sanitized_values( $_POST );
	$post_data = array();
	
	$post_data['post_title'] = $sanitized_values['submitted_photo_title'];
	unset( $sanitized_values['submitted_photo_title'] );
	
	$post_data['post_content'] = $sanitized_values['submitted_photo_description'];
	unset( $sanitized_values['submitted_photo_description'] );
	
	$post_data['post_type'] = 'bppc_photo_entry';
	$post_data['post_status'] = ( 0 < $entry_fee ) ? 'draft' : 'pending';
	$post_data['post_date'] = date( 'Y-m-d H:i:s', bppc_get_date_on_first_day_of_next_month() );
	$post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
	
	
	$new_post_id = wp_insert_post( $post_data, true );
	if( is_wp_error( $new_post_id ) ) {
		return $cmb->prop( 'submission_error', $new_post_id );
	}
	
	// Upload the photo.
	if ( ! function_exists( 'media_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
	}
	
	$img_id = media_handle_upload( 'submitted_photo', $new_post_id, $post_data );
	if( is_wp_error( $img_id ) ) {
		// Delete the uploaded post.
		wp_delete_post( $new_post_id, true );
		
		// Return error.
		return $cmb->prop( 'submission_error', $img_id );
	}
	
	// Set the photo entry's post thumbnail.
	set_post_thumbnail( $new_post_id, $img_id );
	
	// Set the photo's votes to zero.
	update_post_meta( $new_post_id, 'bppc_votes', 0 );
	
	// Redirect to the thank you page/payment page depending on the entry fee.
	if( 0 < $entry_fee ) {
		$payment_gateway = new PayUMoney_Payment_Gateway( 
			get_option( 'bppc_payu_merchant_key' ),
			get_option( 'bppc_payu_salt' ),
			get_permalink( get_option( 'bppc_payment_success_page' ) ),
			get_permalink( get_option( 'bppc_payment_failure_page' ) ),
			( 'on' == get_option( 'bppc_payu_mode' ) )
		);
		
		$user_firstname = get_the_author_meta( 'display_name', get_current_user_id() );
		$user_email = get_the_author_meta( 'user_email', get_current_user_id() );
		
		$response = $payment_gateway->request_payment( array(
			'txnid'		  => 'birderpics.com_' . $new_post_id,
			'amount'	  => $entry_fee,
			'firstname'	  => $user_firstname,
			'email'		  => $user_email,
			'phone'		  => $sanitized_values['submitted_phone_number'],
			'productinfo' => 'Photo Entry ' . $new_post_id,
			'enforce_paymethod' => 'creditcard|debitcard|netbanking',
		) );

		$next_page = get_permalink( 
			get_option( 'bppc_photo_submission_thankyou_page' ) );
	}
	else {
		$next_page = get_permalink( 
			get_option( 'bppc_photo_submission_thankyou_page' ) );
	}
	
	wp_redirect( esc_url_raw( $next_page ) );
	
	exit;
}

add_action( 'cmb2_after_init', 'bppc_photo_submission_form_submit' );

/**
 *	Return the date on the 1 day of the next month.
 *
 *	@since	1.0.0
 */
function bppc_get_date_on_first_day_of_next_month() {
	$year  = date( 'Y' );
	$month = date( 'm', strtotime( '+1 month' ) );
	$day   = 1;
	
	if( 1 == $month ) {
		$year = intval( $year ) + 1;
	}
	
	return date( strtotime( $year . '-' . $month . '-' . $day ) );
}
 
/**
 *	Send the user to the login page with ref query parameter set to this photo
 *	submission page's permalink.
 *
 *	@since	1.0.0
 */
function bppc_login_before_photo_submission() {
	$login_url = add_query_arg( 'ref', get_permalink( get_option( 'bppc_photo_submission_page' ) ), 
		get_permalink( get_option( 'bppc_login_page' ) ) );
	
	wp_redirect( esc_url_raw( $login_url ) );
}
 
/**
 *	Handle payment success.
 *
 *	@since	1.0.0
 */
function bppc_payment_successful_page_content( $content ) {
	global $post;
	$payment_success_page = get_option( 'bppc_payment_success_page', 0 );
	
	bppc_log( 'Current page: ' . $post->ID );
	bppc_log( 'Success page: ' . $payment_success_page );
	
	// Check if this page is the payment success page.
	if( $post->ID == $payment_success_page ) {
		// Check if this is a POST from payumoney
		bppc_log( $_POST );
		
		if( ! empty( $_POST ) && isset( $_POST['mihpayid'] ) ) {
			// Get the photo entry.
			$photo_entry_id = str_replace( 'birderpics.com_', '', $_POST['txnid'] );
			$photo_entry = get_post( $photo_entry_id );
			
			bppc_log( 'Photo Entry Status: ' . $photo_entry->post_status );
			bppc_log( $photo_entry );
			
			// Check if the photo entry status is draft?
			if( 'draft' == $photo_entry->post_status ) {
				$post_data = array(
					'ID'		  => $photo_entry->ID,
					'post_status' => 'pending',
				);
				
				wp_update_post( $post_data );
				update_post_meta( $photo_entry->ID, 'bppc_payment_id', $_POST['mihpayid'] );
				update_post_meta( $photo_entry->ID, 'bppc_payment_date', $_POST['addedon'] );
				update_post_meta( $photo_entry->ID, 'bppc_payment_amount', $_POST['amount'] );
				
				bppc_log( get_post_meta( $photo_entry->ID ) );
				
				$content .=
					'<table>' .
						'<tr>' .
							'<td>' . __( 'Transaction id: ', 'bppc' ) . '</td>' .
							'<td>' . $_POST['mihpayid'] . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td>' . __( 'Transaction date: ', 'bppc' ) . '</td>' .
							'<td>' . date( 'F j, Y h:i a', strtotime( $_POST['addedon'] ) ) . '</td>' .
						'</tr>' .
						'<tr>' .
							'<td>' . __( 'Transaction amount: ', 'bppc' ) . '</td>' .
							'<td>' . $_POST['amount'] . '</td>' .
						'</tr>' .
					'</table>';
					
				return $content;
			}
			
			wp_redirect( home_url( '/' ) );
			exit;
		}
	}
	
	return $content;
}

add_filter( 'the_content', 'bppc_payment_successful_page_content' );