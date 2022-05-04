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
 * Version: 1.6.0
 * 20220427 namespaced and renamed after classname.
 * 20220430 try with static calls
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget;

class SimpleicalBlock {
    /**
     * Block init register block with help of block.json
     *
     * @param .
     */
    static function init_block() {
        register_block_type( dirname(__DIR__) .'/block.json',
        array('render_callback' => array('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget\SimpleicalBlock', 'render_block'))
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
       
       $output = '<ul>'. PHP_EOL;
       foreach ($block_attributes as $key => $value) {
           $output = $output . '<li>[' . $key . ']=' . $value . PHP_EOL;
       }
       $output = $output . '</ul>'. PHP_EOL;
           
       return '<div>' . $output . '</div>'. '<div class="content">' . $content . '</div>'  ;
  
    }
} // end class SimpleicalBlock