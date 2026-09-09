<?php 
/*
 * version 3.2.0.2
 */
if ( ! defined( 'ABSPATH' ) ) exit;
return array('handle' => 'simplegoogleicalenderwidget-block', 'dependencies' => array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n', 'wp-server-side-render', 'simplegoogleicalenderwidget-helper' ), 'version' => '3.2.0.2-' . filemtime( plugin_dir_path( __FILE__ ) . 'simple-ical-block.js' ));
