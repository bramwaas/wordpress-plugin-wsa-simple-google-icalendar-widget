=== Simple Google Calendar Outlook Events Block Widget ===
Plugin name: Simple Google Calendar Outlook Events Block Widget
Contributors: bramwaas
Tags: Google Calendar, iCal, Events, Block, Calendar
Requires at least: 5.3.0
Tested up to: 6.6
Requires PHP: 5.3.0
Stable tag: 2.4.3
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
This is probably caused because the javascript view file with the fetch command is not loaded e.g. in the editor of Elementor or an other pagebuilder that tries to show a preview of the widget but does not load the necessary Javascript. This is a known issue, you could work around it by first set "Use WordPress timezone settings, no REST" until you are satisfied with all the other settings and then set "Use Client timezone ...".
If you change the Sibid without clicking the Update button, the new Sibid may already be saved in the plugin options for the REST call, but not in the block attributes. If you still click Update, the problem will be resolved.   
The REST call might also have failed by other reasons, then another try would probably solve the issue, but I have never seen that in testing.  
If you cannot resolve it, you can of course report an error / question in our [community support forum](https://wordpress.org/support/plugin/simple-google-icalendar-widget)    

= Can I use an event calendar that only uses days, not times, like a holiday calendar? =

 Yes you can, since v1.2.0, I have tested with [https://p24-calendars.icloud.com/holiday/NL_nl.ics](https://p24-calendars.icloud.com/holiday/NL_nl.ics) .

= This block has encountered an error and cannot be previewed =

Probably you have (re)opened a page where the block is edited but your password cookie is expired.   
Log in in Wordpress again and open the page again. The block will be available. 

= After an update 6.6 of Wordpress a page with this block in a synced pattern on it freezes in the editor. =   

Maybe the block is long time ago placed on several pages as a synced pattern or reusable block and everything worked fine until Wordpress 6.5   
It is possible that the id of the block is not initialized, the editor tries to initialize the id but this is not prossible in a synced pattern.
Before 6.6 the update failed and the processing went ahead, from 6.6 the update fails and tries again (in an endless loop).     
Solution: Update and save the block in the editor of the pattern to which the block belongs.       

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
* Displays only events in a selected period with a length of the setting "Number of days after today with events" from now limited by the time of the day or the beginning of the day at the start and the and of the at the end.
* Displays events in timezone of WordPress setting, or in Clients timezone with javascript REST call fetched from the clients browser.
* Displays event start-date and summary; toggle details, description, start-, end-time, location. 
* Displays most common repeating events 
* Frequency Yearly, Monthly, Weekly, Dayly (not parsed Hourly, Minutely ...), INTERVAL (default 1), WKST (default MO)
* End of repeating by COUNT or UNTIL
* By day month, monthday or setpos (BYDAY, BYMONTH, BYMONTHDAY, BYSETPOS) no other by...   
  (not parsed: BYWEEKNO, BYYEARDAY, BYHOUR, BYMINUTE, RDATE)
* Exclude events on EXDATE from recurrence set (after evaluating BYSETPOS)
* Respects Timezone and Day Light Saving time. Build and tested with Iana timezones as used in php, Google, and Apple now also tested with Microsoft timezones and unknown timezones. For unknown timezone-names using the default timezone of Wordpress (probably the local timezone). 

(This widget is a Fork of version 0.7 of that simple google calendar widget by NBoehr
https://nl.wordpress.org/plugins/simple-google-calendar-widget/)

== Copyright and License ==

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
2017&thinsp;&ndash;&thinsp;2023 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

== Upgrade Notice ==
* error in WP 6.6 this block (with serverside rendering) breaks editor when placed on a page via a synced pattern. Issue reported as WordPress Trac #61592. probably olved in 2.4.4-a.
* from 2024 (v2.3.0) requires php 7.4. "Use Client timezone settings, with REST" in "Use client timezone settings" works only correct with Javascript enabled in a browser with version newer than 2016 but not in Internet Explorer (fetch and Promise are used).         
* from 2023 (v2.1.1) deprecation php older than 7.4. I don't test in older php versions than 7.4 older versions might still work, but in future I may use new functions of php 8.
* in v2.1.1 Start with summary is replaced with a select. After upgrade you may have to choose the correct option again. 
* since v1.2.0 Wordpress version 5.3.0 is required because of the use of wp_date() 

== Changelog ==
* 2.4.4-a initialization sibid also with direct assign in case setAttribute does not work (e.g. in Synced pattern 6.6)
* 2.4.3 replaced render_callback in server side register_block_type by render in block.json (v3 plus ( is_wp_version_compatible( '6.3' ) ))  simplifying initialization edit js to reduce change of looping when used in synced pattern and reviewing initializing in block.json.
* 2.4.2 replaced null by 'admin.php' to solve issue 'Deprecation warnings in PHP 8.3' of Knut Sparhell (@knutsp) on support forum. Moved older entries of changelog to changelog.txt.
* 2.4.1 added defaults to all used keys of $args to solve issue 'PHP warnings' of johansam on support forum. Undefined array key “classname” in .../simple-google-icalendar-widget.php on line 170
* 2.4.0 exclude DTEND from event that is evend ends before (<) DTEND in stead of at (<=) DTEND. removed modulo 4    
 Checks if time zone ID with Etc/GMT 'replaced by'Etc/GMT+' is a Iana timezone then return this timezone.    
* more in changelog.txt.