<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
//if ( ! defined( 'ABSPATH' ) ) {

    die;
}


delete_option( 'simple_ical_block_attrs' );

// for site options in Multisite
delete_site_option( 'simple_ical_block_attrs' );