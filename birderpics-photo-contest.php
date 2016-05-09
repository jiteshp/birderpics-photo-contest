<?php
/**
 *	Plugin Name:	BirderPics Photo Contest
 *	Plugin URI:		http://birderpics.com/
 *	Description:	Runs monthly photo contest on the BirderPics.com website.
 *	Version:		1.0.0
 *	Author:			Jitesh Patil
 *	Author URI:		http://jiteshpatil.com/
 *	License:		GPLv2+
 *	Domain Path:	/languages
 *	Text Domain:	birderpics-photo-contest
 *	GitHub Plugin URI: https://github.com/jiteshp/birderpics-photo-contest
 */
 
/**
 *	Abort if this file is called directly.
 */
if( ! defined( 'WPINC' ) ) {
	die;
}
 
/**
 *	Define constants.
 */
define( 'BPPC_PAYU_TEST_MODE', 1 );
 
/**
 *	Include the CMB2 code.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/cmb2/init.php';
 
/**
 *	Include the utility code.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/utils.php';
 
/**
 *	Include the code responsible for working with the photo entry.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/photo-entry.php';
 
/**
 *	Include the code responsible for creating a settings page.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';
 
/**
 *	Include the code responsible for payments.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/payumoney-payment.php';
 
/**
 *	Include the code responsible for loggin in with Facebook.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/login.php';
 
/**
 *	Include the code responsible for submitting the photo.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/photo-submission-form.php';
 
/**
 *	Include the code responsible for handling payment failure.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/retry-payment-form.php';
 
/**
 *	Include the code responsible for submitting a vote.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/voting-form.php';
 
/**
 *	Include the code responsible for custom templating.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/custom-templating.php';
 
/**
 *	Activate the plugin.
 *
 *	@since	1.0.0
 */
function bppc_install() {
	// Register the photo entry custom post type function.
	bppc_register_photo_entry();
}

register_activation_hook( __FILE__, 'bppc_install' );
 
/**
 *	Enqueue plugin stylesheets & scripts.
 *
 *	@since	1.0.0
 */
function bppc_scripts() {
	wp_enqueue_style( 'bppc-style', plugin_dir_url( __FILE__ ) . 'style.css' );
}

add_action( 'wp_enqueue_scripts', 'bppc_scripts' );