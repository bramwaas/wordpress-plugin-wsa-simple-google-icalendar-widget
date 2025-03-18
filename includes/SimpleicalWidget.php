<?php
/*
 * SimpleicalWidget.php
 *
 * legagcy widget 
 *
 * @package Simple Google iCalendar Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2024 - 2025, Bram Waasdorp
 * 
 * 2.6.0 in a separate class with namespace since 2.6.0 no underscores in classname. SimpleicalBlock => SimpleicalHelper
 * Replace echo by $secho a.o. in widget(), to simplify escaping output by replacing multiple echoes by one.
 * known error: in wp 5.9.5 with elementor 3.14.1 aria-expanded and aria-controls are stripped bij wp_kses before wp 6.3.0 (see wp_kses.php) 
 *   issue is solved tested with wp 6.7.1 with elementor 3.26.5 . 
 * 2.6.1  Started simplifying (bootstrap) collapse by toggles for adding javascript and trigger collapse by title.
 *  Remove toggle to allow safe html in summary and description, save html is always allowed now. 
 * 2.7.0 Enable to add words of summary to categories for filtering. Add support for details/summary tag combination.      
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
class SimpleicalWidget extends \WP_Widget
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
            $secho = '';
            $args = array_merge(['before_widget' => '',
                'before_title' => '<h3 class="widget-title block-title">',
                'after_title' => '<h3 class="widget-title block-title">',
                'after_widget' => '',
                'classname' => 'Simple_iCal_Widget' ],
                $args);
            $instance = array_merge(SimpleicalHelper::$default_block_attributes,
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
            $secho .= sprintf($args['before_widget'],
                ('w-' . $instance['anchorId']),
                $args['classname']) ;
            $secho .= '<span id="' . $instance['anchorId'] . '" data-sib-id="'
                . $instance['sibid'] . '" data-sib-utzui="' . $instance['rest_utzui']
                . (('rest_ph_w' == $instance['wptype']) ? '" data-sib-st="0-start" >' : '">');
            $args['after_widget'] = '</span>' . $args['after_widget'];
            
            if (! empty($instance['title'])) {
                if (false === stripos(' data-sib-t="true" ', $args['before_title'])) {
                    $l = explode('>', $args['before_title'], 2);
                    $args['before_title'] = implode(' data-sib-t="true" >', $l);
                }
                if (!empty($instance['title_collapse_toggle'])){
                    $args['before_title'] .= '<a data-toggle="collapse" data-bs-toggle="collapse" href="#lg' .$instance['anchorId'] . '" role="button" aria-expanded="'.(('collapse' == $instance['title_collapse_toggle'])?'false':'true').'" aria-controls="collapseMod">';
                    $args['after_title']  = '</a>' . $args['after_title'];
                }
                $title = apply_filters('widget_title', $instance['title']);
                $secho .= $args['before_title']. $title. $args['after_title'];
            }
            if ('rest_ph_w' == $instance['wptype'] ) {
                SimpleicalHelper::update_rest_attrs($instance );
                $secho .= '<p>';
               $secho .= __('Processing', 'simple-google-icalendar-widget');
                $secho .= '</p>';
            } else {
                SimpleicalHelper::display_block($instance, $secho);
            }
            // end lay-out block
            $secho .= $args['after_widget'];
            echo wp_kses($secho, 'post');
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
            $instance['title'] = wp_strip_all_tags($new_instance['title']);
            
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
            $instance['add_sum_catflt'] = !empty($new_instance['add_sum_catflt']);
            
            $instance['event_count'] = $new_instance['event_count'];
            if(is_numeric($new_instance['event_count']) && 0 < $new_instance['event_count']) {
                $instance['event_count'] = $new_instance['event_count'];
            } else {
                $instance['event_count'] = 5;
            }
            // using wp_strip_all_tags because it can start with space or contain more classe seperated by spaces
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
                $instance['period_limits'] = wp_strip_all_tags($new_instance['period_limits']);
            }
            if(!empty($new_instance['rest_utzui']) &&  is_numeric($new_instance['rest_utzui'])) {
                $instance['rest_utzui'] = wp_strip_all_tags($new_instance['rest_utzui']);
            }
            $instance['tag_sum'] = wp_strip_all_tags($new_instance['tag_sum']);
            // prevent trimming first space in suffix
            $instance['suffix_lg_class'] = substr(wp_strip_all_tags('a' . $new_instance['suffix_lg_class']),1);
            $instance['suffix_lgi_class'] = substr(wp_strip_all_tags('a' . $new_instance['suffix_lgi_class']),1);
            $instance['suffix_lgia_class'] = substr(wp_strip_all_tags('a' . $new_instance['suffix_lgia_class']),1);
            $instance['after_events'] = ($new_instance['after_events']);
            $instance['no_events'] = ($new_instance['no_events']);
            if (!empty($new_instance['blockid']) && empty($new_instance['sibid'])) {
                $new_instance['sibid'] = $new_instance['blockid'];
            }
            $instance['title_collapse_toggle'] = wp_strip_all_tags($new_instance['title_collapse_toggle'] ?? '' );
            
            $instance['sibid'] = wp_strip_all_tags($new_instance['sibid']);
            $instance['anchorId'] = wp_strip_all_tags($new_instance['anchorId'], 1) ?? $instance['sibid'];
            
            if (!empty($this->number && is_numeric($this->number))) {
                $instance['postid'] = (string) $this->id;
            }
            if (!empty($old_instance['sibid'])) $instance['prev_sibid'] = $old_instance['sibid'];
            if (SimpleicalHelper::update_rest_attrs($instance )) $instance['prev_sibid'] = $instance['sibid'];
            
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
                SimpleicalHelper::$default_block_attributes);
            
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
          <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('calendar_id')); ?>"><?php esc_attr_e('Calendar ID, or iCal URL:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calendar_id')); ?>" name="<?php echo esc_attr($this->get_field_name('calendar_id')); ?>" type="text" value="<?php echo esc_attr($instance['calendar_id']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('event_count')); ?>"><?php esc_attr_e('Number of events displayed:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('event_count')); ?>" name="<?php echo esc_attr($this->get_field_name('event_count')); ?>" type="text" value="<?php echo esc_attr($instance['event_count']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('event_period')); ?>"><?php esc_attr_e('Number of days after today with events displayed:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('event_period')); ?>" name="<?php echo esc_attr($this->get_field_name('event_period')); ?>" type="text" value="<?php echo esc_attr($instance['event_period']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>"><?php esc_attr_e('Lay-out:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" name="<?php echo esc_attr($this->get_field_name('layout')); ?>" >
            <option value="1"<?php echo (1==esc_attr($instance['layout']))?'selected':''; ?>><?php esc_attr_e('Startdate higher level', 'simple-google-icalendar-widget'); ?></option>
  			<option value="2"<?php echo (2==esc_attr($instance['layout']))?'selected':''; ?>><?php esc_attr_e('Start with summary', 'simple-google-icalendar-widget'); ?></option>
  			<option value="3"<?php echo (3==esc_attr($instance['layout']))?'selected':''; ?>><?php esc_attr_e('Old style', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
         <p>
          <label for="<?php echo esc_attr($this->get_field_id('dateformat_lg')); ?>"><?php esc_attr_e('Date format first line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateformat_lg')); ?>" name="<?php echo esc_attr($this->get_field_name('dateformat_lg')); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_lg']); ?>" />
        </p>
         <p>
          <label for="<?php echo esc_attr($this->get_field_id('dateformat_lgend')); ?>"><?php esc_attr_e('Enddate format first line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateformat_lgend')); ?>" name="<?php echo esc_attr($this->get_field_name('dateformat_lgend')); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_lgend']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('dateformat_tsum')); ?>"><?php esc_attr_e('Time format time summary line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateformat_tsum')); ?>" name="<?php echo esc_attr($this->get_field_name('dateformat_tsum')); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tsum']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('dateformat_tsend')); ?>"><?php esc_attr_e('Time format end time summary line:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateformat_tsend')); ?>" name="<?php echo esc_attr($this->get_field_name('dateformat_tsend')); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tsend']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('dateformat_tstart')); ?>"><?php esc_attr_e('Time format start time:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateformat_tstart')); ?>" name="<?php echo esc_attr($this->get_field_name('dateformat_tstart')); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tstart']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('dateformat_tend')); ?>"><?php esc_attr_e('Time format end time:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateformat_tend')); ?>" name="<?php echo esc_attr($this->get_field_name('dateformat_tend')); ?>" type="text" value="<?php echo esc_attr($instance['dateformat_tend']); ?>" />
        </p>
        <h4>Advanced</h4>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('cache_time')); ?>"><?php esc_attr_e('Cache expiration time in minutes:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('cache_time')); ?>" name="<?php echo esc_attr($this->get_field_name('cache_time')); ?>" type="text" value="<?php echo esc_attr($instance['cache_time']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('excerptlength')); ?>"><?php esc_attr_e('Excerpt length, max length of description:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('excerptlength')); ?>" name="<?php echo esc_attr($this->get_field_name('excerptlength')); ?>" type="text" value="<?php echo esc_attr($instance['excerptlength']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('period_limits')); ?>"><?php esc_attr_e('Period limits:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('period_limits')); ?>" name="<?php echo esc_attr($this->get_field_name('period_limits')); ?>" >
            <option value="1"<?php echo ('1'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php esc_attr_e('Start Whole  day, End Whole  day', 'simple-google-icalendar-widget'); ?></option>
  			<option value="2"<?php echo ('2'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php esc_attr_e('Start Time of day, End Whole  day', 'simple-google-icalendar-widget'); ?></option>
  			<option value="3"<?php echo ('3'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php esc_attr_e('Start Time of day, End Time of day', 'simple-google-icalendar-widget'); ?></option>
  			<option value="4"<?php echo ('4'==esc_attr($instance['period_limits']))?'selected':''; ?>><?php esc_attr_e('Start Whole  day, End Time of day', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('rest_utzui')); ?>"><?php esc_attr_e('Use client timezone settings:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('rest_utzui')); ?>" name="<?php echo esc_attr($this->get_field_name('rest_utzui')); ?>" >
  			<option value=""<?php echo (''==esc_attr($instance['rest_utzui']))?'selected':''; ?>><?php esc_attr_e('Use WordPress timezone settings, no REST', 'simple-google-icalendar-widget'); ?></option>
            <option value="1"<?php echo ('1'==esc_attr($instance['rest_utzui']))?'selected':''; ?>><?php esc_attr_e('Use Client timezone settings, with REST', 'simple-google-icalendar-widget'); ?></option>
  		 </select>
  		</p> 	
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('categories_filter_op')); ?>"><?php esc_attr_e('Categories Filter Operator:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('categories_filter_op')); ?>" name="<?php echo esc_attr($this->get_field_name('categories_filter_op')); ?>" >
  			<option value=""<?php echo (''==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php esc_attr_e('No filter', 'simple-google-icalendar-widget'); ?></option>
            <option value="ANY"<?php echo ('ANY'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php esc_attr_e('ANY, one or more match', 'simple-google-icalendar-widget'); ?></option>
            <option value="ALL"<?php echo ('ALL'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php esc_attr_e('ALL, all match', 'simple-google-icalendar-widget'); ?></option>
            <option value="NOTANY"<?php echo ('NOTANY'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php esc_attr_e('NOT ANY, no match', 'simple-google-icalendar-widget'); ?></option>
            <option value="NOTALL"<?php echo ('NOTALL'==esc_attr($instance['categories_filter_op']))?'selected':''; ?>><?php esc_attr_e('NOT ALL, not all match', 'simple-google-icalendar-widget'); ?></option>
  		 </select>
  		</p> 	
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('categories_filter')); ?>"><?php esc_attr_e('Categories Filter List:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('categories_filter')); ?>" name="<?php echo esc_attr($this->get_field_name('categories_filter')); ?>" type="text" value="<?php echo esc_attr($instance['categories_filter']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('categories_display')); ?>"><?php esc_attr_e('Display categories with separator:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('categories_display')); ?>" name="<?php echo esc_attr($this->get_field_name('categories_display')); ?>" type="text" value="<?php echo esc_attr($instance['categories_display']); ?>" />
		<label style="font-size:12px; color:#7f7f7f;"><?php esc_attr_e('Empty no display. Else display categories above event with this separator.', 'simple-google-icalendar-widget'); ?></label>
        </p>
        
        <p>
          <input class="checkbox" id="<?php echo $this->get_field_id('add_sum_catflt'); ?>" name="<?php echo $this->get_field_name('add_sum_catflt'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['add_sum_catflt'] ); ?> />
          <label for="<?php echo $this->get_field_id('add_sum_catflt'); ?>"><?php _e('Add summary to categories filter.', 'simple-google-icalendar-widget'); ?></label> 
          <!-- Add words from summary to categories for filtering  -->
        </p>

		<p>
          <label for="<?php echo esc_attr($this->get_field_id('tag_sum')); ?>"><?php esc_attr_e('Tag for summary:', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('tag_sum')); ?>" name="<?php echo esc_attr($this->get_field_name('tag_sum')); ?>" >
            <option value="a"<?php echo ('a'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('a (link)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="b"<?php echo ('b'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('b (attention, bold)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="div"<?php echo ('div'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('div', 'simple-google-icalendar-widget'); ?></option>
  			<option value="h4"<?php echo ('h4'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('h4 (sub header)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="h5"<?php echo ('h5'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('h5 (sub header)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="h6"<?php echo ('h6'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('h6 (sub header)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="i"<?php echo ('i'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('i (idiomatic, italic)', 'simple-google-icalendar-widget'); ?></option>
  			<option value="span"<?php echo ('span'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('span', 'simple-google-icalendar-widget'); ?></option>
  			<option value="strong"<?php echo ('strong'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('strong', 'simple-google-icalendar-widget'); ?></option>
  			<option value="summary"<?php echo ('summary'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('summary with details', 'simple-google-icalendar-widget'); ?></option>
  			<option value="u"<?php echo ('u'==esc_attr($instance['tag_sum']))?'selected':''; ?>><?php esc_attr_e('u (unarticulated, underline )', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('suffix_lg_class')); ?>"><?php esc_attr_e('Suffix group class:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('suffix_lg_class')); ?>" name="<?php echo esc_attr($this->get_field_name('suffix_lg_class')); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lg_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('suffix_lgi_class')); ?>"><?php esc_attr_e('Suffix event start class:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('suffix_lgi_class')); ?>" name="<?php echo esc_attr($this->get_field_name('suffix_lgi_class')); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lgi_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('suffix_lgia_class')); ?>"><?php esc_attr_e('Suffix event details class:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('suffix_lgia_class')); ?>" name="<?php echo esc_attr($this->get_field_name('suffix_lgia_class')); ?>" type="text" value="<?php echo esc_attr($instance['suffix_lgia_class']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('after_events')); ?>"><?php esc_attr_e('Closing HTML after available events:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('after_events')); ?>" name="<?php echo esc_attr($this->get_field_name('after_events')); ?>" type="text" value="<?php echo esc_attr($instance['after_events']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('no_events')); ?>"><?php esc_attr_e('Closing HTML when no events:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('no_events')); ?>" name="<?php echo esc_attr($this->get_field_name('no_events')); ?>" type="text" value="<?php echo esc_attr($instance['no_events']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('anchorId')); ?>"><?php esc_attr_e('HTML anchor:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('anchorId')); ?>" name="<?php echo esc_attr($this->get_field_name('anchorId')); ?>" type="text" value="<?php echo esc_attr($instance['anchorId']); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('title_collapse_toggle')); ?>"><?php esc_attr_e('Title as collapse toggle.', 'simple-google-icalendar-widget'); ?></label> 
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('title_collapse_toggle')); ?>" name="<?php echo esc_attr($this->get_field_name('title_collapse_toggle')); ?>" >
            <option value=""<?php echo (''==esc_attr($instance['title_collapse_toggle']))?'selected':''; ?>><?php esc_attr_e('No toggle', 'simple-google-icalendar-widget'); ?></option>
  			<option value="collapse"<?php echo ('collapse'==esc_attr($instance['title_collapse_toggle']))?'selected':''; ?>><?php esc_attr_e('Start collapsed', 'simple-google-icalendar-widget'); ?></option>
  			<option value="collapse show"<?php echo ('collapse show'==esc_attr($instance['title_collapse_toggle']))?'selected':''; ?>><?php esc_attr_e('Start open', 'simple-google-icalendar-widget'); ?></option>
  		 </select>	
        </p>
        <p>
          <label><?php esc_attr_e('Use plugin options form to add Bootstrap collapse code (js and css) when not provided by theme.', 'simple-google-icalendar-widget'); ?>
          <br> 
            <?php echo '<a href="' . esc_url(admin_url('admin.php?page=simple_ical_options')) . '" target="_blank">' ; 
                esc_attr_e('Options form', 'simple-google-icalendar-widget'); 
                echo '</a>';
                ?>
           </label>
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('sibid')); ?>"><?php esc_attr_e('Sib ID:', 'simple-google-icalendar-widget'); ?></label> 
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('sibid')); ?>" name="<?php echo esc_attr($this->get_field_name('sibid')); ?>" type="text" value="<?php echo esc_attr($instance['sibid']); ?>" readonly />
        </p>
         <p>
          <button class="button" id="<?php echo esc_attr($this->get_field_id('reset_id')); ?>" name="<?php echo esc_attr($this->get_field_name('reset_id')); ?>" onclick="document.getElementById('<?php echo esc_attr($this->get_field_id('sibid')); ?>').value = '<?php echo esc_attr($nwsibid); ?>'" ><?php esc_attr_e('Reset ID.', 'simple-google-icalendar-widget')  ?></button>
          <label for="<?php echo esc_attr($this->get_field_id('reset_id')); ?>"><?php esc_attr_e('Reset ID, only necessary to clear cache or after duplicating block. You may have to change another field to save the new values to the DB', 'simple-google-icalendar-widget'); ?></label> 
        </p>
        <p>
            <?php echo '<a href="' . esc_url(admin_url('admin.php?page=simple_ical_info')) . '" target="_blank">' ; 
                esc_attr_e('Need help?', 'simple-google-icalendar-widget'); 
                echo '</a>';
                ?>
        </p>
        <?php
	return '';    
    }
	
} // end class
