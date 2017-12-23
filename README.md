# Wordpress plugin wsa simple google calendar widget
Google offers some HTML snippets to embed your public Google Calendar into your website.
These are great, but as soon as you want to make a few adjustments to the styling,
that goes beyond changing some colors, they’re not enough.

Because of that Nico Boehr wrote a very simple widget, that fetches events from a public google
calendar and nicely displays them in form of a widget, allowing you to apply all kinds of CSS.
I needed support for repeating events so I extended the widget to give limited support for repaeting
events, improved the support for timezones and day-light saving, and made the deafult output in line
with bootstrap 4 list.

## Plugin Features

* Calendar widget to display appointments/events of a public Google calendar or
* an ics iCal file.
* Small footprint, uses only Google ID or URL of the calendar to get event information via iCal
* Output in unorderd list with Bootstrap 4 listgroup classes and toggle for details.

## Installation
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
* Gets calendar events via iCal url of google calendar ID, or an other url that point to an ics file.
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

from spec rfc5545:
...
     The BYDAY rule part specifies a COMMA-separated list of days of
      the week; SU indicates Sunday; MO indicates Monday; TU indicates
      Tuesday; WE indicates Wednesday; TH indicates Thursday; FR
      indicates Friday; and SA indicates Saturday.

      Each BYDAY value can also be preceded by a positive (+n) or
      negative (-n) integer.  If present, this indicates the nth
      occurrence of a specific day within the MONTHLY or YEARLY "RRULE".



Desruisseaux                Standards Track                    [Page 41]

RFC 5545                       iCalendar                  September 2009


      For example, within a MONTHLY rule, +1MO (or simply 1MO)
      represents the first Monday within the month, whereas -1MO
      represents the last Monday of the month.  The numeric value in a
      BYDAY rule part with the FREQ rule part set to YEARLY corresponds
      to an offset within the month when the BYMONTH rule part is
      present, and corresponds to an offset within the year when the
      BYWEEKNO or BYMONTH rule parts are present.  If an integer
      modifier is not present, it means all days of this type within the
      specified frequency.  For example, within a MONTHLY rule, MO
      represents all Mondays within the month.  The BYDAY rule part MUST
      NOT be specified with a numeric value when the FREQ rule part is
      not set to MONTHLY or YEARLY.  Furthermore, the BYDAY rule part
      MUST NOT be specified with a numeric value with the FREQ rule part
      set to YEARLY when the BYWEEKNO rule part is specified.

      The BYMONTHDAY rule part specifies a COMMA-separated list of days
      of the month.  Valid values are 1 to 31 or -31 to -1.  For
      example, -10 represents the tenth to the last day of the month.
      The BYMONTHDAY rule part MUST NOT be specified when the FREQ rule
      part is set to WEEKLY.
...
      Recurrence rules may generate recurrence instances with an invalid
      date (e.g., February 30) or nonexistent local time (e.g., 1:30 AM
      on a day where the local time is moved forward by an hour at 1:00
      AM).  Such recurrence instances MUST be ignored and MUST NOT be
      counted as part of the recurrence set.

      Information, not contained in the rule, necessary to determine the
      various recurrence instance start time and dates are derived from
      the Start Time ("DTSTART") component attribute.  For example,
      "FREQ=YEARLY;BYMONTH=1" doesn't specify a specific day within the
      month or a time.  This information would be the same as what is
      specified for "DTSTART".

      BYxxx rule parts modify the recurrence in some manner.  BYxxx rule
      parts for a period of time that is the same or greater than the
      frequency generally reduce or limit the number of occurrences of
      the recurrence generated.  For example, "FREQ=DAILY;BYMONTH=1"
      reduces the number of recurrence instances from all days (if
      BYMONTH rule part is not present) to all days in January.  BYxxx
      rule parts for a period of time less than the frequency generally
      increase or expand the number of occurrences of the recurrence.
      For example, "FREQ=YEARLY;BYMONTH=1,2" increases the number of
      days within the yearly recurrence set from 1 (if BYMONTH rule part
      is not present) to 2.




Desruisseaux                Standards Track                    [Page 43]

