<?php
/*
Plugin Name: Simple Google iCalendar Widget
Description: Widget that displays events from a public google calendar
Plugin URI: https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
Author: Bram Waasdorp
Version: 0.6.6
License: GPL3
Tested up to: 4.8.3
Requires PHP:  5.3.0 tested with 7.0
Text Domain:  simple_ical
Domain Path:  /languages

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
require_once( 'includes/ical.php' );
require_once('includes/widget-admin.php');


class Simple_iCal_Widget extends WP_Widget 
{
    
    public function __construct()
    {
        // load our textdomain
        load_plugin_textdomain('simple_ical', false, basename( dirname( __FILE__ ) ) . '/languages' );
        
        parent::__construct('simple_ical_widget', // Base ID
			    'Simple Google iCalendar Widget', // Name
			    array( // Args
				   'classname' => 'Simple_iCal_Widget', 
				   'description' => __('Displays events from a public Google Calendar or other iCal source', 'simple_ical'),
			          )
        		);
    }
 
   private function getTransientId()
    {
        return 'wp_ical_widget_'.$this->id;
    }
    
    private function getCalendarUrl($calId)
    {
    	if (substr($calId, 0, 4) == 'http')
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
        	$data = $this->fetch($calId, $instance['event_count'], $instance['event_period']);

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
    
    private function fetch($calId, $count, $period)
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
        	$penddate = strtotime("+$period day");
        	$parser = new IcsParser();
        	$parser->parse($httpData['body'], $penddate,  $count);

        	$events = $parser->getFutureEvents($penddate);
            return $this->limitArray($events, $count);
        } catch(IcsParsingException $e) {
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
        $sflg = (isset($instance['suffix_lg_class'])) ? $instance['suffix_lg_class'] : '' ;
        $sflgi = (isset($instance['suffix_lgi_class'])) ? $instance['suffix_lgi_class'] : '' ;
        $sflgia = (isset($instance['suffix_lgia_class'])) ? $instance['suffix_lgia_class'] : '' ;
        $data = $this->getData($instance);
        if (!empty($data) && is_array($data)) {
           date_default_timezone_set(get_option('timezone_string'));
           echo '<ul class="list-group' .  $sflg . ' simple-ical-widget">';
           $prevdate = '';
            foreach($data as $e) {
            	$idlist = explode("@", esc_attr($e->uid) );
            	$itemid = $this->id  . '_' . $idlist[0];
            	/* of dateformat  =  'l ' . get_option( 'date_format' ) */
            	echo '<li class="list-group-item' .  $sflgi . ' ical-date">';
            	if ($prevdate !=  ucfirst(date_i18n( 'l j F Y', $e->start, false ))) {
            		$prevdate =  ucfirst(date_i18n( 'l j F Y', $e->start, false ));
            		echo $prevdate, '<br>';
            	}
                echo  '<a class="ical_summary' .  $sflgia . '" data-toggle="collapse" href="#',
                   	$itemid, '" aria-expanded="false" aria-controls="', 
                   	$itemid, '">';
                   	if (date('z', $e->start) === date('z', $e->end))	{
                   		echo date_i18n( 'G:i ', $e->start, false ); 
                   	}
                if(!empty($e->summary)) {
                  	echo $e->summary;
                }	
                echo	'</a>' ;
                echo '<div class="collapse gcal_details' .  $sflgia . '" id="',  $itemid, '">';	    
               if(!empty($e->description)) {
               	echo   $e->description
                    ,'<br>';
                }
                if (date('z', $e->start) === date('z', $e->end))	{    
             echo '<span class="time">', date_i18n( 'G:i', $e->start, false ), 
		  '</span> - <span class="time">', date_i18n( 'G:i', $e->end, false ), '</span>' ;
	     } else {
		echo '-';      
	     }
              if(!empty($e->location)) {
                    echo  ' ',  $e->location;
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
        if(is_numeric($new_instance['event_count']) && $new_instance['event_count'] > 1) {
        	$instance['event_count'] = $new_instance['event_count'];
        } else {
        	$instance['event_count'] = 5;
        }
        // using strip_tags because it can start with space or contain more classe seperated by spaces
        $instance['suffix_lg_class'] = strip_tags($new_instance['suffix_lg_class']);  
        $instance['suffix_lgi_class'] = strip_tags($new_instance['suffix_lgi_class']);  
        $instance['suffix_lgia_class'] = strip_tags($new_instance['suffix_lgia_class']); 
        
        // delete our transient cache
        $this->clearData();
        
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
        		'suffix_lg_class' => '',
        		'suffix_lgi_class' => ' py-0',
        		'suffix_lgia_class' => '',
        		
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
//            <?php _e('Need <a href="https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget/blob/master/README.md" target="_blank">help</a>?', 'simple_ical'); ?>
            <?php _e('Need <a href="', 'simple_ical');
            echo admin_url('admin.php?page=simple_ical_info'); 
                _e('" target="_blank">help</a>?', 'simple_ical'); ?>
 
        </p>
        <?php
	return '';    
    }

}
$ical_admin = new Simple_iCal_Admin;
add_action('admin_menu',array ($ical_admin, 'simple_ical_admin_menu'));

add_action('widgets_init', create_function('', 'return register_widget("Simple_iCal_Widget");'));
