# Wordpress plugin wsa simple google calendar widget
Google offers some HTML snippets to embed your public Google Calendar into your website.
These are great, but as soon as you want to make a few adjustments to the styling,
that goes beyond changing some colors, they’re useless.

Because of that NBoehr wrote a very simple widget, that fetches events from a public google
calendar and nicely displays them in form of a widget, allowing you to apply all kinds of CSS.
https://nl.wordpress.org/plugins/simple-google-calendar-widget/

This widget is a Fork of version 0.7 of that simple google calendar widget by NBoehr

## Plugin Features

* Calendar widget to display appointments/events of a public Google calendar 
* Small footprint, uses only Google ID of the calendar to get event information via iCal
* Output in unorderd list with Bootstrap 4 listgroup classes and toggle for details.

## Installation
* Do the usual setup procedure… you know… downloading… unpacking… uploading… activating. 
Or just install it through the wordpress plugin directory.
* As soon as you activated the plugin, you should see a new widget under Design › Widgets.
Just drag it into your sidebar.
* Fill out all the necessary configuration fields.
 Under Calendar ID enter the calendar ID displayed by Google Calendar.
 You can find it by going to Calendar settings › Calendars, clicking on the appropriate calendar,
 scrolling all the way down to “Calendar address”. There’s your calendar id.
* You’re done!

## Documentation
* Gets calendar events via iCal url of google calendar
* Displays selected number of events, or events in a selected period from now as listgroup-items
* Displays event start-date and summary; toggle details, description, start-, end-time, location. 

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
2017&thinsp;&ndash;&thinsp;2017 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

## Changelog

* 0.0 imported V0.7 of NBoehr
* 20171108: Added support for start and end time with timezone
* 20171108: Changed lay-out of output of teh widget so that is more in line with bootstrap 4 and with the iframe-widget of google
