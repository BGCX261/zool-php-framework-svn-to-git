
var Zool = {};

Zool.forms = {};

Zool.byId = function(id) {
	return document.getElementById(id);
}

Zool.valueById = function(id) {
	var el = Zool.byId(id);
	var val = el.value;

	return val;
}

Zool.openDialog = function(url, mode) {
	window
			.openDialog(
					'chrome://myapp/content/dialog.xul',
					'showmore',
					mode || 'chrome, centerscreen, resizable, model, dependent, close, alwaysRaised,fullscreen',
					url);
}

Zool.popup = function(title, text) {
	try {
		Components.classes['@mozilla.org/alerts-service;1'].getService(
				Components.interfaces.nsIAlertsService).showAlertNotification(
				"chrome://myapp/content/main.png", title, text, false, '', null);
	} catch (e) {
		// prevents runtime error on platforms that don't implement
		// nsIAlertsService
	}
}

Zool.renderedScript = '';

Zool.buildElementByJson = function(elem, isScript) {
	
	if (typeof elem == 'string' && !isScript) {		
		return document.createCDATASection(elem);
	}else if(isScript){
		eval(elem);		
		return document.createCDATASection('');
	}
	
	var name = elem[0];
	var attributes = elem[1] || {};
	var children = elem[2] || [];

	var element = document.createElement(name);
	for (key in attributes) {
		var value = attributes[key];
		element.setAttribute(key, value);
	}
	for (i in children) {
		var child = Zool.buildElementByJson(children[i], name == 'script');
		element.appendChild(child);
	}

	return element;

}

Zool.reRenderElement = function(id, content) {

	if (!content)
		return;

	var element = document.getElementById(id);

	if (!element)
		return;

	/*
	 * Special handling
	 */
	if (element.nodeName == 'window' || element.nodeName == 'dialog') {
		for (attri in content[1]) {
			var attr = content[1][attri];
			try {
				document[attri] = attr;
			} catch (e) {
			}
		}

		while (element.hasChildNodes()) {
			element.removeChild(element.childNodes[0]);
		}

		// children
		for (chi in content[2]) {
			var child = content[2][chi];
			child = Zool.buildElementByJson(child);

			element.appendChild(child);
		}

		return;
	}

	var parent = element.parentNode;

	var newElement = Zool.buildElementByJson(content);

	parent.insertBefore(newElement, element);
	parent.removeChild(element);


};

Zool.reRenderByJson = function(json) {
	for (id in json) {
		Zool.reRenderElement(id, json[id]);
	}
};

Zool.handleResponse = function(json) {
	if(!json)return;
	
	if (json.error) {
		Zool.popup('Error', json.error);
		return;
	}

	if (json.reRender) {
		Zool.reRenderByJson(json.reRender);
	}

	if (json.message) {
		Zool.popup(json.message.title, json.message.content);
	}

	// demo
	/*
	var sss = Components.classes["@mozilla.org/content/style-sheet-service;1"]
			.getService(Components.interfaces.nsIStyleSheetService);
	var ios = Components.classes["@mozilla.org/network/io-service;1"]
			.getService(Components.interfaces.nsIIOService);
	var uri = ios.newURI("http://localhost/zool/css/style.css", null, null);
	sss.loadAndRegisterSheet(uri, sss.USER_SHEET);
	*/

};
// // end of Zool

function getWebBrowserPrint() {
	return _content.QueryInterface(Components.interfaces.nsIInterfaceRequestor)
			.getInterface(Components.interfaces.nsIWebBrowserPrint);
}

function loadApplication(loadUrl) {
	return function(){
        Sys
        .ajax({
            url : loadUrl,
            success : Zool.handleResponse,
            error: function(e){Sys.log(e)},
            parse : 'json'
        });
    };
}

function loadDialog() {
	Sys
	.ajax({
		url : window.arguments[0]
				|| 'http://lengyelzsolt.com/zool/?action=m&reRender=/view/dialog.zool:main-window',
		success : Zool.handleResponse,
		parse : 'json'
	});
}
