<?php
/*
 Plugin Name: Simple Google Calendar Outlook Events Widget
 Description: Widget that displays events from a public google calendar or iCal file
 Plugin URI: https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 Author: Bram Waasdorp
 Version: 2.7.0
 License: GPLv2
 Tested up to: 6.8
 Requires at least: 5.3
 Requires PHP:  7.4
 Text Domain:  simple-google-icalendar-widget
 *   bw 20230403 v2.1.1 replaced almost all of the widget (display) function by a call to SimpleicalBlock::display_block($instance); to make the html roughly the same as that of the block.
 *               added layout setting in the settings form. removed strip-tags from date-time fields in settings form
 *   bw 20230409 v2.1.2 small adjustments befor_widget id (probably without effect)
 *   bw 20230418 v2.1.3 added optional placeholder HTML output when no upcoming events are avalable. Also added optional output after the events list (when upcoming events are available).
 *   bw 20230623 v2.1.4 solved a number of typos in dateformat_... in update function to save all options.
 *   bw 20230823 v2.1.5 added defaults from SimpleicalBlock block_attributes for all used keys in instance to prevent Undefined array key warnings/errors.
 *   bw 20240106 v2.2.0 Changed the text domain to simple-google-icalendar-widget to make translations work by following the WP standard
 *   bw 20240123 v2.2.1 after an isue of black88mx6 in support forum: don't display description line when excerpt-length = 0
 *   bw 20240125 v2.3.0 v2 dir for older versions eg block.json version 2 for WP6.3 - Extra save instance/attributes in option 'simple_ical_block_attrs', like in standaard
 *      wp-widget in array with sibid as index so that the attributes are available for REST call.
 *   bw 20240509 v2.4.1 added defaults to all used keys of $args to solve issue 'PHP warnings' of johansam on support forum. Undefined array key “classname” in .../simple-google-icalendar-widget.php on line 170
 *   bw 20240727 v2.4.4 simplified defaulting args and improved code around that for the widget output
 *   bw 20241028 v2.5.0 Add support for categories    Tested with 6.7-RC and 5.9.5.
 *   bw 20250112 v2.6.0 plugin check, Using simple classloader and PSR-4 name conventions. Moved  SimpleicalWidget class to separate file.
 *   bw 20250219 v2.6.1 use bootstrap collapse script if desired
 */
/*
 Simple Google Calendar Outlook Events Widget
 Copyright (C) Bram Waasdorp 2017 - 2025
 2025-03-31
 Forked from Simple Google Calendar Widget v 0.7 by Nico Boehr
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;

if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\Classloader')) {
    require_once( 'includes/Classloader.php' );
}
Classloader::register();

if ( is_wp_version_compatible( '6.3' ) )   { // block  v3
    // Static class method call with name of the class
    \add_action( 'init', array (__NAMESPACE__ .'\SimpleicalHelper', 'init_block') );
    
} // end wp-version > 6.3 block v3
else if ( is_wp_version_compatible( '5.9' ) )   { // block  v2
    \add_action( 'init', array (__NAMESPACE__ .'\SimpleicalHelper', 'init_block_v2') );
    
} // end wp-version > 5.9 block v2

\add_action('rest_api_init', array(
    __NAMESPACE__ .'\RestController',
    'init_and_register_routes'
));
$ical_admin = new SimpleicalWidgetAdmin;
$options = SimpleicalWidgetAdmin::get_plugin_options();
add_action('wp_enqueue_scripts', __NAMESPACE__ .'\enqueue_view_script');
if ($options['simpleical_add_collapse_code']){
    add_action('wp_enqueue_scripts', __NAMESPACE__ .'\enqueue_bs_scripts');
}
if ($options['simpleical_add_collapse_code_admin']){
    add_action( 'enqueue_block_assets', __NAMESPACE__ .'\enqueue_bs_block_assets' );}/**
    * Register our simple_ical_settings_init to the admin_init action hook.
    * Register our simple_ical_options_page and simple_ical_info_page to the admin_menu action hook.
    */
    add_action( 'admin_init', [$ical_admin, 'simple_ical_settings_init'] );
    add_action('admin_menu',[$ical_admin, 'simple_ical_options_page']);
    add_action('admin_menu',array ($ical_admin, 'simple_ical_info_page'));
    
    /**
     * enqueue scripts for use in client REST view
     * for v 6.3 up args array strategy = defer, else in_footer = that array is casted to boolean true.
     */
    function enqueue_view_script()
    {
        wp_enqueue_script('simplegoogleicalenderwidget-simple-ical-block-view-script', plugins_url('/js/simple-ical-block-view.js', __FILE__), [], '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'js/simple-ical-block-view.js'),
            ['strategy' => 'defer' ]);
        wp_add_inline_script('simplegoogleicalenderwidget-simple-ical-block-view-script', '(window.simpleIcalBlock=window.simpleIcalBlock || {}).restRoot = "' . get_rest_url() . '"', 'before');
    }
    /**
     * enqueue bootstrap scripts and css for collapse (in footer to load it only when 'add_collapse_code' is true)
     * for v 6.3 up args array strategy = defer, else in_footer = that array is casted to boolean true.
     */
    function enqueue_bs_scripts()
    {
        wp_enqueue_script('simplegoogleicalenderwidget-collapse-bundle-script', plugins_url('/vendor/bs/js/collapse.bundle.js', __FILE__),
            [],
            '5.3.3-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/collapse.bundle.js'),
            ['strategy' => 'defer' ]);
        wp_enqueue_style('simplegoogleicalenderwidget-collapse-style', plugins_url('/vendor/bs/css/collapse.css', __FILE__),
            [],
            '5.3.3-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/css/collapse.css'),
            'all');
    }
    /**
     * Enqueue block content assets but only in the Editor.
     */
    function enqueue_bs_block_assets() {
        if ( is_admin() ) {
            enqueue_bs_scripts();
        }
    }
    add_action ('widgets_init', array (__NAMESPACE__ .'\SimpleicalHelper','simple_ical_widget')  );
    