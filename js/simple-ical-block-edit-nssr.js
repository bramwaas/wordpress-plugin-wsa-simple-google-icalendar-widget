/**
 * simple-ical-block-edit.js
 * copy of view simple-ical-block 
 * replaced fetch() by apiFetch therefore no need to define restRoot
 * extra choice of timezone 
 * v2.4.3
**/
let titl;

window.simpleIcalBlock = {...(window.simpleIcalBlock || {}), ...{
	fetchFromRest: function(dobj, ni) {
		const fpath = "/simple-google-icalendar-widget/v1/content-by-ids";
		window.wp.apiFetch({
			path: fpath,
			method: 'POST',
			data: dobj,
		}).then((res) => {
			console.log(res);
			ni.setAttribute('data-sib-st', 'completed');
			if (ni.getAttribute('data-sib-notitle')) titl = ''; else titl = ni.querySelector( '[data-sib-t="true"]' ).outerHTML;
			ni.innerHTML = titl + res.content;
		}).catch((error) => {
			console.log(error);
			ni.setAttribute('data-sib-st', 'Error :' + error.code + ':' + error.message);
			ni.innerHTML = '<p>= Code: ' + error.code + '<br>= Msg: ' + error.message + '</p>';
		});
	}
	,
	getBlockByIds: function() {
		/* first select document of iFrame
		example:
		  var x = document.querySelector('iframe[id="myframe"]');
  var y = (x.contentWindow || x.contentDocument);
  if (y.document)y = y.document;
  y.body.style.backgroundColor = "red";
*/
		
		const nodeList = document.querySelectorAll('[data-sib-st]');
			console.log(nodeList);
		const ptzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
			console.log(ptzid_ui);
		let paramsObj = {"wptype": "REST", "tzid_ui":ptzid_ui};
		for (let i = 0; i < nodeList.length; i++) {
			paramsObj.sibid = nodeList[i].getAttribute('data-sib-id');
			nodeList[i].setAttribute('data-sib-st', 'f1');
			this.fetchFromRest(paramsObj, nodeList[i]);
		}
	}
}
}	

window.simpleIcalBlock.getBlockByIds();

