<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Imagegallery plugin
class YellowImagegallery
{
	const Version = "0.0.0";
	var $yellow;			//access to API

	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("imagegalleryStyle", "imagegallery");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $typeShortcut)
	{
		$output = NULL;
		if($name=="imagegallery" && $typeShortcut)
		{
			list($pattern, $style, $size) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("imagegalleryStyle");
			if(empty($size)) $size = "100%";
			$output = "<ul class=\"".htmlspecialchars($style)."\">\n";
			if($this->yellow->plugins->isExisting("image"))
			{
				$files = empty($pattern) ? $page->getFiles(true) : $this->yellow->files->index(true, true)->match("/$pattern/");
				foreach($files as $file)
				{
					list($src, $width, $height) = $this->yellow->plugins->get("image")->getImageInfo($file->fileName, $size, $size);
					$output .= "<li><a href=\"".htmlspecialchars($file->getLocation())."\">";
					$output .="<img src=\"".htmlspecialchars($src)."\" width=\"".htmlspecialchars($width)."\" height=\"".
						htmlspecialchars($height)."\" alt=\"".htmlspecialchars(basename($file->getLocation()))."\" title=\"".
						htmlspecialchars(basename($file->getLocation()))."\" />";
					$output .= "</a></li>\n";
				}
			}
			$output .= "</ul>";
		}
		return $output;
	}

	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = "";
		if($name == "header")
		{
			//TODO: See https://github.com/datenstrom/yellow/issues/70
		}
		return $output;
	}
}

$yellow->plugins->register("imagegallery", "YellowImagegallery", YellowImagegallery::Version);
?>