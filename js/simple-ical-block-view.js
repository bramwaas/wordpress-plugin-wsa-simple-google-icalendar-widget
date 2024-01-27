/**
 * simple-ical-block-view.js
 * view simple-ical-block output with extra client parameter tzid_ui using REST fetched @wordpress/api-fetch 
 * why this is better than plain javascript Fetch API I don't know yet.
 * v2.3.0
*/ 
let paramsObj = {
	"after_events": "",
	"allowhtml": false,
	"anchorId": "Simple-ical-Block-2",
	"blockid": "c19146a0c-f760-495f-8560-8cd988b1590d",
	"cache_time": 60,
	"calendar_id": "#example,https://calendar.google.com/calendar/ical/nl.dutch%23holiday%40group.v.calendar.google.com/public/basic.ics",
	"clear_cache_now": false,
	"dateformat_lg": "l jS \\of F",
	"dateformat_lgend": "",
	"dateformat_tend": " - G:i ",
	"dateformat_tsend": "",
	"dateformat_tstart": "G:i",
	"dateformat_tsum": "G:i ",
	"event_count": 5,
	"event_period": 92,
	"excerptlength": "",
	"layout": 3,
	"no_events": "",
	"period_limits": "1",
	"suffix_lg_class": "",
	"suffix_lgi_class": " py-0",
	"suffix_lgia_class": "",
	"tag_sum": "div",
	"title": "Events (default) js"
};

(function getBlockByIds(paramsObj) {
	const fpath = "/simple-google-icalendar-widget/v1/content-by-attributes";
	paramsObj.tzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
	window.wp.apiFetch({
		path: fpath,
		method: 'POST',
		data: paramsObj,
	}).then((res) => {
				console.log(res);
		document.getElementById("content").innerHTML = res.content;
		//		document.getElementById("params").innerHTML = JSON.stringify(res.params);
	}
	);
}
);
getBlockByIds( // call params
	//   window.wp.i18n,
	paramsObj
);

 