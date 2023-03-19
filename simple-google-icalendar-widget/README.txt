=== Simple Google Calendar Outlook Events Block Widget ===
Plugin name: Simple Google Calendar Outlook Events Block Widget
Contributors: bramwaas
Tags: Event Calendar, Google Calendar, iCal, Events, Block, Calendar, iCalendar, Outlook, iCloud
Requires at least: 5.3.0
Tested up to: 6.2
Requires PHP: 5.3.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Block widget that displays events from a public google calendar or iCal file.
 
== Description ==

Simple block or widget to display events from a public google calendar, microsoft office outlook calendar or an other iCal file, in the style of your website.

This simple widget fetches events from a public google calendar (or other calendar in iCal format) and displays them in simple list allowing you to fully adapt to your website by applying all kinds of CSS. 
Google offers some HTML snippets to embed your public Google Calendar into your website.
These are great, but as soon as you want to make a few adjustments to the styling, that goes beyond changing some colors, they're not enough.

== Plugin Features ==

* Calendar block or widget to display appointments/events of a public Google calendar or other iCal file.
* Block gives live preview in the editor and is not constrained to widget area. Old widget will be displayed in legacy widget block only in widget area.
* Small footprint, uses only Google ID of the calendar, or ICS link for Outlook, or Url of iCal file, to get event information via iCal
* Merge more calendars into one block
* Manage events in Google Calendar, or other iCalendar source.
* Fully adaptable to your website with CSS. Output in unordered list with Bootstrap 4 listgroup classes and toggle for details.
* Choose date / time format in settings screen that best suits your website.
* Displays per event DTSTART, DTEND, SUMMARY, LOCATION and DESCRIPTION. DTSTART is required other components are optional. 
* Displays most common repeating events. Frequency Yearly, Monthly, Weekly, Dayly (not Hourly, Minutely and smaller periods)
* In the screenshot below: Left the block with default settings and clicked on one summary. Right with some adjusted settings.   
Adjusted settings for start with summary:  
Start with summary.: "true"  
Date format first line: ".<\b\r>l jS \o\f  F"  
Enddate format first line: " - l jS \o\f F"  
Time format time summary line: " G:i"   
Time format end time summary line: " - G:i"   
Time format start time: ""  
Time format end time: ""  
Tag for summary: "strong" 
  
 == Screenshots ==
1. With theme Twenty Twenty-Two.
2. With theme WP Bootstrap Starter (with bootstrap 4 css and js).
3. Transform from Legacy widget block to Simple ical Block.
 
== Installation ==

* Do the usual setup procedure... you know... downloading... unpacking... uploading... activating.   
Or download the zip-file and upload it via Plugins Add new ... install and activate.    
Or just install it through the wordpress plugin directory.       
* For WP 5.9 and higher: As soon as you activated the plugin, you should see a new block 'Simple ical Block' in the (block) Editor in the category Widgets.       
You can enter the block in a post or a page with the block-editor (eg. (+ sign)Toggle block inserter / WIDGETS).           
If your theme has a widget area you can also enter the block as a widget in a widget area:          
 Appearance / Widgets / (+ sign)Toggle block inserter / WIDGETS. Just drag it into your sidebar.    
* Alternative : Select 'Simple Google Calendar Outlook Events Widget' or select the Legacy widget and choose 'Simple Google Calendar Outlook Events Widget'     
  and drag it into the sidebar.
* Fill out all the necessary configuration fields.
 In Calendar ID enter the calendar ID displayed by Google Calendar, or complete url of a  Google calendar or other iCal file.
 You can find Google calendar ID by going to Calendar Settings / Calendars, clicking on the appropriate calendar, scrolling all the way down to find the Calendar ID at the bottom under the Integrate Calendar section. There's your calendar id.
* You're done!

== Frequently Asked Questions ==

= How to use Google Calendar? =

