<?php
/*
 * SimpleicalBlock.php
 *
 * @package    Simple Google iCalendar Block
 * @subpackage Block
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2022 - 2024, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Gutenberg Block functions since v2.1.2 also used for widget.
 * Version: 2.3.0
 * 20220427 namespaced and renamed after classname.
 * 20220430 try with static calls
 * 20220509 fairly correct front-end display. attributes back to block.json
 * 20220510 attributes again in php also added anchor, align and className who can be added by support hopefully that is enough for ServerSideRender.
 * 20220511 integer excerptlength not initialised with '' because serversiderender REST type validation gives an error (rest_invalid_type)
 *  excerptlength string and in javascript  '' when not parsed as integer.
 * 20220526 added example
 * 20220620 added enddate/times for startdate and starttime added Id as anchor and choice of tagg for summary, collaps only when tag_for summary = a.
 * 2.1.0 add calendar class to list-group-item
 *   add htmlspecialchars() to summary, description and location when not 'allowhtml', replacing similar code from IcsParser
 * 2.1.1 20230401 use select 'layout' in stead of 'start with summary' to create more lay-out options.
 * 2.1.2 20230410 move assignment of cal_class to a place where e=>cal_class is available.
 * 2.1.3 20230418 Added optional placeholder HTML output when no upcoming events are avalable. Also added optional output after the events list (when upcoming events are available).
 * 2.1.5 20230824 default_block_attributes as static variable and alowed_tags_sum made public to use same values also in the widget, option anchorId moved to render_block.
 * 2.2.0 20240106 changed text domain to simple-google-icalendar-widget
 * 2.2.1 20240123 don't display description line when excerpt-length = 0
 * 2.3.0 remove definition of attributes, leave it to block.json 
 *    improvement of working with client timezone: add client timezone as an extra parameter to wp_date because date_default_timezone_set has no effect
 *    block default version 3 version 2; add <span class="dsc"> to description output to make it easier to refer to in css   
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;

class SimpleicalBlock {
    /**
     * tags allowed for summary
     * @var array
     */
    static $allowed_tags_sum = ['a', 'b', 'div', 'h4', 'h5', 'h6', 'i', 'span', 'strong', 'u'] ;
    /**
     * deafault value for block_attributes (or instance)
     * @var array
     */
    static $default_block_attributes = [
        'wptype' => 'block',
        'blockid' => 'AZ',
        'postid' => '0',
        'calendar_id' => '',
        'event_count' => 10,
        'event_period' => 92,
        'cache_time' => 60,
        'layout' => 3,
        'dateformat_lg' => 'l jS \of F',
        'dateformat_lgend' => '',
        'period_limits' => '1',
        'tag_sum' => 'a',
        'dateformat_tsum' => 'G:i ',
        'dateformat_tsend' => '',
        'dateformat_tstart' => 'G:i',
        'dateformat_tend' => ' - G:i ',
        'excerptlength' => '',
        'suffix_lg_class' => '',
        'suffix_lgi_class' => ' py-0',
        'suffix_lgia_class' => '',
        'allowhtml' => false,
        'after_events' => '',
        'no_events' => '',
        'clear_cache_now' => false,
        'className'=>'',
        'anchorId'=> '',
    ];

    /**
     * Block init register block with help of block.json
     *
     * @param
     *            .
     */
    static function init_block()
    {
        register_block_type(dirname(__DIR__) . '/block.json', array(
            'render_callback' => array(
                'WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock',
                'render_block'
            )
        ));
        add_action( 'enqueue_block_assets', array(
            'WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock','enqueue_block_script') );
        
    }
    /**
     * Block init register block with help of block.json
     *
     * @param .
     */
    static function init_block_v2()
    {
        register_block_type(dirname(__DIR__) . '/v2/block.json', array(
            // 'attributes' => [
            // 'wptype' => ['type' => 'string'],
            // 'blockid' => ['type' => 'string'],
            // 'title' => ['type' => 'string', 'default' => __('Events', 'simple-google-icalendar-widget')],
            // 'calendar_id' => ['type' => 'string', 'default' => ''],
            // 'event_count' => ['type' => 'integer', 'default' => 10],
            // 'event_period' => ['type' => 'integer', 'default' => 92],
            // 'layout' => ['type' => 'integer', 'default' => 3],
            // 'cache_time' => ['type' => 'integer', 'default' => 60],
            // 'dateformat_lg' => ['type' => 'string', 'default' => 'l jS \of F'],
            // 'dateformat_lgend' => ['type' => 'string', 'default' => ''],
            // 'tag_sum' => ['type' => 'string', 'enum' => self::$allowed_tags_sum, 'default' => 'a'],
            // 'dateformat_tsum' => ['type' => 'string', 'default' => 'G:i '],
            // 'dateformat_tsend' => ['type' => 'string', 'default' => ''],
            // 'dateformat_tstart' => ['type' => 'string', 'default' => 'G:i'],
            // 'dateformat_tend' => ['type' => 'string', 'default' => ' - G:i '],
            // 'excerptlength' => ['type' => 'string', ''],
            // 'suffix_lg_class' => ['type' => 'string', 'default' => ''],
            // 'suffix_lgi_class' => ['type' => 'string', 'default' => ' py-0'],
            // 'suffix_lgia_class' => ['type' => 'string', 'default' => ''],
            // 'allowhtml' => ['type' => 'boolean', 'default' => false],
            // 'after_events' => ['type' => 'string', 'default' => ''],
            // 'no_events' => ['type' => 'string', 'default' => ''],
            // 'clear_cache_now' => ['type' => 'boolean', 'default' => false],
            // 'anchorId' => ['type' => 'string', 'default' => ''],
            // ],
            'render_callback' => array(
                'WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock',
                'render_block'
            )
        ));
    }
