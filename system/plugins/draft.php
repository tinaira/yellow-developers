<?php
// Copyright (c) 2013-2014 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Draft status plugin
class YellowDraft
{
	const Version = "0.1.5";
	var $yellow;			//access to API
	
	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("draftStatusCode", "404");
	}
	
	// Handle page meta data parsing
	function onParseMeta($page, $text)
	{
		if($page->get("status") == "draft") $page->visible = false;
	}
	
	// Handle page content parsing
	function onParseContent($page, $text)
	{
		if($page->get("status") == "draft") $page->error($this->yellow->config->get("draftStatusCode"), "Page has 'draft' status!");
	}
}

$yellow->plugins->register("draft", "YellowDraft", YellowDraft::Version);
?>