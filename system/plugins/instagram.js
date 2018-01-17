// Instagram plugin, https://github.com/datenstrom/yellow-plugins/tree/master/instagram
// Copyright (c) 2013-2018 Datenstrom, https://datenstrom.se
// This file may be used and distributed under the terms of the public license.

var initInstagramFromDOM = function()
{
	var elements = document.querySelectorAll(".instagram-media");
	if(elements.length)
	{
		// Load Instagram JavaScript widget on demand
		var fjs = document.getElementsByTagName("script")[0];
		var js = document.createElement("script");
		js.src = "https://platform.instagram.com/en_US/embeds.js";
		fjs.parentNode.insertBefore(js, fjs);
	}
};

window.addEventListener("load", initInstagramFromDOM, false);
