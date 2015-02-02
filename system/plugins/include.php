<?php
// Copyright (c) 2013-2014 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Include parser plugin
class YellowInclude
{
	const Version = "0.1.7";
	var $yellow;			//access to API
	
	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page custom type parsing
	function onParseType($page, $name, $text, $typeShortcut)
	{
		$output = NULL;
		if($name=="include" && $typeShortcut)
		{
			$args = explode(' ', $text);
			$type = array_shift($args);
			switch($type)
			{
				case "file":	list($fileName) = $args;
								$location = $this->yellow->toolbox->findLocationFromFile(
									$fileName, $this->yellow->config->get("contentDir"),
									$this->yellow->config->get("contentRootDir"), $this->yellow->config->get("contentHomeDir"),
									$this->yellow->config->get("contentDefaultFile"), $this->yellow->config->get("contentExtension"));
								$content = $this->yellow->pages->find($location);
								if($content)
								{
									$page->setLastModified($content->getModified());
									$output = $content->getContent();
								} else {
									$page->error(500, "Include '$fileName' does not exist!");
								}
								break;
				case "snippet": list($snippet) = $args;
								$fileNameSnippet = $this->yellow->config->get("snippetDir")."$snippet.php";
								if(is_file($fileNameSnippet))
								{
									$page->setLastModified(filemtime($fileNameSnippet));
									$this->yellow->pages->snippetArgs = $args;
									ob_start();
									global $yellow;
									require($fileNameSnippet);
									$output = ob_get_contents();
									ob_end_clean();
								} else {
									$page->error(500, "Include '$snippet' does not exist!");
								}
								break;
				case "text":	list($key, $language) = $args;
								if(empty($language)) $language = $page->get("language");
								if($this->yellow->text->isExisting($key, $language))
								{
									$output = $this->yellow->text->getTextHtml($key, $language);
								} else {
									$page->error(500, "Include '$key' does not exist!");
								}
								break;
			}
		}
		return $output;
	}
}

$yellow->plugins->register("include", "YellowInclude", YellowInclude::Version);
?>