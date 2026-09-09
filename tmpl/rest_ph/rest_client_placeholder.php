<?php
/**
 * @version $Id: rest-client-placeholder.php
 * @package simpleicalblock
 * @copyright Copyright (C) 2026 -2026 simpleicalblock, All rights reserved.
 * @license GNU General Public License version 3 or later
 * @author url: https://www.waasdorpsoekhan.nl
 * @author email contact@waasdorpsoekhan.nl
 * @developer A.H.C. Waasdorp
 *
 *
 * simpleicalblock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * creates a placeholder to be populated by the view JavaScript. Via data-sib-utzui is communicated if a client timezone is needed. 

 * 3.2.0 created for wordpress plugin.
 */
// no direct access
defined('ABSPATH') or die ('Restricted access');

$secho .= sprintf($block_attributes['before_widget'], ($block_attributes['anchorId'] . '" data-sib-id="' . $block_attributes['sibid'] . '" data-sib-utzui="' . $block_attributes['rest_utzui'] . '" data-sib-st="0-start' ), $wrapperattr);
if (! empty($block_attributes['title'])) {
    $secho .= $titlenode;
}
$secho .= '<p>';
$secho .= __('Processing', 'simple-google-icalendar-widget');
$secho .= '</p>';
$secho .= $block_attributes['after_widget'];

