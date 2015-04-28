<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Include plugin
class YellowInclude
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $typeShortcut)
	{
		$output = NULL;
		if($name=="include" && $typeShortcut)
		{
			$args = explode(' ', $text);
			list($fileName) = $args;
			$location = $this->yellow->lookup->findLocationFromFile($fileName);
			$content = $this->yellow->pages->find($location);
			if($content)
			{
				$page->setLastModified($content->getModified());
				$output = $content->getContent();
			} else {
				$page->error(500, "Include '$fileName' does not exist!");
			}
		}
		if($name=="snippet" && $typeShortcut)
		{
			$args = explode(' ', $text);
			list($snippet) = $args;
			$fileNameSnippet = $this->yellow->config->get("snippetDir")."$snippet.php";
			if(is_file($fileNameSnippet))
			{
				ob_start();
				$page->setLastModified(filemtime($fileNameSnippet));
				$this->yellow->pages->snippetArgs = $args;
				global $yellow;
				require($fileNameSnippet);
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				$page->error(500, "Snippet '$snippet' does not exist!");
			}
		}
		return $output;
	}
}

$yellow->plugins->register("include", "YellowInclude", YellowInclude::Version);
?>