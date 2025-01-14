<?php
/*
 * renderssv3.php
 *
 * @package Simple Google iCalendar Block
 * @subpackage Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2024 - 2025, Bram Waasdorp
 * call server side render for block.json v3
 * available variables:
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 * 
 * version 2.6.0
 * 2.6.0 escape output, use namespace
  * 2.4.3 created to replace render_callback option in server side register_block_type    
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
echo wp_kses_post(SimpleicalHelper::render_block($attributes));
