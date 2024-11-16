<?php
/*
 Plugin Name: Simple Google Calendar Outlook Events Widget
 Description: Widget that displays events from a public google calendar or iCal file
 Plugin URI: https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 Author: Bram Waasdorp
 Version: 2.5.0.RC
 License: GPL3
 Tested up to: 6.7
 Requires at least: 5.3
 Requires PHP:  7.4 tested with 8
 Text Domain:  simple-google-icalendar-widget
 Domain Path:  /languages
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
 *   bw 20240509 v2.4.1 added defaults to all used keys of $args to solve issue 'PHP warnings' of johansam on support forum. Undefined array key �classname� in .../simple-google-icalendar-widget.php on line 170
 *   bw 20240727 v2.4.4 simplified defaulting args and improved code around that for the widget output
 *   bw 20241028 v2.5.0 Add support for categories    Tested with 6.7-RC and 5.9.5. 
 */
/*
 Simple Google Calendar Outlook Events Widget
 Copyright (C) Bram Waasdorp 2017 - 2024
 2024-08-31
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
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\IcsParser;
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock;
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalWidgetAdmin;



if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\IcsParser')) {
    require_once( 'includes/IcsParser.php' );
    class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\IcsParser', 'IcsParser');
}

if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock')) {
    require_once( 'includes/SimpleicalBlock.php' );
    class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock', 'SimpleicalBlock');
}
if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\RestController')) {
     require_once( 'includes/RestController.php' );
     class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\RestController', 'RestController');
}
if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalWidgetAdmin')) {
    require_once('includes/SimpleicalWidgetAdmin.php');
    class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalWidgetAdmin', 'SimpleicalWidgetAdmin');
}
if ( is_wp_version_compatible( '6.3' ) )   { // block  v3
    // Static class method call with name of the class
    add_action( 'init', array ('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock', 'init_block') );
    
} // end wp-version > 6.3 block v3
else if ( is_wp_version_compatible( '5.9' ) )   { // block  v2
    add_action( 'init', array ('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock', 'init_block_v2') );
    
} // end wp-version > 5.9 block v2

{ // old widget always
    add_action('rest_api_init', array(
        'WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\RestController',
        'init_and_register_routes'
    ));
    add_action( 'wp_enqueue_scripts', 'enqueue_view_script');
    /**
     * enqueue scripts for use in client REST view
     * for v 6.3 up args array strategy = defer, else in_footer = that array is casted to boolean true. 
     */
    function enqueue_view_script()
    {
        wp_enqueue_script('simplegoogleicalenderwidget-simple-ical-block-view-script', plugins_url('/js/simple-ical-block-view.js', __FILE__), [], '2.3.0-' . filemtime(plugin_dir_path(__FILE__) . 'js/simple-ical-block-view.js'), 
            ['strategy' => 'defer' ]);
        wp_add_inline_script('simplegoogleicalenderwidget-simple-ical-block-view-script', '(window.simpleIcalBlock=window.simpleIcalBlock || {}).restRoot = "' . get_rest_url() . '"', 'before');
    }
    
    
    if ( !class_exists( 'Simple_iCal_Widget' ) ) {
        class Simple_iCal_Widget extends WP_Widget
        {
           /*
             * contruct the old widget
             *
             */
            public function __construct()
            {
                // load our textdomain
                load_plugin_textdomain('simple-google-icalendar-widget', false, basename( dirname( __FILE__ ) ) . '/languages' );
                
                parent::__construct('simple_ical_widget', // Base ID
                    'Simple Google iCalendar Widget', // Name
                    array( // Args
                        'classname' => 'Simple_iCal_Widget',
                        'description' => __('Displays events from a public Google Calendar or other iCal source', 'simple-google-icalendar-widget'),
                        'show_instance_in_rest' => true, // allow migrating to block
                    )
                    );
            }

            /**
             * Front-end display of widget.
             *
             * @see WP_Widget::widget()
             *
             * @param array $args
             *            Widget arguments.
             * @param array $instance
             *            Saved values from database.
             */
            public function widget($args, $instance)
            {
                    $args = array_merge(['before_widget' => '',
                        'before_title' => '<h3 class="widget-title block-title">',
                        'after_title' => '<h3 class="widget-title block-title">',
                    'after_widget' => '',
                    'classname' => 'Simple_iCal_Widget' ],
                    $args);  
                $instance = array_merge(SimpleicalBlock::$default_block_attributes,
                    ['title' => __('Events', 'simple-google-icalendar-widget'),
                     'tzid_ui' => wp_timezone_string(),
                     'wptype' => 'widget'],
                    $instance  );
                if (empty($instance['anchorId'])) $instance['anchorId'] =  $instance['sibid'];

                if (! empty($instance['rest_utzui']) &&  is_numeric($instance['rest_utzui'])) {
                    $instance['wptype'] = 'rest_ph_w';
                }
                // lay-out block:
                $instance['clear_cache_now'] = false;
                
                echo sprintf($args['before_widget'],
                    ('w-' . $instance['anchorId'] ),
                    $args['classname']) ;
                echo '<span id="' . $instance['anchorId'] . '" data-sib-id="'
                        . $instance['sibid'] . '" data-sib-utzui="' . $instance['rest_utzui'] 
                        . (('rest_ph_w' == $instance['wptype']) ? '" data-sib-st="0-start" >' : '">');
                $args['after_widget'] = '</span>' . $args['after_widget'];
                
                if (! empty($instance['title'])) {
                        if (false === stripos(' data-sib-t="true" ', $args['before_title'])) {
                            $l = explode('>', $args['before_title'], 2);
                            $args['before_title'] = implode(' data-sib-t="true" >', $l);
                        }
                        $title = apply_filters('widget_title', $instance['title']);
                        echo $args['before_title'], $title, $args['after_title'];
                }
                if ('rest_ph_w' == $instance['wptype'] ) {
                    SimpleicalBlock::update_rest_attrs($instance );
                    echo '<p>';
                    _e('Processing', 'simple-google-icalendar-widget');
                    echo '</p>';
                } else {
                    SimpleicalBlock::display_block($instance);
                }
                   // end lay-out block
                echo $args['after_widget'];
            }
            /**
             * Sanitize widget form values as they are saved.
             *
             * @see WP_Widget::update()
             *
             * @param array $new_instance Values just sent to be saved.
             * @param array $old_instance Previously saved values from database.
             *
             * @return array Updated safe values to be saved.
             */
            public function update($new_instance, $old_instance)
            {
                $instance['title'] = strip_tags($new_instance['title']);
                
                $instance['calendar_id'] = htmlspecialchars($new_instance['calendar_id']);
                
                if(is_numeric($new_instance['cache_time']) && 1 < $new_instance['cache_time']) {
                    $instance['cache_time'] = $new_instance['cache_time'];
                } else {
                    $instance['cache_time'] = 60;
                }
                
                if(is_numeric($new_instance['event_period']) && 1 < $new_instance['event_period']) {
                    $instance['event_period'] = $new_instance['event_period'];
                } else {
                    $instance['event_period'] = 366;
                }
                
                if(is_numeric($new_instance['layout']) && $new_instance['layout'] > 0) {
                    $instance['layout'] = $new_instance['layout'];
                } else {
                    $instance['layout'] = 3;
                }
                $instance['categories_filter_op'] = ($new_instance['categories_filter_op'])??'';
                $instance['categories_filter'] = ($new_instance['categories_filter'])??'';
                $instance['categories_display'] = ($new_instance['categories_display'])??'';
                
                $instance['event_count'] = $new_instance['event_count'];
                if(is_numeric($new_instance['event_count']) && 0 < $new_instance['event_count']) {
                    $instance['event_count'] = $new_instance['event_count'];
                } else {
                    $instance['event_count'] = 5;
                }
                // using strip_tags because it can start with space or contain more classe seperated by spaces
                $instance['dateformat_lg'] = ($new_instance['dateformat_lg']);
                $instance['dateformat_lgend'] = ($new_instance['dateformat_lgend']);
                $instance['dateformat_tsum'] = ($new_instance['dateformat_tsum']);
                $instance['dateformat_tsend'] = ($new_instance['dateformat_tsend']);
                $instance['dateformat_tstart'] = ($new_instance['dateformat_tstart']);
                $instance['dateformat_tend'] = ($new_instance['dateformat_tend']);
                if(is_numeric($new_instance['excerptlength']) && 0 <= $new_instance['excerptlength']) {
                    $instance['excerptlength'] = intval($new_instance['excerptlength']);
                } else {
                    $instance['excerptlength'] = '';
                }
                if(!empty($new_instance['period_limits']) &&  is_numeric($new_instance['period_limits'])) {
                    $instance['period_limits'] = strip_tags($new_instance['period_limits']);
                }
                if(!empty($new_instance['rest_utzui']) &&  is_numeric($new_instance['rest_utzui'])) {
                    $instance['rest_utzui'] = strip_tags($new_instance['rest_utzui']);
                }
                $instance['tag_sum'] = strip_tags($new_instance['tag_sum']);
                $instance['suffix_lg_class'] = strip_tags($new_instance['suffix_lg_class']);
                $instance['suffix_lgi_class'] = strip_tags($new_instance['suffix_lgi_class']);
                $instance['suffix_lgia_class'] = strip_tags($new_instance['suffix_lgia_class']);
                $instance['after_events'] = ($new_instance['after_events']);
                $instance['no_events'] = ($new_instance['no_events']);
                $instance['allowhtml'] = !empty($new_instance['allowhtml']);
                if (!empty($new_instance['blockid']) && empty($new_instance['sibid'])) {
                    $new_instance['sibid'] = $new_instance['blockid'];
                }
                $instance['anchorId'] = strip_tags($new_instance['anchorId']);
                $instance['sibid'] = strip_tags($new_instance['sibid']);
                
                if (!empty($this->number && is_numeric($this->number))) {
                   $instance['postid'] = (string) $this->id;
                }
                if (!empty($old_instance['sibid'])) $instance['prev_sibid'] = $old_instance['sibid'];
                if (SimpleicalBlock::update_rest_attrs($instance )) $instance['prev_sibid'] = $instance['sibid'];
                
                return $instance;
            }
            /**
             * Back-end widget form.
             *
             * @see WP_Widget::form()
             *
             * @param array $instance Previously saved values from database.
             */
            public function form($instance)
            {
                
                $default = wp_parse_args( [
                    'wptype' => 'widget',
                    'title' => __('Events', 'simple-google-icalendar-widget'),
                ],
                    SimpleicalBlock::$default_block_attributes);
                
                if (empty($instance['sibid'])) {
                    if  (!empty($instance['blockid'])) {
                        $instance['sibid'] = $instance['blockid'];
                        unset($instance['blockid']);
                    }
                    else $instance['sibid'] = 'W' . bin2hex(random_bytes(7));
                }
                $instance = wp_parse_args((array) $instance, $default);
                $nwsibid = 'w' .  bin2hex(random_bytes(7));
                
                ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('calendar_id'); ?>"><?php _e('Calendar ID, or iCal URL:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('calendar_id'); ?>" name="<?php echo $this->get_field_name('calendar_id'); ?>" type="text" value="<?php echo esc_attr($instance['calendar_id']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('event_count'); ?>"><?php _e('Number of events displayed:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('event_count'); ?>" name="<?php echo $this->get_field_name('event_count'); ?>" type="text" value="<?php echo esc_attr($instance['event_count']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('event_period'); ?>"><?php _e('Number of days after today with events displayed:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('event_period'); ?>" name="<?php echo $this->get_field_name('event_period'); ?>" type="text" value="<?php echo esc_attr($instance['event_period']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Lay-out:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" >
            <option value="1"<?php echo (1==esc_attr($instance['layout']))?'selected':''; ?>><?php _e('Startdate higher level', 'simple-google-icalendar-widget'); ?></option>
  			<option value="2"<?php echo (2==esc_attr($instance['layout']))?'selected':''; ?>><?php _e('Start with summary', 'simple-google-icalendar-widget'); ?></option>
  			<option value="3"<?php echo (3==esc_attr($instance['layout']))?'selected':''; ?>><?php _e('Old style', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('dateformat_lg'); ?>"><?php _e('Date format first line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_lg'); ?>" name="<?php echo $this->get_field_name('dateformat_lg'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_lg']); ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('dateformat_lgend'); ?>"><?php _e('Enddate format first line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_lgend'); ?>" name="<?php echo $this->get_field_name('dateformat_lgend'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_lgend']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tsum'); ?>"><?php _e('Time format time summary line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tsum'); ?>" name="<?php echo $this->get_field_name('dateformat_tsum'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tsum']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tsend'); ?>"><?php _e('Time format end time summary line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tsend'); ?>" name="<?php echo $this->get_field_name('dateformat_tsend'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tsend']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tstart'); ?>"><?php _e('Time format start time:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tstart'); ?>" name="<?php echo $this->get_field_name('dateformat_tstart'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tstart']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tend'); ?>"><?php _e('Time format end time:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tend'); ?>" name="<?php echo $this->get_field_name('dateformat_tend'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tend']); ?>" />
        </p>
        <h4>Advanced</h4>
        <p>
          <label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php _e('Cache expiration time in minutes:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="text" value="<?php echo esc_attr($instance['cache_time']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('excerptlength'); ?>"><?php _e('Excerpt length, max length of description:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('excerptlength'); ?>" name="<?php echo $this->get_field_name('excerptlength'); ?>" type="text" value="<?php echo esc_attr($instance['excerptlength']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('period_limits'); ?>"><?php _e('Period limits:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('period_limits'); ?>" name="<?php echo $this->get_field_name('period_limits'); ?>" >
            <option value="1"<?php echo ('1'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php _e('Start Whole  day, End Whole  day', 'simple-google-icalendar-widget'); ?></option>
  			<option value="2"<?php echo ('2'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php _e('Start Time of day, End Whole  day', 'simple-google-icalendar-widget'); ?></option>
  			<option value="3"<?php echo ('3'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php _e('Start Time of day, End Time of day'); ?></option>
  			<option value="4"<?php echo ('4'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php _e('Start Whole  day, End Time of day', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('rest_utzui'); ?>"><?php _e('Use client timezone settings:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('rest_utzui'); ?>" name="<?php echo $this->get_field_name('rest_utzui'); ?>" >
  			<option value=""<?php echo (''==esc_attr($instance['rest_utzui']))?'selected':''; ?>><?php _e('Use WordPress timezone settings, no REST'); ?></option>
            <option value="1"<?php echo ('1'==esc_attr($instance['rest_utzui']))?'selected':''; ?>><?php _e('Use Client timezone settings, with REST', 'simple-google-icalendar-widget'); ?></option>
  		 </select>
  		</p> 	
        <p>
          <label for="<?php echo $this->get_field_id('categories_filter_op'); ?>"><?php _e('Categories Filter Operator:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('categories_filter_op'); ?>" name="<?php echo $this->get_field_name('categories_filter_op'); ?>" >
  			<option value=""<?php echo (''==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php _e('No filter'); ?></option>
            <option value="ANY"<?php echo ('ANY'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php _e('ANY, one or more match', 'simple-google-icalendar-widget'); ?></option>
            <option value="ALL"<?php echo ('ALL'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php _e('ALL, all match', 'simple-google-icalendar-widget'); ?></option>
            <option value="NOTANY"<?php echo ('NOTANY'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php _e('NOT ANY, no match', 'simple-google-icalendar-widget'); ?></option>
            <option value="NOTALL"<?php echo ('NOTALL'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php _e('NOT ALL, not all match', 'simple-google-icalendar-widget'); ?></option>
  		 </select>
  		</p> 	
        <p>
          <label for="<?php echo $this->get_field_id('categories_filter'); ?>"><?php _e('Categories Filter List:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('categories_filter'); ?>" name="<?php echo $this->get_field_name('categories_filter'); ?>" type="text" value="<?php echo esc_attr($instance['categories_filter']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('categories_display'); ?>"><?php _e('Display categories with separator:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('categories_display'); ?>" name="<?php echo $this->get_field_name('categories_display'); ?>" type="text" value="<?php echo esc_attr($instance['categories_display']); ?>" />
		<label style="font-size:12px; color:#7f7f7f;"><?php _e('Empty no display. Else display categories above event with this separator.', 'simple-google-icalendar-widget'); ?></label>
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('tag_sum'); ?>"><?php _e('Tag for summary:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo $this->get_field_id('tag_sum'); ?>" name="<?php echo $this->get_field_name('tag_sum'); ?>" >
            <option value="a"<?php echo ('a'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('a (link)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="b"<?php echo ('b'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('b (attention, bold)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="div"<?php echo ('div'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('div', 'simple-google-icalendar-widget'); ?></option>
  			<option value="h4"<?php echo ('h4'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('h4 (sub header)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="h5"<?php echo ('h5'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('h5 (sub header)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="h6"<?php echo ('h6'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('h6 (sub header)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="i"<?php echo ('i'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('i (idiomatic, italic)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="span"<?php echo ('span'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('span', 'simple-google-icalendar-widget'); ?></option>
  			<option value="strong"<?php echo ('strong'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('strong', 'simple-google-icalendar-widget'); ?></option>
  			<option value="u"<?php echo ('u'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php _e('u (unarticulated, underline )', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('suffix_lg_class'); ?>"><?php _e('Suffix group class:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('suffix_lg_class'); ?>" name="<?php echo $this->get_field_name('suffix_lg_class'); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lg_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('suffix_lgi_class'); ?>"><?php _e('Suffix event start class:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('suffix_lgi_class'); ?>" name="<?php echo $this->get_field_name('suffix_lgi_class'); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lgi_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('suffix_lgia_class'); ?>"><?php _e('Suffix event details class:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('suffix_lgia_class'); ?>" name="<?php echo $this->get_field_name('suffix_lgia_class'); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lgia_class']); ?>" />
        </p>
        <p>
          <input class="checkbox" id="<?php echo $this->get_field_id('allowhtml'); ?>" name="<?php echo $this->get_field_name('allowhtml'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['allowhtml'] ); ?> />
          <label for="<?php echo $this->get_field_id('allowhtml'); ?>"><?php _e('Allow safe html in description and summary.', 'simple-google-icalendar-widget'); ?></label> 
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('after_events'); ?>"><?php _e('Closing HTML after available events:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('after_events'); ?>" name="<?php echo $this->get_field_name('after_events'); ?>" type="text" value="<?php echo esc_attr($instance['after_events']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('no_events'); ?>"><?php _e('Closing HTML when no events:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('no_events'); ?>" name="<?php echo $this->get_field_name('no_events'); ?>" type="text" value="<?php echo esc_attr($instance['no_events']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('anchorId'); ?>"><?php _e('HTML anchor:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('anchorId'); ?>" name="<?php echo $this->get_field_name('anchorId'); ?>" type="text" value="<?php echo esc_attr($instance['anchorId']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('sibid'); ?>"><?php _e('Sib ID:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('sibid'); ?>" name="<?php echo $this->get_field_name('sibid'); ?>" type="text" value="<?php echo esc_attr($instance['sibid']); ?>" readonly />
        </p>
         <p>
          <button class="button" id="<?php echo $this->get_field_id('reset_id'); ?>" name="<?php echo $this->get_field_name('reset_id'); ?>" onclick="document.getElementById('<?php echo $this->get_field_id('sibid'); ?>').value = '<?php echo $nwsibid; ?>'" ><?php _e('Reset ID.', 'simple-google-icalendar-widget')  ?></button>
          <label for="<?php echo $this->get_field_id('reset_id'); ?>"><?php _e('Reset ID, only necessary to clear cache or after duplicating block. You may have to change another field to save the new values to the DB', 'simple-google-icalendar-widget'); ?></label> 
        </p>
        <p>
            <?php echo '<a href="' . admin_url('admin.php?page=simple_ical_info') . '" target="_blank">' ; 
                _e('Need help?', 'simple-google-icalendar-widget'); 
                echo '</a>';
                ?>
        </p>
        <?php
	return '';    
    }
	
} // end class
} // !class_exists( 'Simple_iCal_Widget' )
function simple_ical_widget () {  register_widget( 'Simple_iCal_Widget' );}
add_action ('widgets_init', 'simple_ical_widget'  );

$ical_admin = new SimpleicalWidgetAdmin;
add_action('admin_menu',array ($ical_admin, 'simple_ical_admin_menu'));

} // old widget