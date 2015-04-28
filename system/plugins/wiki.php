<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Wiki plugin
class YellowWiki
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("wikiPaginationLimit", "30");
	}
	
	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("template") == "wikipages")
		{
			if($this->yellow->toolbox->isLocationArgs($this->yellow->toolbox->getLocation()))
			{
				$pages = $this->yellow->page->getChildren(!$this->yellow->page->isVisible());				
				$pagesFilter = array();
				if($_REQUEST["special"] == "changes")
				{
					$chronologicalOrder = true;
					array_push($pagesFilter, $this->yellow->text->get("wikiSpecialChanges"));
				}
				if($_REQUEST["tag"])
				{
					$pages->filter("tag", $_REQUEST["tag"]);
					array_push($pagesFilter, $pages->getFilter());
				}
				if($_REQUEST["title"])
				{
					$pages->filter("title", $_REQUEST["title"], false);
					array_push($pagesFilter, $pages->getFilter());
				}
				if($_REQUEST["modified"])
				{
					$pages->filter("modified", $_REQUEST["modified"], false);
					array_push($pagesFilter, $this->yellow->text->normaliseDate($pages->getFilter()));
				}
				if(!empty($pagesFilter))
				{
					$pages->sort($chronologicalOrder ? "modified" : "title", $chronologicalOrder);
					$pages->pagination($this->yellow->config->get("wikiPaginationLimit"));
					if(!$pages->getPaginationNumber()) $this->yellow->page->error(404);
					$title = implode(' ', $pagesFilter);
					$this->yellow->page->set("titleHeader", $title." - ".$this->yellow->page->get("sitename"));
					$this->yellow->page->set("titleWiki", $this->yellow->text->get("wikiFilter")." ".$title);
					$this->yellow->page->set("wikipagesChronologicalOrder", $chronologicalOrder);
				}
				$this->yellow->page->setPages($pages);
				$this->yellow->page->setLastModified($pages->getModified());
				$this->yellow->page->setHeader("Cache-Control", "max-age=60");
			}
		}
	}
}

$yellow->plugins->register("wiki", "YellowWiki", YellowWiki::Version);
?>