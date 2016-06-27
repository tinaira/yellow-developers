// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Slider plugin 0.6.2
var initFlickityFromDOM = function() {
	
	// Parse slider options from DOM
	var parseOptions = function(el) {
		var keyNames = ['prevNextButtons', 'pageDots', 'arrowShape', 'lazyLoad', 'autoPlay', 'initialIndex',
					'draggable', 'clickable', 'freeScroll', 'wrapAround', 'asNavFor', 'cellSelector', 'cellAlign'],
		numKeyNames = keyNames.length,
		numAttributes = el.attributes.length,
		options = {};
		for(var i = 0; i < numAttributes; i++) {
			var att = el.attributes[i], key, value;
			if(att.nodeName.substring(0, 5) == 'data-') {
				key = att.nodeName.substring(5);
				for(var j = 0; j < numKeyNames; j++) {
					if (key == keyNames[j].toLowerCase()) {
						key = keyNames[j];
						break;
					}
				}
				switch(att.nodeValue)
				{
					case 'true': value = true; break;
					case 'false': value = false; break;
					default: value = att.nodeValue;
				}
				options[key] = value;
			}
		}
		return options;
	};
	
	// Parse slider and picture index from URL
	var parseHash = function() {
		var hash = window.location.hash.substring(1),
		params = {};
		if(hash.length < 5) {
			return params;
		}
		var vars = hash.split('&');
		for (var i = 0; i < vars.length; i++) {
			if(!vars[i]) {
				continue;
			}
			var pair = vars[i].split('=');
			if(pair.length < 2) {
				continue;
			}
			params[pair[0]] = pair[1];
		}
		return params;
	};	
	
	// Initialise slider elements and bind events
	var sliders = {};
	var sliderElements = document.querySelectorAll( '.flickity' );
	for(var i = 0, l = sliderElements.length; i < l; i++) {
		var options = parseOptions(sliderElements[i]);
		sliders[i] = new Flickity(sliderElements[i], options);
		if(options.clickable) {
			sliders[i].on('staticClick', function() { this.next(options.wrapAround) });
		}
	}
	
	// Check if URL contains slider and picture index
	if(sliderElements.length)
	{
		var params = parseHash();
		if(params.sid>0 && params.sid<=sliderElements.length && params.pid>0) {
			sliders[params.sid-1].select(params.pid-1, false, true);
		}
	}
};

if(window.addEventListener) {
	window.addEventListener('load', initFlickityFromDOM, false);
} else {
	window.attachEvent('onload', initFlickityFromDOM);
}