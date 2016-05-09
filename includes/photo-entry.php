<?php
/**
 *	Register the photo entry custom post type.
 *	
 *	@since	1.0.0
 */
function bppc_register_photo_entry() {
	register_post_type( 'bppc_photo_entry', array(
		'public'		=> true,
		'has_archive'	=> true,
		'heirarchical'	=> false,
		'menu_position'	=> 30,
		'menu_icon'		=> 'dashicons-format-gallery',
		'label'			=> __( 'Photo Entries' ),
		'supports'		=> array(
			'title', 'editor', 'author', 'thumbnail',
		),
		'rewrite'		=> array(
			'slug'		=> 'photo-contest',
		),
		'labels'		=> array(
			'name'			=> __( 'Photo Entries' ),
			'singular_name'	=> __( 'Photo Entry' ),
			'menu_name'		=> __( 'Photo Contest' ),
			'all_items'		=> __( 'All Photo Entries' ),
		)
	) );
	
	flush_rewrite_rules();
}

add_action( 'init', 'bppc_register_photo_entry' );

/**
 *	Add payumoney payment id, date & amount to photo entry list.
 *	
 *	@since	1.0.0
 */
function bppc_add_photo_entry_columns( $columns ) {
	return array_merge(
		$columns,
		array(
			'bppc_payment_id' => __( 'Payment ID' ),
			'bppc_payment_date' => __( 'Payment Date' ),
			'bppc_payment_amount' => __( 'Amount' ),
		)
	);
}

add_action( 'manage_bppc_photo_entry_posts_columns', 'bppc_add_photo_entry_columns' );

/**
 *	Add payumoney payment data to photo entry list.
 *	
 *	@since	1.0.0
 */
function bppc_photo_entry_column_data( $column, $post_id ) {
	switch( $column )  {
		case 'bppc_payment_id':
			$payment_id = trim( get_post_meta( $post_id , 'bppc_payment_id' , true ) );
			
			if( '' != $payment_id ) {
				echo $payment_id;
			}
			else {
				echo '&mdash;';
			}
			
			break;
			
		case 'bppc_payment_date':
			$payment_date = trim( get_post_meta( $post_id , 'bppc_payment_date' , true ) );
			
			if( '' != $payment_date ) {
				echo date( 'F j, Y h:i a', strtotime( $payment_date ) );
			}
			else {
				echo '&mdash;';
			}
			
			break;
					
		case 'bppc_payment_amount':
			$payment_amount = get_post_meta( $post_id , 'bppc_payment_amount' , true );
			
			if( '' != $payment_amount ) {
				echo $payment_amount;
			}
			else {
				echo '&mdash;';
			}
			
			break;
	}
	
}

add_action( 'manage_bppc_photo_entry_posts_custom_column', 'bppc_photo_entry_column_data', 10, 2 );