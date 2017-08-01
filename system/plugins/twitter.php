<?php
// Twitter plugin, https://github.com/datenstrom/yellow-plugins/tree/master/twitter
// Copyright (c) 2013-2017 Datenstrom, https://datenstrom.se
// This file may be used and distributed under the terms of the public license.

class YellowTwitter
{
	const VERSION = "0.7.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = null;
		if($name=="twitter" && $shortcut)
		{
			list($id, $width, $height, $theme) = $this->yellow->toolbox->getTextArgs($text);
			$output = "<div class=\"twitter\">\n";
			$output .= "<a class=\"twitter-timeline\"";
			if($width && $height) $output .=" data-width=\"".htmlspecialchars($width)."\" data-height=\"".htmlspecialchars($height)."\"";
			if($theme) $output .=" data-theme=\"".htmlspecialchars($theme)."\"";
			$output .= " data-dnt=\"true\" href=\"https://twitter.com/".rawurlencode($id)."\">Tweets by @".htmlspecialchars($id)."</a>\n";
			$output .= "<script async src=\"https://platform.twitter.com/widgets.js\"></script>\n";
			$output .= "</div>";
		}
		if($name=="twitterfollow" && $shortcut)
		{
			list($id) = $this->yellow->toolbox->getTextArgs($text);
			$output = "<div class=\"twitterfollow\">\n";
			$output .= "<a href=\"https://twitter.com/".rawurlencode($id)."\" class=\"twitter-follow-button\" data-size=\"large\" data-dnt=\"true\">Follow @".htmlspecialchars($id)."</a>\n";
			$output .= "<script async src=\"https://platform.twitter.com/widgets.js\"></script>\n";
			$output .= "</div>";
		}
		return $output;
	}
}

$yellow->plugins->register("twitter", "YellowTwitter", YellowTwitter::VERSION);
?>
