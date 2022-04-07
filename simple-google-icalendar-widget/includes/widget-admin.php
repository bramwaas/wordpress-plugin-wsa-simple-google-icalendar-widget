<?php
/*
 * widget-admin.php
 * * Admin menus
 *
 * @package    Simple Google iCalendar Widget
 * @subpackage Admin
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2017 - 2022, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Version: 1.5.0
 */
class Simple_iCal_Admin {
    //menu items
    /**
     * Back-end sub menu item to display widget help page.
     *
     * @see WP_Widget::form()
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
     * @see WP_Widget::form()
     *
     * @param .
     */
    public function simple_ical_info () {
        //
        echo('<div class="wrap">');
        _e('<h2>Info on Simple Google iCal Calendar Widget</h2>', 'simple_ical');
        _e('<p>Arguments for this widget: </p>' );
        
        _e('<p><strong>Title</strong></p>', 'simple_ical');
        _e('<p>Title of this instance of the widget</p>', 'simple_ical');
        
        _e('<p><strong>Calendar ID, or iCal URL</strong></p>', 'simple_ical');
        _e('<p>The Google calendar ID, or the URL of te iCal file to display.</p>', 'simple_ical');
        
        _e('<p><strong>Number of events displayed</strong></p>', 'simple_ical');
        _e('<p>The maximum number of events to display.</p>', 'simple_ical');
        
        _e('<p><strong>Number of days after today with events displayed</strong></p>', 'simple_ical');
        _e('<p>Last date to display events in number of days after today.</p>', 'simple_ical');
        
        _e('<p><strong>Cache expiration time in minutes</strong></p>', 'simple_ical');
        _e('<p>Minimal time in minutes between reads from source.</p>', 'simple_ical');
        
        _e('<p><strong>Date format first line</strong></p>', 'simple_ical');
        _e('<p>Date format first line. Default: l jS \of F,<br>l = day of the week (Monday); j =  day of the month (25) F = name of month (december)<br>y or Y = Year (17 or 2017); see also https://www.php.net/manual/en/datetime.format.php .</p>', 'simple_ical');
        
        _e('<p><strong>Time format time summary line</strong></p>', 'simple_ical');
        _e('<p>Time format summary line. Default: G:i ,<br>G or g = 24 or 12 hour format of an hour without leading zeros<br>i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem<br>make empty if you don\'t want to show time.</p>', 'simple_ical');
        
        _e('<p><strong>Time format start time</strong></p>', 'simple_ical');
        _e('<p>Time format start time. Default: G:i,<br>G or g = 24 or 12 hour format of an hour without leading zeros<br>i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem<br>make empty if you don\'t want to show start time.<br>You can also use other text to embellish it<br>escape characters with special meaning with a slash(\) e.g.:\F\r\o\m G:i .</p>', 'simple_ical');
        
        _e('<p><strong>Time format end time</strong></p>', 'simple_ical');
        _e('<p>Time format separator and end time. Default:  - G:i,<br>G or g = 24 or 12 hour format of an hour without leading zeros<br>i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem<br>make empty if you don\'t want to show end time.<br>You can also use other text to embellish it<br>escape characters with special meaning with a slash(\)e.g.: \t\o G:i .</p>', 'simple_ical');
        
        _e('<p><strong>Excerpt length</strong></p>', 'simple_ical');
        _e('<p>Max length of the description in characters.<br>If there is a space or end-of-line character within 10 characters of this end, break there.<br>Note, not all characters have the same width, so the number of lines is not completely fixed by this. So you need additional CSS for that.<br><b>Warning:</b> If you allow html in the description, necessary end tags may disappear here.<br> Default: empty, all characters will be displayed</p>', 'simple_ical');
        
        _e('<p><strong>Suffix group class</strong></p>', 'simple_ical');
        _e('<p>Suffix to add after css-class around the event (list-group),<br>start with space to keep the original class and add another class.</p>', 'simple_ical');
        
        _e('<p><strong>Suffix event start class</strong></p>', 'simple_ical');
        _e('<p>Suffix to add after the css-class around the event start line (list-group-item),<br>start with space to keep the original class and add another class.<br>E.g.:  py-0 with leading space; standard bootstrap 4 class to set padding top and bottom  to 0;  ml-1 to set margin left to 0.25 rem</p>', 'simple_ical');
        
        _e('<p><strong>Suffix event details classs</strong></p>', 'simple_ical');
        _e('<p>Suffix to add after the css-class around the event details link (ical_details),<br>start with space to keep the original class and add another class.</p>', 'simple_ical');

        _e('<p><strong>Checkbox Allow safe html in description and summary.</strong></p>', 'simple_ical');
        _e('<p>Check checkbox to allow the use of some safe html in description and summary,<br>otherwise it will only be displayed as text.</p>', 'simple_ical');
        
        _e('<p><strong>Checkbox clear cache on save.</strong></p>', 'simple_ical');
        _e('<p>Check checkbox to clear cache on save, otherwise it will be cleared after cache time is expired.</p>', 'simple_ical');
        
        
        echo('</div>');
        
    }
    // info in admin-menu
}