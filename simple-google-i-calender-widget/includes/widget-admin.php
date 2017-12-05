<?php 
/*
 * widget-admin.php
 * * Admin menus
 *
 * @package    wsa allotment
 * @subpackage Admin
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2017, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsaallotment
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Version: 0.6.4
*/
//menu items
function wsaallotment_admin_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page(__('Allotments', 'wsaallotment'), //page title
			__('Allotments', 'wsaallotment'), //menu title
			'member_administration', //capabilities
			'wsaallotment_allotments_list', //menu slug
			'wsaallotment_allotments_list' //function
			);
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
			__('Update section', 'wsaallotment'), //page title
			__('Update', 'wsaallotment'), //menu titlemanage_options
			'member_administration', //capability
			'wsaallotment_section_update', //menu slug
			'wsaallotment_section_update'); //function
			
add_submenu_page('wsaallotment_allotments_list', //parent slug
		__('Info', 'wsaallotment'), //page title
		__('Info', 'wsaallotment'), //menu title
		'member_administration', //capability
		'wsaallotment_info', //menu slug
		'wsaallotment_info'); //function
		
}
function wsaallotment_info () {
	//
	echo('<div class="wrap">');
	_e('<h2>Info</h2><p>', 'wsaallotment');
	
	_e('<strong>Closed shortcodes.</strong></p><p>', 'wsaallotment');
	
	_e('Format: [shortcode]...text to be edited ...[/shortcode]</p><p>', 'wsaallotment');
	
	_e('The text between the startshortcode [shortcode] and the endshortcode [/shortcode] wil be edited. In this plugin the content is visble or not.</p><p>', 'wsaallotment');
	
	_e('<strong>Examples:</strong></p><p>', 'wsaallotment');
	
	echo ('<strong>is_gardener:</strong></p><p>');
	
	_e('[is_gardener]shows this content when user is a gardener[/is_gardener]</p><p>', 'wsaallotment');
	
	echo('<strong>not_gardener</strong>:</p><p>');
	
	_e('[not_gardener]shows this content when user is not a gardener or is not logged in[/not_gardener]</p><p>', 'wsaallotment');
	
	echo('<strong>has_allotment</strong>:</p><p>');
	
	_e('[has_allotment]shows this content when user is a gardener and has an allotment[/has_allotment]</p><p>', 'wsaallotment');
	
	echo('<strong>not_allotment</strong>:</p><p>');
	
	_e('[not_allotment]shows this content when user has not an allotment, is not a gardener or is not logged in[/not_allotment]</p><p>', 'wsaallotment');
	
	_e('<strong>Open (simple)  shortcodes.</strong></p><p>', 'wsaallotment');
	
	_e('Format: [shortcode]</p><p>', 'wsaallotment');
	
	_e('The shortcode is replaced by content from plugin.</p><p>', 'wsaallotment');
	
	_e('<strong>Examples:</strong></p><p>', 'wsaallotment');
	
	_e('<strong>view_gardener,</p><p>', 'wsaallotment');
	_e('</strong>Shows a view on the gardener information we have about this user:</p><p>', 'wsaallotment');
	
	echo('[view_gardener]</p><p>');
	
	_e('<strong>view_allotment,</p><p>', 'wsaallotment');
	_e('</strong>Shows a view on the alottment information we have about this user:</p><p>', 'wsaallotment');
	echo('[view_allotment]</p><p>');
	echo('</p></div>');
	
	
	
	
}