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
 * version 2.5.1
  * 2.4.3 created to replace render_callback option in server side register_block_type    
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
echo SimpleicalHelper::render_block($attributes);
