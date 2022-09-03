<?php
/*
 * SimpleicalWidgetAdmin.php
 * * Admin menus
 *
 * @package    Simple Google iCalendar Widget
 * @subpackage Admin
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2017 - 2022, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Version: 2.1.0
 * 20220410 namespaced and renamed after classname.
 * 2.1.0 option for comma seperated list of IDs
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalenderWidget;

class SimpleicalWidgetAdmin {
    //menu items
    /**
     * Back-end sub menu item to display widget help page.
     *
     * @see \WP_Widget::form()
     *
     * @param .
     */
    public function simple_ical_admin_menu() {
        
        //ther is no main menu item for simple_ical
        //this submenu is HIDDEN, however, we need to add it to create a page in the admin area
        add_submenu_page(null, //parent slug
            __('Info', 'simple_ical'), //page title
            __('Info', 'simple_ical'), //menu title
            'read', //capability read because it 's only info
            'simple_ical_info', //menu slug
            //			'simple_ical_info'); //function
            array($this, 'simple_ical_info')); //function
            
    }
    /**
     * Back-end widget help page.
     *
     * @see \WP_Widget::form()
     *
     * @param .
     */
    public function simple_ical_info () {
        //
        echo('<div class="wrap">');
        _e('<h2>Info on Simple Google Calendar Outlook Events Block Widget</h2>', 'simple_ical');
        _e('<h3>Settings for this block/widget: </h3>' );
        
        _e('<p><strong>Title</strong></p>', 'simple_ical');
        _e('<p>Title of this instance of the widget</p>', 'simple_ical');
        
        _e('<p><strong>Calendar ID(s), or iCal URL</strong></p>', 'simple_ical');
        _e('<p>The Google calendar ID, or the URL of te iCal file to display, or #example, or comma separated list of ID&apos;s.</p>', 'simple_ical');
        _e('<p>In the block you can use #example to get example events</p>', 'simple_ical');
        _e('<p>Or a comma separated list of ID&apos;s; optional you can add a html-class separated by a semicolon to some or all ID&apos;s to distinguish the descent in the lay-out of the event.</p>', 'simple_ical');
        
        _e('<p><strong>Number of events displayed</strong></p>', 'simple_ical');
        _e('<p>The maximum number of events to display.</p>', 'simple_ical');
        
        _e('<p><strong>Number of days after today with events displayed</strong></p>', 'simple_ical');
        _e('<p>Last date to display events in number of days after today.</p>', 'simple_ical');
        
        _e('<p><strong>Toggle start with summary</strong></p>', 'simple_ical');
        _e('<p>Start with summary before first date line. Default: Off, summary after first date line.</p>', 'simple_ical');
        _e('<p>Only available in block.</p>', 'simple_ical');
        
        _e('<p><strong>Date format first line</strong></p>', 'simple_ical');
        _e('<p>Start date format first (date) line. Default: l jS \of F,<br>l = day of the week (Monday); j =  day of the month (25) F = name of month (december)<br>y or Y = Year (17 or 2017);<br>make empty if you don\'t want to show this date.<br>Although this is intended for date all date and time fields contain date and time so you can als use time formats in date fields and date formats in time field<br>You can also use other text or simple html tags to embellish or emphasize it<br>escape characters with special meaning with a slash(\) e.g.:&lt;\b&gt;\F\r\o\m l jS \of F.&lt;/\b&gt;<br>see also https://www.php.net/manual/en/datetime.format.php .</p>', 'simple_ical');
        
        _e('<p><strong>End date format first line</strong></p>', 'simple_ical');
        _e('<p>End date format first (date) line. Default: empty, no display.<br>End date will only be shown as date part is different from start date and format not empty.</p>', 'simple_ical');
        _e('<p>Only available in block.</p>', 'simple_ical');
        
        _e('<p><strong>Time format start time after summary</strong></p>', 'simple_ical');
        _e('<p>Start time format summary line. Default: G:i ,<br>G or g = 24 or 12 hour format of an hour without leading zeros<br>i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem<br>make empty if you don\'t want to show this field.<br>Linebreak before this field will be removed when summary is before first date line, if desired you can get it back by starting the format with &lt;\b\r&gt;<br>This field will only be shown when date part of enddate is equal to start date and format not empty.</p>', 'simple_ical');
        
        _e('<p><strong>Time format end time after summary</strong></p>', 'simple_ical');
        _e('<p>End time format summary line. Default: empty , no display.</p>', 'simple_ical');
        _e('<p>Only available in block.</p>', 'simple_ical');
        
        _e('<p><strong>Time format start time</strong></p>', 'simple_ical');
        _e('<p>Time format start time. Default: G:i,<br>G or g = 24 or 12 hour format of an hour without leading zeros<br>i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem<br>make empty if you don\'t want to show start time.<br>You can also use other text to embellish it<br>escape characters with special meaning with a slash(\) e.g.:\F\r\o\m G:i .<br>This field will only be shown when date part of enddate is equal to start date and format not empty.</p>', 'simple_ical');
        
        _e('<p><strong>Time format end time</strong></p>', 'simple_ical');
        _e('<p>Time format separator and end time. Default:  - G:i,<br>G or g = 24 or 12 hour format of an hour without leading zeros<br>i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem<br>make empty if you don\'t want to show end time.<br>You can also use other text to embellish it<br>escape characters with special meaning with a slash(\)e.g.: \t\o G:i .<br>This field will only be shown when date part of enddate is equal to start date and format not empty.</p>', 'simple_ical');

        _e('<h3>Advanced settings</h3>' );
        
        _e('<p><strong>Calendar cache expiration time in minutes</strong></p>', 'simple_ical');
        _e('<p>Minimal time in minutes between reads from calendar source.</p>', 'simple_ical');
        
        _e('<p><strong>Excerpt length</strong></p>', 'simple_ical');
        _e('<p>Max length of the description in characters.<br>If there is a space or end-of-line character within 10 characters of this end, break there.<br>Note, not all characters have the same width, so the number of lines is not completely fixed by this. So you need additional CSS for that.<br><b>Warning:</b> If you allow html in the description, necessary end tags may disappear here.<br> Default: empty, all characters will be displayed</p>', 'simple_ical');
        
        _e('<p><strong>Tag for summary</strong></p>', 'simple_ical');
        _e('<p>Tag for summary. Choose a tag from the list. Default: a (link)<br>When using bootstrap or other collapse css and java-script the description is collapsed and wil be opened bij clicking on the summary link.<br>Link is not included with the other tags.<br>If not using bootstrap h4, div or strong may be a better choice then a..</p>', 'simple_ical');
        _e('<p>Only available in block.</p>', 'simple_ical');
        
        
        _e('<p><strong>Suffix group class</strong></p>', 'simple_ical');
        _e('<p>Suffix to add after css-class around the event (list-group),<br>start with space to keep the original class and add another class.</p>', 'simple_ical');
        
        _e('<p><strong>Suffix event start class</strong></p>', 'simple_ical');
        _e('<p>Suffix to add after the css-class around the event start line (list-group-item),<br>start with space to keep the original class and add another class.<br>E.g.:  py-0 with leading space; standard bootstrap 4 class to set padding top and bottom  to 0;  ml-1 to set margin left to 0.25 rem</p>', 'simple_ical');
        
        _e('<p><strong>Suffix event details classs</strong></p>', 'simple_ical');
        _e('<p>Suffix to add after the css-class around the event details link (ical_details),<br>start with space to keep the original class and add another class.</p>', 'simple_ical');

        _e('<p><strong>Checkbox Allow safe html in description and summary.</strong></p>', 'simple_ical');
        _e('<p>Check checkbox to allow the use of some safe html in description and summary,<br>otherwise it will only be displayed as text.</p>', 'simple_ical');
        
        _e('<p><strong>Checkbox clear cache now.</strong></p>', 'simple_ical');
        _e('<p>Check checkbox to clear cache now, otherwise it will be cleared after cache time is expired.<br>(to prevent very much calls to agenda, returns to false in block-editor after a short time)<br>In the (legacy)widget clears cache only on save.</p>', 'simple_ical');
        
        _e('<p><strong>Button Reset ID</strong></p>', 'simple_ical');
        _e('<p>(only in block) Press button Reset ID to copy the blockid from the clientid in the editor after duplicating the block, to make blockid unique again.</p>', 'simple_ical');
        
        
        _e('<p><strong>HTML anchor</strong></p>', 'simple_ical');
        _e('<p>HTML anchor for this block.<br>Type one or two words — no spaces — to create a unique web address for this block, called an “anchor.” Then you can link directly to this section on your page.<br>You can als use this ID to make parts of your extra css specific for this block</p>', 'simple_ical');
        _e('<p>Only available in block.</p>', 'simple_ical');
        
        
        echo('</div>');
        
    }
    // info in admin-menu
}