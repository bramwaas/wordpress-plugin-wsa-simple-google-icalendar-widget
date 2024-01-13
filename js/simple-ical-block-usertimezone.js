/**
 * simple-ical-block-usertimezone.js
 * get the user clients timezone string in a cookie to use it in php
 * v2.2.0
*/ 
(function() {

  var userTimezoneString = Intl.DateTimeFormat().resolvedOptions().timeZone;
  document.cookie = "simplegoogleicalendarwidget=" +  userTimezoneString + "; max-age=3600; secure; SameSite=Lax";
})();
