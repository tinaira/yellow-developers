<?php
// Copyright (c) 2013-2014 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Fontawesome plugin
class YellowFontawesome
{
	const Version = "0.1.4";
	var $yellow;			//access to API
	
	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page extra header
	function onHeaderExtra($page)
	{
		$header = "";
		$locationStylesheet = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation")."fontawesome.css";
		$fileNameStylesheet = $this->yellow->config->get("pluginDir")."fontawesome.css";
		if(is_file($fileNameStylesheet)) $header = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$locationStylesheet\" />\n";
		return $header;
	}
}

$yellow->plugins->register("fontawesome", "YellowFontawesome", YellowFontawesome::Version);
?>