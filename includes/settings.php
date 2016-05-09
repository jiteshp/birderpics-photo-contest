<?php
/**
 *	Add the settings sub menu.
 *	
 *	@since 	1.0.0
 */
function bppc_register_settings_sub_menu() {
	add_submenu_page(
		'edit.php?post_type=bppc_photo_entry',
		__( 'Photo Contest Settings' ),
		__( 'Settings' ),
		'manage_options',
		'settings',
		'bppc_settings_page'
	);
}

add_action( 'admin_menu', 'bppc_register_settings_sub_menu' );

/**
 *	Display the settings form page.
 *	
 *	@since 	1.0.0
 */
function bppc_settings_page() {
	
	$all_pages = get_pages(); ?>
	
	<div class="wrap">
		<h2><?php _e( 'Photo Contest Settings' ); ?></h2>
		
		<form action="options.php" method="post">
			<?php @settings_fields( 'bppc_options' ); ?>
			<?php @do_settings_fields( 'bppc_options' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="bppc_login_page"><?php _e( 'Photo Login Page' ); ?></label>
					</th>
					
					<td>
						<select name="bppc_login_page" id="bppc_login_page">
							<option value=""><?php _e( '&mdash; Select One &mdash;' ); ?></option>
							<?php
								$login_page = get_option( 'bppc_login_page', 0 );
								
								foreach( $all_pages as $page ) {
									$selected = ( $login_page == $page->ID ) ? ' selected' : '';
									
									printf( 
										'<option value="%1$s"%2$s>%3$s</option>', 
										$page->ID, 
										$selected, 
										$page->post_title 
									);
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_photo_submission_page"><?php _e( 'Photo Submission Page' ); ?></label>
					</th>
					
					<td>
						<select name="bppc_photo_submission_page" id="bppc_photo_submission_page">
							<option value=""><?php _e( '&mdash; Select One &mdash;' ); ?></option>
							<?php
								$photo_submission_page = get_option( 'bppc_photo_submission_page', 0 );
								
								foreach( $all_pages as $page ) {
									$selected = ( $photo_submission_page == $page->ID ) ? ' selected' : '';
									
									printf( 
										'<option value="%1$s"%2$s>%3$s</option>', 
										$page->ID, 
										$selected, 
										$page->post_title 
									);
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_photo_submission_thankyou_page"><?php _e( 'Photo Submission Thank You Page' ); ?></label>
					</th>
					
					<td>
						<select name="bppc_photo_submission_thankyou_page" id="bppc_photo_submission_thankyou_page">
							<option value=""><?php _e( '&mdash; Select One &mdash;' ); ?></option>
							<?php
								$post_photo_submission_page = get_option( 'bppc_photo_submission_thankyou_page', 0 );
								
								foreach( $all_pages as $page ) {
									$selected = ( $post_photo_submission_page == $page->ID ) ? ' selected' : '';
									
									printf( 
										'<option value="%1$s"%2$s>%3$s</option>', 
										$page->ID, 
										$selected, 
										$page->post_title 
									);
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_photos_per_page"><?php _e( 'Number of Photos Per Page' ); ?></label>
					</th>
					
					<td>
						<input type="number" name="bppc_photos_per_page" id="bppc_photos_per_page" min="9" step="3" value="<?php echo get_option( 'bppc_photos_per_page', 24 ); ?>">
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_entry_fee"><?php _e( 'Entry Fee' ); ?></label>
					</th>
					
					<td>
						<input type="number" name="bppc_entry_fee" id="bppc_entry_fee" min="0" step="50" value="<?php echo get_option( 'bppc_entry_fee', 0 ); ?>">
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_payment_success_page"><?php _e( 'Payment Success Page' ); ?></label>
					</th>
					
					<td>
						<select name="bppc_payment_success_page" id="bppc_payment_success_page">
							<option value=""><?php _e( '&mdash; Select One &mdash;' ); ?></option>
							<?php
								$payment_success_page = get_option( 'bppc_payment_success_page', 0 );
								
								foreach( $all_pages as $page ) {
									$selected = ( $payment_success_page == $page->ID ) ? ' selected' : '';
									
									printf( 
										'<option value="%1$s"%2$s>%3$s</option>', 
										$page->ID, 
										$selected, 
										$page->post_title 
									);
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_payment_failure_page"><?php _e( 'Payment Failure Page' ); ?></label>
					</th>
					
					<td>
						<select name="bppc_payment_failure_page" id="bppc_payment_failure_page">
							<option value=""><?php _e( '&mdash; Select One &mdash;' ); ?></option>
							<?php
								$payment_failure_page = get_option( 'bppc_payment_failure_page', 0 );
								
								foreach( $all_pages as $page ) {
									$selected = ( $payment_failure_page == $page->ID ) ? ' selected' : '';
									
									printf( 
										'<option value="%1$s"%2$s>%3$s</option>', 
										$page->ID, 
										$selected, 
										$page->post_title 
									);
								}
							?>
						</select>
					</td>
				</tr>
												
				<tr>
					<th scope="row">
						<label for="bppc_payu_merchant_key"><?php _e( 'PayUMoney Merchant Key' ); ?></label>
					</th>
					
					<td>
						<input type="text" name="bppc_payu_merchant_key" id="bppc_payu_merchant_key"value="<?php echo get_option( 'bppc_payu_merchant_key', '' ); ?>">
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="bppc_payu_salt"><?php _e( 'PayUMoney Salt' ); ?></label>
					</th>
					
					<td>
						<input type="text" name="bppc_payu_salt" id="bppc_payu_salt"value="<?php echo get_option( 'bppc_payu_salt', '' ); ?>">
					</td>
				</tr>
			</table>
			
			<?php @submit_button(); ?>
		</form>
	</div> <?php
	
}

/**
 *	Register the settings options.
 *	
 *	@since	1.0.0
 */
function bppc_register_settings() {
	register_setting( 'bppc_options', 'bppc_login_page' );
	
	register_setting( 'bppc_options', 'bppc_photo_submission_page' );
	
	register_setting( 'bppc_options', 'bppc_photo_submission_thankyou_page' );
	
	register_setting( 'bppc_options', 'bppc_payment_success_page' );
	
	register_setting( 'bppc_options', 'bppc_payment_failure_page' );
	
	register_setting( 'bppc_options', 'bppc_entry_fee' );
	
	register_setting( 'bppc_options', 'bppc_photos_per_page' );
	
	register_setting( 'bppc_options', 'bppc_payu_merchant_key' );
	
	register_setting( 'bppc_options', 'bppc_payu_salt' );
}

add_action( 'admin_init', 'bppc_register_settings' );