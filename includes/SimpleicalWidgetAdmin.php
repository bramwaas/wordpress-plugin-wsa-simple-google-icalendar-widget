<?php
/*
 * SimpleicalWidgetAdmin.php
 * * Admin menus
 *
 * @package    Simple Google iCalendar Widget
 * @subpackage Admin
 * @author     Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright  Copyright (c)  2017 - 2025, Bram Waasdorp
 * @link       https://github.com/bramwaas/wordpress-plugin-wsa-simple-google-calendar-widget
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Version: 2.6.1
 * 20220410 namespaced and renamed after classname.
 * 2.1.0 option for comma seperated list of IDs
 * 2.1.3 block footer after events and placeholder when no events.
 * 2.2.0 fix spell-error in namespace, and use new correct text domain
 * 2.3.0 anchors (id) at several places in document
 * 2.4.2 replaced null by 'admin.php' to solve issue 'Deprecation warnings in PHP 8.3'  
 * 2.4.4 added tag_title and extra option for timzone settings 
 * 2.6.1  Started simplifying (bootstrap) collapse by toggles for adding javascript and trigger collapse by title.
   Remove toggle to allow safe html in summary and description, save html is always allowed now.      
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;

class SimpleicalWidgetAdmin {
    const SIB_OPTIONS = 'simple_ical_options';
    
// Start Options    
    /**
     * custom option and settings
     */
    function simple_ical_settings_init() {
        // Register a new setting for "simple_ical_options" page.
        register_setting( 'simpleical_options_form', 'simple_ical_options' );
        
        // Register a new section in the "simpleical" page.
        add_settings_section(
            'simpleical_section_developers',
            __( 'Plugin level settings.', 'simple-google-icalendar-widget' ),
            [$this, 'simpleical_section_developers_callback'],
            'simpleical_options_form'
            );
        // Register a new field in the "simpleical_section_developers" section, inside the "simpleical" page.
        add_settings_field(
            'simpleical_add_collapse_code', // As of WP 4.6 this value is used only internally.
            // Use $args' field_name to populate the id inside the callback.
            __( 'Site add BS collapse code', 'simple-google-icalendar-widget' ),
            [$this, 'simpleical_add_collapse_code_cb'],
            'simpleical_options_form',
            'simpleical_section_developers',
            array(
                'field_name'         => 'simpleical_add_collapse_code',
                'class'             => 'checkbox',
                'simpleical_custom_data' => 'custom',
                'field_desc' => __('Check checkbox to add Bootstrap collapse code in front-end (live site) when not provided by theme.', 'simple-google-icalendar-widget' ),
            )
            );
        // Register a new field in the "simpleical_section_developers" section, inside the "simpleical" page.
        add_settings_field(
            'simpleical_add_collapse_code_admin', // As of WP 4.6 this value is used only internally.
            // Use $args' field_name to populate the id inside the callback.
            __( 'Admin add BS collapse code', 'simple-google-icalendar-widget' ),
            [$this, 'simpleical_add_collapse_code_cb'],
            'simpleical_options_form',
            'simpleical_section_developers',
            array(
                'field_name'         => 'simpleical_add_collapse_code_admin',
                'class'             => 'checkbox',
                'simpleical_custom_data' => 'custom',
                'field_desc' => __('Check checkbox to add Bootstrap collapse code in admin (block editor).', 'simple-google-icalendar-widget' ),
            )
            );
    }
    /**
     * Developers section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
     */
    function simpleical_section_developers_callback( $args ) {
        ?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Bootstrap collapse code, javascript and css necessary for correct operation of collapse toggle html. If code is twice loaded it may not work. So don\'t check if code is already loaded e.g. by theme.', 'simple-google-icalendar-widget' ); ?>
    <br/><?php esc_html_e( '(Initial preview legacy widget uses front-end code)', 'simple-google-icalendar-widget' ); ?>
    </p>
    
	<?php
}

/**
 * Cecknox field callback function.
 *
 * WordPress has magic interaction with the following keys: field_name, class.
 * - the "field_name" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function simpleical_add_collapse_code_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
    $options = self::get_plugin_options();
	?>	 
	<input
			id="<?php echo esc_attr( $args['field_name'] ); ?>"
			data-custom="<?php echo esc_attr( $args['simpleical_custom_data'] ); ?>"
			name="simple_ical_options[<?php echo esc_attr( $args['field_name'] ); ?>]"
			type="checkbox" value="1"
			 <?php checked( '1', $options[ $args['field_name'] ] ); ?> 
			
			>
	</input>
	<p class="description">
		<?php echo wp_kses_post($args['field_desc']) ?>
	</p>
	<?php
}

/**
 * Add the top level menu page.
 */