RFC 5545                       iCalendar                  September 2009


      If multiple BYxxx rule parts are specified, then after evaluating
      the specified FREQ and INTERVAL rule parts, the BYxxx rule parts
      are applied to the current set of evaluated occurrences in the
      following order: BYMONTH, BYWEEKNO, BYYEARDAY, BYMONTHDAY, BYDAY,
      BYHOUR, BYMINUTE, BYSECOND and BYSETPOS; then COUNT and UNTIL are
      evaluated.

      The table below summarizes the dependency of BYxxx rule part
      expand or limit behavior on the FREQ rule part value.

      The term "N/A" means that the corresponding BYxxx rule part MUST
      NOT be used with the corresponding FREQ value.

      BYDAY has some special behavior depending on the FREQ value and
      this is described in separate notes below the table.

   +----------|--------|--------|-------|-------|------|-------|------|
   |          |SECONDLY|MINUTELY|HOURLY |DAILY  |WEEKLY|MONTHLY|YEARLY|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYMONTH   |Limit   |Limit   |Limit  |Limit  |Limit |Limit  |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYWEEKNO  |N/A     |N/A     |N/A    |N/A    |N/A   |N/A    |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYYEARDAY |Limit   |Limit   |Limit  |N/A    |N/A   |N/A    |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYMONTHDAY|Limit   |Limit   |Limit  |Limit  |N/A   |Expand |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYDAY     |Limit   |Limit   |Limit  |Limit  |Expand|Note 1 |Note 2|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYHOUR    |Limit   |Limit   |Limit  |Expand |Expand|Expand |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYMINUTE  |Limit   |Limit   |Expand |Expand |Expand|Expand |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYSECOND  |Limit   |Expand  |Expand |Expand |Expand|Expand |Expand|
   +----------|--------|--------|-------|-------|------|-------|------|
   |BYSETPOS  |Limit   |Limit   |Limit  |Limit  |Limit |Limit  |Limit |
   +----------|--------|--------|-------|-------|------|-------|------|

      Note 1:  Limit if BYMONTHDAY is present; otherwise, special expand
               for MONTHLY.

      Note 2:  Limit if BYYEARDAY or BYMONTHDAY is present; otherwise,
               special expand for WEEKLY if BYWEEKNO present; otherwise,
               special expand for MONTHLY if BYMONTH present; otherwise,
               special expand for YEARLY.






Desruisseaux                Standards Track                    [Page 44]

RFC 5545                       iCalendar                  September 2009


      Here is an example of evaluating multiple BYxxx rule parts.

       DTSTART;TZID=America/New_York:19970105T083000
       RRULE:FREQ=YEARLY;INTERVAL=2;BYMONTH=1;BYDAY=SU;BYHOUR=8,9;
        BYMINUTE=30

      First, the "INTERVAL=2" would be applied to "FREQ=YEARLY" to
      arrive at "every other year".  Then, "BYMONTH=1" would be applied
      to arrive at "every January, every other year".  Then, "BYDAY=SU"
      would be applied to arrive at "every Sunday in January, every
      other year".  Then, "BYHOUR=8,9" would be applied to arrive at
      "every Sunday in January at 8 AM and 9 AM, every other year".
      Then, "BYMINUTE=30" would be applied to arrive at "every Sunday in
      January at 8:30 AM and 9:30 AM, every other year".  Then, lacking
      information from "RRULE", the second is derived from "DTSTART", to
      end up in "every Sunday in January at 8:30:00 AM and 9:30:00 AM,
      every other year".  Similarly, if the BYMINUTE, BYHOUR, BYDAY,
      BYMONTHDAY, or BYMONTH rule part were missing, the appropriate
      minute, hour, day, or month would have been retrieved from the
      "DTSTART" property.

(This widget is a Fork of version 0.7 of that simple google calendar widget by NBoehr
https://nl.wordpress.org/plugins/simple-google-calendar-widget/)



## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
2017&thinsp;&ndash;&thinsp;2017 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

## Changelog

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
* 0.7.0 BYDAY with DAILY frequency tested. Test code deleted. Present as RC to wordpress.
        
