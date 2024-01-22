/**
 * FetchREST.js
 * plain javascript function to demonstrate/test REST output.
 * v2.2.0
*/ 
(function() {
	const url = "https://dev1.waasdorpsoekhan.nl/wp6/wp-json/simple-google-icalendar-widget/v1/content-by-attributes";
	let paramsObj = {
		"after_events" :"",
		"allowhtml" :false,
		"anchorId" :"Simple-ical-Block-1",
		"blockid" :"b19146a0c-f760-495f-8560-8cd988b1590d",
		"cache_time" :60,
		"calendar_id" :"#example,https://calendar.google.com/calendar/ical/nl.dutch%23holiday%40group.v.calendar.google.com/public/basic.ics",
		"clear_cache_now" :false,
		"dateformat_lg" :"l jS \\of F",
		"dateformat_lgend" :"",
		"dateformat_tend" :" - G:i ",
		"dateformat_tsend" :"",
		"dateformat_tstart" :"G:i",
		"dateformat_tsum" :"G:i ",
		"event_count" :5,
		"event_period" :92,
		"excerptlength" :"",
		"layout" :3,
		"no_events" :"",
		"period_limits" :"1",
		"suffix_lg_class" :"",
		"suffix_lgi_class" :" py-0",
		"suffix_lgia_class" :"",
		"tag_sum" :"div",
		"title" :"Events (default)"
	};
	paramsObj.tzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;

	let responsePromise = requestREST(url, paramsObj);
	responsePromise.then(function(value) { processRestResponse(value); },
		function(error) { console.log(error); }
	);
	async function requestREST(url = "", paramsObj = {}) {
		const response = await fetch(url, {
			method: "POST", // *GET, POST, OPTIONS, PUT, DELETE, etc.
			mode: "cors", // no-cors, *cors, same-origin
			cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
			credentials: "same-origin", // include, *same-origin, omit
			headers: { "Content-Type": "application/json", }, // 'Content-Type': 'application/x-www-form-urlencoded',
			redirect: "follow", // manual, *follow, error
			referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
			body: JSON.stringify(paramsObj), // body data type must match "Content-Type" header
		});
		const rJson = await response.json();
		return rJson;
	}

	function processRestResponse(value) {
		document.getElementById("content").innerHTML = value.content;
		document.getElementById("params").innerHTML = JSON.stringify(value.params);
	}
})();