function simple_ical_options_page() {
add_options_page(
//    add_menu_page(
	    __('Simple Google Calendar Outlook Events Widget options', 'simple-google-icalendar-widget' ), //title
	    __('Simple iCal Options','simple-google-icalendar-widget' ), //menuoption
	    'manage_options', //capability 
	    'simple_ical_options', //menu slug
	    [$this, 'simple_ical_options_page_html'] //function
	);
}

    
/**
 * Register our simple_ical_options_page to the admin_menu action hook.
 */
//add_action( 'admin_menu', 'simple_ical_options_page' );


/**
 * Top level menu callback function
 */
function simple_ical_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'simpleical_messages', 'simpleical_message', __( 'Settings Saved', 'simple-google-icalendar-widget' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'simpleical_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "simpleical_options_form"
			settings_fields( 'simpleical_options_form' );
			// output setting sections and their fields
			// (sections are registered for "simpleical_options_form", each field is registered to a specific section)
			do_settings_sections( 'simpleical_options_form' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>

	</div>
	<?php
}
/**
 * Gets options and assigns defaults if necessary.
 *
 * @param void
 * @return array of plugin options
 */
static function get_plugin_options(){
    $options = get_option(self::SIB_OPTIONS);
    if (! is_array($options)) $options = [];
    $options = array_merge([
        'simpleical_add_collapse_code' => false,
        'simpleical_add_collapse_code_admin' => false,
    ], $options);
    return $options;
}
// End options    
// Start info    
    //menu items
    /**
     * Back-end sub menu item to display widget help page.
     *
     * @see \WP_Widget::form()
     *
     * @param .
     */
    public function simple_ical_info_page() {
//       add_submenu_page('admin.php', //parent slug (or the file name of a standard WordPress admin page).
       add_options_page(
       __('Simple Google Calendar Outlook Events Widget Help', 'simple-google-icalendar-widget'), //page title
            __('Simple iCal Help', 'simple-google-icalendar-widget'), //menu title
            'read', //capability read because it 's only info
            'simple_ical_info', //menu slug
            array($this, 'simple_ical_info_page_html')); //function
            
    }
    /**
     * Back-end widget help page.
     *
     * @see  \WP_Widget::form()
     *
     * @param .
     */
    public function simple_ical_info_page_html () {
        //
        echo wp_kses_post( '<div class="wrap"><h2>' .
        __('Info on Simple Google Calendar Outlook Events Block Widget settings', 'simple-google-icalendar-widget') .
        '</h2><h3>' .
        __('Settings for this block/widget:' , 'simple-google-icalendar-widget') .
        '</h3><p><strong>'.
        __('Title', 'simple-google-icalendar-widget') .
        '</strong></p><p>' .
        __('Title of this instance of the widget', 'simple-google-icalendar-widget') .
        '</p><p>' .
        __('With empty title no html is displayed, if you want the html with empty content use &lt;&gt; or another invalid tag that will be filtered away as title', 'simple-google-icalendar-widget') .
        '</p>');
        
        echo wp_kses_post('<span id="calendar-id"></span>'.
        '<p><strong>'.
       __('Calendar ID(s), or iCal URL', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('The Google calendar ID, or the URL of te iCal file to display, or #example, or comma separated list of ID&apos;s.', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('You can use #example to get example events', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Or a comma separated list of ID&apos;s; optional you can add a html-class separated by a semicolon to some or all ID&apos;s to distinguish the descent in the lay-out of the event.', 'simple-google-icalendar-widget') .
       '</p><p><strong>'.
        __('Number of events displayed', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('The maximum number of events to display', 'simple-google-icalendar-widget') .
        '</p>');
        
        echo wp_kses_post('<span id="event-period"></span>'.
        '<p><strong>'.
       __('Number of days after today with events displayed</strong>', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Last date to display events in number of days after today.', 'simple-google-icalendar-widget').
            '</p>');
       echo wp_kses_post('<p><a href="#period-limits" target="_self" >' .
        __('See also', 'simple-google-icalendar-widget').
        '<strong> '.   
           __('Period limits', 'simple-google-icalendar-widget').
        '</strong></a></p>');
        
        echo wp_kses_post('<span id="layout"></span>'.
        '<p><strong>'.
       __('Select lay-out</strong>', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Startdate line on a higher level in the list; Start with summary before first date line; Old style, summary after first date line, remove duplicate date lines.', 'simple-google-icalendar-widget').
        '</p>');
        
        echo wp_kses_post('<span id="dateformat-lg"></span>'.
        '<p><strong>'.
       __('Date format first line</strong>', 'simple-google-icalendar-widget').
        '</p><p>'.
        __('Start date format first (date) line. Default: l jS \of F,', 'simple-google-icalendar-widget').
        '<br>' .
        __('l = day of the week (Monday); j =  day of the month (25) F = name of month (december)', 'simple-google-icalendar-widget').
            '<br>' .
        __('y or Y = Year (17 or 2017);<br>make empty if you don\'t want to show this date', 'simple-google-icalendar-widget').
            '<br>' .
        __('Although this is intended for date all date and time fields contain date and time so you can als use time formats in date fields and date formats in time field', 'simple-google-icalendar-widget').
        '<br>' .
        __('You can also use other text or simple html tags to embellish or emphasize it<br>escape characters with special meaning with a slash(\) e.g.:&lt;\b&gt;\F\r\o\m l jS \of F.&lt;/\b&gt;<br>see also https://www.php.net/manual/en/datetime.format.php .', 'simple-google-icalendar-widget').
        __('</p><p><strong>End date format first line</strong>', 'simple-google-icalendar-widget').
        '</p><p>'.
        __('End date format first (date) line. Default: empty, no display. End date will only be shown as date part is different from start date and format not empty.', 'simple-google-icalendar-widget').
         '</p><p><strong>'.
        __('Time format start time after summary', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Start time format summary line. Default: G:i ,<br>G or g = 24 or 12 hour format of an hour without leading zeros i = Minutes with leading zeros a or A = Lowercase or Uppercase Ante meridiem and Post meridiem. Make empty if you don\'t want to show this field.<br>Linebreak before this field will be removed when summary is before first date line, if desired you can get it back by starting the format with &lt;\b\r&gt; This field will only be shown when date part of enddate is equal to start date and format not empty.', 'simple-google-icalendar-widget').
       '</p><p><strong>' . 
        __('Time format end time after summary', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('End time format summary line. Default: empty , no display.', 'simple-google-icalendar-widget') .
        '</p><p><strong>' .
        __('Time format start time', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Time format start time. Default: G:i,<br>G or g = 24 or 12 hour format of an hour without leading zeros i = Minutes with leading zeros<br>a or A = Lowercase or Uppercase Ante meridiem and Post meridiem. Make empty if you don\'t want to show start time.<br>You can also use other text to embellish it<br>escape characters with special meaning with a slash(\) e.g.:\F\r\o\m G:i .<br>This field will only be shown when date part of enddate is equal to start date and format not empty.', 'simple-google-icalendar-widget') .
        '</p><p><strong>' .
        __('Time format end time', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Time format separator and end time. Default:  - G:i, G or g = 24 or 12 hour format of an hour without leading zeros i = Minutes with leading zeros a or A = Lowercase or Uppercase Ante meridiem and Post meridiem. Mmake empty if you don\'t want to show end time.<br>You can also use other text to embellish it<br>escape characters with special meaning with a slash(\)e.g.: \t\o G:i .<br>This field will only be shown when date part of enddate is equal to start date and format not empty.', 'simple-google-icalendar-widget') .
       '</p><h3>' . 
        __('Advanced settings', 'simple-google-icalendar-widget'));
        
        echo wp_kses_post('</h3><span id="meta-block-name"></span>'.
        '<p><strong>'.
       __('Change block name</strong>', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Change technical block name.', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Not implemented.', 'simple-google-icalendar-widget').
        '</p><p><strong>' .
        __('Calendar cache expiration time in minutes', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Minimal time in minutes between reads from calendar source.', 'simple-google-icalendar-widget').
        '</p><p><strong>'.
        __('Excerpt length', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Max length of the description in characters. If there is a space or end-of-line character within 10 characters of this end, break there. Note, not all characters have the same width, so the number of lines is not completely fixed by this. So you need additional CSS for that.<br><b>Warning:</b> If you allow html in the description, necessary end tags may disappear here.<br> Default: empty, all characters will be displayed.', 'simple-google-icalendar-widget').
       '</p>'); 
        echo wp_kses_post('<span id="tag-title"></span>'.
        '<p><strong>'.
       __('Tag for title', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Tag for title. Choose a tag from the list that matches your theme and location of the block on the page. Default: h3 (sub header)', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Only available in block.', 'simple-google-icalendar-widget').
        '</p>');
        
        echo wp_kses_post('<span id="tag-sum"></span>'.
        '<p><strong>'.
       __('Tag for summary', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Tag for summary. Choose a tag from the list. Default: a (link) When using bootstrap or other collapse css and java-script the description is collapsed and wil be opened bij clicking on the summary link.<br>Link is not included with the other tags. If not using bootstrap collapse h4, div or strong may be a better choice then a..', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Only available in block.', 'simple-google-icalendar-widget').
        '</p>');
        
        echo wp_kses_post('<span id="period-limits"></span>'.
        '<p><strong>'.
       __('Period limits', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Determination of start and end time of periode where events are displayed and timezone.<br>"Time of day", or "Whole  day"', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('With "Time of day" as limit at both ends: <br>The "Number of days after today" is the number of 24-hour periods after the current time. It is a window that moves as the day progresses.', 'simple-google-icalendar-widget') .
        '<br>' .    
        __('So, if today is Monday at 9am and you have a 3-day window, then events that start before 9am on Thursday will be shown, but an event that starts at 1pm will not.<br>As the day progresses, any of today&quot;s events that are completed before the current time will drop off the top of the list, and events that fall within the window will appear at the bottom. ', 'simple-google-icalendar-widget') .
        '<br>' .
        __('"Whole  Day" as limit moves the Start of the window to the beginning of the day (0:00 AM) in "local time" and/or moves the End to the beginning of the next day.', 'simple-google-icalendar-widget') .
        '</p>');

        echo wp_kses_post('<span id="rest_utzui"></span>'.
        '<p><strong>'.
       __('Use client timezone settings', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Default "Use WordPress timezone settings, no REST" all processing happens on server,<br>javascript is not needed in client browser. "local time" is measured in timezone of WordPress installation.', 'simple-google-icalendar-widget') .
       '<br>' .
       __('With "Use Client timezone settings, with REST" the timezone of client browser is used and processing happens with this timezone setting.', 'simple-google-icalendar-widget') .
       '<br>' .
       __('At first a placeholder with title and some Id\'s to use later is created and displayed.<br>After pageload the timezone of client browser is fetched with javascript to process the output and get it with a REST call,<br>then this output is placed over the placeholder.', 'simple-google-icalendar-widget') .
       '<br>' .
       __('With "Use WordPress timezone settings, with REST" timezone of WordPress installation is used.', 'simple-google-icalendar-widget') .
            '<br>' .
       __('At first a placeholder with title and some Id\'s to use later is created and displayed.<br>After pageload the output is fetched with a REST call, then this output is placed over the placeholder.', 'simple-google-icalendar-widget').
       '</p>');

        echo wp_kses_post('<span id="categories_filter"></span>'.
        '<p><strong>'.
       __('Categories Filter Operator', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Here you can choose how to compare the filter categories with the event categories.', 'simple-google-icalendar-widget').
       '</p><li>'.
       __('Default: Empty no filtering.', 'simple-google-icalendar-widget') . 
       '</li><li>' .
        __('ANY is true if at least one of the elements of the filter set is present in the event set, or in other words the filter set intersects the event set, the intersection contains at least one element. This seems to me to be the most practical operator.', 'simple-google-icalendar-widget') .
        '</li><li>' .
        __('ALL is true if all elements of the filter set exist in the event set, or in other words, the intersection contains the same number of elements as the filter set. The event set can contain other elements.', 'simple-google-icalendar-widget') .
        '</li><li>' .
        __('NOTANY is true if ANY is NOT true. The intersection is empty.', 'simple-google-icalendar-widget') .
        '</li><li>' .
        __('NOTALL is true if ALL is NOT true. The intersection contains fewer elements than the filter set.', 'simple-google-icalendar-widget').
        '</li><p>'.
       __('A special case are events without categories. In the filter, the plugin handles this as if the category were a null string ("").', 'simple-google-icalendar-widget') .
       '</p><p><strong>' .
        __('Categories Filter List', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('List of filter categories separated by a comma (not in double quotes). If a category contains a comma, you must add a backslash (\,) to it. A null string is created as a category if nothing is entered in the list or if the list ends with a comma, or if there are two comma separators immediately next to each other.', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Categories (at least in this plugin) behave like simple tags and have no intrinsic meaning or relationship. So if you want to select all events with category flower, rose or tulip, you have to add them all to the filter list. With category flower, you don\'t automatically select rose and tulip too.', 'simple-google-icalendar-widget').
       '</p><p><strong>' .
        __('Display categories with Separator', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Default: Empty, categories are not displayed. Here you can choose to display the list of event categories after the summary and with what separator. If you leave this field empty, the list will not be displayed.', 'simple-google-icalendar-widget').
        '</p><p><strong>' .
        __('Suffix group class', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Suffix to add after css-class around the event (list-group),<br>start with space to keep the original class and add another class.', 'simple-google-icalendar-widget').
        '</p><p><strong>' .
        __('Suffix event start class', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Suffix to add after the css-class around the event start line (list-group-item),<br>start with space to keep the original class and add another class.<br>E.g.:  py-0 with leading space; standard bootstrap 4 class to set padding top and bottom  to 0;  ml-1 to set margin left to 0.25 rem', 'simple-google-icalendar-widget').
       '</p><p><strong>' .
        __('Suffix event details classs', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Suffix to add after the css-class around the event details link (ical_details),<br>start with space to keep the original class and add another class.', 'simple-google-icalendar-widget').
        __('Closing HTML after available events.', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Closing (safe) HTML after events list, when events are available.<br><br>This text with simple HTML will be displayed after the events list.<br>Use &amp;lt; or &amp;gt; if you want to output &lt; or &gt; otherwise they may be removed as unknown and therefore unsafe tags.<br>E.g. &lt;hr class=&quot;module-ft&quot;	&gt;.', 'simple-google-icalendar-widget').
       '</p><p><strong>' .
        __('Closing HTML when no events.', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Closing (safe) HTML output when no events are available.<br>This text with simple HTML will be displayed instead of the events list.<br>Use &amp;lt; or &amp;gt; if you want to output &lt; or &gt; otherwise they may be removed as unknown and therefore unsafe tags.<br>E.g. &lt;p  class=&quot;warning&quot; &gt;&amp;lt; No events found. &amp;gt;&lt;\p&gt;&lt;hr class=&quot;module-ft&quot;&gt;.', 'simple-google-icalendar-widget').
       '</p><p><strong>' .
        __('Button Reset ID', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('Press button Reset ID to copy the sibid from the clientid in the editor after duplicating the block, to make sibid unique again.', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('In the legacy widget the bin2hex(random_bytes(7)) function is used to create a new sibid. Because this is not visible, the save button works only when also another field is changed.', 'simple-google-icalendar-widget').
        '</p><p>'.
       __('Since the transient cache id is derived from the block id, this also clears the data cache once.', 'simple-google-icalendar-widget') .
       '</p>');
        
        echo wp_kses_post('<span id="html-anchor"></span>'.
        '<p><strong>'.
       __('HTML anchor', 'simple-google-icalendar-widget').
        '</strong></p><p>'.
       __('HTML anchor for this block.<br>Type one or two words - no spaces - to create a unique web address for this block, called an "anchor". Then you can link directly to this section on your page.<br>You can als use this ID to make parts of your extra css specific for this block', 'simple-google-icalendar-widget').
        '</p>');

       echo wp_kses_post('<span id="title_collapse_toggle"></span>'.
           '<p><strong>' .
           __('Title as collapse toggle.', 'simple-google-icalendar-widget').
           '</strong></p><p>'.
           __('Use title link as collapse/show toggle for this module content.', 'simple-google-icalendar-widget').
           '</p><p><strong>' .
           __('Checkbox Add bootstrap collapse code.', 'simple-google-icalendar-widget').
       '</strong></p><p>'.
       __('Check checkbox to Add bootstrap collapse code (js and css) when not provided by theme', 'simple-google-icalendar-widget').
       '</p>' .
        __('Use plugin options form to add Bootstrap collapse code (js and css) when not provided by theme.', 'simple-google-icalendar-widget') .
        '<p><a href="' .
        esc_url(admin_url('admin.php?page=simple_ical_options')) .
        '" target="_blank">' . 
        __('Options form', 'simple-google-icalendar-widget') .
        '</a></p></div>');
    }
    // info in admin-menu
}