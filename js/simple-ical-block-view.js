/**
 * simple-ical-block-view.js
 * view simple-ical-block output with extra client parameter tzid_ui using REST fetched @wordpress/api-fetch 
 * why this is better than plain javascript Fetch API I don't know yet.
 * v2.3.0
**/ 
const endpoint = document.querySelector('link[rel="https://api.w.org/"]').href + "simple-google-icalendar-widget/v1/content-by-ids";
let ms = Date.now();
let fms = 0;
let rresult = null;
getBlockByIds(
	{}
);

function getBlockByIds(paramsObj2) {
	const nodeList = document.querySelectorAll('[data-sib-st]');
	const ptzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
	let paramsObj = {};
	for (let i = 0; i < nodeList.length; i++) {
		paramsObj.tzid_ui = ptzid_ui;
		paramsObj.blockid = nodeList[i].getAttribute('data-sib-id');
		paramsObj.postid = nodeList[i].getAttribute('data-sib-pid');
		console.log(paramsObj);
		nodeList[i].setAttribute('data-sib-st', 'f1');
		ms = Date.now();
		fetchFromRest(paramsObj, nodeList[i]);
	}
}

function fetchFromRest(dobj, ni) {
	let mcatch = { "content": "" };
	requestREST(endpoint, dobj).then((res) => {
		fms = (Date.now() - ms);
		ni.setAttribute('data-sib-st', 'completed-' + fms);
		console.log(res);
		rresult = res;
		ni.innerHTML = res.content + '<div>fms:' + fms + '</div>';
		//		ni.setAttribute('data-sib-st', 'completed-' + fms);
	},
		(error) => {
			ni.innerHTML = '<p>= Code: ' + error.code + '<br>= Msg: ' + error.message + '</p>' + + '<div>= Fms:' + fms + '</div>';
		}
	);
	async function requestREST(url = "", dobj = {}) {
		try {
			const response = await fetch(url, {
				method: "POST",
				cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
				headers: { "Content-Type": "application/json", },
				body: JSON.stringify(dobj),
			});
			const rJson = await response.json();
			return rJson;
		} catch (error) { console.error("Error:", error); };
		mcatch.content = '<h3 class="widget-title"> Error fetching data, try again </h3><p>- fms:' + fms + '</p>'
		return JSON.stringify(mcatch);
	}

}

