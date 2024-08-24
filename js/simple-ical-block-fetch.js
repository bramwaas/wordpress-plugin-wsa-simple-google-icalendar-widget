/**
 * simple-ical-block-fetch.js
 * set attributes in option, gets rendered output with fetch from server.
 * started as copy of view simple-ical-block 
 * replaced fetch() by apiFetch therefore no need to define restRoot
 * added search in iFrame,  selection on sibid, and choice of Timezone
 * object simpleIcalBlock differentiated by adding F
 * v2.4.4 use present attrs to fetch content not from option in case attrs are not set in option
**/
window.simpleIcalBlockF = {...(window.simpleIcalBlockF || {}), ...{
	bizzySavingAttrs: 0, 
	bizzySibid: '',
	fetchFromRest: function(dobj, ni) {
		const fpath = "/simple-google-icalendar-widget/v1/content-by-ids";
		let titl;
		window.wp.apiFetch({
			path: fpath,
			method: 'POST',
			data: dobj,
		}).then((res) => {
			if (res.params && res.params.title) {
					if (!res.params.tag_title) {res.params.tag_title = 'h3';}
					titl = '<' + res.params.tag_title + ' class="widget-title block-title" data-sib-t="true">' + res.params.title + '</' + res.params.tag_title + '>';
			} else {
				titl = '';
			}
			ni.innerHTML = titl + res.content;
			ni.setAttribute('data-sib-st', 'completed');
		}).catch((error) => {
			ni.setAttribute('data-sib-st', 'Error :' + error.code + ':' + error.message);
			ni.innerHTML = '<p>= Code: ' + error.code + '<br>= Msg: ' + error.message + '</p>';
		});
	}
	,
	processNodelist: function (nodeList, paramsObj){
			const ptzid_ui = Intl.DateTimeFormat().resolvedOptions().timeZone;
  			paramsObj.wptype = "REST";
			for (let i = 0; i < nodeList.length; i++) {
				paramsObj.sibid = nodeList[i].getAttribute('data-sib-id');
    			paramsObj.tzid_ui = (typeof nodeList[i].getAttribute('data-sib-utzui') == 'string' && nodeList[i].getAttribute('data-sib-utzui') == '1') ?  ptzid_ui : ''; 
				nodeList[i].setAttribute('data-sib-st', 'f1');
				this.fetchFromRest(paramsObj, nodeList[i]);
			}
		
	}
	,
	getBlockByIds: function(attrs) {
		const nf = document.querySelectorAll('iframe');
		let sibid = attrs.sibid;
		let cwf, nodeList = document.querySelectorAll('[data-sib-st][data-sib-id='+ sibid + ']');
        this.processNodelist(nodeList, attrs);
		for (let j = 0; j < nf.length; j++) {
			cwf = (nf[j].contentWindow  || nf[j].contentDocument );
			if (cwf.document)cwf = cwf.document;
			nodeList =cwf.querySelectorAll('[data-sib-st][data-sib-id='+ sibid + ']');
	        this.processNodelist(nodeList, attrs);
		}
	}
	,
	/**
	 * encapsulate setTimeout in a thenable function to use it in await
	*/
	sleep :function (ms) {return new Promise(resolve => setTimeout(resolve, ms));
    }
	,
	/**
	 * Copies attributes in Option via asynchrone REST call and test if succeeded max 5 times 
	 * only if no other process is  already is doing this (in the same window) else wait.
	 */
	setSibAttrs: async function(attrs) {
		if (typeof attrs.sibid != 'string' || '' == attrs.sibid) return;
		const fpath = "/simple-google-icalendar-widget/v1/set-sib-attrs";
		const lcBizzySavingAttrs = Date.now();
		let res = null;
		for (let i = 100; i > 0; i--) {	
			if (0 == this.bizzySavingAttrs){
				this.bizzySavingAttrs = lcBizzySavingAttrs;
				this.bizzySibid = attrs.sibid;
				i = 5;
			}
			if ( lcBizzySavingAttrs == this.bizzySavingAttrs &&	attrs.sibid == this.bizzySibid) {			
				res = await window.wp.apiFetch({path: fpath, method: 'POST', data: attrs, });
			    if (true === res.content) {
					this.bizzySavingAttrs = 0;
					break;
				}
				await this.sleep(50);	
			}
			else {
				await this.sleep(250);	
			}
		} 
		if (this.bizzySavingAttrs == lcBizzySavingAttrs) {
							this.bizzySavingAttrs = 0;
		}
	}

}
}	

