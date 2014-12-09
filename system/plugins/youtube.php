<?php
// Copyright (c) 2013-2014 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Youtube parser plugin
class YellowYoutube
{
	const Version = "0.1.6";
	var $yellow;			//access to API
	
	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("youtubeStyle", "flexible");
	}
	
	// Handle page custom type parsing
	function onParseType($page, $name, $text, $typeShortcut)
	{
		$output = NULL;
		if($name=="youtube" && $typeShortcut)
		{
			list($id, $style, $width, $height) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("youtubeStyle");
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$output .= "<iframe src=\"https://www.youtube.com/embed/".rawurlencode($id)."\" frameborder=\"0\" allowfullscreen";
			if($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
			$output .= "></iframe></div>";
		}
		return $output;
	}
}

$yellow->plugins->register("youtube", "YellowYoutube", YellowYoutube::Version);
?>