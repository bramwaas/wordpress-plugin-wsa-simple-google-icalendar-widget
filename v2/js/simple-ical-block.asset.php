<?php 
/*
 * version 3.1.0.2
 */
if ( ! defined( 'ABSPATH' ) ) exit;
return array('handle' => 'sib-helper', 'dependencies' => array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n', 'wp-server-side-render', 'sib-helper' ), 'version' => '3.2.0.2-' . filemtime( plugin_dir_path( __FILE__ ) . 'simple-ical-block.js' ));
