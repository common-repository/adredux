<?php
// Exit if File Accessed Directly
if ( ! defined( 'ABSPATH' ) ) {
	die( "Cheating! You are not allowed to access this page directly." );
}

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'adredux_settings';
 
// For Single
delete_option($option_name);
 
// For Multisite
delete_site_option($option_name);