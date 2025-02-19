<?php
/*
 Plugin Name: Simple Google Calendar Outlook Events Widget
 Description: Widget that displays events from a public google calendar or iCal file
 Plugin URI: https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 Author: Bram Waasdorp
 Version: 2.6.0
 License: GPLv2
 Tested up to: 6.7
 Requires at least: 5.3
 Requires PHP:  7.4
 Text Domain:  simple-google-icalendar-widget
 *   bw 20201122 v1.2.0 find solution for DTSTART and DTEND without time by explicit using isDate and only displaying times when isDate === false.;
 *               found that date_i18n($format, $timestamp) formats according to the locale, but not the timezone but the newer function wp_date() does,
 *               so date_i18n() replaced bij wp_date()
 *   bw 20201123 V1.2.2 added a checkbox to clear cache before expiration.
 *   bw 20210408 V1.3.0 made time formats configurable.
 *   bw 20210421 v1.3.1 test for http changed in test with esc_url_raw() to accomodate webcal protocol e.g for iCloud
 *   bw 20210616 V1.4.0 added parameter excerptlength to limit the length in characters of the description
 *   bw 20220223 fixed timezone error in response to a support topic of edwindekuiper (@edwindekuiper): If timezone appointment is empty or incorrect
 *               timezone fall back was to new \DateTimeZone(get_option('timezone_string')) but with UTC+... UTC-... timezonesetting this string
 *               is empty so I use now wp_timezone() and if even that fails fall back to new \DateTimeZone('UTC').
 *   bw 20220404 V1.5.0 in response to a support topic on github of fhennies added parameter allowhtml (htmlspecialchars) to allow Html
 *               in Description, Summary and Location added wp_kses('post') to output to keep preventing XSS
 *   bw 20220407 Extra options for parser in array poptions and added temporary new option notprocessdst to don't process differences in DST between start of series events and the current event.
 *      20220410 V1.5.1 As notprocessdst is always better within one timezone removed the correction and this option.
 *               If this causes other problems when using more timezones then find specific solution.
 *   bw 20220421 V1.6.0 First steps to convert widget to block
 *      20220430 Block in own class  SimpleicalBlock called when function_exists( 'register_block_type') else old widget (later maybe always also old widget)
 *   bw 20220503 Replaced ( function_exists( 'register_block_type' ) ) by ( is_wp_version_compatible( '5.9' ) ) because we use the newest version of blocks and removed else for the old widget, so that
 *              the legacy block with the old widget still keeps working
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
 */
/*
 Simple Google Calendar Outlook Events Widget
 Copyright (C) Bram Waasdorp 2017 - 2025
 2025-01-13
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
\add_action('wp_enqueue_scripts', __NAMESPACE__ .'\enqueue_view_script');
\add_action('wp_enqueue_scripts', __NAMESPACE__ .'\enqueue_bs_scripts', 999);
//todo facultatief maken
// \do_action('sib_enqueue_bs_scripts');

/**
 * enqueue scripts for use in client REST view
 * for v 6.3 up args array strategy = defer, else in_footer = that array is casted to boolean true. 
 */
function enqueue_view_script()
{
    wp_enqueue_script('simplegoogleicalenderwidget-simple-ical-block-view-script', plugins_url('/js/simple-ical-block-view.js', __FILE__), [], '2.6.0-' . filemtime(plugin_dir_path(__FILE__) . 'js/simple-ical-block-view.js'), 
        ['strategy' => 'defer' ]);
    wp_add_inline_script('simplegoogleicalenderwidget-simple-ical-block-view-script', '(window.simpleIcalBlock=window.simpleIcalBlock || {}).restRoot = "' . get_rest_url() . '"', 'before');
}
/**
 * enqueue bootstrap scripts and css for collapse
 * for v 6.3 up args array strategy = defer, else in_footer = that array is casted to boolean true.
 */
function enqueue_bs_scripts()
{
    wp_register_script('simplegoogleicalenderwidget-util-index-script', plugins_url('/vendor/bs/js/util/index.js', __FILE__),
        [],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/util/index.js'),
        ['strategy' => 'defer' ]);
    wp_register_script('simplegoogleicalenderwidget-manipulator-script', plugins_url('/vendor/bs/js/dom/manipulator.js', __FILE__),
        [],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/dom/manipulator.js'),
        ['strategy' => 'defer' ]);
    wp_register_script('simplegoogleicalenderwidget-dom-data-script', plugins_url('/vendor/bs/js/dom/data.js', __FILE__),
        [],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/dom/data.js'),
        ['strategy' => 'defer' ]);
    wp_register_script('simplegoogleicalenderwidget-util-config-script', plugins_url('/vendor/bs/js/util/config.js', __FILE__),
        ['simplegoogleicalenderwidget-manipulator-script','simplegoogleicalenderwidget-util-index-script'],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/util/config.js'),
        ['strategy' => 'defer' ]);
    wp_register_script('simplegoogleicalenderwidget-selector-engine-script', plugins_url('/vendor/bs/js/dom/selector-engine.js', __FILE__),
        ['simplegoogleicalenderwidget-util-index-script'],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/dom/selector-engine.js'),
        ['strategy' => 'defer' ]);
    wp_register_script('simplegoogleicalenderwidget-dom-event-handler-script', plugins_url('/vendor/bs/js/dom/event-handler.js', __FILE__),
        ['simplegoogleicalenderwidget-util-index-script'],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/dom/event-handler.js'),
        ['strategy' => 'defer' ]);
    wp_register_script('simplegoogleicalenderwidget-base-component-script', plugins_url('/vendor/bs/js/base-component.js', __FILE__),
        ['simplegoogleicalenderwidget-dom-data-script', 'simplegoogleicalenderwidget-dom-event-handler-script', 'simplegoogleicalenderwidget-util-config-script','simplegoogleicalenderwidget-util-index-script'],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/base-component.js'),
        ['strategy' => 'defer' ]);
    wp_enqueue_script('simplegoogleicalenderwidget-collapse-script', plugins_url('/vendor/bs/js/collapse.js', __FILE__),
        ['simplegoogleicalenderwidget-base-component-script','simplegoogleicalenderwidget-dom-event-handler-script','simplegoogleicalenderwidget-selector-engine-script','simplegoogleicalenderwidget-util-index-script'],
        '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/js/collapse.js'),
        ['strategy' => 'defer' ]);
    wp_enqueue_style('simplegoogleicalenderwidget-collapse-style', plugins_url('/vendor/bs/css/collapse.css', __FILE__), [], '2.6.1-' . filemtime(plugin_dir_path(__FILE__) . 'vendor/bs/css/collapse.css'),
        'all');
}
    
    \add_action ('widgets_init', array (__NAMESPACE__ .'\SimpleicalHelper','simple_ical_widget')  );
    
$ical_admin = new SimpleicalWidgetAdmin;
add_action('admin_menu',array ($ical_admin, 'simple_ical_admin_menu'));
