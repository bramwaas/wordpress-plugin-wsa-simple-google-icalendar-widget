/**
 * simple-ical-block-edit.js
 * copy of view simple-ical-block 
 * replaced fetch() by apiFetch therefore no need to define restRoot
 * added search in iFrame,  selection on sibid, and choice of Timezone
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
	processNodelist: function (nodeList){
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
	,
	getBlockByIds: function(sibid) {
		const nf = document.querySelectorAll('iframe');
        console.log(nf);
		let cwf, nodeList = document.querySelectorAll('[data-sib-st][data-sib-id='+ sibid + ']');
			console.log(nodeList);
        this.processNodelist(nodeList);
		for (let j = 0; j < nf.length; j++) {
			cwf = (nf[j].contentWindow  || nf[j].contentDocument );
			if (cwf.document)cwf = cwf.document;
			nodeList =cwf.querySelectorAll('[data-sib-st][data-sib-id='+ sibid + ']');
			console.log(nodeList);
	        this.processNodelist(nodeList);
		}
	}
}
}	

/* window.simpleIcalBlock.getBlockByIds(); */

