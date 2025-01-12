<?php
/*
 * renderssv3.php
 *
 * @package Simple Google iCalendar Block
 * @subpackage Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2024 - 2024, Bram Waasdorp
 * call server side render for block.json v3
 * available variables:
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 * 
 * version 2.6.0
 * 2.6.0 escape output
  * 2.4.3 created to replace render_callback option in server side register_block_type    
 */
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock;

if (!class_exists('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock')) {
    require_once( 'SimpleicalBlock.php' );
    class_alias('WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalBlock', 'SimpleicalBlock');
}
?>
<?php echo wp_kses_post(SimpleicalBlock::render_block($attributes));
