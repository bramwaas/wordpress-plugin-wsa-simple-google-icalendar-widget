/**
 * simple-ical-block-view.js
 * view simple-ical-block output with extra client parameter tzid_ui using REST 
 * restRoot for endpoint passed via inlinescript and this script in enqueue_block_assets 
 * v2.3.0
**/
const endpoint = window.simpleIcalBlock.restRoot + "simple-google-icalendar-widget/v1/content-by-ids";
let ms = Date.now(), fms = 0, stry = 1;

window.simpleIcalBlock = {...(window.simpleIcalBlock || {}), ...{
	fetchFromRest: function(dobj, ni) {
		stry = 1;
		fetch(endpoint, {
			method: "POST",
			cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
			headers: { "Content-Type": "application/json", },
			body: JSON.stringify(dobj),
		}).then((response) => {
			console.log(response);
			if (!response.ok) {
				stry = 10;
				throw new Error(`HTTP error, status = ${response.status}`);
			}
			return response.json();
		}).then((res) => {
			fms = (Date.now() - ms);
			ni.setAttribute('data-sib-st', 'completed-' + stry + '-' + fms);
			console.log(res);
			ni.innerHTML = res.content + '<div>Res try:' + stry + ' fms:' + fms + '</div>';
		}
		).catch((error) => {
			stry = stry + 200;
			fms = (Date.now() - ms);
			console.log('Try:' + stry + 'fms :' + fms);
			console.log(error);
			ni.setAttribute('data-sib-st', 'Error :' + error.code + ':' + error.message + ' try:' + stry + 'fms :' + fms);
			ni.innerHTML = '<p>= Code: ' + error.code + '<br>= Msg: ' + error.message + '</p><div>=Error try:' + stry + ' Fms:' + fms + '</div>';
		})
	}
	,

	getBlockByIds: function() {
		const nodeList = document.querySelectorAll('[data-sib-st]');
		const ptzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
		let paramsObj = {"wptype": "REST", "tzid_ui":ptzid_ui};
		for (let i = 0; i < nodeList.length; i++) {
			paramsObj.sibid = nodeList[i].getAttribute('data-sib-id');
			paramsObj.postid = nodeList[i].getAttribute('data-sib-pid');
			console.log(paramsObj);
			nodeList[i].setAttribute('data-sib-st', 'f1');
			ms = Date.now();
			this.fetchFromRest(paramsObj, nodeList[i]);
		}
	}
}
}

window.simpleIcalBlock.getBlockByIds(
	
);
