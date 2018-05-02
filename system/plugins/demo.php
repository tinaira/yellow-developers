<?php
// Demo plugin, https://github.com/datenstrom/yellow-developers
// Copyright (c) 2013-2018 Datenstrom, https://datenstrom.se
// This file may be used and distributed under the terms of the public license.

class YellowDemo
{
	const VERSION = "0.7.3";
	var $yellow;			//access to API

	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page meta data parsing
	function onParseMeta($page)
	{
		if($page==$this->yellow->page)
		{
			$prefix = strtoloweru($this->yellow->text->getText("LanguageDescription",  $page->get("language")));
			$page->set("editLoginEmail", "$prefix@demo.com");
			$page->set("editLoginPassword", "demo");
		}
	}
}

$yellow->plugins->register("demo", "YellowDemo", YellowDemo::VERSION);
?>
