<?php 
/*
 * widget-admin.php
 * * Admin menus
 *
 * @package    Simple Google iCalendar Widget
 * @subpackage Admin
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2017, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Version: 0.6.7
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
	
	_e('<p><strong>Suffix group class</strong></p>', 'simple_ical');
	_e('<p>Suffix to add after css-class around the event (list-group),<br>start with space to keep the original class and add another class.</p>', 'simple_ical');
	
	_e('<p><strong>Suffix event start class</strong></p>', 'simple_ical');
	_e('<p>Suffix to add after the css-class around the event start line (list-group-item),<br>start with space to keep the original class and add another class.<br>E.g.:  py-0 with leading space; standard bootstrap 4 class to set padding top and bottom  to 0;  ml-1 to set margin left to 0.25 rem</p>', 'simple_ical');
	
	_e('<p><strong>Suffix event details classs</strong></p>', 'simple_ical');
	_e('<p>Suffix to add after the css-class around the event details link (ical_details),<br>start with space to keep the original class and add another class.</p>', 'simple_ical');
	
	echo('</div>');
	
}
// CRUD actions in admin-menu
}



