/**
 * simple-ical-block-usertimezone.js
 * get the user clients timezone string in a cookie to use it in php
 * v2.2.0
*/ 
  var userTimezoneString = Intl.DateTimeFormat().resolvedOptions().timeZone;
  document.cookie = "userTimezoneString=" +  userTimezoneString;
