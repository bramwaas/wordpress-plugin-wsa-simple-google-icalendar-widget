<?php
/*
 * SimpleicalBlock.php
 *
 * @package    Simple Google iCalendar Block
 * @subpackage Block
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2022 - 2022, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Gutenberg Block functions
 * used in newer wp versions where Gutenbergblocks are available. (tested with function_exists( 'register_block_type' ))
 * Version: 1.6.0
 * 20220427 namespaced and renamed after classname.
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget;

class SimpleicalBlock {
    /**
     * Front end block init
     *
     * @see \WP_Widget::form()
     *
     * @param .
     */
    public function init() {
        register_block_type( dirname(__DIR__) );
//        wp_enqueue_style(
//            'simple-google-icalendar-block-css', plugin_dir_url( dirname(__FILE__) ) . 'simple-google-icalendar-block.min.css',
//            null, filemtime( plugin_dir_path( dirname(__FILE__) ) . 'simple-google-icalendar-block.min.css' )
            );
    }
    /**
     * Back end (admin) block init
     *
     * @see \WP_Widget::form()
     *
     * @param .
     */
    public function admin_init() {
            echo '<!-- admin_init -->';
            wp_register_script(
                'simple-google-icalendar-sidebar-script',
                plugins_url( 'js/simple-google-icalendar-widget.js', dirname(__FILE__) ),
                array(
                    'wp-plugins',
                    'wp-edit-post',
                    'wp-element',
                    'wp-components',
                    'wp-data'
                ),
                filemtime( plugin_dir_path( dirname(__FILE__) ) . 'js/simple-google-icalendar-widget.js' ),
                true,                
                );
            wp_register_style(
                'simple-google-icalendar-sidebar-style',
                plugins_url( 'css/simple-google-icalendar-sidebar.css', dirname(__FILE__) ),
                null,
                filemtime( plugin_dir_path( dirname(__FILE__) ) . 'css/simple-google-icalendar-sidebar.css' ),
                );
            register_post_meta( 'post', 'sidebar_plugin_meta_block_field', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ) );
            
            
            
            register_block_type( 'simple-google-icalendar-block', array(
                'editor_script' => 'simple-google-icalendar-sidebar-script',
                //              'editor_style'  => 'simple-google-icalendar-sidebar-css'
            ));
            // wp_add_inline_script(
            // 	'meow-faq-block-js',
            // 	'wp.i18n.setLocaleData( ' . json_encode( gutenberg_get_jed_locale_data( 'faq-block' ) ) . ', "faq-block" );',
            // 	'before'
            // );
            
            // Params
            //            wp_localize_script( 'meow-faq-block-js', 'meow_faq_block_params', array(
            //            'logo' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'img/meowapps.png'
            //    ) );
            load_plugin_textdomain('simple_ical', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }
    public function simple_google_icalendar_script_enqueue() {
        wp_enqueue_script( 'simple-google-icalendar-sidebar-script' );
        wp_enqueue_style( 'simple-google-icalendar-sidebar-style' );
    }
} // end class SimpleicalBlock