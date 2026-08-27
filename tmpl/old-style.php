<?php
/**
 * @version $Id: old_style.php
 * @package simpleicalblock
 * 
 * @package Simple Google iCalendar Widget
 * @subpackage Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2022 - 2026, Bram Waasdorp
 * @link https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Gutenberg Block functions since v2.1.2 also used for widget.
 * Version: 3.2.0
 * v3.2.0 To make layout of output overridable: display_block() with optional code depending on layout attribute replaced by different template files for each layout option.
 * layout options 1 => startdate_higher_level, 2 => start_with_summary 3 => old_style.             
 */
// no direct access
defined('ABSPATH') or die('Restricted access');

use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\IcsParser;
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalHelper;
/**
 * Front-end display of module, block or widget for layout old_style.
 *
 * @param array $block_attributes
 * @param
 *            string &$secho (reference to $secho), output to echo in calling function, to simplify escaping output by replacing multiple echoes by one
 *            Saved attribute/option values from database.
 *            was static function display_block($block_attributes, &$secho)
 */
// start
{
    $sn = 0;
    try {
        $block_attributes['tz_ui'] = new \DateTimeZone($block_attributes['tzid_ui']);
    } catch (\Exception $exc) {}
    if (empty($block_attributes['tz_ui']))
        try {
            $block_attributes['tzid_ui'] = str_replace('Etc/GMT ', 'Etc/GMT+', $block_attributes['tzid_ui']);
            $block_attributes['tz_ui'] = new \DateTimeZone($block_attributes['tzid_ui']);
        } catch (\Exception $exc) {}
    if (empty($block_attributes['tz_ui']))
        try {
            $block_attributes['tzid_ui'] = wp_timezone_string();
            $block_attributes['tz_ui'] = new \DateTimeZone($block_attributes['tzid_ui']);
        } catch (\Exception $exc) {}
    if (empty($block_attributes['tz_ui'])) {
        $block_attributes['tzid_ui'] = 'UTC';
        $block_attributes['tz_ui'] = new \DateTimeZone('UTC');
    }
    $dflg = (isset($block_attributes['dateformat_lg'])) ? $block_attributes['dateformat_lg'] : 'l jS \of F';
    $dflgend = (isset($block_attributes['dateformat_lgend'])) ? $block_attributes['dateformat_lgend'] : '';
    $dftsum = (isset($block_attributes['dateformat_tsum'])) ? $block_attributes['dateformat_tsum'] : 'G:i ';
    $dftsend = (isset($block_attributes['dateformat_tsend'])) ? $block_attributes['dateformat_tsend'] : '';
    $dftstart = (isset($block_attributes['dateformat_tstart'])) ? $block_attributes['dateformat_tstart'] : 'G:i';
    $dftend = (isset($block_attributes['dateformat_tend'])) ? $block_attributes['dateformat_tend'] : ' - G:i ';
    $excerptlength = (isset($block_attributes['excerptlength']) && ' ' < trim($block_attributes['excerptlength'])) ? (int) $block_attributes['excerptlength'] : '';
    $block_attributes['suffix_lg_class'] = SimpleicalHelper::sanitize_html_clss($block_attributes['suffix_lg_class']);
    $sflgi = SimpleicalHelper::sanitize_html_clss($block_attributes['suffix_lgi_class']);
    $sflgia = SimpleicalHelper::sanitize_html_clss($block_attributes['suffix_lgia_class']);
    if (empty($block_attributes['categories_display'])) {
        $cat_disp = false;
    } else {
        $cat_disp = true;
        $cat_sep = '</small>' . $block_attributes['categories_display'] . '<small>';
    }
    if (! in_array($block_attributes['tag_sum'], SimpleicalHelper::$allowed_tags_sum))
        $block_attributes['tag_sum'] = 'a';
    $ipd = IcsParser::getData($block_attributes);
    $data = $ipd['data'];
    if (! empty($data) && is_array($data)) {
        $secho .= '<ul id="lg' . $block_attributes['anchorId'] . '" class="list-group' . $block_attributes['suffix_lg_class'] . ' simple-ical-widget ' . $block_attributes['title_collapse_toggle'] . '" > ';
        $curdate = '';
        foreach ($data as $e) {
            $idlist = explode("@", $e->uid, 2);
            $itemid = $block_attributes['sibid'] . '_' . strval(++ $sn) . '_' . $idlist[0];
            $evdate = wp_date($dflg, $e->start, $block_attributes['tz_ui']);
            $sameday = (wp_date('yz', $e->start, $block_attributes['tz_ui']) === wp_date('yz', $e->end, $block_attributes['tz_ui']));
            $ev_class = ((! empty($e->cal_class)) ? ' ' . sanitize_html_class($e->cal_class) : '');
            $cat_list = '';
            if (! empty($e->categories)) {
                $ev_class = $ev_class . ' ' . implode(' ', array_map("sanitize_html_class", $e->categories));
                if ($cat_disp) {
                    $cat_list = '<div class="categories"><small>' . implode($cat_sep, str_replace("\n", '<br>', $e->categories)) . '</small></div>';
                }
            }
            if (! $sameday) {
                $evdate = str_replace(array(
                    "</div><div>",
                    "</h4><h4>",
                    "</h5><h5>",
                    "</h6><h6>"
                ), '', $evdate . wp_date($dflgend, $e->end - 1, $block_attributes['tz_ui']));
            }
            $evdtsum = (($e->startisdate === false) ? wp_date($dftsum, $e->start, $block_attributes['tz_ui']) . wp_date($dftsend, $e->end, $block_attributes['tz_ui']) : '');
            $secho .= '<li class="list-group-item' . $sflgi . $ev_class . '">';
            if ($curdate != $evdate) {
                $secho .= '<span class="ical-date">' . ucfirst($evdate) . '</span>' . (('a' == $block_attributes['tag_sum']) ? '<br>' : '');
            }
            if ('summary' == $block_attributes['tag_sum']) {
                $secho .= '<details class="ical_details' . $sflgia . '" id="' . $itemid . '">';
            }

            $secho .= '<' . $block_attributes['tag_sum'] . ' class="ical_summary' . $sflgia . (('a' == $block_attributes['tag_sum']) ? '" data-toggle="collapse" data-bs-toggle="collapse" href="#' . $itemid . '" aria-expanded="false" aria-controls="' . $itemid . '">' : '">');
            $secho .= $evdtsum;
            if (! empty($e->summary)) {
                $secho .= str_replace("\n", '<br>', $e->summary);
            }
            $secho .= '</' . $block_attributes['tag_sum'] . '>' . $cat_list;

            if ('summary' != $block_attributes['tag_sum']) {
                $secho .= '<div class="ical_details' . $sflgia . (('a' == $block_attributes['tag_sum']) ? ' collapse' : '') . '" id="' . $itemid . '">';
            }

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
                $e->description = str_replace("\n", '<br>', $e->description);
                $secho .= '<span class="dsc">' . $e->description . ((strrpos($e->description, '<br>') === (strlen($e->description) - 4)) ? '' : '<br>') . '</span>';
            }
            if ($e->startisdate === false && $sameday) {
                $secho .= '<span class="time">' . wp_date($dftstart, $e->start, $block_attributes['tz_ui']) . '</span><span class="time">' . wp_date($dftend, $e->end, $block_attributes['tz_ui']) . '</span> ';
            } else {
                $secho .= '';
            }
            if (! empty($e->location)) {
                $secho .= '<span class="location">' . str_replace("\n", '<br>', $e->location) . '</span>';
            }
            if ('summary' == $block_attributes['tag_sum']) {
                $secho .= '</details></li>';
            } else {
                $secho .= '</div></li>';
            }
            $curdate = $evdate;
        }
        $secho .= '</ul>';
        $secho .= $block_attributes['after_events'];
    } else {
        $secho .= $block_attributes['no_events'];
        }
        $secho .= '<br class="clear v320 old style" />';
}
/* end display_block */


