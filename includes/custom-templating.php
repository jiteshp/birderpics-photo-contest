<?php
/**
 *	Handle displaying the archive template. Check if the theme has the archive
 *	template. If not use the plugin's archive template.
 *
 *	@since		1.0.0
 */
function bppc_archive_template( $template ) {
	if( is_post_type_archive( 'bppc_photo_entry' ) ) {
		$exists_in_theme = locate_template( 'archive-photo-entry.php', false );
		
		if( '' == $exists_in_theme ) {
			$template = plugin_dir_path( dirname( __FILE__ ) ) . 
				'templates/archive-photo-entry.php';
		}
	}
	
	return $template;
}

add_filter( 'archive_template', 'bppc_archive_template' );

/**
 *	Modify the query arguments on archive page to load this month's photo 
 *	entries.
 *
 *	@since		1.0.0
 */
function bppc_archive_template_query( $query ) {
	if( ! is_admin() && 
		is_post_type_archive( 'bppc_photo_entry' ) && 
		$query->is_main_query() ) {
		
		// Get only photo entries for this month.
		$query->set( 'year', date( 'Y' ) );
		$query->set( 'monthnum', date( 'm' ) );
		
		// Order photo entries by vote count.
		$query->set( 'order', 'DESC' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'meta_query', array(
			'relation'	=> 'OR',
			array( 'key' => 'bppc_votes', 'compare' => 'EXISTS' ),
			array( 'key' => 'bppc_votes', 'compare' => 'NOT EXISTS' ),
		) );
		
		// Set number of posts to display per page.
		$query->set( 'posts_per_page', get_option( 'bppc_photos_per_page', 24 ) );
	}
	
	return $query;
}

add_filter( 'pre_get_posts', 'bppc_archive_template_query' );

/**
 *	Handle displaying the single template. Check if the theme has the single
 *	template. If not use the plugin's single template.
 *
 *	@since		1.0.0
 */
function bppc_single_template( $template ) {
	if( 'bppc_photo_entry' == get_post_type() ) {
		$exists_in_theme = locate_template( 'single-photo-entry.php', false );
		
		if( '' == $exists_in_theme ) {
			$template = plugin_dir_path( dirname( __FILE__ ) ) . 
				'templates/single-photo-entry.php';
		}
		
	}
	
	return $template;
}

add_filter( 'single_template', 'bppc_single_template' );