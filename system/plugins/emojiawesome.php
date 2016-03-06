<?php
// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Emojiawesome plugin
class YellowEmojiawesome
{
	const Version = "0.6.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("emojiawesomeCdn", "https://cdnjs.cloudflare.com/ajax/libs/twemoji/2.0.0/");
		$this->yellow->config->setDefault("emojiawesomeStylesheetGenerate", "0");
		$this->yellow->config->setDefault("emojiawesomeNormaliseUnicode", "0");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if((empty($name) || $name=="ea") && $shortcut)
		{
			list($shortname, $style) = $this->yellow->toolbox->getTextArgs($text);
			if(preg_match("/ea-(.+)/", $shortname, $matches)) $shortname = strreplaceu("-", "_", $matches[1]);
			if($this->isShortname($shortname))
			{
				$class = $this->normaliseClass(trim("ea ea-$shortname $style"));
				$output = "<i class=\"".htmlspecialchars($class)."\"";
				$output .= " title=\"".htmlspecialchars(":$shortname:")."\"";
				$output .= "></i>";
			}
		}
		return $output;
	}
	
	// Handle page content parsing
	function onParseContentText($page, $text)
	{
		if($this->yellow->config->get("emojiawesomeNormaliseUnicode")) $text = $this->normaliseUnicode($text);
		return $text;
	}

	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name == "header")
		{
			$locationStylesheet = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation")."emojiawesome.css";
			$fileNameStylesheet = $this->yellow->config->get("pluginDir")."emojiawesome.css";
			if(is_file($fileNameStylesheet)) $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$locationStylesheet\" />\n";
			if($this->yellow->config->get("emojiawesomeStylesheetGenerate"))
			{
				$cdn = $this->yellow->config->get("emojiawesomeCdn");
				foreach($this->getLookupData() as $entry)
				{
					$class = $this->normaliseClass("ea-$entry[shortname]");
					$outputData .= ".$class {\n";
					$outputData .= "	background-image: url(\"{$cdn}svg/$entry[image].svg\");\n";
					$outputData .= "}\n";
				}
				$output = "<style type=\"text/css\">\n$outputData</style>";
			}
		}
		return $output;
	}
	
	// Normalise emoji CSS class
	function normaliseClass($text)
	{
		return strreplaceu(array("+1", "-1", "_"), array("plus1", "minus1", "-"), $text);
	}
	
	// Normalise emoji UTF-8 into shortname
	function normaliseUnicode($text)
	{
		//TODO: raw Unicode should be converted too, wunderfeyd wunderfeyd wunderfeyd
		return $text;
	}
	
	// Check if emoji shortname exists
	function isShortname($shortname)
	{
		$found = false;
		foreach($this->getLookupData() as $entry) if($entry["shortname"] == $shortname) { $found = true; break; }
		return $found;
	}
	
	// Return emoji lookup data
	function getLookupData()
	{
		return array(
			array("shortname"=>"smile", "utf8"=>"\xf0\x9f\x98\x84", "image"=>"1f604"),
			array("shortname"=>"laughing", "utf8"=>"\xf0\x9f\x98\x86", "image"=>"1f606"),
			array("shortname"=>"angry", "utf8"=>"\xf0\x9f\x98\xa0", "image"=>"1f620"),
			array("shortname"=>"heart", "utf8"=>"\xe2\x9d\xa4\xef\xb8\x8f", "image"=>"2764"),
			array("shortname"=>"dog", "utf8"=>"\xf0\x9f\x90\xb6", "image"=>"1f436"),
			array("shortname"=>"cat", "utf8"=>"\xf0\x9f\x90\xb1", "image"=>"1f431"),
			array("shortname"=>"yellow_heart", "utf8"=>"\xf0\x9f\x92\x9b", "image"=>"1f49b"),
			array("shortname"=>"coffee", "utf8"=>"\xe2\x98\x95\xef\xb8\x8f", "image"=>"2615"),
			array("shortname"=>"thumbsup", "utf8"=>"\xf0\x9f\x91\x8d", "image"=>"1f44d"),
			array("shortname"=>"+1", "utf8"=>"\xf0\x9f\x91\x8d", "image"=>"1f44d"),
			array("shortname"=>"thumbsdown", "utf8"=>"\xf0\x9f\x91\x8e", "image"=>"1f44e"),
			array("shortname"=>"-1", "utf8"=>"\xf0\x9f\x91\x8e", "image"=>"1f44e"),
		);
	}
}

$yellow->plugins->register("emojiawesome", "YellowEmojiawesome", YellowEmojiawesome::Version);
?>