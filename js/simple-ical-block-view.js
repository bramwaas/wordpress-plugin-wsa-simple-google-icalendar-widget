/**
 * simple-ical-block-view.js
 * view simple-ical-block output with extra client parameter tzid_ui using REST 
 * restRoot for endpoint passed via inlinescript and this script in enqueue_block_assets
 *  * v2.4.30
 * 2.4.3 add search in iFrame and choice of used timezone via data-sib-utzui
**/
const endpoint = window.simpleIcalBlock.restRoot + "simple-google-icalendar-widget/v1/content-by-ids";
let titl;

window.simpleIcalBlock = {...(window.simpleIcalBlock || {}), ...{
	fetchFromRest: function(dobj, ni) {
		fetch(endpoint, {
			method: "POST",
			cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
			headers: { "Content-Type": "application/json", },
			body: JSON.stringify(dobj),
		}).then((response) => {
			if (!response.ok) {
				throw new Error(`HTTP error, status = ${response.status}`);
			}
			return response.json();
		}).then((res) => {
			if (res.params.title) {
				if (ni.querySelector('[data-sib-t="true"]')) {
					ni.querySelector('[data-sib-t="true"]').innerHTML = res.params.title;
					titl = ni.querySelector('[data-sib-t="true"]').outerHTML;
				} else {
					if (!res.params.tag_title) {res.params.tag_title = 'h3';}
					titl = '<' + res.params.tag_title + ' class="widget-title" data-sib-t="true">' + res.params.title + '</' + res.params.tag_title + '>';
				}
			} else {
				titl = '';
			}
			ni.innerHTML = titl + res.content;
			ni.setAttribute('data-sib-st', 'completed');
		}
		).catch((error) => {
			console.log(error);
			ni.setAttribute('data-sib-st', 'Error :' + error.code + ':' + error.message);
			ni.innerHTML = '<p>= Code: ' + error.code + '<br>= Msg: ' + error.message + '</p>';
		})
	}
	,
	processNodelist: function (nodeList){
		const ptzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
		let paramsObj = {"wptype": "REST"};
		for (let i = 0; i < nodeList.length; i++) {
			paramsObj.sibid = nodeList[i].getAttribute('data-sib-id');
   			paramsObj.tzid_ui = (typeof nodeList[i].getAttribute('data-sib-utzui') == 'string' && nodeList[i].getAttribute('data-sib-utzui') == '1') ? ptzid_ui : ''; 
			nodeList[i].setAttribute('data-sib-st', 'f1');
			this.fetchFromRest(paramsObj, nodeList[i]);
		}
	}
	,
	getBlockByIds: function() {
		const nf = document.querySelectorAll('iframe');
		let cwf, nodeList = document.querySelectorAll('[data-sib-st]');
        this.processNodelist(nodeList);
		for (let j = 0; j < nf.length; j++) {
			cwf = (nf[j].contentWindow  || nf[j].contentDocument );
			if (cwf.document)cwf = cwf.document;
			nodeList =cwf.querySelectorAll('[data-sib-st]');
	        this.processNodelist(nodeList);
		}
        
	}
}
}
window.simpleIcalBlock.getBlockByIds();
