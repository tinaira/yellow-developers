<?php
// Traffic plugin, https://github.com/datenstrom/yellow-plugins/tree/master/traffic
// Copyright (c) 2013-2017 Datenstrom, https://datenstrom.se
// This file may be used and distributed under the terms of the public license.

class YellowTraffic
{
	const VERSION = "0.7.1";
	var $yellow;			//access to API
	var $days;				//detected days
	var $views;				//detected views

	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("trafficDays", 30);
		$this->yellow->config->setDefault("trafficLinesMax", 8);
		$this->yellow->config->setDefault("trafficLogDir", "/var/log/apache2/");
		$this->yellow->config->setDefault("trafficLogFile", "(.*)access.log");
		$this->yellow->config->setDefault("trafficLocationIgnore", "/(media|system|edit)/");
		$this->yellow->config->setDefault("trafficSpamFilter", "bot|crawler|spider|localhost");
	}

	// Handle command help
	function onCommandHelp()
	{
		return "traffic [DAYS LOCATION FILENAME]\n";
	}
	
	// Handle command
	function onCommand($args)
	{
		list($command) = $args;
		switch($command)
		{
			case "traffic":	$statusCode = $this->trafficCommand($args); break;
			default:		$statusCode = 0;
		}
		return $statusCode;
	}

	// Create traffic analytics
	function trafficCommand($args)
	{
		$statusCode = 0;
		list($command, $days, $location, $fileName) = $args;
		if(empty($location) || $location[0]=='/')
		{
			if($this->checkStaticConfig())
			{
				$statusCode = $this->processRequests($days, $location, $fileName);
			} else {
				$statusCode = 500;
				$this->days = $this->views = 0;
				$fileName = $this->yellow->config->get("configDir").$this->yellow->config->get("configFile");
				echo "ERROR checking files: Please configure StaticUrl in file '$fileName'!\n";
			}
			echo "Yellow $command: $this->days day".($this->days!=1 ? 's' : '').", ";
			echo "$this->views view".($this->views!=1 ? 's' : '')."\n";
		} else {
			$statusCode = 400;
			echo "Yellow $command: Invalid arguments\n";
		}
		return $statusCode;
	}
	
	// Analyse and show traffic
	function processRequests($days, $location, $fileName)
	{
		$this->yellow->toolbox->timerStart($time);
		if(empty($location)) $location = "/";
		if(empty($days)) $days = $this->yellow->config->get("trafficDays");
		if(empty($fileName))
		{
			$path = $this->yellow->config->get("trafficLogDir");
			$regex = "/^".basename($this->yellow->config->get("trafficLogFile"))."$/";
			$fileNames = $this->yellow->toolbox->getDirectoryEntries($path, $regex, true, false);
			list($statusCode, $sites, $content, $search, $errors) = $this->analyseRequests($days, $location, $fileNames);
		} else {
			list($statusCode, $sites, $content, $search, $errors) = $this->analyseRequests($days, $location, array($fileName));
		}
		if($statusCode==200)
		{
			$this->showRequests($sites, "Referring sites");
			$this->showRequests($content, "Popular content");
			$this->showRequests($search, "Search queries");
			$this->showRequests($errors, "Error pages");
		}
		$this->yellow->toolbox->timerStop($time);
		if(defined("DEBUG") && DEBUG>=1) echo "YellowTraffic::processRequests time:$time ms\n";
		return $statusCode;
	}
	
	// Analyse logfile requests
	function analyseRequests($days, $locationFilter, $fileNames)
	{
		$this->days = $this->views = 0;
		$sites = $content = $search = $errors = $clients = array();
		if(!empty($fileNames))
		{
			$statusCode = 200;
			$timeStart = $timeFound = time();
			$timeStop = time() - (60 * 60 * 24 * $days);
			$staticUrl = $this->yellow->config->get("staticUrl");
			list($scheme, $address, $base) = $this->yellow->lookup->getUrlInformation($staticUrl);
			$locationIgnore = $this->yellow->config->get("trafficLocationIgnore");
			$locationSearch = $this->yellow->config->get("searchLocation");
			$faviconFile = $this->yellow->config->get("faviconFile");
			$robotsFile = $this->yellow->config->get("robotsFile");
			$spamFilter = $this->yellow->config->get("trafficSpamFilter");
			foreach($fileNames as $fileName)
			{
				if(defined("DEBUG") && DEBUG>=1) echo "YellowTraffic::analyseRequests file:$fileName\n";
				$fileHandle = @fopen($fileName, "r");
				if($fileHandle)
				{
					$filePos = filesize($fileName)-1; $fileTop = -1;
					while(($line = $this->getFileLinePrevious($fileHandle, $filePos, $fileTop, $dataBuffer))!==false)
					{
						if(preg_match("/^(\S+) (\S+) (\S+) \[(.+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) \"(.*?)\" \"(.*?)\"$/", $line, $matches))
						{
							list($line, $ip, $dummy1, $dummy2, $timestamp, $method, $uri, $protocol, $status, $size, $referer, $userAgent) = $matches;
							$timeFound = strtotime($timestamp);
							if($timeFound<$timeStop) break;
							$location = $this->getLocation($uri);
							$referer = $this->getReferer($referer, "$address$base/");
							if($status<400)
							{
								$clientsRequestThrottle = substru($timestamp, 0, 17).$method.$location;
								if($clients[$ip]==$clientsRequestThrottle) { --$sites[$referer]; continue; }
								$clients[$ip] = $clientsRequestThrottle;
								if(!$this->checkRequestArguments($method, $location, $referer)) continue;
								if(!preg_match("#^$base$locationFilter#", $location)) continue;
								if($locationFilter=="/")
								{
									if(preg_match("#^$base(.*)$locationIgnore#", $location)) continue;
									if(preg_match("#^$base(.*)/($faviconFile)$#", $location)) continue;
									if(preg_match("#^$base(.*)/($robotsFile)$#", $location)) continue;
								}
								if(preg_match("#$spamFilter#i", $referer.$userAgent)) continue;
								if($status>=301 && $status<=303) continue;
								++$content[$this->getUrl($scheme, $address, $base, $location)];
								++$sites[$referer];
								++$search[$this->getSearchUrl($scheme, $address, $base, $location, $locationSearch)];
								++$this->views;
							} else {
								if(preg_match("#$spamFilter#i", $referer.$userAgent) && $status==404) continue;
								++$errors[$this->getUrl($scheme, $address, $base, $location)." - ".$this->getStatusFormatted($status)];
							}
						}
					}
					fclose($fileHandle);
				} else {
					$statusCode = 500;
					echo "ERROR reading logfiles: Can't read file '$fileName'!\n";
				}
			}
			unset($sites["-"]); unset($search["-"]);
			if($locationFilter!="/") $search = array();
			$this->days = $timeStart!=$timeFound ? $days : 0;
		} else {
			$statusCode = 500;
			$path = $this->yellow->config->get("trafficLogDir");
			echo "ERROR reading logfiles: Can't find files in directory '$path'!\n";
		}
		return array($statusCode, $sites, $content, $search, $errors);
	}
	
	// Show top requests
	function showRequests($array, $text)
	{
		uasort($array, strnatcasecmp);
		$array = array_reverse(array_filter($array, function($value) { return $value>0; }));
		$array = array_slice($array, 0, $this->yellow->config->get("trafficLinesMax"));
		if(!empty($array))
		{
			echo "$text\n\n";
			foreach($array as $key=>$value) echo "- $value $key\n";
			echo "\n";
		}
	}
	
	// Check static configuration
	function checkStaticConfig()
	{
		$staticUrl = $this->yellow->config->get("staticUrl");
		return !empty($staticUrl);
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
	
	// Return referer, decode logfile-encoding and URL-encoding
	function getReferer($referer, $refererSelf)
	{
		$referer = preg_replace_callback("#(\\\x[0-9a-f]{2})#", function($matches) { return chr(hexdec($matches[1])); }, $referer);
		$referer = rawurldecode($referer);
		if(preg_match("#^(\w+:\/\/[^/]+)$#", $referer)) $referer .= '/';
		return preg_match("#$refererSelf#", $referer) ? "-" : $referer;
	}
	
	// Return URL
	function getUrl($scheme, $address, $base, $location)
	{
		return "$scheme://$address$location";
	}

	// Return search URL, if available
	function getSearchUrl($scheme, $address, $base, $location, $locationSearch)
	{
		$locationSearch = $base.$locationSearch."query".$this->yellow->toolbox->getLocationArgsSeparator();
		$searchUrl = preg_match("#^$locationSearch([^/]+)/$#", $location) ? ("$scheme://$address".strtoloweru($location)) : "-";
		return strreplaceu(array("\x1c", "\x1d", "\x1e"), array('%1C','%1D','%1E'), $searchUrl);
	}
	
	// Return human readable status
	function getStatusFormatted($statusCode)
	{
		return $this->yellow->toolbox->getHttpStatusFormatted($statusCode, true);
	}
	
	// Return previous text line from file, false if not found
	function getFileLinePrevious($fileHandle, &$filePos, &$fileTop, &$dataBuffer)
	{
		if($filePos>=0)
		{
			$line = "";
			$lineEndingSearch = false;
			$this->getFileLineBuffer($fileHandle, $filePos, $fileTop, $dataBuffer);
			$endPos = $filePos - $fileTop;
			for(;$filePos>=0; --$filePos)
			{
				$currentPos = $filePos - $fileTop;
				if($dataBuffer===false) { $line = false; break; }
				if($dataBuffer[$currentPos]=="\n" && $lineEndingSearch)
				{
					$line = substru($dataBuffer, $currentPos+1, $endPos-$currentPos).$line;
					break;
				}
				if($currentPos==0)
				{
					$line = substru($dataBuffer, $currentPos, $endPos-$currentPos+1).$line;
					$this->getFileLineBuffer($fileHandle, $filePos-1, $fileTop, $dataBuffer);
					$endPos =  $filePos-1 - $fileTop;
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
		if($filePos>=0)
		{
			$top = intval($filePos / 4096) * 4096;
			if($fileTop!=$top)
			{
				$fileTop = $top;
				fseek($fileHandle, $fileTop);
				$dataBuffer = fread($fileHandle, 4096);
			}
		}
	}
}

$yellow->plugins->register("traffic", "YellowTraffic", YellowTraffic::VERSION);
?>
