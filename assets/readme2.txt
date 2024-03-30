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

