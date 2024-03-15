=== Simple Google Calendar Outlook Events Block Widget ===
Plugin name: Simple Google Calendar Outlook Events Block Widget
Contributors: bramwaas
Tags: Event Calendar, Google Calendar, iCal, Events, Block, Calendar, iCalendar, Outlook, iCloud
Requires at least: 5.3.0
Tested up to: 6.4
Requires PHP: 5.3.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Block widget that displays events from a public google calendar or iCal file.
 
== Description ==

Simple block or widget to display events from a public google calendar, microsoft office outlook calendar or an other iCal file, in the style of your website.

The Gutenberg block requires at least Wordpress 5.9.
This simple block/widget fetches events from a public google calendar (or other calendar in iCal format) and displays them in simple list allowing you to fully adapt to your website by applying all kinds of CSS. 
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
Lay-out: Start with summary.     
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

* Do the usual just install it through the wordpress plugin directory.
Or download the zip-file and upload it via Plugins Add new ... install and activate.    
Or do the old manual setup procedure... you know... downloading... unpacking... uploading... activating.       
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
  
But if you trust the output of the calendar application you can set a checkbox to allow safe html in the output. So if you manage to get the HTML in the Description and you set the checkbox to allow safe html you can get that html in the output, with exception of the tags that are not considered safe like SCRIPT and unknown tags.          
And with the current version  of Google Calendar you can put some HTML in the Description output. (April 2022) I saw the  &lt;a&gt; (link),  &lt;b&gt; (bold text),  &lt;i&gt; (italic text),  &lt;u&gt; (underlined text) and  &lt;br&gt; (linebreak) tags in a iCal description. They will all come through with "Allow safe html" checkbox on. Probably even more is possible, but Google can also decide to comply more to the standard.   
With Microsoft Outlook the HTML tags were filtered away and did not reach the iCal description         

In case you have all kinds of HTML in your appointments a plugin that uses the API of te calendar-application might be a better choice for you.     

= How to use Microsoft Office Outlook Calendar? =

