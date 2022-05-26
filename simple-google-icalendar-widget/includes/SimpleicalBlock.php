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
 * Version: 2.0.1
 * 20220427 namespaced and renamed after classname.
 * 20220430 try with static calls
 * 20220509 fairly correct front-end display. attributes back to block.json
 * 20220510 attributes again in php also added anchor, align and className who can be added by support hopefully that is enough for ServerSideRender.
 * 20220511 integer excerptlength not initialised with '' because serversiderender REST type validation gives an error (rest_invalid_type)
 *  excerptlength string and in javascript  '' when not parsed as integer. 
 * 20220526 added example 
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget;

class SimpleicalBlock {
    /**
     * @var string events to display in example 
     * EOL's and one space before second description line are important.
     */
    private static $example_events = 'BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20220526T150000
DTEND:20220526T160000
RRULE:FREQ=WEEKLY;INTERVAL=3;BYDAY=SU,WE,SA
UID:a-1
DESCRIPTION:Description event every 3 weeks sunday wednesday and saturday. t
 est A-Z.\nLine 2 of description.
LOCATION:At home or somewhere else
SUMMARY: Every 3 weeks sunday wednesday and saturday
END:VEVENT
BEGIN:VEVENT
DTSTART:20220529T143000
DTEND:20220529T153000
RRULE:FREQ=MONTHLY;COUNT=24;BYMONTHDAY=29
UID:a-2
DESCRIPTION:
LOCATION:
SUMMARY:Example event\, Monthly day 29
END:VEVENT
END:VCALENDAR';
    
