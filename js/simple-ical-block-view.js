/**
 * simple-ical-block-view.js
 * view simple-ical-block output with extra client parameter tzid_ui using REST fetched @wordpress/api-fetch 
 * why this is better than plain javascript Fetch API I don't know yet.
 * v2.3.0
*/ 
let ms = Date.now();
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

function fetchFromRest1(dobj, ni) {
	const fpath = "/simple-google-icalendar-widget/v1/content-by-ids";
	window.wp.apiFetch({
		path: fpath,
		method: 'POST',
		data: dobj,
	}).then((res) => {
		ni.setAttribute('data-sib-st', 'completed-' + (Date.now() - ms));
		console.log(res);
		rresult = res;
		ni.innerHTML = res.content;
//		ni.setAttribute('data-sib-st', 'completed-' + (d_now.getTime() - time));
	},
	(error) => {
		ni.innerHTML = '<p>Code: ' + error.code + '<br>Msg: ' + error.message + '</p>' ;
	}
	);

}

function fetchFromRest(dobj, ni) {
	const url = "https://dev1.waasdorpsoekhan.nl/wp6/wp-json/simple-google-icalendar-widget/v1/content-by-ids";

	let responsePromise = requestREST(url, dobj);
	responsePromise.then((res) => {
		ni.setAttribute('data-sib-st', 'completed_' + (Date.now() - ms));
		console.log(res);
		rresult = res;
		ni.innerHTML = res.content;
		//		ni.setAttribute('data-sib-st', 'completed-' + (d_now.getTime() - time));
	},
		(error) => {
			ni.innerHTML = '<p>= Code: ' + error.code + '<br>= Msg: ' + error.message + '</p>';
		}
	);
	async function requestREST(url = "", dobj = {}) {
		const response = await fetch(url, {
			method: "POST", // *GET, POST, OPTIONS, PUT, DELETE, etc.
			mode: "cors", // no-cors, *cors, same-origin
			cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
			credentials: "same-origin", // include, *same-origin, omit
			headers: { "Content-Type": "application/json", }, // 'Content-Type': 'application/x-www-form-urlencoded',
			redirect: "follow", // manual, *follow, error
			referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
			body: JSON.stringify(dobj), // body data type must match "Content-Type" header
		});
		const rJson = await response.json();
		return rJson;
	}

}

