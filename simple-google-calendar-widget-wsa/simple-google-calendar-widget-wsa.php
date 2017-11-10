<?php
/*
Plugin Name: Simple Google Calendar Widget wsa
Description: Widget that displays events from a public google calendar
Plugin URI: https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
Author: Bram Waasdorp
Version: 0.0.1
License: GPL3
Tested up to: 4.8.3
*/
/*
    Simple Google calendar widget for Wordpress
    Copyright (C) Bram Waasdorp 2017 
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

require 'ical.php';

class Simple_Gcal_Widget extends WP_Widget 
{
    
    public function __construct()
    {
        // load our textdomain
        load_plugin_textdomain('simple_gcal', false, basename( dirname( __FILE__ ) ) . '/languages' );
        
        parent::__construct('Simple_Gcal_Widget', 'Simple Google Calendar Widget', array('description' => __('Displays events from a public Google Calendar', 'simple_gcal')));
    }
    
    private function getTransientId()
    {
        return 'wp_gcal_widget_'.$this->id;
    }
    
    private function getCalendarUrl($calId)
    {
        return 'https://www.google.com/calendar/ical/'.$calId.'/public/basic.ics';
    }
    
    private function getData($instance)
    {
        $widgetId = $this->id;
        $calId = $instance['calendar_id'];
        $transientId = $this->getTransientId();
        
        if(false === ($data = get_transient($transientId))) {
            $data = $this->fetch($calId, $instance['event_count']);

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
    
    private function fetch($calId, $count)
    {
        $url = $this->getCalendarUrl($calId);
        $httpData = wp_remote_get($url);
        
        if(is_wp_error($httpData)) {
            echo 'Simple Google Calendar: ', $httpData->get_error_message();
            return false;
        }
        
        if(!is_array($httpData) || !array_key_exists('body', $httpData)) {
            return false;
        }

        try {
            $parser = new IcsParser();
            $parser->parse($httpData['body']);

            $events = $parser->getFutureEvents();
            return $this->limitArray($events, $count);
        } catch(IcsParsingException $e) {
            return null;
        }
    }

    public function widget($args, $instance) 
    {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget']; 
        if(isset($instance['title'])) {
            echo $args['before_title'], $instance['title'], $args['after_title'];
        }
         $data = $this->getData($instance);

        if (!empty($data) && is_array($data)) {
           date_default_timezone_set(get_option('timezone_string'));
           echo '<ul class="list-group simple-gcal-widget">';
            foreach($data as $e) {
		/* of dateformat  =  'l ' . get_option( 'date_format' ) */
              echo '<li class="list-group-item gcal-date">',  date_i18n( 'l j F Y', $e->start, false );
               if(!empty($e->summary)) {
                    echo  '<br><a class="btn btn-primary" data-toggle="collapse" href="#', esc_attr($e->uid), '" aria-expanded="false" aria-controls="', esc_attr($e->uid), '">',   htmlspecialchars(str_replace(array("\r\n", "\n", "\r"), "<br>", $e->summary)), '</a>' ;
                }
	       echo '<div class="collapse" id="',  esc_attr($e->uid), '">';	    
               if(!empty($e->description)) {
                    echo  '<br>',  htmlspecialchars(str_replace(array("\r\n", "\n", "\r"), "<br>", $e->description));
                }
	     if ($e->end > $e->start )	{    
             echo '<br><span class="time">', date_i18n( 'G:i', $e->start, false ), 
		  '</span> - <span class="time">', date_i18n( 'G:i', $e->end, false ), '</span>' ;
	     } else {
		echo '    ';      
	     }
              if(!empty($e->location)) {
                    echo  ' ',  htmlspecialchars($e->location);
                }

 
            echo '</div></li>';
            }
	echo '</ul>';
            date_default_timezone_set('UTC');
        }

        echo '<br class="clear" />';
        echo $args['after_widget']; 
    }

    public function update($new_instance, $old_instance) 
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        
        $instance['calendar_id'] = htmlspecialchars($new_instance['calendar_id']);
        
        $instance['cache_time'] = $new_instance['cache_time'];
        if(is_numeric($new_instance['cache_time']) && $new_instance['cache_time'] > 1) {
            $instance['cache_time'] = $new_instance['cache_time'];
        } else {
            $instance['cache_time'] = 60;
        }
        
        $instance['event_count'] = $new_instance['event_count'];
        if(is_numeric($new_instance['event_count']) && $new_instance['event_count'] > 1) {
            $instance['event_count'] = $new_instance['event_count'];
        } else {
            $instance['event_count'] = 5;
        }

        // delete our transient cache
        $this->clearData();
        
        return $instance;
    }

    public function form($instance) 
    {
        $default = array(
            'title' => __('Events', 'simple_gcal'),
            'cache_time' => 60,
        );
        $instance = wp_parse_args((array) $instance, $default);
        
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'simple_gcal'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('calendar_id'); ?>"><?php _e('Calendar ID:', 'simple_gcal'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('calendar_id'); ?>" name="<?php echo $this->get_field_name('calendar_id'); ?>" type="text" value="<?php echo esc_attr($instance['calendar_id']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('event_count'); ?>"><?php _e('Number of events displayed:', 'simple_gcal'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('event_count'); ?>" name="<?php echo $this->get_field_name('event_count'); ?>" type="text" value="<?php echo esc_attr($instance['event_count']); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php _e('Cache expiration time in minutes:', 'simple_gcal'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="text" value="<?php echo esc_attr($instance['cache_time']); ?>" />
        </p>
        <p>
            <?php _e('Need <a href="http://wordpress.org/extend/plugins/simple-google-calendar-widget/" target="_blank">help</a>?', 'simple_gcal'); ?>
        </p>
        <?php
    }

}

add_action('widgets_init', create_function('', 'return register_widget("Simple_Gcal_Widget");'));