    /**
     * Block init register block with help of block.json
     *
     * @param .
     */
    static function init_block() {
        register_block_type( dirname(__DIR__) .'/block.json',
            array(
        'attributes' => [
            'blockid' => ['type' => 'string'],
            'title' => ['type' => 'string', 'default' => __('Events', 'simple_ical')],
            'calendar_id' => ['type' => 'string', 'default' => ''],
            'event_count' => ['type' => 'integer', 'default' => 10],
            'event_period' => ['type' => 'integer', 'default' => 92],
            'cache_time' => ['type' => 'integer', 'default' => 60],
            'dateformat_lg' => ['type' => 'string', 'default' => 'l jS \of F'],
            'dateformat_tsum' => ['type' => 'string', 'default' => 'G:i '],
            'dateformat_tstart' => ['type' => 'string', 'default' => 'G:i'],
            'dateformat_tend' => ['type' => 'string', 'default' => ' - G:i '],
            'excerptlength' => ['type' => 'string', ''],
            'suffix_lg_class' => ['type' => 'string', 'default' => ''],
            'suffix_lgi_class' => ['type' => 'string', 'default' => ' py-0'],
            'suffix_lgia_class' => ['type' => 'string', 'default' => ''],
            'allowhtml' => ['type' => 'boolean', 'default' => false],
            'clear_cache_now' => ['type' => 'boolean', 'default' => false],
        ],
            'render_callback' => array('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalBlock', 'render_block'))
        );
   }
    /**
     * Render the content of the block
     *
     * see 
     *
     * @param array $block_attributes the block attributes (that are changed from default therefore first merged with defaults.)
     * @param array $content as saved in post by save in ...block.js
     * @return string  HTML to render for the block (frontend)
     */
   static function render_block($block_attributes, $content) {
       $block_attributes = wp_parse_args((array) $block_attributes,
           array(
               'blockid' => 'AZ',
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
//               'align'=>'', 
               'className'=>'',
           )
           );
       
       $output = '';
       ob_start();
       self::display_block([], $block_attributes);
       $output = $output . ob_get_clean();
       return '<div class="' . $block_attributes['className'] . ((isset($block_attributes['align'])) ? (' align' . $block_attributes['align']) : ' ')   .  '" >' . $output . '</div>'. '<div class="content">' . $content . '</div>'  ;
    }
    /**
     * Front-end display of block or widget.
     *
     * @see 
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved attribute/option values from database.
     */
    static function display_block($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        if (isset($args['before_widget'])) {
        echo $args['before_widget'];
        if(isset($instance['title'])) {
            echo $args['before_title'], $instance['title'], $args['after_title'];
        }}
        else echo '<h3 class="widget-title block-title">' . $instance['title'] . '</h3>';
        $dflg = (isset($instance['dateformat_lg'])) ? $instance['dateformat_lg'] : 'l jS \of F' ;
        $dftsum = (isset($instance['dateformat_tsum'])) ? $instance['dateformat_tsum'] : 'G:i ' ;
        $dftstart = (isset($instance['dateformat_tstart'])) ? $instance['dateformat_tstart'] : 'G:i' ;
        $dftend = (isset($instance['dateformat_tend'])) ? $instance['dateformat_tend'] : ' - G:i ' ;
        $excerptlength = (isset($instance['excerptlength'])) ? $instance['excerptlength'] : '' ;
        $sflg = (isset($instance['suffix_lg_class'])) ? $instance['suffix_lg_class'] : '' ;
        $sflgi = (isset($instance['suffix_lgi_class'])) ? $instance['suffix_lgi_class'] : '' ;
        $sflgia = (isset($instance['suffix_lgia_class'])) ? $instance['suffix_lgia_class'] : '' ;
        $data = self::getData($instance);
        if (!empty($data) && is_array($data)) {
            date_default_timezone_set(get_option('timezone_string'));
            echo '<ul class="list-group' .  $sflg . ' simple-ical-widget">';
            $curdate = '';
            foreach($data as $e) {
                $idlist = explode("@", esc_attr($e->uid) );
                $itemid = $instance['blockid'] . '_' . $idlist[0]; //TODO find correct block id when duplicate
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
    }
    /**
     * Gets data from calender or transient cache
     *
     * @param array $instance the block attributes 
     *    ['blockid']      to create transientid
     *    ['calendar_id']  id or url of the calender to fetch data
     *    ['event_count']  max number of events to return
     *    ['event_period'] max number of days after now to fetch events.
     *      
     * @return array event objects
     */
    private static function getData($instance)
    {
        $transientId = 'SimpleicalBlock'  . $instance['blockid']   ;
        if ($instance['clear_cache_now']) delete_transient($transientId);
        if(false === ($data = get_transient($transientId))) {
            $data =self::fetch(  $instance,  );
             // do not cache data if fetching failed
            if ($data) {
                set_transient($transientId, $data, $instance['cache_time']*60);
            }
        }
        return $data;
    }
    /**
     * Fetches from calender 
     *
     * @param array $instance the block attributes
     *    ['calendar_id']  id or url of the calender to fetch data
     *    ['event_count']  max number of events to return
     *    ['event_period'] max number of days after now to fetch events.
     *
     * @return array event objects
     */
    private static function fetch( $instance )
    {
        $period = $instance['event_period'];
        if ('#example' == $instance['calendar_id']){ 
            $httpData['body'] = self::$example_events;
        }
        else  {
            $url = self::getCalendarUrl($instance['calendar_id']);
            $httpData = wp_remote_get($url);
            if(is_wp_error($httpData)) {
                echo '<!-- ' . $url . ' not found ' . 'fall back to https:// -->';
                $httpData = wp_remote_get('https://' . explode('://', $url)[1]);
                if(is_wp_error($httpData)) {
                    echo 'Simple iCal Block: ', $httpData->get_error_message();
                    return false;
                }
            }
        }
        
        if(!is_array($httpData) || !array_key_exists('body', $httpData)) {
            return false;
        }
        
        try {
            $penddate = strtotime("+$period day");
            $parser = new IcsParser();
            $parser->parse($httpData['body'], $penddate, $instance['event_count'],  $instance );
            
            $events = $parser->getFutureEvents($penddate);
            return self::limitArray($events, $instance['event_count']);
        } catch(\Exception $e) {
            return null;
        }
    }
    
    private static function getCalendarUrl($calId)
    {
        
        $protocol = strtolower(explode('://', $calId)[0]);
        if (array_search($protocol, array('http', 'https', 'webcal')))
        { return $calId; }
        else
        { return 'https://www.google.com/calendar/ical/'.$calId.'/public/basic.ics'; }
    }
    
    private static function limitArray($arr, $limit)
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
    
    
} // end class SimpleicalBlock