<?php
/*
 * SIB_SimpleicalWidgetNNS.php
 *
 * frontend without namespace for legagcy widget 
 *
 * @package Simple Google iCalendar Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2026 - 2026, Bram Waasdorp
 * 
 * 3.1.3 designed to support legacy ('Stone Age') applications that cannot work with namespaces, 
 *   first for SiteOrigin page builer. Not loaded automatic by classloader, so use require before registering.      
 */
// no direct access
defined('ABSPATH') or die ('Restricted access');

class SIB_SimpleicalWidgetNNS extends  WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget\SimpleicalWidget
    {
        /*
         * contruct the old widget
         *
         */
        public function __construct($id_base = '', $name = '', $widget_options = array(), $control_options = array())
        {
            if (empty( $id_base )) $id_base = 'sib_simple_ical_widget_nns';
            if (empty( $name )) $name = 'Simple Google iCalendar Widget No Namespace';
            if (empty( $widget_options )) $widget_options = [ 
                'classname' => 'Simple_iCal_WidgetNNS',
                'description' => __('Displays events from a public Google Calendar or other iCal source No Namespace', 'simple-google-icalendar-widget'),
                'show_instance_in_rest' => true, // allow migrating to block
            ];
            parent::__construct($id_base, // Base ID
                $name , // Name
                $widget_options,
                $control_options
                );
        }
	
} // end class
