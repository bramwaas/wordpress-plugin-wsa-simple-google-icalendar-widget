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
/* not in editor, only in frontend view				
				if (ni.querySelector('[data-sib-t="true"]')) {
					ni.querySelector('[data-sib-t="true"]').innerHTML = res.params.title;
					titl = ni.querySelector('[data-sib-t="true"]').outerHTML;
				} else 
end not in editor				*/
				{
					if (!res.params.tag_title) {res.params.tag_title = 'h3';}
					titl = '<' + res.params.tag_title + ' class="widget-title block-title" data-sib-t="true">' + res.params.title + '</' + res.params.tag_title + '>';
				}
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
	 * Copies attributes in Option via asynchrone REST call 
	*/
	setSibAttrs1: function(attrs) {
		const fpath = "/simple-google-icalendar-widget/v1/set-sib-attrs";
	return	window.wp.apiFetch({
			path: fpath,
			method: 'POST',
			data: attrs,
		});
	}
	,
	/**
	 * tests attributes from Option via asynchrone REST call 
	*/
	testSibAttrs: function(attrs) {
		const fpath = "/simple-google-icalendar-widget/v1/test-sib-attrs";
	return	window.wp.apiFetch({
			path: fpath,
			method: 'POST',
			data: attrs,
		});
	}
	,
	sleep :function (ms) {return new Promise(resolve => setTimeout(resolve, ms));
    }
	,
	/**
	 * Copies attributes in Option via asynchrone REST call and test if succeeded max 10 times 
	 * only if no other process is  already is doing this (in the same window) else wait.
	 */
	setSibAttrs: async function(attrs) {
		if (typeof attrs.sibid != 'string' || '' == attrs.sibid) return;
		const lcBizzySavingAttrs = Date.now();
		let test = null;
		for (let i = 1; i <= 10; i++) {	
			if (0 == this.bizzySavingAttrs){
				this.bizzySavingAttrs = lcBizzySavingAttrs;
				this.bizzySibid = attrs.sibid;
			}
			if ( lcBizzySavingAttrs == this.bizzySavingAttrs &&	attrs.sibid == this.bizzySibid) {			
				console.log('ssA lSA:' + lcBizzySavingAttrs + ' sibid:' + attrs.sibid + ' now:' + Date.now()); 
				$test = await this.setSibAttrs1(attrs);
				console.log('ssA after test bSA:'  + lcBizzySavingAttrs + ' sibid:' + attrs.sibid + ' nu:' + Date.now());
				console.log($test);			
			    if (true === $test.content) {
					this.bizzySavingAttrs = 0;
					break;
				}
				await this.sleep(37);	
			}
			else {
				await this.sleep(227);	
				console.log('ssA bizzy   bSA:' + this.bizzySavingAttrs + ' sibid:' + this.bizzySibid + ' now:' + Date.now());
				console.log('ssA waiting lSA:' + lcBizzySavingAttrs + ' sibid:' + attrs.sibid);
		}
		} 	

	}

}
}	

