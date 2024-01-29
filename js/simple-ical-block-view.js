/**
 * simple-ical-block-view.js
 * view simple-ical-block output with extra client parameter tzid_ui using REST fetched @wordpress/api-fetch 
 * why this is better than plain javascript Fetch API I don't know yet.
 * v2.3.0
*/ 

function getBlockByIds(paramsObj2) {
	const fpath = "/simple-google-icalendar-widget/v1/content-by-ids";
	const nodeList = document.querySelectorAll('[data-sib-st]');
	const ptzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
	let paramsObj = {};
	for (let i = 0; i < nodeList.length; i++) {
		paramsObj.tzid_ui = ptzid_ui;
		paramsObj.blockid = nodeList[i].getAttribute('data-sib-id');
		paramsObj.postid = nodeList[i].getAttribute('data-sib-pid');
		console.log(paramsObj);
		window.wp.apiFetch({
			path: fpath,
			method: 'POST',
			data: paramsObj,
		}).then((res) => {
			console.log(res);
			nodeList[i].innerHTML = res.content;
			nodeList[i].setAttribute('data-sib-st', 'completed');
		}
		);
	}
}
getBlockByIds(
	{}
);