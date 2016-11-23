<?php
// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Links plugin
class YellowLinks
{
	const VERSION = "0.6.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("linksPagePrevious", "0");
		$this->yellow->config->setDefault("linksPageNext", "1");
		$this->yellow->config->setDefault("linksStyle", "links");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = null;
		if($name=="links" && $shortcut)
		{
			$style = $this->yellow->config->get("linksStyle");
			$parent = $page->getParent();
			$pages = $parent ? $page->getSiblings(!$parent->isVisible()) : $this->yellow->pages->clean();
			$page->setLastModified($pages->getModified());
			if(count($pages))
			{
				$output = "<div class=\"".htmlspecialchars($style)."\">\n";
				if($pages->getPagePrevious($page) && $this->yellow->config->get("linksPagePrevious"))
				{
					$pagePrevious = $pages->getPagePrevious($page);
					$text = $this->yellow->text->get("pagePrevious");
					$text = preg_replace("/@title/i", $pagePrevious->get("title"), $text);
					$output .= "<a class=\"previous\" href=\"".$pagePrevious->getLocation(true)."\">".htmlspecialchars($text)."</a>\n";
				}
				if($pages->getPageNext($page) && $this->yellow->config->get("linksPageNext"))
				{
					$pageNext = $pages->getPageNext($page);
					$text = $this->yellow->text->get("pageNext");
					$text = preg_replace("/@title/i", $pageNext->get("title"), $text);
					$output .= "<a class=\"next\" href=\"".$pageNext->getLocation(true)."\">".htmlspecialchars($text)."</a>\n";
				}
				$output .="</div>\n";
			}
		}
		return $output;
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		return $this->onParseContentBlock($this->yellow->page, $name, "", true);
	}
}

$yellow->plugins->register("links", "YellowLinks", YellowLinks::VERSION);
?>