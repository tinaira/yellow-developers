<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Syntax highlight plugin
class YellowSyntaxhighlight
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("syntaxStylesheetDefault", "0");
		$this->yellow->config->setDefault("syntaxLineNumber", "0");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $typeShortcut)
	{
		$output = NULL;
		if(!empty($name) && !$typeShortcut)
		{
			list($name, $lineNumber) = explode(':', $name);
			if(is_null($lineNumber)) $lineNumber = $this->yellow->config->get("syntaxLineNumber");
			$geshi = new GeSHi(trim($text), $name);
			$geshi->set_language_path($this->yellow->config->get("pluginDir")."/syntaxhighlight/");
			$geshi->set_header_type(GESHI_HEADER_PRE_TABLE);
			$geshi->enable_line_numbers($lineNumber ? GESHI_NORMAL_LINE_NUMBERS : GESHI_NO_LINE_NUMBERS);
			$geshi->start_line_numbers_at($lineNumber);
			$geshi->enable_classes(true);
			$geshi->enable_keyword_links(false);
			$output = $geshi->parse_code();
			$output = preg_replace("#<pre(.*?)>(.+?)</pre>#s", "<pre$1><code>$2</code></pre>", $output);
		}
		return $output;
	}
	
	// Handle page extra HTML data
	function onExtra()
	{
		$output = "";
		if(!$this->yellow->config->get("syntaxStylesheetDefault"))
		{
			$locationStylesheet = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation")."syntaxhighlight.css";
			$fileNameStylesheet = $this->yellow->config->get("pluginDir")."syntaxhighlight.css";
			if(is_file($fileNameStylesheet)) $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$locationStylesheet\" />\n";
		} else {
			$geshi = new GeSHi();
			$geshi->set_language_path($this->yellow->config->get("pluginDir")."/syntaxhighlight/");
			foreach($geshi->get_supported_languages() as $language)
			{
				if($language == "geshi") continue;
				$geshi->set_language($language);
				$output .= $geshi->get_stylesheet(false);
			}
			$output = "<style type=\"text/css\">\n$output</style>";
		}
		return $output;
	}
}
	
require_once("syntaxhighlight/geshi.php");

$yellow->plugins->register("syntaxhighlight", "YellowSyntaxhighlight", YellowSyntaxhighlight::Version);
?>