First you have to share your calendar to make it public available, or to create and share a public calendar. Private calendars cannot be accessed by this plugin.
Then publish it as  an ICS link and use this link address. (something like https://outlook.live.com/owa/calendar/00000000-0000-0000-0000-000000000000/.../cid-.../calendar.ics) (works from version 1.3.1 of this widget)
[More details on Microsoft Office support](https://support.office.com/en-us/article/share-your-calendar-in-outlook-on-the-web-7ecef8ae-139c-40d9-bae2-a23977ee58d5)

= I only see the widget not the block =
Are you using at least WP 5.9? Below 5.9 the block doesn't work.
Are you using a page builder like Elementor? The block might not show in the page builder editor, try if it is available in the Wordpress editor.
To support users wo cannot use the Gutenberg block I have in v2.1.1 (with pain in my hart because Gutenberg blocks are in my opinion the future of WP) synchronized the output of the widget again with that of the block. 
 Otherwise they needed to use a work-around with an extra plugin like described in [How to Display Gutenberg Blocks in Other Page Builders (Elementor, Divi, etc)](https://gutenberghub.com/how-to-display-gutenberg-blocks-in-other-page-builders/) or use an other plugin that adds a shortcode to a Gutenberg block or maybe some pro functionality of Elementor.   

= How to use Apple Calendar (iCloud Mac/ios)? =
Choose the calendar you want to share. On the line of that calendar click on the radio symbol (a dot with three quart circles) right in that line. In the pop up Calendar Sharing check the box Public Calendar. You see the url below something like webcal://p59-caldav.icloud.com/published/2/MTQxNzk0NDA2NjE0MTc5AAAAAXt2Dy6XXXXXPXXxuZnTLDV9xr6A6_m3r_GU33Qj. Click on Copy Link and OK. Paste that in the "Calendar ID, or iCal URL" field of the widget (before version 1.3.1 you have to change webcal in https)
[More details on the MacObserver](https://www.macobserver.com/tips/quick-tip/icloud-configure-public-calendar)

= Error: cURL error 28: Operation timed out after 5000 milliseconds with 0 bytes received =

Probably the calendar is not public (yet), you can copy the link before the agenda is actually published. Check if the agenda has already been published and try again.

= I only see the headline of the calendar, but no events =

There are no events found within the selection. Test e.g. with an appointment for the next day and refresh the cache or wait till the cache is refreshed.
Check if you can download the ics file you have designated in the widget with a browser. At least if it is a text file with the first line "BEGIN:VCALENDAR" and further lines "BEGIN:VEVENT" and lines "END:VEVENT". If you cannot resolve it, you can of course report an error / question in our
[community support forum](https://wordpress.org/support/plugin/simple-google-icalendar-widget)

= I only see the title of the calendar, and the text 'Processing' even after waiting more the a minute, or a message &#61 Code: undefined &#61;	Msg: HTTP error, status &#61; 500  =

Probably you have chosen the setting "Use Client timezone settings, with REST" in "Use client timezone settings". With this setting active, at first the widget will be presented as a placeholder with only the title and the text processing. In the HTML of this placeholder are also some ID\'s as parameters for the javascript REST call to fetch the content after the page is loaded. This fetch is not executed (correct).   
To work correct Javascript must be enabled in a browser with version newer than 2016 but not in Internet Explorer.  
This is probably caused because the javascript view file with the fetch command is not loaded e.g. in the editor of Elementor or an other pagebuilder that tries to show a preview of the widget but does not load the necessary Javascript. This is a known issue, you could work around it by first set "Use WordPress timezone sttings, no REST" until you are satisfied with all the other settings and then set "Use Client timezone ...".
The REST call might also have failed by other reasons, then another try would probably solve the issue, but I have never seen that in testing. 
If you cannot resolve it, you can of course report an error / question in our [community support forum](https://wordpress.org/support/plugin/simple-google-icalendar-widget)    

= Can I use an event calendar that only uses days, not times, like a holiday calendar? =

 Yes you can, since v1.2.0, I have tested with [https://p24-calendars.icloud.com/holiday/NL_nl.ics](https://p24-calendars.icloud.com/holiday/NL_nl.ics) .

= How do I set different colours and text size for the dates, the summary, and the details? =

There is no setting for the color or font of parts in this plugin.
My philosophy is that layout and code/content should be separated as much as possible.
Furthermore, the plugin should seamlessly fit the style of the website and be fully customizable via CSS

So for color and font, the settings of the theme are used and are then applied via CSS.
But you can give each element within the plugin its own style (such as color and font size) from the theme via CSS.

If you know your theme css well and it contains classes you want to use on these fields you can add those class-names in
the Advanced settings: &#34;SUFFIX GROUP CLASS:&#34;, &#34;SUFFIX EVENT START CLASS:&#34; and &#34;SUFFIX EVENT DETAILS CLASS:&#34;

Otherwise you can add a block of additional CSS (or extra css or user css or something like that), which is possible with most themes.   
IMPORTANT:   
In order to target the CSS very specifically to the simple-ical-block, it is best to enter something unique in the settings of the block/widget under Advanced in &#34;HTML ANCHOR&#34;, for example &#39;Simple-ical-Block-1&#39; the code translated into a high-level ID of the block.
With the next block of additional CSS you can make the Dates red and 24 px, the Summary blue and 16 px,
and the Details green with a gray background.

~~~
/*additional CSS for Simple-ical-Block-1 */
&#35;Simple-ical-Block-1 .ical-date {
color: #ff0000;
font-size: 24px;
}
&#35;Simple-ical-Block-1 .ical_summary {
color: #0000ff;
font-size: 16px;
}
&#35;Simple-ical-Block-1 .ical_details {
color: #00ff00;
background-color: gray;
font-size: 16px;
}
/*end additional CSS for Simple-ical-Block-1 */
~~~

= How do I contribute to Simple Google Calendar Outlook Events Widget? =

We'd love your help! Here's a few things you can do:

* [Rate our plugin](https://wordpress.org/support/view/plugin-reviews/simple-google-icalendar-widget?postform#postform) and help spread the word!
* report bugs or help answer questions in our [community support forum](https://wordpress.org/support/plugin/simple-google-icalendar-widget).
* Help add or update a [plugin translation](https://translate.wordpress.org/projects/wp-plugins/simple-google-icalendar-widget).

== Documentation ==

* Gets calendar events via iCal url or google calendar ID
* Merge more calendars into one block
* Displays maximum the selected number of events as listgroup-items     
* Displays only or events in a selected period with a length of the setting "Number of days after today with events" from now limited by the time of the day or the beginning of the day at the start and the and of the at the end.
* Displays events in timezone of WordPress setting, or in Clients timezone with javascript REST call fetched from the clients browser.
* Displays event start-date and summary; toggle details, description, start-, end-time, location. 
* Displays most common repeating events 
* Frequency Yearly, Monthly, Weekly, Dayly (not parsed Hourly, Minutely ...), INTERVAL (default 1), WKST (default MO)
* End of repeating by COUNT or UNTIL
* By day month, monthday or setpos (BYDAY, BYMONTH, BYMONTHDAY, BYSETPOS) no other by...   
  (not parsed: BYWEEKNO, BYYEARDAY, BYHOUR, BYMINUTE, RDATE)
* Exclude events on EXDATE from recurrence set (after evaluating BYSETPOS)
* Respects Timezone and Day Light Saving time. Build and tested with Iana timezones as used in php, Google, and Apple now also tested with Microsoft timezones and unknown timezones. For unknown timezone-names using the default timezone of Wordpress (probably the local timezone). 

=== Settings for this block/widget ===

*Title*

Title of this instance of the widget   
With empty title no html is displayed, if you want the html with empty content use <> or another invalid tag that will be filtered away as title

*Calendar ID(s), or iCal URL*

The Google calendar ID, or the URL of te iCal file to display, or #example, or comma separated list of ID's.   
You can use #example to get example events   

Or a comma separated list of ID's; optional you can add a html-class separated by a semicolon to some or all ID's to distinguish the descent in the lay-out of the event.

*Number of events displayed*

The maximum number of events to display.   

*Number of days after today with events displayed*

Last date to display events in number of days after today.  
(See also Period limits)

*Select lay-out*

-Startdate line on a higher level in the list; 
-Start with summary before first date line; 
-Old style, summary after first date line, remove duplicate date lines.

*Date format first line*

Start date format first (date) line. Default: l jS \of F,   
l = day of the week (Monday); j = day of the month (25) F = name of month (december)   
y or Y = Year (24 or 2024);   
make empty if you don't want to show this date. 
Although this is intended for date all date and time fields contain date and time so you can als use time formats in date fields and date formats in time field.    
You can also use other text or simple html tags to embellish or emphasize it
escape characters with special meaning with a slash(\) e.g.:<\b>\F\r\o\m l jS \of F.</\b>
see also https://www.php.net/manual/en/datetime.format.php .

*End date format first line*

End date format first (date) line. Default: empty, no display.
End date will only be shown as date part is different from start date and format not empty.

*Time format start time after summary*

Start time format summary line. Default: G:i ,    
G or g = 24 or 12 hour format of an hour without leading zeros    
i = Minutes with leading zeros    
a or A = Lowercase or Uppercase Ante meridiem and Post meridiem make empty if you don't want to show this field.    
Linebreak before this field will be removed when summary is before first date line, if desired you can get it back by starting the format with &lt;\b\r&gt;        
This field will only be shown when date part of enddate is equal to start date and format not empty.   

*Time format end time after summary*

End time format summary line. Default: empty , no display.

*Time format start time*

Time format start time. Default: G:i,    
see "Time format start time after summary"   
This field will only be shown when date part of enddate is equal to start date and format not empty.

*Time format end time*

Time format separator and end time. Default: - G:i,    
see "Time format start time after summary"    
This field will only be shown when date part of enddate is equal to start date and format not empty.    

====Advanced settings====     

*Change block name*   

Change technical block name.   
Not implemented.  

*Calendar cache expiration time in minutes*

Minimal time in minutes between reads from calendar source.   
Necessary to prevent blocking due to too many calls to the calendar app.  
Also helpfull to prevent slowness due to unnecessary processing while calendar events don't change so much.

*Excerpt length*

Max length of the description in characters.
If there is a space or end-of-line character within 10 characters of this end, break there.
Note, not all characters have the same width, so the number of lines is not completely fixed by this. So you need additional CSS for that.
Warning: If you allow html in the description, necessary end tags may disappear here.
Default: empty, all characters will be displayed

*Tag for summary*

Tag for summary. Choose a tag from the list. Default: a (link)
When using bootstrap or other collapse css and java-script the description is collapsed and wil be opened bij clicking on the summary link.
Link is not included with the other tags.
If not using bootstrap h4, div or strong may be a better choice then a.


*Period limits*

Determination of start and end time of periode where events are displayed ("the moving time window").  
"Time of day", or "Whole day"

With "Time of day" as limit at both ends:
The "Number of days after today" is the number of 24-hour periods after the current time. It is a window that moves as the day progresses.
So, if today is Monday at 9am and you have a 3-day window, then events that start before 9am on Thursday will be shown, but an event that starts at 1pm will not.
As the day progresses, any of today's events that are completed before the current time will drop off the top of the list, and events that fall within the window will appear at the bottom.

"Whole Day" as limit moves the Start of the window to the beginning of the day (0:00 AM) in "local time" and/or moves the End to the beginning of the next day. The window now moves with jumps of a day (24 hours).

*Use client timezone settings*

Default all processing happens on server "local time" is measured in timezone of WordPress installation.
With "Use Client Timezone..." the timezone of client browser is fetched first with a REST call and processing happens with this timezone setting.
At first a placeholder with title and some Id's to use later is created and displayed, after pageload the timezone of client browser is fetched with javascript to process the output and get it with a REST call, then this output is placed over the placeholder. To be sure that there is place for the necessary attributes with ID's an extra span tag is added in the widget output.

*Suffix group class*

Suffix to add after css-class around the event (list-group),
start with space to keep the original class and add another class.

*Suffix event start class*

Suffix to add after the css-class around the event start line (list-group-item),
start with space to keep the original class and add another class.
E.g.: py-0 with leading space; standard bootstrap 4 class to set padding top and bottom to 0; ml-1 to set margin left to 0.25 rem

*Suffix event details class*

Suffix to add after the css-class around the event details link (ical_details),
start with space to keep the original class and add another class.

*Checkbox Allow safe html in description and summary*

Check checkbox to allow the use of some safe html in description and summary,
otherwise it will only be displayed as text.

*Closing HTML after available events*

Closing (safe) HTML after events list, when events are available.
This text with simple HTML will be displayed after the events list.
Use &amp;lt; or &amp;gt; if you want to output < or > otherwise they may be removed as unknown and therefore unsafe tags.
E.g. &lt;hr class="module-ft" &gt;.

*Closing HTML when no events*

Closing (safe) HTML output when no events are available.
This text with simple HTML will be displayed instead of the events list.
Use &amp;lt; or &amp;gt;  if you want to output < or > otherwise they may be removed as unknown and therefore unsafe tags.
E.g. &lt;p class="warning" &gt;&amp;lt; No events found. &amp;gt;&lt;\p&gt;&lt;hr class="module-ft" &gt;.

*Button Reset ID*

Press button Reset ID to copy the sibid from the clientid in the editor after duplicating the block, to make sibid unique again.   
In the legacy widget the bin2hex(random_bytes(7)) function is used to create a new sibid. Because this is not visible, the save button works only when also another field is changed.   
Since the transient cache id is derived from the block id, this also clears the data cache once.

*HTML anchor*

HTML anchor for this block.
Type one or two words - no spaces - to create a unique web address for this block, called an "anchor". Then you can link directly to this section on your page.
You can also use this ID to make parts of your extra css specific for this block.    
In the legacy widget (without the setting "Use Client Timezone...") it depends on the theme whether the HTML in which this ID should appear is available.   
   

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
or https://icalendar.org/iCalendar-RFC-5545/
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
2017&thinsp;&ndash;&thinsp;2023 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

== Upgrade Notice ==
* from 2024 (v2.3.0) requires php 7.4. "Use Client timezone settings, with REST" in "Use client timezone settings" works only correct with Javascript enabled in a browser with version newer than 2016 but not in Internet Explorer (fetch and Promise are used).         
* from 2023 (v2.1.1) deprecation php older than 7.4. I don't test in older php versions than 7.4 older versions might still work, but in future I may use new functions of php 8.
* in v2.1.1 Start with summary is replaced with a select. After upgrade you may have to choose the correct option again. 
* since v1.2.0 Wordpress version 5.3.0 is required because of the use of wp_date() 

== Changelog ==
* 2.3.0 Tie moving display events window created by Now and 'Number of days after today' to the display time instead of the data-retrieve/cache time. Make it possible to let the window start at 0H00 and end at 23H59 local time of the startdate and enddate of the window in addition to the current solution where both ends are at the time of the day the data is displayed/retrieved. Add <span class="dsc"> to description output to make it easier to refer to in css. Remove HTML for block title when title is empty. Add unescape \\ to \ and improve \, to ,   \; to ;  chars that should be escaped following the text specification. Extra save attributes in widget option to increase the chance the REST call finds them in that option.      
Tested with WP 5.3 (only widget) 5.9, 6.4 (block and widget in legacy block and in Elementor) 6.5 RC1  
* 2.2.1 20240123 after an isue of black88mx6 in support forum: don't display description line when excerpt-length = 0
* 2.2.0 after an issue of gonzob (@gonzob) in WP support forum: 'Bug with repeating events' improved handling of EXDATE so that also the first event of a recurrent set can be excluded.  
Basic parse Recurrence-ID (only one Recurrence-ID event to replace one occurrence of the recurrent set) to support changes in individual recurrent events in Google Calendar. Remove _ chars from UID.  
Changed textdomain from simple_ical to simple-google-icalendar-widget to make translations work by following the WP standard.  
Add help text's in block settings panel.
Tested with WordPress 6.4.  
* 2.1.5 20230824 after an issue of johansam (@johansam) in wp support forum: 'Warning: Undefined array key' reviewed and improved initialising of options for legacy widget.
* 2.1.4 20230725 tested with WordPress 6.3-RC1 running Twenty Twenty-Two theme.    
20230626 added quotes to the options of the Layout SelectControl in simple-ical-block.js conform / Block Editor Handbook / Reference Guides / Component Reference / SelectControl to emphasize that the return value is a string;   
To make transform smoother: Add parseInt to all integers in transform and added missing transformations in block.js     
Solved a number of typos in dateformat_... in update function in widget.php that prevented saving some options/settings.
* 2.1.3 After a feature request of achimmm (in github on Joomla module) added optional placeholder HTML output when no upcoming events are avalable. Also added optional output after the events list (when upcoming events are available).   
added parseInt on line 147(148) of simple-ical-block.js to keep layout in block-editor
* 2.1.2 fix classes from calendar id and improve hierarchical layout     
* 2.1.1 In response to a support issue of (@marijnvr). New lay-out for block with first date line on a higer level li. 'Start with summary' toggle-setting changed in 'layout' select-setting with options 'Startdate higher level', 'Start with summary', 'Old style'.   
After some testing with Elementor:     
Synchronize widget lay-out with block layout by largely using the same code. 
In widget use ID based on timestamp in blockid setting. To be sure that ID in frontend is the same as in admin form. (this was not always so with the ID based on instance-id of widget). Added reset ID in settings to change the ID once and in the same time clear the transient cache which is named after the blockid. Removed clear cache now setting in widget because ResetId is more reliable.   
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
