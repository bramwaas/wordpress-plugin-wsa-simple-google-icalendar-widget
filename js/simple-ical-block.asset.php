<?php 
/*
 * version 3.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
return array('handle' => 'sib-block', 'dependencies' => array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n', 'sib-helper' ), 'version' => '3.2.0-' . filemtime( plugin_dir_path( __FILE__ ) . 'simple-ical-block.js' ));
