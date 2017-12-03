=== Simple Google iCalendar Widget ===
Contributors: bramwaas
Tags: ical iCalendar GoogleCalendar
Requires at least: 4.8.4
Tested up to: 4.9.1
Requires PHP: 5.3.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Widget that displays events from a public google calendar
 
== Description ==

Google offers some HTML snippets to embed your public Google Calendar into your website.
These are great, but as soon as you want to make a few adjustments to the styling,
that goes beyond changing some colors, they’re useless.

Because of that Nico Boehr wrote a very simple widget, that fetches events from a public google
calendar and nicely displays them in form of a widget, allowing you to apply all kinds of CSS.
https://nl.wordpress.org/plugins/simple-google-calendar-widget/

This widget is a Fork of version 0.7 of that simple google calendar widget by NBoehr

== Plugin Features ==

* Calendar widget to display appointments/events of a public Google calendar 
* Small footprint, uses only Google ID of the calendar to get event information via iCal
* Output in unorderd list with Bootstrap 4 listgroup classes and toggle for details.

== Installation ==
* Do the usual setup procedure… you know… downloading… unpacking… uploading… activating. 
Or just install it through the wordpress plugin directory.
* As soon as you activated the plugin, you should see a new widget under Design › Widgets.
Just drag it into your sidebar.
* Fill out all the necessary configuration fields.
 Under Calendar ID enter the calendar ID displayed by Google Calendar, or complete url of a
 Google calendar or other iCal file.
 You can find Google calendar ID by going to Calendar Settings › Calendars, clicking on the appropriate calendar, scrolling all the way down to “Calendar address”. There’s your calendar id.
* You’re done!

## Documentation
* Gets calendar events via iCal url of google calendar ID
* Displays selected number of events, or events in a selected period from now as listgroup-items
* Displays event start-date and summary; toggle details, description, start-, end-time, location. 
*   see http://www.ietf.org/rfc/rfc5545.txt for specification of te ical format.
* Displays most common repeating events 

(see 3.3.10. [Page 38] Recurrence Rule in specification
* Frequency Yearly, Monthly, Weekly, Dayly (not parsed Hourly, Minutely ...)
* End of repeating by COUNT or UNTIL
* By day month or by monthday (BYDAY, BYMONTH, BYMONTHDAY) no other by
  (not parsed: BYYEARDAY, BYSETPOS, BYHOUR, BYMINUTE, WKST)
* Respects Timezone and Day Light Saving time 

   +----------+-------+------+-------+------+
   |          |DAILY  |WEEKLY|MONTHLY|YEARLY|
   +----------+-------+------+-------+------+
   |BYMONTH   |Limit  |Limit |Limit  |Expand|
   +----------+-------+------+-------+------+
   |BYMONTHDAY|Limit  |N/A   |Expand |Expand|
   +----------+-------+------+-------+------+
   |BYDAY     |Limit  |Expand|Note 1 |Note 2|
   +----------+-------+------+-------+------+
   
      Note 1:  Limit if BYMONTHDAY is present; otherwise, special expand
               for MONTHLY.

      Note 2:  Limit if BYYEARDAY or BYMONTHDAY is present; otherwise,
               special expand for WEEKLY if BYWEEKNO present; otherwise,
               special expand for MONTHLY if BYMONTH present; otherwise,
               special expand for YEARLY.



== Copyright and License ==

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
2017&thinsp;&ndash;&thinsp;2017 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

== Changelog ==

* 0.0 imported V0.7 of NBoehr
* 20171108: Added support for start and end time with timezone
* 20171108: Changed lay-out of output of teh widget so that is more in line with bootstrap 4 and with the iframe-widget of google
* 0.0.1 renamed starting .php file to simple-google-calendar-widget.php
* 0.1.0 a lot of small changes eg: better support for events in a timezone and events that last a whole day. Replace escaped chars for summary, description and location. Refinements in output HTML.
TODO repeating events.
* 0.2.0 starting work on repeating events 
* 0.3.3 simple repeating events (only full periods) works
* 0.3.5 discard non existent days like 31th november first try with byday
* 0.5.0 BYDAY complete first try with sort tested with wordpress 4.8.3 php 7
* 0.6.0 BYDAY and BYMONTHDAY work with complete sorting and unifying in MONTH frequency
        adding class suffixes from setting.
