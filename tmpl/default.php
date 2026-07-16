<?php
/**
 * @version $Id: default.php
 * @package simpleicalblock
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
 */
// no direct access
defined('ABSPATH') or die ('Restricted access');

use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\IcsParser;
use WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalHelper;
/*
if (!empty($wa))$wa->addInlineStyle('.simple_ical_block p[hidden]{display:none !important;}', ['name' => 'simple-ical-block-inline-style']);
if (empty($secho)) {  $secho = ''; }

if (empty($nohead) ) {
    $attributes = SimpleicalHelper::render_attributes( $params->toArray());
    $secho .= '<div id="' . $attributes['anchorId']  .'" data-sib-id="' . $attributes['sibid'] . '" ' . ' class="simple_ical_block ' . $attributes['title_collapse_toggle']. '" >';
}
*/
/**
 * Front-end display of module, block or widget.
 *
 * @see
 *
 * @param array $attributes
 * @param string &$secho (reference to $secho), output to echo in calling function, to simplify escaping output by replacing multiple echoes by one
 *            Saved attribute/option values from database.
 * was static function display_block($attributes, &$secho)
 */
 // start
{
    $sn = 0;
    try {
        $attributes['tz_ui'] = new \DateTimeZone($attributes['tzid_ui']);
    } catch (\Exception $exc) {}
    if (empty($attributes['tz_ui']))
        try {
            $attributes['tzid_ui'] = str_replace('Etc/GMT ','Etc/GMT+',$attributes['tzid_ui']);
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
    $attributes['suffix_lg_class'] = self::sanitize_html_clss($attributes['suffix_lg_class']);
    $sflgi = self::sanitize_html_clss($attributes['suffix_lgi_class']);
    $sflgia = self::sanitize_html_clss($attributes['suffix_lgia_class']);
    if (empty($attributes['categories_display'])) {
        $cat_disp = false;
    } else {
        $cat_disp = true;
        $cat_sep = '</small>'.$attributes['categories_display'].'<small>';
    }
    if (! in_array($attributes['tag_sum'], self::$allowed_tags_sum))
        $attributes['tag_sum'] = 'a';
        $ipd = IcsParser::getData($attributes);
        $data = $ipd['data'];
        if (! empty($data) && is_array($data)) {
            $secho .= '<ul id="lg' .$attributes['anchorId'] .'" class="list-group' . $attributes['suffix_lg_class'] . ' simple-ical-widget '. $attributes['title_collapse_toggle'] . '" > ';
            $curdate = '';
            foreach ($data as $e) {
                $idlist = explode("@", $e->uid,2);
                $itemid = $attributes['sibid'] . '_' . strval(++ $sn) . '_' . $idlist[0];
                $evdate = wp_date($dflg, $e->start, $attributes['tz_ui']);
                $sameday = (wp_date('yz', $e->start, $attributes['tz_ui']) === wp_date('yz', $e->end, $attributes['tz_ui']));
                $ev_class = ((! empty($e->cal_class)) ? ' ' . sanitize_html_class($e->cal_class) : '');
                $cat_list = '';
                if (!empty($e->categories)) {
                    $ev_class = $ev_class . ' ' . implode( ' ', array_map( "sanitize_html_class", $e->categories ));
                    if ($cat_disp) {
                        $cat_list = '<div class="categories"><small>'
                            . implode($cat_sep,str_replace("\n", '<br>', $e->categories ))
                            . '</small></div>';
                    }
                }
                if (! $sameday ) {
                    $evdate = str_replace(array(
                        "</div><div>",
                        "</h4><h4>",
                        "</h5><h5>",
                        "</h6><h6>"
                    ), '', $evdate . wp_date($dflgend, $e->end - 1, $attributes['tz_ui']));
                }
                $evdtsum = (($e->startisdate === false) ? wp_date($dftsum, $e->start, $attributes['tz_ui']) . wp_date($dftsend, $e->end, $attributes['tz_ui']) : '');
                if ($layout < 2 && $curdate != $evdate) {
                    if ($curdate != '') {
                        $secho .= '</ul></li>';
                    }
                    $secho .= '<li class="list-group-item' . $sflgi . ' head">' . '<span class="ical-date">' . ucfirst($evdate) . '</span><ul class="list-group' . $attributes['suffix_lg_class'] . '">';
                }
                $secho .= '<li class="list-group-item' . $sflgi . $ev_class . '">';
                if ($layout == 3 && $curdate != $evdate) {
                    $secho .= '<span class="ical-date">' . ucfirst($evdate) . '</span>' . (('a' == $attributes['tag_sum']) ? '<br>' : '');
                }
                
                if ('summary' == $attributes['tag_sum']) {
                    $secho .= '<details class="ical_details' . $sflgia . '" id="'. $itemid. '">';
                }
                
                $secho .=  '<' . $attributes['tag_sum'] . ' class="ical_summary' . $sflgia . (('a' == $attributes['tag_sum']) ? '" data-toggle="collapse" data-bs-toggle="collapse" href="#' . $itemid . '" aria-expanded="false" aria-controls="' . $itemid . '">' : '">');
                if ($layout != 2) {
                    $secho .= $evdtsum;
                }
                if (! empty($e->summary)) {
                    $secho .= str_replace("\n", '<br>', $e->summary);
                }
                $secho .= '</' . $attributes['tag_sum'] . '>' . $cat_list;
                if ($layout == 2) {
                    $secho .= '<span>'. $evdate . $evdtsum . '</span>';
                }
                
                if ('summary' != $attributes['tag_sum']) {
                    $secho .= '<div class="ical_details' . $sflgia . (('a' == $attributes['tag_sum']) ? ' collapse' : '') . '" id="'. $itemid. '">';
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
                    $secho .= '<span class="dsc">'. $e->description. ((strrpos($e->description, '<br>') === (strlen($e->description) - 4)) ? '' : '<br>'). '</span>';
                }
                if ($e->startisdate === false && $sameday) {
                    $secho .= '<span class="time">'. wp_date($dftstart, $e->start, $attributes['tz_ui']). '</span><span class="time">'. wp_date($dftend, $e->end, $attributes['tz_ui']). '</span> ';
                } else {
                    $secho .= '';
                }
                if (! empty($e->location)) {
                    $secho .= '<span class="location">'. str_replace("\n", '<br>', $e->location). '</span>';
                }
                if ('summary' == $attributes['tag_sum']) {
                    $secho .= '</details></li>';
                } else {
                    $secho .= '</div></li>';
                }
                $curdate = $evdate;
            }
            if ($layout < 2) {
                $secho .= '</ul></li>';
            }
            $secho .= '</ul>';
            $secho .= $attributes['after_events'];
        } else {
            $secho .= $attributes['no_events'];
        }
        $secho .= '<br class="clear v310" />';
}
/* end display_block */
if (empty($nohead)) {
    $secho .= '</div>';
}

echo SimpleicalHelper::clean_output($secho);
$secho = '';