/**
 * enqueue scripts for use in editor on block code (iframe) 
 */
    static function enqueue_block_script(){
                wp_enqueue_script(  "simplegoogleicalenderwidget-simple-ical-block-view-script",
                    plugins_url( '/js/simple-ical-block-view.js', __DIR__ ) , [],
                    '2.3.0-' . filemtime( plugin_dir_path( __DIR__ ) . '/js/simple-ical-block-view.js' )
                    , ['strategy' => 'defer'] );
        
    }
    
    /**
     * Render the content of the block
     *
     * see
     *
     * @param array $block_attributes
     *            the block attributes (that are changed from default therefore first merged with defaults.)
     * @param array $content
     *            as saved in post by save in ...block.js
     * @param object $block
     *            the bolck that is rendered
     * @return string HTML to render for the block (frontend)
     */
    static function render_block($block_attributes, $content = null, $block = null)
    {
        $block_attributes = wp_parse_args((array) $block_attributes, (array(
            'title' => __('Events', 'simple-google-icalendar-widget'),
            'tzid_ui' => wp_timezone_string()
        ) + self::$default_block_attributes));
        $block_attributes['anchorId'] = sanitize_html_class($block_attributes['anchorId'], $block_attributes['blockid']);

        $output = '';
        ob_start();
        if (4 >= $block_attributes['period_limits']) {
            echo '<div id="' . $block_attributes['anchorId'] . '" class="' . $block_attributes['className'] . ((isset($block_attributes['align'])) ? (' align' . $block_attributes['align']) : ' ') . '" data-sib-id="' . $block_attributes['blockid'] . '" >';
            self::display_block($block_attributes);
            echo '</div>';
        } else if (!empty($block_attributes['rest_end'])) {
            self::display_block($block_attributes);
        } else {
            self::display_rest_start($block_attributes, $content, $block);
        }
        $output = $output . ob_get_clean();
        return $output;
    }
    /**
     * Front-end display of block or widget.
     *
     * @see
     *
     * @param array $instance Saved attribute/option values from database.
     */
    static function display_block($instance)
    {
        if (!isset($instance['wptype']) || 'block' == $instance['wptype']) {
            echo '<h3 class="widget-title block-title">' . $instance['title'] . '</h3>';
        }
        $sn = 0;
        $data_sib = 'client TZID=' . $instance['tzid_ui'];
        try {
            $instance['tz_ui'] = new \DateTimeZone($instance['tzid_ui']);
        } catch (\Exception $exc) {}
        if (empty($instance['tz_ui']))
            try {
                $instance['tzid_ui'] = wp_timezone_string();
                $instance['tz_ui'] = new \DateTimeZone($instance['tzid_ui']);
            } catch (\Exception $exc) {}
        if (empty($instance['tz_ui'])) {
            $instance['tzid_ui'] = 'UTC';
            $instance['tz_ui'] = new \DateTimeZone('UTC');
        }
        $layout = (isset($instance['layout'])) ? $instance['layout'] : 3;
        $dflg = (isset($instance['dateformat_lg'])) ? $instance['dateformat_lg'] : 'l jS \of F' ;
        $dflgend = (isset($instance['dateformat_lgend'])) ? $instance['dateformat_lgend'] : '' ;
        $dftsum = (isset($instance['dateformat_tsum'])) ? $instance['dateformat_tsum'] : 'G:i ' ;
        $dftsend = (isset($instance['dateformat_tsend'])) ? $instance['dateformat_tsend'] : '' ;
        $dftstart = (isset($instance['dateformat_tstart'])) ? $instance['dateformat_tstart'] : 'G:i' ;
        $dftend = (isset($instance['dateformat_tend'])) ? $instance['dateformat_tend'] : ' - G:i ' ;
        $excerptlength = (isset($instance['excerptlength']) && ' ' < trim($instance['excerptlength']) ) ? (int) $instance['excerptlength'] : '' ;
        $instance['suffix_lg_class'] = wp_kses($instance['suffix_lg_class'], 'post');
        $sflgi = wp_kses($instance['suffix_lgi_class'], 'post');
        $sflgia = wp_kses($instance['suffix_lgia_class'], 'post');
        if (!in_array($instance['tag_sum'], self::$allowed_tags_sum)) $instance['tag_sum'] = 'a';
        $data = IcsParser::getData($instance);
        if (!empty($data) && is_array($data)) {
            echo '<ul class="list-group' .  $instance['suffix_lg_class'] . ' simple-ical-widget" data-sib="' . $data_sib . '"> ';
            $curdate = '';
            foreach($data as $e) {
                $idlist = explode("@", esc_attr($e->uid) );
                $itemid = $instance['blockid'] .'_' . strval(++$sn) . '_' . $idlist[0];
                $evdate = wp_kses(wp_date( $dflg, $e->start, $instance['tz_ui']), 'post');
                $cal_class = ((!empty($e->cal_class)) ? ' ' . sanitize_html_class($e->cal_class): '');
                if ( !$instance['allowhtml']) {
                    if (!empty($e->summary)) $e->summary = htmlspecialchars($e->summary);
                    if (!empty($e->description)) $e->description = htmlspecialchars($e->description);
                    if (!empty($e->location)) $e->location = htmlspecialchars($e->location);
                }
                if (date('yz', $e->start) != date('yz', $e->end)) {
                    $evdate = str_replace(array("</div><div>", "</h4><h4>", "</h5><h5>", "</h6><h6>" ), '', $evdate . wp_kses(wp_date( $dflgend, $e->end - 1, $instance['tz_ui']) , 'post'));
                }
                $evdtsum = (($e->startisdate === false) ? wp_kses(wp_date( $dftsum, $e->start, $instance['tz_ui']) . wp_date( $dftsend, $e->end, $instance['tz_ui']), 'post') : '');
                if ($layout < 2 && $curdate != $evdate) {
                    if  ($curdate != '') { echo '</ul></li>';}
                    echo '<li class="list-group-item' .  $sflgi . ' head">' .
                        '<span class="ical-date">' . ucfirst($evdate) . '</span><ul class="list-group' .  $instance['suffix_lg_class'] . '">';
                }
                echo '<li class="list-group-item' .  $sflgi . $cal_class . '">';
                if ($layout == 3 && $curdate != $evdate) {
                    echo '<span class="ical-date">' . ucfirst($evdate) . '</span>' . (('a' == $instance['tag_sum'] ) ? '<br>': '');
                }
                echo  '<' . $instance['tag_sum'] . ' class="ical_summary' .  $sflgia . (('a' == $instance['tag_sum'] ) ? '" data-toggle="collapse" data-bs-toggle="collapse" href="#'.
                    $itemid . '" aria-expanded="false" aria-controls="'.
                    $itemid . '">' : '">') ;
                if ($layout != 2)	{
                    echo $evdtsum;
                }
                if(!empty($e->summary)) {
                    echo str_replace("\n", '<br>', wp_kses($e->summary,'post'));
                }
                echo	'</' . $instance['tag_sum'] . '>' ;
                if ($layout == 2) {
                    echo '<span>', $evdate, $evdtsum, '</span>';
                }
                echo '<div class="ical_details' .  $sflgia . (('a' == $instance['tag_sum'] ) ? ' collapse' : '') . '" id="',  $itemid, '">';
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
                    echo   '<span class="dsc">', $e->description ,(strrpos($e->description, '<br>') === (strlen($e->description) - 4)) ? '' : '<br>', '</span>';
                }
                if ($e->startisdate === false && date('yz', $e->start) === date('yz', $e->end))	{
                    echo '<span class="time">', wp_kses(wp_date( $dftstart, $e->start, $instance['tz_ui'] ), 'post'),
                    '</span><span class="time">', wp_kses(wp_date( $dftend, $e->end, $instance['tz_ui'] ), 'post'), '</span> ' ;
                } else {
                    echo '';
                }
                if(!empty($e->location)) {
                    echo  '<span class="location">', str_replace("\n", '<br>', wp_kses($e->location,'post')) , '</span>';
                }
                echo '</div></li>';
                $curdate =  $evdate;
            }
            if ($layout < 2 ) {
                echo '</ul></li>';
            }
            echo '</ul>';
            echo wp_kses($instance['after_events'],'post');
        }
        else {
            echo wp_kses($instance['no_events'],'post');
            
        }
        echo '<br class="clear" />';
    }

    /**
     * Placeholder starting point for REST processing display of block or widget.
     * cache data (although maybe with wrong timezone) in transient to accelerate REST proces
     *
     * @param array $block_attributes
     *            Saved attribute/option values from database.
     * @param string $content
     *            Saved content from database.
     * @param object $block
     *            saved Block (context) from database.
     */
    static function display_rest_start($block_attributes, $content = null, $block = null)
    {
        $postid = (empty($block) || empty($block->context['postId'])) ? 0 : $block->context['postId'];
        echo '<div id="' . $block_attributes['anchorId'] . '" class="' . $block_attributes['className']
            . ((isset($block_attributes['align'])) ? (' align' . $block_attributes['align']) : ' ') . '" ' . ' data-sib-id="' . $block_attributes['blockid']
            . '" data-sib-pid="' . $postid .  '" data-sib-st="start"' .'data-sib-apid="' . ((isset($block_attributes['postid'])) ? ($block_attributes['postid']) : ' ')
            . '" data-sib-ep="' . get_rest_url()
            . '" >';
        echo '<h3 class="widget-title block-title">' . $block_attributes['title'] . '</h3><p>';
        _e( 'Processing', 'simple-google-icalendar-widget');
        echo '</p></div>';
//        echo $context;
        echo '<div><button onclick="window.simpleIcalBlock.getBlockByIds({})" >' . __('Retry', 'simple-google-icalendar-widget') . '</button></div>';
    }
    
} // end class SimpleicalBlock