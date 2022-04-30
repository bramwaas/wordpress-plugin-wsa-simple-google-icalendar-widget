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
        register_block_type( dirname(__DIR__) .'/block.json' );
   }
    /**
     * Back end (admin) block init
     *
     * see \WP_Widget::form()
     *
     * @param .
     */
    public function admin_init() {
             register_block_type( dirname(__DIR__) . '/block.json' );
    }
} // end class SimpleicalBlock