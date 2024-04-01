<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
//if ( ! defined( 'ABSPATH' ) ) {

    die;
}

$option_name = 'simple_ical_block_attrs';

delete_option( $option_name );

// for site options in Multisite
delete_site_option( $option_name );