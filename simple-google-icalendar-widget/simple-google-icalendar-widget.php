<?php
/*
 Plugin Name: Simple Google Calendar Outlook Events Widget
 Description: Widget that displays events from a public google calendar or iCal file
 Plugin URI: https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 Author: Bram Waasdorp
 Version: 1.6.0
 License: GPL3
 Tested up to: 5.9
 Requires PHP:  5.3.0 tested with 7.2
 Text Domain:  simple_ical
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
 */
/*
 Simple Google Calendar Outlook Events Widget
 Copyright (C) Bram Waasdorp 2017 - 2022
 2022-04-21
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
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\IcsParser;
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalWidgetAdmin;

if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\IcsParser')) {
    require_once( 'includes/IcsParser.php' );
    class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\IcsParser', 'IcsParser');
}
if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalWidgetAdmin')) {
    require_once('includes/SimpleicalWidgetAdmin.php');
    class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalWidgetAdmin', 'SimpleicalWidgetAdmin');
}
if ( !class_exists( 'Simple_iCal_Widget' ) ) {
class Simple_iCal_Widget extends WP_Widget
{
    
    public function __construct()
    {
        if ( function_exists( 'register_block_type' ) ) {
            if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalBlock')) {
                require_once('includes/SimpleicalBlock.php');
                class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalBlock', 'SimpleicalBlock');
            }
            $ical_block = 'SimpleicalBlock'; // Static class method call
//            $ical_block = new SimpleicalBlock(); // Object method call if necessary
            if ( is_admin() ) {
                add_action( 'admin_init', array( $ical_block, 'admin_init' ) );
                add_action( 'enqueue_block_editor_assets', array( $ical_block,  'simple_google_icalendar_script_enqueue') );
            }
            else {
                // ONLY LOAD THIS IF THE CURRENT PAGE HAS
                add_action( 'init', array( $ical_block, 'init' ) );
            }
        }
        else 
        {
            // load our textdomain
            load_plugin_textdomain('simple_ical', false, basename( dirname( __FILE__ ) ) . '/languages' );
            $widget_ops = array(  // Args
                'classname' => 'Simple_iCal_Widget',
                'description' => __('Displays events from a public Google Calendar or other iCal source', 'simple_ical'),
                'show_instance_in_rest' => true, // allow migrating to block
            );
            parent::__construct('simple_ical_widget', // Base ID
            'Simple Google Calendar Outlook ical Events Widget', // Name
            $widget_ops
             );
            
        }
        
    }
    
    private function getTransientId()
    {
        return 'wp_ical_widget_'.$this->id;
    }
    
    private function getCalendarUrl($calId)
    {
        
        $protocol = strtolower(explode('://', $calId)[0]);
        if (array_search($protocol, array('http', 'https', 'webcal')))
        { return $calId; }
        else
        { return 'https://www.google.com/calendar/ical/'.$calId.'/public/basic.ics'; }
    }
    
    private function getData($instance)
    {
        $widgetId = $this->id;
        $calId = $instance['calendar_id'];
        $transientId = $this->getTransientId();
        if(false === ($data = get_transient($transientId))) {
            $data = $this->fetch($calId, $instance['event_count'], $instance['event_period'], $instance );
            
            // do not cache data if fetching failed
            if ($data) {
                set_transient($transientId, $data, $instance['cache_time']*60);
            }
        }
        
        return $data;
    }
    
    private function clearData()
    {
        return delete_transient($this->getTransientId());
    }
    
    private function limitArray($arr, $limit)
    {
        $i = 0;
        
        $out = array();
        foreach ($arr as $e) {
            $i++;
            
            if ($i > $limit) {
                break;
            }
            $out[] = $e;
        }
        
        return $out;
    }
    
    private function fetch($calId, $count, $period,  $instance )
    {
        $url = $this->getCalendarUrl($calId);
        $httpData = wp_remote_get($url);
        
        if(is_wp_error($httpData)) {
            echo '<!-- ' . $url . ' not found ' . 'fall back to https:// -->';
            $httpData = wp_remote_get('https://' . explode('://', $url)[1]);
            if(is_wp_error($httpData)) {
                echo 'Simple Google Calendar: ', $httpData->get_error_message();
                return false;
            }
        }
        
        if(!is_array($httpData) || !array_key_exists('body', $httpData)) {
            return false;
        }
        
        try {
            $penddate = strtotime("+$period day");
            $parser = new IcsParser();
            $parser->parse($httpData['body'], $penddate,  $count,  $instance );
            
            $events = $parser->getFutureEvents($penddate);
            return $this->limitArray($events, $count);
        } catch(\Exception $e) {
            return null;
        }
    }
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if(isset($instance['title'])) {
            echo $args['before_title'], $instance['title'], $args['after_title'];
        }
        $dflg = (isset($instance['dateformat_lg'])) ? $instance['dateformat_lg'] : 'l jS \of F' ;
        $dftsum = (isset($instance['dateformat_tsum'])) ? $instance['dateformat_tsum'] : 'G:i ' ;
        $dftstart = (isset($instance['dateformat_tstart'])) ? $instance['dateformat_tstart'] : 'G:i' ;
        $dftend = (isset($instance['dateformat_tend'])) ? $instance['dateformat_tend'] : ' - G:i ' ;
        $excerptlength = (isset($instance['excerptlength'])) ? $instance['excerptlength'] : '' ;
        $sflg = (isset($instance['suffix_lg_class'])) ? $instance['suffix_lg_class'] : '' ;
        $sflgi = (isset($instance['suffix_lgi_class'])) ? $instance['suffix_lgi_class'] : '' ;
        $sflgia = (isset($instance['suffix_lgia_class'])) ? $instance['suffix_lgia_class'] : '' ;
        $data = $this->getData($instance);
        if (!empty($data) && is_array($data)) {
            date_default_timezone_set(get_option('timezone_string'));
            echo '<ul class="list-group' .  $sflg . ' simple-ical-widget">';
            $curdate = '';
            foreach($data as $e) {
                $idlist = explode("@", esc_attr($e->uid) );
                $itemid = $this->id  . '_' . $idlist[0];
                echo '<li class="list-group-item' .  $sflgi . ' ical-date">';
                if ($curdate !=  ucfirst(wp_date( $dflg, $e->start))) {
                    $curdate =  ucfirst(wp_date( $dflg, $e->start ));
                    echo $curdate, '<br>';
                }
                echo  '<a class="ical_summary' .  $sflgia . '" data-toggle="collapse" href="#',
                $itemid, '" aria-expanded="false" aria-controls="',
                $itemid, '">';
                if ($e->startisdate === false)	{
                    echo wp_date( $dftsum, $e->start);
                }
                if(!empty($e->summary)) {
                    echo str_replace("\n", '<br>', wp_kses($e->summary,'post'));
                }
                echo	'</a>' ;
                echo '<div class="collapse ical_details' .  $sflgia . '" id="',  $itemid, '">';
                if(!empty($e->description) && trim($e->description) > '' && $excerptlength !== 0) {
                    if ($excerptlength !== '' && strlen($e->description) > $excerptlength) {$e->description = substr($e->description, 0, $excerptlength + 1);
                    if (rtrim($e->description) !== $e->description) {$e->description = substr($e->description, 0, $excerptlength);}
                    else {if (strrpos($e->description, ' ', max(0,$excerptlength - 10))!== false OR strrpos($e->description, "\n", max(0,$excerptlength - 10))!== false )
                    {$e->description = substr($e->description, 0, max(strrpos($e->description, "\n", max(0,$excerptlength - 10)),strrpos($e->description, ' ', max(0,$excerptlength - 10))));
                    } else
                    {$e->description = substr($e->description, 0, $excerptlength);}
                    }
                    }
                    $e->description = str_replace("\n", '<br>', wp_kses($e->description,'post') );
                    echo   $e->description ,(strrpos($e->description, '<br>') == (strlen($e->description) - 4)) ? '' : '<br>';
                }
                if ($e->startisdate === false && date('z', $e->start) === date('z', $e->end))	{
                    echo '<span class="time">', wp_date( $dftstart, $e->start ),
                    '</span><span class="time">', wp_date( $dftend, $e->end ), '</span> ' ;
                } else {
                    echo '';
                }
                if(!empty($e->location)) {
                    echo  '<span class="location">', str_replace("\n", '<br>', wp_kses($e->location,'post')) , '</span>';
                }
                
                
                echo '</div></li>';
            }
            echo '</ul>';
            date_default_timezone_set('UTC');
        }
        
        echo '<br class="clear" />';
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
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        
        $instance['calendar_id'] = htmlspecialchars($new_instance['calendar_id']);
        
        $instance['cache_time'] = strip_tags($new_instance['cache_time']);
        if(is_numeric($new_instance['cache_time']) && $new_instance['cache_time'] > 1) {
            $instance['cache_time'] = $new_instance['cache_time'];
        } else {
            $instance['cache_time'] = 60;
        }
        
        $instance['event_period'] = $new_instance['event_period'];
        if(is_numeric($new_instance['event_period']) && $new_instance['event_period'] > 1) {
            $instance['event_period'] = $new_instance['event_period'];
        } else {
            $instance['event_period'] = 366;
        }
        
        $instance['event_count'] = $new_instance['event_count'];
        if(is_numeric($new_instance['event_count']) && $new_instance['event_count'] > 0) {
            $instance['event_count'] = $new_instance['event_count'];
        } else {
            $instance['event_count'] = 5;
        }
        // using strip_tags because it can start with space or contain more classe seperated by spaces
        $instance['dateformat_lg'] = strip_tags($new_instance['dateformat_lg']);
        $instance['dateformat_tsum'] = strip_tags($new_instance['dateformat_tsum']);
        $instance['dateformat_tstart'] = strip_tags($new_instance['dateformat_tstart']);
        $instance['dateformat_tend'] = strip_tags($new_instance['dateformat_tend']);
        $instance['cache_time'] = strip_tags($new_instance['cache_time']);
        if(is_numeric($new_instance['excerptlength']) && $new_instance['excerptlength'] >= 0) {
            $instance['excerptlength'] = intval($new_instance['excerptlength']);
        } else {
            $instance['excerptlength'] = '';
        }
        
        $instance['suffix_lg_class'] = strip_tags($new_instance['suffix_lg_class']);
        $instance['suffix_lgi_class'] = strip_tags($new_instance['suffix_lgi_class']);
        $instance['suffix_lgia_class'] = strip_tags($new_instance['suffix_lgia_class']);
        $instance['allowhtml'] = $new_instance['allowhtml'];
        
        
        if (!empty($new_instance['clear_cache_now'])){
            // delete our transient cache
            $this->clearData();
            $instance['clear_cache_now'] = $new_instance['clear_cache_now'];
        } else {
            $instance['clear_cache_now'] = 'no' ;
        }
        
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
        $default = array(
            'title' => __('Events', 'simple_ical'),
            'calendar_id' => '',
            'event_count' => 10,
            'event_period' => 92,
            'cache_time' => 60,
            'dateformat_lg' => 'l jS \of F',
            'dateformat_tsum' => 'G:i ',
            'dateformat_tstart' => 'G:i',
            'dateformat_tend' => ' - G:i ',
            'excerptlength' => '',
            'suffix_lg_class' => '',
            'suffix_lgi_class' => ' py-0',
            'suffix_lgia_class' => '',
            'allowhtml' => 0,
            'clear_cache_now' => 0,
        );
        $instance = wp_parse_args((array) $instance, $default);
        
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('calendar_id'); ?>"><?php _e('Calendar ID, or iCal URL:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('calendar_id'); ?>" name="<?php echo $this->get_field_name('calendar_id'); ?>" type="text" value="<?php echo esc_attr($instance['calendar_id']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('event_count'); ?>"><?php _e('Number of events displayed:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('event_count'); ?>" name="<?php echo $this->get_field_name('event_count'); ?>" type="text" value="<?php echo esc_attr($instance['event_count']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('event_period'); ?>"><?php _e('Number of days after today with events displayed:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('event_period'); ?>" name="<?php echo $this->get_field_name('event_period'); ?>" type="text" value="<?php echo esc_attr($instance['event_period']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php _e('Cache expiration time in minutes:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="text" value="<?php echo esc_attr($instance['cache_time']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_lg'); ?>"><?php _e('Date format first line:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_lg'); ?>" name="<?php echo $this->get_field_name('dateformat_lg'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_lg']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tsum'); ?>"><?php _e('Time format time summary line:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tsum'); ?>" name="<?php echo $this->get_field_name('dateformat_tsum'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tsum']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tstart'); ?>"><?php _e('Time format start time:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tstart'); ?>" name="<?php echo $this->get_field_name('dateformat_tstart'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tstart']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('dateformat_tend'); ?>"><?php _e('Time format end time:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('dateformat_tend'); ?>" name="<?php echo $this->get_field_name('dateformat_tend'); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tend']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('excerptlength'); ?>"><?php _e('Excerpt length, max length of description:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('excerptlength'); ?>" name="<?php echo $this->get_field_name('excerptlength'); ?>" type="text" value="<?php echo esc_attr($instance['excerptlength']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('suffix_lg_class'); ?>"><?php _e('Suffix group class:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('suffix_lg_class'); ?>" name="<?php echo $this->get_field_name('suffix_lg_class'); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lg_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('suffix_lgi_class'); ?>"><?php _e('Suffix event start class:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('suffix_lgi_class'); ?>" name="<?php echo $this->get_field_name('suffix_lgi_class'); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lgi_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('suffix_lgia_class'); ?>"><?php _e('Suffix event details class:', 'simple_ical'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('suffix_lgia_class'); ?>" name="<?php echo $this->get_field_name('suffix_lgia_class'); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lgia_class']); ?>" />
        </p>
        <p>
          <input class="checkbox" id="<?php echo $this->get_field_id('allowhtml'); ?>" name="<?php echo $this->get_field_name('allowhtml'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['allowhtml'] ); ?> />
          <label for="<?php echo $this->get_field_id('allowhtml'); ?>"><?php _e('Allow safe html in description and summary.', 'simple_ical'); ?></label> 
        </p>
         <p>
          <input class="checkbox" id="<?php echo $this->get_field_id('clear_cache_now'); ?>" name="<?php echo $this->get_field_name('clear_cache_now'); ?>" type="checkbox" value='1' <?php checked( '1', $instance['clear_cache_now'] ); ?>/>
          <label for="<?php echo $this->get_field_id('clear_cache_now'); ?>"><?php _e(' clear cache on save.', 'simple_ical'); ?></label> 
        </p>
        <p>
            <?php echo '<a href="' . admin_url('admin.php?page=simple_ical_info') . '" target="_blank">' ; 
                _e('Need help?', 'simple_ical'); 
                echo '</a>';
                ?>
        </p>
        <?php
	return '';    
    }
} // end class
} // !class_exists( 'Simple_iCal_Widget' )

if ( function_exists( 'register_block_type' ) ) {
    new Simple_iCal_Widget();
}
else 
{
    function simple_ical_widget () {  register_widget( 'Simple_iCal_Widget' );}
    add_action ('widgets_init', 'simple_ical_widget'  );
    
}
$ical_admin = new SimpleicalWidgetAdmin;
add_action('admin_menu',array ($ical_admin, 'simple_ical_admin_menu'));


