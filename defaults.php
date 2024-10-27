<?php

// Exit if File Accessed Directly
if ( ! defined( 'ABSPATH' ) ) {
	die( "Cheating! You are not allowed to access this page directly." );
}

// Set Default Plugin Settings Values Here
function adredux_default_options(){

	$default = array (
		'version' => ADREDUX_VERSION,
		'ad_page' => array ( '0' => 'posts' ),
		'ad_words' => '0',
		'min_words' => '300',
		'min_paras' => '5',
		'exclude_categories' => array ( '0' => '0' ),
		'exclude_tags' => array ( '0' => '0' ),
		'exclude_postids' => '',
		'adredux_stylesheet' => array ( '0' => 'yes' ),
		'adredux_header_codes' => '',
		'adredux_body_codes' => '',
		'adredux_footer_codes' => '',
		'adredux_showads' => array ( '0' => '1' ),
		'adredux_pagelevel_adcode' => '',
		'adcode_one' => '',
		'adcode_location_one' => array ( '0' => 'before-content' ),
		'adcode_align_one' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_one' => '',
		'adcode_two' => '',
		'adcode_location_two' => array ( '0' => 'before-content' ),
		'adcode_align_two' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_two' => '',
		'adcode_three' => '',
		'adcode_location_three' => array ( '0' => 'before-content' ),
		'adcode_align_three' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_three' => '',
		'adcode_four' => '',
		'adcode_location_four' => array ( '0' => 'before-content' ),
		'adcode_align_four' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_four' => '',
		'adcode_five' => '',
		'adcode_location_five' => array ( '0' => 'before-content' ),
		'adcode_align_five' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_five' => '',
		'adcode_six' => '',
		'adcode_location_six' => array ( '0' => 'before-content' ),
		'adcode_align_six' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_six' => '',
		'adcode_seven' => '',
		'adcode_location_seven' => array ( '0' => 'before-content' ),
		'adcode_align_seven' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_seven' => '',
		'adcode_eight' => '',
		'adcode_location_eight' => array ( '0' => 'before-content' ),
		'adcode_align_eight' => array ( '0' => 'alignleft' ),
		'adcode_maxwidth_eight' => '',
		);
    
    

	if ( get_option( ADREDUX_SETTINGS ) !== false ) {
		// Option Exists, Nothing to Update
	} else {
	    // Option Not Added Yet. Add Defaults.
	    add_option( ADREDUX_SETTINGS, $default);
	}

}

add_action( 'adredux_activation', 'adredux_default_options' );