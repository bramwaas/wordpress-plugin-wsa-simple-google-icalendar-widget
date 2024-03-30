<?php
/*
 * SimpleicalBlock.php
 *
 * @package Simple Google iCalendar Block
 * @subpackage Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2022 - 2024, Bram Waasdorp
 * @link https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Gutenberg Block functions since v2.1.2 also used for widget.
 * Version: 2.3.0
 * 20220427 namespaced and renamed after classname.
 * 20220430 try with static calls
 * 20220509 fairly correct front-end display. attributes back to block.json
 * 20220510 attributes again in php also added anchor, align and className who can be added by support hopefully that is enough for ServerSideRender.
 * 20220511 integer excerptlength not initialised with '' because serversiderender REST type validation gives an error (rest_invalid_type)
 * excerptlength string and in javascript '' when not parsed as integer.
 * 20220526 added example
 * 20220620 added enddate/times for startdate and starttime added Id as anchor and choice of tagg for summary, collaps only when tag_for summary = a.
 * 2.1.0 add calendar class to list-group-item
 * add htmlspecialchars() to summary, description and location when not 'allowhtml', replacing similar code from IcsParser
 * 2.1.1 20230401 use select 'layout' in stead of 'start with summary' to create more lay-out options.
 * 2.1.2 20230410 move assignment of cal_class to a place where e=>cal_class is available.
 * 2.1.3 20230418 Added optional placeholder HTML output when no upcoming events are avalable. Also added optional output after the events list (when upcoming events are available).
 * 2.1.5 20230824 default_block_attributes as static variable and alowed_tags_sum made public to use same values also in the widget, option anchorId moved to render_block.
 * 2.2.0 20240106 changed text domain to simple-google-icalendar-widget
 * 2.2.1 20240123 don't display description line when excerpt-length = 0
 * 2.3.0 remove definition of attributes, leave it to block.json
 * improvement of working with client timezone: add client timezone as an extra parameter to wp_date because date_default_timezone_set has no effect
 * block default version 3 version 2; add <span class="dsc"> to description output to make it easier to refer to in css
 * title with more wptypes, no display of empty title, title output secured with wp_kses (to display empty title line use <>.
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;

class SimpleicalBlock
{
    const SIB_ATTR = 'simple_ical_block_attrs';

    /**
     * tags allowed for summary
     *
     * @var array
     */
    static $allowed_tags_sum = [
        'a',
        'b',
        'div',
        'h4',
        'h5',
        'h6',
        'i',
        'span',
        'strong',
        'u'
    ];

    /**
     * deafault value for block_attributes (or instance)
     *
     * @var array
     */
    static $default_block_attributes = [
        'wptype' => 'block',
        'sibid' => '',
        'postid' => '0',
        'calendar_id' => '',
        'event_count' => 10,
        'event_period' => 92,
        'cache_time' => 60,
        'layout' => 3,
        'dateformat_lg' => 'l jS \of F',
        'dateformat_lgend' => '',
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
        'period_limits' => '1',
        'rest_utzui' => '',
        'className' => '',
        'anchorId' => '',
        'before_widget' => '<div id="%1$s" %2$s>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title block-title">',
        'after_title'   => '</h3>'
        
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
    }

    /**
     * Block init register block with help of block.json
     *
     * @param
     *            .
     */
    static function init_block_v2()
    {
        register_block_type(dirname(__DIR__) . '/v2/block.json', array(
            'attributes' => [
            'wptype' => ['type' => 'string'],
            'sibid' => ['type' => 'string'],
            'prev_sibid' => ['type' => 'string'],
            'postid' => ['type' => 'string'],
            'tzid_ui'=> ['type'=> 'string'],
            'title' => ['type' => 'string', 'default' => __('Events', 'simple-google-icalendar-widget')],
            'calendar_id' => ['type' => 'string', 'default' => ''],
            'event_count' => ['type' => 'integer', 'default' => 10],
            'event_period' => ['type' => 'integer', 'default' => 92],
            'layout' => ['type' => 'integer', 'default' => 3],
            'cache_time' => ['type' => 'integer', 'default' => 60],
            'dateformat_lg' => ['type' => 'string', 'default' => 'l jS \of F'],
            'dateformat_lgend' => ['type' => 'string', 'default' => ''],
            'tag_sum' => ['type' => 'string', 'enum' => self::$allowed_tags_sum, 'default' => 'a'],
            'dateformat_tsum' => ['type' => 'string', 'default' => 'G:i '],
            'dateformat_tsend' => ['type' => 'string', 'default' => ''],
            'dateformat_tstart' => ['type' => 'string', 'default' => 'G:i'],
            'dateformat_tend' => ['type' => 'string', 'default' => ' - G:i '],
            'excerptlength' => ['type' => 'string', ''],
            'suffix_lg_class' => ['type' => 'string', 'default' => ''],
            'suffix_lgi_class' => ['type' => 'string', 'default' => ' py-0'],
            'suffix_lgia_class' => ['type' => 'string', 'default' => ''],
            'allowhtml' => ['type' => 'boolean', 'default' => false],
            'after_events' => ['type' => 'string', 'default' => ''],
            'no_events' => ['type' => 'string', 'default' => ''],
            'period_limits' => ['type' => 'string', 'enum' => ['1', '2', '3', '4'], 'default' => '1'],
            'rest_utzui' => ['type' => 'string', 'enum' => ['', '1', '2'], 'default' => ''],
            'clear_cache_now' => ['type' => 'boolean', 'default' => false],
            'anchorId' => ['type' => 'string', 'default' => ''],
            'blockid' => ['type' => 'string'],
            ],
            'render_callback' => array(
                'WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock',
                'render_block'
            )
        ));
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
        $block_attributes = array_merge(self::$default_block_attributes, [
            'title' => __('Events', 'simple-google-icalendar-widget'),
            'tzid_ui' => wp_timezone_string()
        ], $block_attributes);
        $block_attributes['anchorId'] = sanitize_html_class($block_attributes['anchorId'], $block_attributes['sibid']);
        if (empty($block_attributes['tzid_ui'])) {
            $block_attributes['tzid_ui'] = wp_timezone_string();
        }
        ;
        if (empty($block_attributes['sibid']) && ! empty($block_attributes['blockid'])) {
            $block_attributes['sibid'] = $block_attributes['blockid'];
        }
        ;

        $output = '';
        ob_start();
        switch ($block_attributes['wptype']) {
            case 'REST':
                // Block displayed via REST
                self::display_block($block_attributes);
                break;
            case 'rest_ph':
                // Placeholder starting point for REST processing display of block or widget.
                if (false === stripos(' data-sib-t="true" ', $block_attributes['before_title'])) {
                    $l = explode('>', $block_attributes['before_title'], 2);
                    $block_attributes['before_title'] = implode(' data-sib-t="true" >', $l);
                }
                $wrapperattr = (is_wp_version_compatible('5.6')) ? get_block_wrapper_attributes() : '';
                echo sprintf($block_attributes['before_widget'], ($block_attributes['anchorId'] . '" data-sib-id="' . $block_attributes['sibid'] . '" data-sib-st="0-start' . ((empty($block_attributes['title'])) ? '" data-sib-notitle="true' : '')), $wrapperattr);
                echo $block_attributes['before_title'] . wp_kses($block_attributes['title'], 'post') . $block_attributes['after_title'] . '<p>';
                _e('Processing', 'simple-google-icalendar-widget');
                echo '</p>' . $block_attributes['after_widget'];
                try {
                    unset($block_attributes['before_widget'], $block_attributes['before_title'], $block_attributes['after_title'], $block_attributes['after_widget']);
                    self::update_rest_attrs($block_attributes);
                } catch (\Exception $e) {
                    echo '<p>Caught exception: ', $e->getMessage(), "</p>\n";
                }
                break;
            case 'block':
            case 'ssr':
                // Block rendered serverside, or in admin via serversiderenderer
                $wrapperattr = (is_wp_version_compatible('5.6')) ? get_block_wrapper_attributes() : '';
                echo sprintf($block_attributes['before_widget'], ($block_attributes['anchorId'] . '" data-sib-id="' . $block_attributes['sibid']), $wrapperattr);
                if (! empty($block_attributes['title'])) {
                    echo $block_attributes['before_title'] . wp_kses($block_attributes['title'], 'post') . $block_attributes['after_title'];
                }
                self::display_block($block_attributes);
                echo $attributes['after_widget'];
                break;
            default:
                echo "<!-- unknown wptype:" . $block_attributes['wptype'] . "-->" . PHP_EOL;
        }
        $output = $output . ob_get_clean();
        return $output;
    }

    /**
     * Front-end display of block or widget.
     *
     * @see
     *
     * @param array $attributes
     *            Saved attribute/option values from database.
     */
    static function display_block($attributes)
    {
        $sn = 0;
        try {
            $attributes['tz_ui'] = new \DateTimeZone($attributes['tzid_ui']);
        } catch (\Exception $exc) {}
        if (empty($attributes['tz_ui']))
            try {
                $attributes['tzid_ui'] = wp_timezone_string();
                $attributes['tz_ui'] = new \DateTimeZone($attributes['tzid_ui']);
            } catch (\Exception $exc) {}
        if (empty($attributes['tz_ui'])) {
            $attributes['tzid_ui'] = 'UTC';
            $attributes['tz_ui'] = new \DateTimeZone('UTC');
        }
        $layout = (isset($attributes['layout'])) ? $attributes['layout'] : 3;
        $dflg = (isset($attributes['dateformat_lg'])) ? $attributes['dateformat_lg'] : 'l jS \of F';
        $dflgend = (isset($attributes['dateformat_lgend'])) ? $attributes['dateformat_lgend'] : '';
        $dftsum = (isset($attributes['dateformat_tsum'])) ? $attributes['dateformat_tsum'] : 'G:i ';
        $dftsend = (isset($attributes['dateformat_tsend'])) ? $attributes['dateformat_tsend'] : '';
        $dftstart = (isset($attributes['dateformat_tstart'])) ? $attributes['dateformat_tstart'] : 'G:i';
        $dftend = (isset($attributes['dateformat_tend'])) ? $attributes['dateformat_tend'] : ' - G:i ';
        $excerptlength = (isset($attributes['excerptlength']) && ' ' < trim($attributes['excerptlength'])) ? (int) $attributes['excerptlength'] : '';
        $attributes['suffix_lg_class'] = wp_kses($attributes['suffix_lg_class'], 'post');
        $sflgi = wp_kses($attributes['suffix_lgi_class'], 'post');
        $sflgia = wp_kses($attributes['suffix_lgia_class'], 'post');
        if (! in_array($attributes['tag_sum'], self::$allowed_tags_sum))
            $attributes['tag_sum'] = 'a';
        $data = IcsParser::getData($attributes);
        if (! empty($data) && is_array($data)) {
            echo '<ul class="list-group' . $attributes['suffix_lg_class'] . ' simple-ical-widget" > ';
            $curdate = '';
            foreach ($data as $e) {
                $idlist = explode("@", esc_attr($e->uid));
                $itemid = $attributes['sibid'] . '_' . strval(++ $sn) . '_' . $idlist[0];
                $evdate = wp_kses(wp_date($dflg, $e->start, $attributes['tz_ui']), 'post');
                $cal_class = ((! empty($e->cal_class)) ? ' ' . sanitize_html_class($e->cal_class) : '');
                if (! $attributes['allowhtml']) {
                    if (! empty($e->summary))
                        $e->summary = htmlspecialchars($e->summary);
                    if (! empty($e->description))
                        $e->description = htmlspecialchars($e->description);
                    if (! empty($e->location))
                        $e->location = htmlspecialchars($e->location);
                }
                if (date('yz', $e->start) != date('yz', $e->end)) {
                    $evdate = str_replace(array(
                        "</div><div>",
                        "</h4><h4>",
                        "</h5><h5>",
                        "</h6><h6>"
                    ), '', $evdate . wp_kses(wp_date($dflgend, $e->end - 1, $attributes['tz_ui']), 'post'));
                }
                $evdtsum = (($e->startisdate === false) ? wp_kses(wp_date($dftsum, $e->start, $attributes['tz_ui']) . wp_date($dftsend, $e->end, $attributes['tz_ui']), 'post') : '');
                if ($layout < 2 && $curdate != $evdate) {
                    if ($curdate != '') {
                        echo '</ul></li>';
                    }
                    echo '<li class="list-group-item' . $sflgi . ' head">' . '<span class="ical-date">' . ucfirst($evdate) . '</span><ul class="list-group' . $attributes['suffix_lg_class'] . '">';
                }
                echo '<li class="list-group-item' . $sflgi . $cal_class . '">';
                if ($layout == 3 && $curdate != $evdate) {
                    echo '<span class="ical-date">' . ucfirst($evdate) . '</span>' . (('a' == $attributes['tag_sum']) ? '<br>' : '');
                }
                echo '<' . $attributes['tag_sum'] . ' class="ical_summary' . $sflgia . (('a' == $attributes['tag_sum']) ? '" data-toggle="collapse" data-bs-toggle="collapse" href="#' . $itemid . '" aria-expanded="false" aria-controls="' . $itemid . '">' : '">');
                if ($layout != 2) {
                    echo $evdtsum;
                }
                if (! empty($e->summary)) {
                    echo str_replace("\n", '<br>', wp_kses($e->summary, 'post'));
                }
                echo '</' . $attributes['tag_sum'] . '>';
                if ($layout == 2) {
                    echo '<span>', $evdate, $evdtsum, '</span>';
                }
                echo '<div class="ical_details' . $sflgia . (('a' == $attributes['tag_sum']) ? ' collapse' : '') . '" id="', $itemid, '">';
                if (! empty($e->description) && trim($e->description) > '' && $excerptlength !== 0) {
                    if ($excerptlength !== '' && strlen($e->description) > $excerptlength) {
                        $e->description = substr($e->description, 0, $excerptlength + 1);
                        if (rtrim($e->description) !== $e->description) {
                            $e->description = substr($e->description, 0, $excerptlength);
                        } else {
                            if (strrpos($e->description, ' ', max(0, $excerptlength - 10)) !== false or strrpos($e->description, "\n", max(0, $excerptlength - 10)) !== false) {
                                $e->description = substr($e->description, 0, max(strrpos($e->description, "\n", max(0, $excerptlength - 10)), strrpos($e->description, ' ', max(0, $excerptlength - 10))));
                            } else {
                                $e->description = substr($e->description, 0, $excerptlength);
                            }
                        }
                    }
                    $e->description = str_replace("\n", '<br>', wp_kses($e->description, 'post'));
                    echo '<span class="dsc">', $e->description, (strrpos($e->description, '<br>') === (strlen($e->description) - 4)) ? '' : '<br>', '</span>';
                }
                if ($e->startisdate === false && date('yz', $e->start) === date('yz', $e->end)) {
                    echo '<span class="time">', wp_kses(wp_date($dftstart, $e->start, $attributes['tz_ui']), 'post'), '</span><span class="time">', wp_kses(wp_date($dftend, $e->end, $attributes['tz_ui']), 'post'), '</span> ';
                } else {
                    echo '';
                }
                if (! empty($e->location)) {
                    echo '<span class="location">', str_replace("\n", '<br>', wp_kses($e->location, 'post')), '</span>';
                }
                echo '</div></li>';
                $curdate = $evdate;
            }
            if ($layout < 2) {
                echo '</ul></li>';
            }
            echo '</ul>';
            echo wp_kses($attributes['after_events'], 'post');
        } else {
            echo wp_kses($attributes['no_events'], 'post');
        }
        echo '<br class="clear" />';
    }

    /**
     * Save attributes in widget option for use in REST call (only when changed on other then excluded keys)
     *
     * @param array $instance
     *            attributes/instance to save $instance['sibid'] is used as (new) key when $w_number is empty.
     * @param string $prev_sibid
     *            Previous save sibid to remeove if sibid is changed.
     * @return succes new value sibid key else false
     */
    static function update_rest_attrs($instance)
    {
        if (empty($instance)) return false;
        $exclude = [
            'saved' => null,
            '__internalWidgetId' => null
        ];
        $instances = (get_option(self::SIB_ATTR));
        if (! is_array($instances)) $instances = [];

        if (! empty($instance['sibid'])) {
            if (! empty($instance['prev_sibid']) && isset($instances[$instance['prev_sibid']]) && ($instance['sibid'] != $instance['prev_sibid'])) {
                unset($instances[$instance['prev_sibid']]);
            }
            $new_instance = array_diff_assoc($instance, self::$default_block_attributes);
            if (!empty($instances[$instance['sibid']]) && ($instances[$instance['sibid']] + $exclude == $new_instance + $exclude)){
                return true;
            }
            else {
                $new_instance['saved'] = date('YmdHis');
                $instances[$instance['sibid']] = $new_instance;
                if (update_option(self::SIB_ATTR, $instances, true))
                    return $instance['sibid'];
            }
        }
        return false;
    }
} // end class SimpleicalBlock