=== Simple Google iCalendar Widget ===
Contributors: bramwaas
Tags: ical iCalendar GoogleCalendar
Requires at least: 4.8.4
Tested up to: 5.2.1
Requires PHP: 5.3.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Widget that displays events from a public google calendar or iCal file.
 
== Description ==

Google offers some HTML snippets to embed your public Google Calendar into your website.
These are great, but as soon as you want to make a few adjustments to the styling,
that goes beyond changing some colors, they're not enough.

Because of that Nico Boehr wrote a very simple widget, that fetches events from a public google
calendar and nicely displays them in form of a widget, allowing you to apply all kinds of CSS.
I needed support for repeating events so I extended the widget to give limited support for repaeting
events, improved the support for timezones and day-light saving, and made the deafult output in line
with a bootstrap 4 list.

== Plugin Features ==

* Calendar widget to display appointments/events of a public Google calendar or other iCal file.
* Small footprint, uses only Google ID of the calendar to get event information via iCal
* Output in unorderd list with Bootstrap 4 listgroup classes and toggle for details.

== Installation ==
* Do the usual setup procedure... you know... downloading... unpacking... uploading... activating. 
Or just install it through the wordpress plugin directory.
* As soon as you activated the plugin, you should see a new widget under Design / Widgets.
Just drag it into your sidebar.
* Fill out all the necessary configuration fields.
 Under Calendar ID enter the calendar ID displayed by Google Calendar, or complete url of a
 Google calendar or other iCal file.
 You can find Google calendar ID by going to Calendar Settings / Calendars, clicking on the appropriate calendar, scrolling all the way down to
 find the Calendar ID at the bottom under the Integrate Calendar section. There's your calendar id.
* You're done!

== Documentation ==
* Gets calendar events via iCal url of google calendar ID
* Displays selected number of events, or events in a selected period from now as listgroup-items
* Displays event start-date and summary; toggle details, description, start-, end-time, location. 
*   see http://www.ietf.org/rfc/rfc5545.txt for specification of te ical format.
* Displays most common repeating events 

(see 3.3.10. [Page 38] Recurrence Rule in specification
* Frequency Yearly, Monthly, Weekly, Dayly (not parsed Hourly, Minutely ...)
* 0.7.0 BYDAY with DAILY frequency tested. Test code deleted. Present as RC to wordpress.* End of repeating by COUNT or UNTIL
* By day month or by monthday (BYDAY, BYMONTH, BYMONTHDAY) no other by
  (not parsed: BYYEARDAY, BYSETPOS, BYHOUR, BYMINUTE, WKST)
* Respects Timezone and Day Light Saving time 

    .____________._________.________._________.________.
    |            |DAILY    |WEEKLY  |MONTHLY  |YEARLY  |
    |____________|_________|________|_________|________|   
    |BYMONTH     |Limit    |Limit   |Limit    |Expand  |
    |____________|_________|________|_________|________|
    |BYMONTHDAY  |Limit    |N/A     |Expand   |Expand  |
    |____________|_________|________|_________|________|
    |BYDAY       |Limit    |Expand  |Note 1   |Note 2  |
    |____________|_________|________|_________|________|
   
      Note 1:  Limit if BYMONTHDAY is present; otherwise, special expand
               for MONTHLY.

      Note 2:  Limit if BYYEARDAY or BYMONTHDAY is present; otherwise,
               special expand for WEEKLY if BYWEEKNO present; otherwise,
               special expand for MONTHLY if BYMONTH present; otherwise,
               special expand for YEARLY.

(This widget is a Fork of version 0.7 of that simple google calendar widget by NBoehr
https://nl.wordpress.org/plugins/simple-google-calendar-widget/)


== Copyright and License ==

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
2017&thinsp;&ndash;&thinsp;2019 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

== Changelog ==

* 1.0.3 trim only "\n\r\0" and first space but keep last space in Description Summary and Location lines.
        adjustments to correct timezone that is ignored in new datetime when the $time parameter is a UNIX timestamp (e.g. @946684800) 
* 1.0.2 Adjustments for multiline Description, summary or location. Tested with wp 5.2.1.
* 1.0.1 PHP 7.2 deprecated create_function changed in anonymous function in widget_init. Tested with wp 5.0.3
* 1.0.0 first version in WP plugin directory, directory and start php renamed after slug simple-google-icalendar-widget
* 0.7.0 BYDAY with DAILY frequency tested. Test code deleted. Present as RC to wordpress.
* 0.6.0 BYDAY and BYMONTHDAY work with complete sorting and unifying in MONTH frequency
        adding class suffixes from setting.
* 0.5.0 BYDAY complete first try with sort tested with wordpress 4.8.3 php 7
* 0.3.5 discard non existent days like 31th november first try with byday
		works als with complete url to ical .ics file.
		renamed plugin to simple-google-i-calendar-widget and 
		renamed references gcal to ical		
* 0.3.3 simple repeating events (only full periods) works* 0.7.0 BYDAY with DAILY frequency tested. Test code deleted. Present as RC to wordpress.
* 0.2.0 starting work on repeating events 
* 0.1.0 Added support for start and end time with timezone
		Changed lay-out of output of teh widget so that is more in line with bootstrap 4 and with the iframe-widget of google
		a lot of small changes eg: better support for events in a timezone and events that last a whole day. Replace escaped chars for summary,
		description and location. Refinements in output HTML.
		renamed starting .php file to simple-google-calendar-widget.php
* 0.0 imported V0.7 of NBoehr
