<?php
// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Statistics command plugin
class YellowStats
{
	const Version = "0.6.2";
	var $yellow;			//access to API
	var $views;				//detected views

	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("statsDays", 30);
		$this->yellow->config->setDefault("statsLinesMax", 8);
		$this->yellow->config->setDefault("statsLogDir", "/var/log/apache2/");
		$this->yellow->config->setDefault("statsLogFile", "(.*)access.log");
		$this->yellow->config->setDefault("statsLocationSearch", "/search/");
		$this->yellow->config->setDefault("statsLocationIgnore", "media|system|edit");
		$this->yellow->config->setDefault("statsSpamFilter", "bot|crawler|spider");
	}

	// Handle command help
	function onCommandHelp()
	{
		return "stats [DAYS FILENAME]\n";
	}
	
	// Handle command
	function onCommand($args)
	{
		list($name, $command) = $args;
		switch($command)
		{
			case "stats":	$statusCode = $this->statsCommand($args); break;
			default:		$statusCode = 0;
		}
		return $statusCode;
	}

	// Create statistics
	function statsCommand($args)
	{
		$statusCode = 0;
		list($dummy, $command, $days, $fileName) = $args;
		if($this->checkStaticConfig())
		{
			if(empty($days)) $days = $this->yellow->config->get("statsDays");
			if(empty($fileName))
			{
				$path = $this->yellow->config->get("statsLogDir");
				$regex = "/^".basename($this->yellow->config->get("statsLogFile"))."$/";
				$fileNames = $this->yellow->toolbox->getDirectoryEntries($path, $regex, true, false);
				$statusCode = $this->analyseRequests($days, $path, $fileNames);
			} else {
				$statusCode = $this->analyseRequests($days, dirname($fileName), array($fileName));
			}
		} else {
			$statusCode = 500;
			$this->views = 0;
			$fileName = $this->yellow->config->get("configDir").$this->yellow->config->get("configFile");
			echo "ERROR creating statistics: Please configure ServerScheme, ServerName,  ServerBase, ServerTime in file '$fileName'!\n";
		}
		echo "Yellow $command: $days day".($days!=1 ? 's' : '').", ";
		echo "$this->views view".($this->views!=1 ? 's' : '')."\n";
		return $statusCode;
	}
	
	// Analyse logfile requests
	function analyseRequests($days, $path, $fileNames)
	{
		$this->yellow->toolbox->timerStart($time);
		$this->views = 0;
		if(!empty($fileNames))
		{
			$statusCode = 200;
			$sites = $content = $search = $errors = $clients = array();
			$timeStop = time() - (60 * 60 * 24 * $days);
			$locationSelf = $this->yellow->config->get("serverBase");
			$locationIgnore = $this->yellow->config->get("statsLocationIgnore");
			$refererSelf = $this->yellow->config->get("serverName").$this->yellow->config->get("serverBase");
			$spamFilter = $this->yellow->config->get("statsSpamFilter");
			foreach($fileNames as $fileName)
			{
				if(defined("DEBUG") && DEBUG>=1) echo "YellowStats::analyseRequests file:$fileName\n";
				$fileHandle = @fopen($fileName, "r");
				if($fileHandle)
				{
					$filePos = filesize($fileName)-1; $fileTop = -1;
					while(($line = $this->getFileLinePrevious($fileHandle, $filePos, $fileTop, $dataBuffer)) !== false)
					{
						if(preg_match("/^(\S+) (\S+) (\S+) \[(.+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) \"(.*?)\" \"(.*?)\"$/", $line, $matches))
						{
							list($line, $ip, $dummy1, $dummy2, $timestamp, $method, $uri, $protocol, $status, $size, $referer, $userAgent) = $matches;
							if(strtotime($timestamp) < $timeStop) break;
							$location = $this->getLocation($uri);
							$referer = $this->getReferer($referer, $refererSelf);
							$clientsRequestThrottle = substru($timestamp, 0, 17).$method.$location;
							if($clients[$ip] == $clientsRequestThrottle) { --$sites[$referer]; continue; }
							$clients[$ip] = $clientsRequestThrottle;
							if($this->checkRequestArguments($method, $location, $referer))
							{
								if(!preg_match("#^$locationSelf#", $location)) continue;
								if(preg_match("#^$locationSelf(.*)/($locationIgnore)/#", $location)) continue;
								if(preg_match("#^$locationSelf(.*)/robots.txt$#", $location)) continue;
								if(preg_match("#$spamFilter#i", $referer.$userAgent)) continue;
								if($status>=301 && $status<=303) continue;
								if($status < 400)
								{
									++$content[$this->getUrl($location)];
									++$sites[$referer];
									++$search[$this->getSearchUrl($location)];
									++$this->views;
								} else {
									++$errors[$this->getUrl($location)." - ".$this->getErrorFormatted($status)];
								}
							}
						}
					}
					fclose($fileHandle);
				} else {
					$statusCode = 500;
					echo "ERROR reading logfiles: Can't read file '$fileName'!\n";
				}
			}
			if($statusCode == 200)
			{
				unset($sites["-"]); unset($search["-"]);
				$this->showRequests($sites, "Referring sites");
				$this->showRequests($content, "Popular content");
				$this->showRequests($search, "Search queries");
				$this->showRequests($errors, "Error pages");
			}
		} else {
			$statusCode = 500;
			echo "ERROR reading logfiles: Can't find files in directory '$path'!\n";
		}
		$this->yellow->toolbox->timerStop($time);
		if(defined("DEBUG") && DEBUG>=1) echo "YellowStats::analyseRequests time:$time ms\n";
		return $statusCode;
	}
	
	// Show top requests
	function showRequests($array, $text)
	{
		if(!empty($array))
		{
			echo "$text\n\n";
			uasort($array, strnatcasecmp);
			$array = array_reverse(array_filter($array, function($value) { return $value>0; }));
			$array = array_slice($array, 0, $this->yellow->config->get("statsLinesMax"));
			foreach($array as $key=>$value) echo "- $value $key\n";
			echo "\n";
		}
	}
	
	// Check static configuration
	function checkStaticConfig()
	{
		$serverScheme = $this->yellow->config->get("serverScheme");
		$serverName = $this->yellow->config->get("serverName");
		$serverBase = $this->yellow->config->get("serverBase");
		return !empty($serverScheme) && !empty($serverName) &&
			$this->yellow->lookup->isValidLocation($serverBase) && $serverBase!="/";
	}
	
	// Check request arguments
	function checkRequestArguments($method, $location, $referer)
	{
		return (($method=="GET" || $method=="POST") && $location[0]=='/' && ($referer=="-" || substru($referer, 0, 4)=="http"));
	}
	
	// Return location, decode logfile-encoding and URL-encoding
	function getLocation($uri)
	{
		$uri = preg_replace_callback("#(\\\x[0-9a-f]{2})#", function($matches) { return chr(hexdec($matches[1])); }, $uri);
		return rawurldecode(($pos = strposu($uri, '?')) ? substru($uri, 0, $pos) : $uri);
	}
	
	// Return referer, ignore referers to self
	function getReferer($referer, $refererSelf)
	{
		$referer = preg_replace_callback("#(\\\x[0-9a-f]{2})#", function($matches) { return chr(hexdec($matches[1])); }, $referer);
		$referer = rawurldecode($referer);
		if(preg_match("#^(\w+:\/\/[^/]+)$#", $referer)) $referer .= '/';
		return preg_match("#$refererSelf#i", $referer) ? "-" : $referer;
	}
	
	// Return URL, with server scheme and server name
	function getUrl($location)
	{
		return $this->yellow->lookup->normaliseUrl(
			$this->yellow->config->get("serverScheme"), $this->yellow->config->get("serverName"), "", $location);
	}

	// Return search URL, if available
	function getSearchUrl($location)
	{
		$locationSearch = $this->yellow->config->get("statsLocationSearch")."query".$this->yellow->toolbox->getLocationArgsSeparator();
		return preg_match("#^$locationSearch([^/]+)/$#", $location) ? $this->getUrl(strtoloweru($location)) : "-";
	}
	
	// Return human readable error
	function getErrorFormatted($statusCode)
	{
		switch($statusCode)
		{
			case 400:	$text = "Bad request"; break;
			case 401:	$text = "Unauthorised"; break;
			case 404:	$text = "Not found"; break;
			case 424:	$text = "Not existing"; break;
			case 500:	$text = "Server error"; break;
			case 503:	$text = "Service unavailable"; break;
			default:	$text = "Error $statusCode";
		}
		return $text;
	}
	
	// Return previous text line from file, false if not found
	function getFileLinePrevious($fileHandle, &$filePos, &$fileTop, &$dataBuffer)
	{
		if($filePos >= 0)
		{
			$line = "";
			$lineEndingSearch = false;
			$endPos = $this->getFileLineBuffer($fileHandle, $filePos, $fileTop, $dataBuffer);
			for(;$filePos>=0; --$filePos)
			{
				$currentPos = $filePos - $fileTop;
				if($dataBuffer[$currentPos]=="\n" && $lineEndingSearch)
				{
					$line = substru($dataBuffer, $currentPos+1, $endPos-$currentPos).$line;
					break;
				}
				if($currentPos == 0)
				{
					$line = substru($dataBuffer, $currentPos, $endPos-$currentPos+1).$line;
					$endPos = $this->getFileLineBuffer($fileHandle, $filePos-1, $fileTop, $dataBuffer);
				}
				$lineEndingSearch = true;
			}
		} else {
			$line = false;
		}
		return $line;
	}
	
	// Update text line buffer
	function getFileLineBuffer($fileHandle, $filePos, &$fileTop, &$dataBuffer)
	{
		if($filePos >= 0)
		{
			$top = intval($filePos / 4096) * 4096;
			if($fileTop != $top)
			{
				$fileTop = $top;
				fseek($fileHandle, $fileTop);
				$dataBuffer = fread($fileHandle, 4096);
			}
		}
		return $filePos - $fileTop;
	}
}

$yellow->plugins->register("stats", "YellowStats", YellowStats::Version);
?>