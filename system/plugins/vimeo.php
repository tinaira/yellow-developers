<?php
// Copyright (c) 2013-2014 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Vimeo parser plugin
class YellowVimeo
{
	const Version = "0.1.6";
	var $yellow;			//access to API
	
	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("vimeoStyle", "flexible");
	}
	
	// Handle page custom type parsing
	function onParseType($page, $name, $text, $typeShortcut)
	{
		$output = NULL;
		if($name=="vimeo" && $typeShortcut)
		{
			list($id, $style, $width, $height) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("vimeoStyle");
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$output .= "<iframe src=\"https://player.vimeo.com/video/".rawurlencode($id)."\" frameborder=\"0\" allowfullscreen";
			if($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
			$output .= "></iframe></div>";
		}
		return $output;
	}
}

$yellow->plugins->register("vimeo", "YellowVimeo", YellowVimeo::Version);
?>