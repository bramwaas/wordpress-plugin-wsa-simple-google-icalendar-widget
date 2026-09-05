<?php
/*
 * SimpleicalHelper.php
 *
 * @package Simple Google iCalendar Widget
 * @subpackage Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2022 - 2026, Bram Waasdorp
 * @link https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Gutenberg Block functions since v2.1.2 also used for widget.
 * Version: 3.1.0
 * 2.6.0 improve security by following Plugin Check recommendations; Moved functions common with Joomla to top. 
   rename SimpleicalBlock to SimpleicalHelper and register widget in this class. 
   Replace echo by $secho in &$secho param a.o. in display_block, to simplify escaping output by replacing multiple echoes by one. 
   known error: in wp 5.9.5 with elementor 3.14.1 aria-expanded and aria-controls are stripped bij wp_kses before wp 6.3.0 (see wp_kses.php) 
    issue is solved tested with wp 6.7.1 with elementor 3.26.5 . 
 * 2.6.1  Started simplifying (bootstrap) collapse by toggles for adding javascript and trigger collapse by title.
   Remove toggle to allow safe html in summary and description, save html is always allowed now.
   Sameday as logical and calculated with localtime instead of gmdate. Add titlenode to REST output. Removed ev_class from li head.
 * 2.7.0 Added cast $class to string in sanitize_html_clss, defaults for new collapse fields. Add support for details/summary tag combination.
 * 3.0.0 removed messages, (replaced by Notices and Warning in error_log)
 * 3.1.0 in response to PCP error replace get_block_wrapper_attributes() by expected result 
   'class="wp-block-simplegoogleicalenderwidget-simple-ical-block"'; // hardcoded untill (is_wp_version_compatible('5.6'));  
 * 3.1.3 extra widget SIB_SimpleicalWidgetNNS with no namespace as frontend for standard legacy widget SimpleicalWidget 
 * 3.2.0 use (overridable) layout files to display content. Get list of layout file-names as array          
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
// no direct access
defined('ABSPATH') or die ('Restricted access');

class SimpleicalHelper
{
    const SIB_ATTR = 'simple_ical_block_attrs';

/* 
 * tags allowed for summary
 *
 * @var array
 */
    static $allowed_tags_sum = [
        'a',
        'b',
        'div',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'i',
        'span',
        'strong',
        'summary',
        'u'
    ];

    /**
     * default value for block_attributes (or instance)
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
        'categories_filter_op' => '',
        'categories_filter' => '',
        'categories_display' => '',
        'add_sum_catflt' => false,
        'sib_layout' => '',
        'dateformat_lg' => 'l jS \of F',
        'dateformat_lgend' => '',
        'tag_title' => 'h3',
        'tag_sum' => 'a',
        'dateformat_tsum' => 'G:i ',
        'dateformat_tsend' => '',
        'dateformat_tstart' => 'G:i',
        'dateformat_tend' => ' - G:i ',
        'excerptlength' => '',
        'suffix_lg_class' => '',
        'suffix_lgi_class' => ' py-0',
        'suffix_lgia_class' => '',
        'allowhtml' => true,
        'after_events' => '',
        'no_events' => '',
        'clear_cache_now' => false,
        'period_limits' => '1',
        'rest_utzui' => '',
        'className' => '',
        'anchorId' => '',
        'title_collapse_toggle' => '',
        'add_collapse_code' => false,
        'before_widget' => '<div id="%1$s" %2$s>',
        'after_widget'  => '</div>'
    ];

    /**
     * block_attributes excluded from test if changed, because they (most of them) are changed during the save process.
     *
     * @var array
     */
    static  $exclude_test_attrs = [
        'saved' => null,
        '__internalWidgetId' => null,
        '_locale' => null,
        'tzid_ui' => null,
    ];
    /**
     * copied from WP sanitize_html_class, and added space as allowed character to accomodate multiple classes in one string.
     * Strips the string down to A-Z, ,a-z,0-9,_,-. If this results in an empty string then it will return the alternative value supplied.
     *
     * @param string $class
     * @param string $fallback
     * @return string sanitized class or fallback.
     */
    static function sanitize_html_clss( $class, $fallback = '' ) {
        // Strip out any %-encoded octets.
        $sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', (string) $class );
        
        // Limit to A-Z, ' ', a-z, 0-9, '_', '-'.
        $sanitized = preg_replace( '/[^A-Z a-z0-9_-]/', '', $sanitized );
        
        if ( '' === $sanitized && $fallback ) {
            return  $fallback;
        }
        return $sanitized;
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
     *            the block that is rendered
     * @return string HTML to render for the block (frontend)
     */
    static function render_block($block_attributes, $content = null, $block = null)
    {
        $block_attributes = array_merge(self::$default_block_attributes, [
            'title' => __('Events', 'simple-google-icalendar-widget'),
            'tzid_ui' => wp_timezone_string()
        ], $block_attributes);
        if (empty($block_attributes['sib_layout'])) {
            switch (($block_attributes['layout'])?? 3) {
                case 1:
                    $block_attributes['sib_layout'] = 'startdate-higher-level';
                    break;
                case 2:
                    $block_attributes['sib_layout'] = 'start-with-summary';
                    break;
                default:
                    $block_attributes['sib_layout'] = 'old-style';
            }
        }
        
        $block_attributes['anchorId'] = self::sanitize_html_clss($block_attributes['anchorId'], $block_attributes['sibid']);
        if (empty($block_attributes['tzid_ui'])) {
            $block_attributes['tzid_ui'] = wp_timezone_string();
        }
        if (empty($block_attributes['sibid']) && ! empty($block_attributes['blockid'])) {
            $block_attributes['sibid'] = $block_attributes['blockid'];
        }
        if  (empty($block_attributes['tag_title']))  $block_attributes['tag_title'] = 'h3';
        if (!empty($block_attributes['title_collapse_toggle'])){
            $block_attributes['title'] = ('<a data-toggle="collapse" data-bs-toggle="collapse" href="#lg' .$block_attributes['anchorId'] . '" role="button" aria-expanded="'.(('collapse' == $block_attributes['title_collapse_toggle'])?'false':'true').'" aria-controls="collapseMod">' . $block_attributes['title'] . '</a>');
        }
        $titlenode = '<' . $block_attributes['tag_title'] 
            .' class="widget-title block-title" data-sib-t="true">'
            . $block_attributes['title']
            . '</' . $block_attributes['tag_title'] . '>';
            
            $secho = '';
            switch ($block_attributes['wptype']) {
                case 'REST_t':
                    $secho .= $titlenode;
                case 'REST':
                    // Block displayed via REST
                    // more includes possible when more intances of the block are on the same page.
                    require self::getLayoutPath($block_attributes['sib_layout']);
                    // self::display_block($block_attributes, $secho);
                    break;
                case 'rest_ph':
                    // Placeholder starting point for REST processing display of block.
                    $wrapperattr = 'class="wp-block-simplegoogleicalenderwidget-simple-ical-block"'; // hardcoded untill (is_wp_version_compatible('5.6')) ? get_block_wrapper_attributes() : '';
                    try {
                        self::update_rest_attrs($block_attributes);
                    } catch (\Exception $e) {
                        $secho .= '<p>Caught exception: ' . $e->getMessage() . "</p>\n";
                        Log::log(Log::WARNING, 'Attributes not saved ' . 'Caught exception: ' . $e->getMessage());
                    }
                    require self::getLayoutPath('rest_ph/rest_client_placeholder');
                    break;
                case 'block':
                case 'ssr':
                    // Block rendered serverside, or in admin via serversiderenderer
                    $wrapperattr = 'class="wp-block-simplegoogleicalenderwidget-simple-ical-block"'; // hardcoded untill (is_wp_version_compatible('5.6')) ? get_block_wrapper_attributes() : '';
                    $secho .= sprintf($block_attributes['before_widget'], ($block_attributes['anchorId'] . '" data-sib-id="' . $block_attributes['sibid']), $wrapperattr);
                    if (! empty($block_attributes['title'])) {
                        $secho .= $titlenode;
                    }
                    // more includes possible when more intances of the block are on the same page.
                    require self::getLayoutPath($block_attributes['sib_layout']);
                    $secho .= $block_attributes['after_widget'];
                    break;
                default:
                    $secho .= "<!-- unknown wptype:" . $block_attributes['wptype'] . "-->" . PHP_EOL;
            }
            return $secho;
    }

    /**
     * In this function we are searching for (an override) template file (also called layout) in the theme etc or default in the plugin
     * It searches for the template file within the SIB_SLU ('simple-google-calendar-widget') directory, checking the following locations in order:
     * 1.
     * the active theme templates directory;
     * 2. the parent theme templates directory (if a child theme is in use);
     * 3. wp-includes//theme-compat/;
     * 4. the plugin directory/tmpl.
     * If the file is not found using the provided name, it searches again in the same order using the name 'default'.
     *
     * @param string $layout
     *            templatename
     *            
     * @return string ... found templatefile path for require_once / not found '' and Log error default.php should always be available.
     *        
     * @since 3.2.0
     */
    static function getLayoutPath($layout = 'default')
    {
        self::getLayoutFiles();
        $template_names[] = $layout . '.php';
        if ('default' != $layout)
            $template_names[] = 'default.php';

        foreach ((array) $template_names as $template_name) {
            if (! $template_name) {
                continue;
            }
            foreach (self::getLayoutDirs() as $dir) {
//                if ('rest' == substr($layout, 0, 4)) Log::log(Log::NOTICE, 'glp:' . $dir . $template_name );
                if (file_exists($dir . $template_name)) {
                    return $dir . $template_name;
                    break;
                }
            }
        }
        Log::log(Log::ERROR, '404 ' . SIB_TEMPLATES_DIR . 'default.php not found; plugin incomplete.');
        return SIB_TEMPLATES_DIR . 'error.php';
    }
    /**
     * Get an array of layout unique file names in layout path directories. In this function we are searching for (an override) template file (also called layout) in the theme etc or default in the plugin
     *
     * @param string $layout templatename
     *
     * @return array found file names.
     *
     * @since 3.2.0
     */
    static function getLayoutFiles()
    {
// Translations of default layout names, only used to create translations for names that are in variables. Does it work ???
        $defaultLayoutsTranslations = [ __('Default', 'simple-google-icalendar-widget'),
          __('Startdate higher level', 'simple-google-icalendar-widget'),
          __('Start with summary', 'simple-google-icalendar-widget'),
          __('Old style', 'simple-google-icalendar-widget'),
        ];
        
        $fnames = [];
        $lfns=[];
        $dirlst = implode(',', self::getLayoutDirs());
        $files = glob("{".$dirlst."}*.php",  GLOB_BRACE);
//        Log::log(Log::NOTICE, 'getLayoutFiles:' . "{".$dirlst."}");
        foreach ($files as $file) {
            $fnames[] = strtolower(basename($file, '.php'));
       }
        asort($fnames,  SORT_NATURAL | SORT_FLAG_CASE );
        foreach (array_unique($fnames) as $key ) {
            if (false === strrpos($key,'_')) {
                $obj = new \stdClass;
                $obj->value = $key;
                $obj->label = __(ucfirst(strtr($key, ['-' => ' '])),'simple-google-icalendar-widget');
                $lfns[] = $obj;
            }
        }
        return $lfns;
    }

    /**
     * Get an array of layout path directories in correct order.
     * It returns the template file directories within the SIB_SLUG ('simple-google-calendar-widget') directory, checking the following locations in order:
     * 1. the active theme templates directory;
     * 2. the parent theme templates directory (if a child theme is in use);
     * 3. wp-includes//theme-compat/;
     * 4. the plugin directory/tmpl.
     *
     * @param string $layout
     *            templatename
     *            
     * @return array found layout directories in corrst order.
     *        
     * @since 3.2.0
     */
    static function getLayoutDirs()
    {
        $dir = get_stylesheet_directory() . '/templates/' . SIB_SLUG . '/';
        if (is_dir($dir))
            $layoutdirs[] = $dir;
        if (is_child_theme()) {
            $dir = get_template_directory() . '/templates/' . SIB_SLUG . '/';
            if (is_dir($dir))
                $layoutdirs[] = $dir;
        }
        $dir = ABSPATH . WPINC . '/theme-compat/' . SIB_SLUG . '/';
        if (is_dir($dir))
            $layoutdirs[] = $dir;
        // SIB_TEMPLATES_DIR should always be avalable and a dir.
        $layoutdirs[] = SIB_TEMPLATES_DIR;
//        Log::log(Log::NOTICE, implode(', ', $layoutdirs));
        return $layoutdirs;
    }
    
    /**
     * Compare attributes with those in widget option and changed
     * Save attributes in widget option for use in REST call (only when changed on other then excluded keys)
     *
     * @param array $instance
     *            attributes/instance to save $instance['sibid'] is used as (new) key when $w_number is empty.
     * @param string $prev_sibid
     *            Previous save sibid to remeove if sibid is changed.
     * @return when not changed true, succes new value sibid key, else false
     */
    static function update_rest_attrs($instance)
    {
        if (empty($instance)) return false;
        $instances = (get_option(self::SIB_ATTR));
        if (! is_array($instances)) $instances = [];
        
        if (! empty($instance['sibid'])) {
            if (! empty($instance['prev_sibid']) && isset($instances[$instance['prev_sibid']]) && ($instance['sibid'] != $instance['prev_sibid'])) {
                unset($instances[$instance['prev_sibid']]);
            }
            $new_instance = array_diff_assoc(array_merge($instance, self::$exclude_test_attrs), self::$default_block_attributes, self::$exclude_test_attrs);
            Log::log(Log::NOTICE, 'upd_rest_a ni sl:' . (($new_instance['sib_layout']) ?? 'empty'));
            if (!empty($instances[$instance['sibid']]) && array_diff_assoc(array_merge($instances[$instance['sibid']], self::$exclude_test_attrs), self::$exclude_test_attrs) == $new_instance){
                return true;
            }
            else {
                $new_instance['saved'] = gmdate('YmdHis');
                $instances[$instance['sibid']] = $new_instance;
                if (update_option(self::SIB_ATTR, $instances, true))
                    return $instance['sibid'];
            }
        }
        return false;
    }
    /**
     * Close html tags in html string
     * @params string $html String with HTML to repair
     * @return string, rpaired HTML string
     */
    static function closetags($html) {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }
    /**
     * Block init register block with help of block.json v3 plus
     *
     * @param
     *            .
     */
    static function init_block()
    {
        register_block_type(dirname(__DIR__) . '/block.json', array( )); 
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
            'categories_filter_op' => ['type' => 'string', 'enum' => ['','ANY','ALL','NOTANY','NOTALL'], 'default' => ''],
            'categories_filter' => ['type' => 'string', 'default' => ''],
            'categories_display' => ['type' => 'string', 'default' => ''],
            'add_sum_catflt' => ['type' => 'boolean', 'default' => false],    
            'sib_layout' => ['type' => 'string'],
            'layout' => ['type' => 'integer'],
            'cache_time' => ['type' => 'integer', 'default' => 60],
            'dateformat_lg' => ['type' => 'string', 'default' => 'l jS \of F'],
            'dateformat_lgend' => ['type' => 'string', 'default' => ''],
            'tag_title' => ['type' => 'string', 'enum' => self::$allowed_tags_sum, 'default' => 'h3'],
            'tag_sum' => ['type' => 'string', 'enum' => self::$allowed_tags_sum, 'default' => 'a'],
            'dateformat_tsum' => ['type' => 'string', 'default' => 'G:i '],
            'dateformat_tsend' => ['type' => 'string', 'default' => ''],
            'dateformat_tstart' => ['type' => 'string', 'default' => 'G:i'],
            'dateformat_tend' => ['type' => 'string', 'default' => ' - G:i '],
            'excerptlength' => ['type' => 'string', ''],
            'suffix_lg_class' => ['type' => 'string', 'default' => ''],
            'suffix_lgi_class' => ['type' => 'string', 'default' => ' py-0'],
            'suffix_lgia_class' => ['type' => 'string', 'default' => ''],
            'after_events' => ['type' => 'string', 'default' => ''],
            'no_events' => ['type' => 'string', 'default' => ''],
            'period_limits' => ['type' => 'string', 'enum' => ['1', '2', '3', '4'], 'default' => '1'],
            'rest_utzui' => ['type' => 'string', 'enum' => ['', '1', '2'], 'default' => ''],
            'clear_cache_now' => ['type' => 'boolean', 'default' => false],
            'anchorId' => ['type' => 'string', 'default' => ''],
            'title_collapse_toggle' => ['type' => 'string', 'enum' => [ '', 'collapse', 'collapse show']],
            'add_collapse_code' => ['type' => 'string', 'default' => ''],
            'blockid' => ['type' => 'string'],
            ],
            'render_callback' => array(
                'WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalHelper',
                'render_blockv2'
            )
        ));
    }
    /**
     * Render the content of the block v2
     *
     * see
     *
     * @param array $block_attributes
     *            the block attributes (that are changed from default therefore first merged with defaults.)
     * @param array $content
     *            as saved in post by save in ...block.js
     * @param object $block
     *            the block that is rendered
     * @return string escaped HTML to render for the block (frontend)
     */
    static function render_blockv2($block_attributes, $content = null, $block = null)
    {
        return wp_kses(SimpleicalHelper::render_block($block_attributes),'post');
    }

    /**
     * Widget init register legacy widget
     *
     * @param
     *            .
     */
    static function simple_ical_widget ()
    {  register_widget( '\WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalWidget' );
       $sib_options = SimpleicalWidgetAdmin::get_plugin_options();
       if ($sib_options['simpleical_add_widget_nns']) {
         require  __DIR__ . DIRECTORY_SEPARATOR . 'SIB_SimpleicalWidgetNNS.php';
         register_widget( "SIB_SimpleicalWidgetNNS" );
       }
    }
} // end class SimpleicalHelper
