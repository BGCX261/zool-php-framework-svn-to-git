
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
	alert('unsupported');
}

Zool.popup = function(title, text) {
	alert(title + '\n' + text);
}

Zool.buildElementByJson = function(elem) {

	if (typeof elem == 'string') {
		return document.createCDATASection(elem)
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
		var child = Zool.buildElementByJson(children[i]);
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
	Sys.log(json);
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

};
// // end of Zool


function loadApplication() {
	Sys
	.ajax({
		url : 'http://localhost/zool/?action=m&reRender=/view/index.zool:main-window',
		success : Zool.handleResponse,
		error: function(e){Sys.log(e)},
		parse : 'json'
	});
}

function loadDialog() {
	Sys
	.ajax({
		url : window.arguments[0]
				|| 'http://localhost/zool/?action=m&reRender=/view/dialog.zool:main-window',
		success : Zool.handleResponse,
		parse : 'json'
	});

}