First you have to share your calendar to make it public available, or to create a public calendar. Private calendars cannot be accessed by this plugin.
Then use the public iCal address or the Google calendar ID.
[More details on Google support](https://support.google.com/calendar/answer/37083)

= Where do I find the Google Calendar Id? =

 You can find Google calendar ID by going to Calendar Settings / Calendars, clicking on the appropriate calendar, scrolling all the way down to find the Calendar ID at the bottom under the Integrate Calendar section. There's your calendar id.
 [More details on Google support](https://support.google.com/calendar/answer/37083#link)

= How to merge more calendars into one module/block =  

Fill a comma separated list of ID's in the Calendar ID field.      
Optional you can add a html-class separated by a semicolon to some or all ID's to distinguish the descent in the lay-out of the event.   
E.g.: #example;blue,https://p24-calendars.icloud.com/holiday/NL_nl.ics;red     
Events of #example will be merged with events of NL holidays; html-class "blue" is added to all events of #example, html-class "red" to all events of NL holidays. 

= Can I use HTML in the description of the appointement?  =

You can use HTML in the most Calendars, but the result in the plugin may not be what you expect.    
First: The original iCalendar standard allowed only plain text as part of an event description. Thus probably most calendars will only give the plain text in the Description in the iCal output.   
Secondly: For security reasons  this plugin filters the HTML to convert characters that have special significance in HTML to the corresponding HTML-entities.
  
But from version 1.5.0 of this widget if you trust the output of the calendar application you can set a checkbox to allow safe html in the output. So if you manage to get the HTML in the Description and you set the checkbox to allow safe html you can get that html in the output, with exception of the tags that are not considered safe like SCRIPT and unknown tags.          
And with the current version  of Google Calendar you can put some HTML in the Description output. (April 2022) I saw the  &lt;a&gt; (link),  &lt;b&gt; (bold text),  &lt;i&gt; (italic text),  &lt;u&gt; (underlined text) and  &lt;br&gt; (linebreak) tags in a iCal description. They will all come through with "Allow safe html" checkbox on. Probably even more is possible, but Google can also decide to comply more to the standard.   
With Microsoft Outlook the HTML tags were filtered away and did not reach the iCal description         

In case you have all kinds of HTML in your appointments a plugin that uses the API of te calendar-application might be a better choice for you.     

= How to use Microsoft Office Outlook Calendar? =

First you have to share your calendar to make it public available, or to create and share a public calendar. Private calendars cannot be accessed by this plugin.
Then publish it as  an ICS link and use this link address. (something like https://outlook.live.com/owa/calendar/00000000-0000-0000-0000-000000000000/.../cid-.../calendar.ics) (works from version 1.3.1 of this widget)
[More details on Microsoft Office support](https://support.office.com/en-us/article/share-your-calendar-in-outlook-on-the-web-7ecef8ae-139c-40d9-bae2-a23977ee58d5)

= How to use Apple Calendar (iCloud Mac/ios)? =
Choose the calendar you want to share. On the line of that calendar click on the radio symbol (a dot with three quart circles) right in that line. In the pop up Calendar Sharing check the box Public Calendar. You see the url below something like webcal://p59-caldav.icloud.com/published/2/MTQxNzk0NDA2NjE0MTc5AAAAAXt2Dy6XXXXXPXXxuZnTLDV9xr6A6_m3r_GU33Qj. Click on Copy Link and OK. Paste that in the "Calendar ID, or iCal URL" field of the widget (before version 1.3.1 you have to change webcal in https)
[More details on the MacObserver](https://www.macobserver.com/tips/quick-tip/icloud-configure-public-calendar)

= Error: cURL error 28: Operation timed out after 5000 milliseconds with 0 bytes received =

Probably the calendar is not public (yet), you can copy the link before the agenda is actually published. Check if the agenda has already been published and try again.

= I only see the headline of the calendar, but no events =

There are no events found within the selection. Test e.g. with an appointment for the next day and refresh the cache or wait till the cache is refreshed.
Check if you can download the ics file you have designated in the widget with a browser. At least if it is a text file with the first line "BEGIN:VCALENDAR" and further lines "BEGIN:VEVENT" and lines "END:VEVENT". If you cannot resolve it, you can of course report an error / question in our
[community support forum](https://wordpress.org/support/plugin/simple-google-icalendar-widget)

= Can I use an event calendar that only uses days, not times, like a holiday calendar? =

 Yes you can, since v1.2.0, I have tested with [https://p24-calendars.icloud.com/holiday/NL_nl.ics](https://p24-calendars.icloud.com/holiday/NL_nl.ics) .

= How do I contribute to Simple Google Calendar Outlook Events Widget? =

We'd love your help! Here's a few things you can do:

* [Rate our plugin](https://wordpress.org/support/view/plugin-reviews/simple-google-icalendar-widget?postform#postform) and help spread the word!
* report bugs or help answer questions in our [community support forum](https://wordpress.org/support/plugin/simple-google-icalendar-widget).
* Help add or update a [plugin translation](https://translate.wordpress.org/projects/wp-plugins/simple-google-icalendar-widget).

== Documentation ==

* Gets calendar events via iCal url or google calendar ID
* Merge more calendars into one block
* Displays selected number of events, or events in a selected period from now as listgroup-items
* Displays event start-date and summary; toggle details, description, start-, end-time, location. 
* Displays most common repeating events 
* Frequency Yearly, Monthly, Weekly, Dayly (not parsed Hourly, Minutely ...), INTERVAL (default 1), WKST (default MO)
* End of repeating by COUNT or UNTIL
* By day month, monthday or setpos (BYDAY, BYMONTH, BYMONTHDAY, BYSETPOS) no other by...   
  (not parsed: BYWEEKNO, BYYEARDAY, BYHOUR, BYMINUTE, RDATE)
* Exclude events on EXDATE from repeat (after evaluating BYSETPOS)
* Respects Timezone and Day Light Saving time. Build and tested with Iana timezones as used in php, Google, and Apple now also tested with Microsoft timezones and unknown timezones. For unknown timezone-names using the default timezone of Wordpress (probably the local timezone).  

=== Recurrent events, Timezone,  Daylight Saving Time ===

Most users don't worry about time zones. The timezonesettings for the Wordpress installation, the calendar application and the events are all the same local time.   
In that case this widget displays all the recurrent events with the same local times. The widget uses the properties of the starttime and duration (in seconds) of the first event to calculate the repeated events. Only when a calculated time is removed during the transition from ST (standard time, wintertime) to DST (daylight saving time, summertime) by turning the clock forward one hour, both the times of the event (in case the starttime is in the transition period) or only the endtime (in case only the endtime is in the transition period)  of the event are pushed forward with that same amount.    
But because the transition period is usually very early in the morning, few events will be affected by it.   
If a calculated day does not exist (i.e. February 30), the event will not be displayed. (Formally this should also happen to the time in the transition from ST to DST)   

For users in countries that span more time zones, or users with international events the calendar applications can add a timezone to the event times. So users in other timezones will see the event in the local time in there timezone (as set in timezone settings of Wordpress) corresponding with the time.    
The widget uses the starttime, the timezone of the starttime and the duration in seconds of the starting event as starting point for the calculation of repeating events. So if the events startime timezone does'not use daylight savings time and and timezone of the widget (i.e. the WP timezone setting) does. During DST the events are placed an hour later than during ST. (more precisely shift with the local time shift). Similar the events will be shift earlier when the timezone of the starttime has DST and the timezone of the widget not.   

Of course the same effect is achieved when you schedule the events in UTC time despite using local time DST in your standard calendar.     
In these cases, a special effect can be seen of using the same times twice in the transition from DST to ST. If an event lasts less than an hour. If the event starts in the last hour of DST then it ends in the first hour of ST in between the local clocks are turned back one hour. According to the local clock, the end time is therefore before the start time. And the widget shows it like this too. The same also applies to Google and Outlook calendar.   
Theoretically this could als happen with recurrent events in the same timezone with DST. In my test I have seen this with Google calendar but not with the widget. PHP and therefore the widget uses the second occurence if the result of the calculation is a time that is twice available (at least in the version of PHP I use), but using the first occurence like Google does is just as good.    

Test results and comparison with Google and Outlook calendar have been uploaded as DayLightSavingTime test.xlsx.
  
=== From the ical specifications ===
~~~
see http://www.ietf.org/rfc/rfc5545.txt for specification of te ical format.
(see 3.3.10. [Page 38] Recurrence Rule in specification
  .____________._________.________._________.________.
  |            |DAILY    |WEEKLY  |MONTHLY  |YEARLY  |
  |____________|_________|________|_________|________|   
  |BYMONTH     |Limit    |Limit   |Limit    |Expand  |
  |____________|_________|________|_________|________|
  |BYMONTHDAY  |Limit    |N/A     |Expand   |Expand  |
  |____________|_________|________|_________|________|
  |BYDAY       |Limit    |Expand  |Note 1   |Note 2  |
  |____________|_________|________|_________|________|
  |BYSETPOS    |Limit    |Limit   |Limit    |Limit   |
  |____________|_________|________|_________|________|
 
    Note 1:  Limit if BYMONTHDAY is present; 
             otherwise, special expand for MONTHLY.

    Note 2:  Limit if BYYEARDAY or BYMONTHDAY is present; otherwise,
             special expand for WEEKLY if BYWEEKNO present; otherwise,
             special expand for MONTHLY if BYMONTH present; otherwise,
             special expand for YEARLY.
~~~
(This widget is a Fork of version 0.7 of that simple google calendar widget by NBoehr
https://nl.wordpress.org/plugins/simple-google-calendar-widget/)

== Copyright and License ==

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
2017&thinsp;&ndash;&thinsp;2022 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

== Upgrade Notice ==

* since v1.2.0 Wordpress version 5.3.0 is required because of the use of wp_date() 

== Changelog ==
    19-3-2023 tested with wordpress 6.2-RC2
* 2.1.0 Support more calendars in one module/block. Support DURATION of event. Move processing 'allowhtml' complete out Parser to template/block. 
  Use properties in IcsParser to limit copying of input params in several functions.
  Solved issue: Warning: date() expects at most 2 parameters, 3 given in ...IcsParser.php on line 549 caused by wp_date() / date() replacement in v2.0.4.     
  Support BYSETPOS in response to a github issue on the WP block of peppergrayxyz. Support WKST.   
* 2.0.4 Improvements IcsParser made as a result of porting to Joomla
* notably solve issue not recognizing http as a valid protocol in array('http', 'https', 'webcal') because index = 0 so added 1 as starting index
* make timezone-string a property of the object filled with the time-zone setting of the CMS (get_option('timezone_string')).
* replace wp_date() by date() because translation of weekday- and month-names is not needed here.
* 2.0.3 Added initial values for new attributes in transform.
* 2.0.2 For block: Added enddate (only when different from startdate) and endtime for first line and summary line, boolean start with summary, selectlist for summary tag,
*       HTML anchor and improved security by using wp_kess on formatted datetimes. Removed editor style.  
*       For all: Improved handling of enddate for recurrent events with DATEs (no times) and selection recurrent events also when enddate is after now (was already so with single events)     
* 2.0.1 Added transform from widget to block. Removed support for anchor because it doesn't work and gives error if you don't save more then attributes. added example.
* 2.0.0 Created a native Gutenberg Block Simple ical Block next to the old the widget when using WP 5.9 or higher with the same backend code and almost the same frontend.
* 1.5.1 After more testing and solving some issues with recurring events and daylight saving time removed the old correction for DST and the temporary checkbox to turn this correction off. Now there is only a correction when time is changed because the calculated time does not exists during transition from ST to DST. In that case in the next recursion the hour and minutes are set back to their beginvalue. Renamed and namespaced classes. Improved Zerodate to UTC-timezone processing. Restructured the IcsParse class a bit.
   20220516 tested up to 6.0 (RC2)
* 1.5.0 in response to a github issue of fhennies added a checkbox to allow safe html in the output. Added wp_kses() function to run for description, digest and location, to echo only html considered safe for wordpress posts, thus preserving some security against XSS.
Extra options for parser in array $instance and added temporary new option notprocessdst to don't process differences in DST between start of series events and the current event. (in response to a WP support topic of @wurzelserver)
   7-4-2022 tested with wordpress 5.9.1
* 1.4.1 in response to a support topic of edwindekuiper (@edwindekuiper) fixed timezone error: If timezone appointment is empty or incorrect 
        timezone fall back was to new \DateTimeZone(get_option('timezone_string')) but with UTC+... UTC-... timezonesetting this string is empty
        so I use now wp_timezone() and if even that fails fall back to new \DateTimeZone('UTC').
* 1.4.0 added parameter excerptlength to limit the length in characters of the description in respons to a comment of justmigrating (@justmigrating).
        Fixed a trim error that occurred in a previous version, revising the entire trimming so that both \r\n and \n end of lines are handled properly
        12-7-2021 tested with 5.8-RC2 and set tested up to to 5.8
* 1.3.1 tested with Outlook and found that different timezones were a problem, solved by using a conversion tabel between Microsoft timezones and Iana timezones and using local (Wordpress configuration) timezone when timezone is unknown.
Also found that colon ended description and summary. Found a solution for that so now you can use a colon in a description or a summary.
Tested with iCloud Apple Calendar, timezones seem to be Iana. Issue with url starting with webcal protocol in stead of http or https, work around is substituting webcal with https:, but solved by change in url check and stricter validation Google Id.
* 1.3.0 made time formats of appointment/event times configurable in response to a comment of carolynclarkdfw (@carolynclarkdfw) on the plugin page. Tested with wordpress 5.7
* 1.2.2 added a checkbox to clear cache before expiration in response to a comment of TrojanObelix. 
* 1.2.1 handle not available DTEND => !isset($e->end) in response to a comment of lillyberger (@lillyberger) on the plugin page, by defaulting $e->end to DTSTART value.
        tested with https://p24-calendars.icloud.com/holiday/NL_nl.ics
* 1.2.0 adjustment not to display time on events that have DTSTART in DATE format instead of DATETIME format after a comment of TrojanObelix.
		found that date_i18n($format, $timestamp) formats according to the locale, but not the timezone so times and sometimes also dates 
		went wrong, but the newer function wp_date() does, so date_i18n() replaced bij wp_date() (that means wp 5.3.0 is now required).
		adjusted use of ID's for the events also to work when lineseperator is \n  in stead of \r\n after seeing example by of TrojanObelix.
		Tested with WP 5.5.3.		
* 1.1.0 parse EXDATE to exclude events from repeat
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
