// Twitter plugin, https://github.com/datenstrom/yellow-plugins/tree/master/twitter
// Copyright (c) 2013-2017 Datenstrom, https://datenstrom.se
// This file may be used and distributed under the terms of the public license.

window.twttr = (function(d, s, id)
{
	var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {};
	if (d.getElementById(id)) return t;
	js = d.createElement(s);
	js.id = id;
	js.src = "https://platform.twitter.com/widgets.js";
	fjs.parentNode.insertBefore(js, fjs);
	t._e = [];
	t.ready = function(f) { t._e.push(f); };
	return t;
}(document, "script", "twitter-wjs"));

var initTwitterFromDOM = function()
{
	// Parse Twitter options from DOM
	var parseOptions = function(element, keyNames)
	{
		var options = {};
		for(var i=0; i<element.attributes.length; i++)
		{
			var attribute = element.attributes[i], key, value;
			if(attribute.nodeName.substr(0, 5)=="data-")
			{
				key = attribute.nodeName.substr(5);
				for(var j=0; j<keyNames.length; j++)
				{
					if(key==keyNames[j].toLowerCase())
					{
						key = keyNames[j];
						break;
					}
				}
				switch(attribute.nodeValue)
				{
					case "true": value = true; break;
					case "false": value = false; break;
					default: value = attribute.nodeValue;
				}
				options[key] = value;
			}
		}
		return options;
	};

	// Initialise twitter elements, show tweets and timelines
	var twitterElements = document.querySelectorAll(".twitter-tweet");
	for(var i=0, l=twitterElements.length; i<l; i++)
	{
		var id = twitterElements[i].getAttribute("data-id");
		var options = parseOptions(twitterElements[i], ["linkColor"]);
		twttr.widgets.createTweet(id, twitterElements[i], options);
	}
	twitterElements = document.querySelectorAll(".twitter-timeline");
	for(var i=0, l=twitterElements.length; i<l; i++)
	{
		var source =
		{
			sourceType: "url",
			url: twitterElements[i].getAttribute("data-url")
		};
		var options = parseOptions(twitterElements[i], ["tweetLimit", "borderColor", "ariaPolite"]);
		twttr.widgets.createTimeline(source, twitterElements[i], options);
	}
};

if(window.addEventListener) {
	window.addEventListener("load", initTwitterFromDOM, false);
} else {
	window.attachEvent("onload", initTwitterFromDOM);
}
