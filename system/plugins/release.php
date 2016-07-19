<?php
// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Release plugin
class YellowRelease
{
	const Version = "0.6.9";

	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("releasePluginsDir", "/Users/Shared/Github/yellow-plugins/");
		$this->yellow->config->setDefault("releaseThemesDir", "/Users/Shared/Github/yellow-themes/");
		$this->yellow->config->setDefault("releaseZipArchiveDir", "zip/");
		$this->yellow->config->setDefault("releaseZipFileIgnore", "README.md|plugin.jpg|theme.jpg");
	}

	// Handle command help
	function onCommandHelp()
	{
		return "release [DIRECTORY]\n";
	}
	
	// Handle command
	function onCommand($args)
	{
		list($command) = $args;
		switch($command)
		{
			case "release":	$statusCode = $this->releaseCommand($args); break;
			default:		$statusCode = 0;
		}
		return $statusCode;
	}

	// Update files
	function releaseCommand($args)
	{
		$statusCode = 0;
		list($command, $path) = $args;
		if(empty($path))
		{
			$path = rtrim($this->yellow->config->get("releasePluginsDir"), '/').'/';
			$statusCode = max($statusCode, $this->updateSoftwareFiles($path));
			$statusCode = max($statusCode, $this->updateSoftwareArchives($path));
			$path = rtrim($this->yellow->config->get("releaseThemesDir"), '/').'/';
			$statusCode = max($statusCode, $this->updateSoftwareFiles($path));
			$statusCode = max($statusCode, $this->updateSoftwareArchives($path));
		} else {
			$path = rtrim($path, '/').'/';
			$statusCode = max($statusCode, $this->updateSoftwareFiles($path));
			$statusCode = max($statusCode, $this->updateSoftwareArchives($path));
		}
		echo "Yellow $command: Release files ".($statusCode!=200 ? "not " : "")."updated\n";
		return $statusCode;
	}
	
	// Update necessary software files
	function updateSoftwareFiles($path)
	{
		$statusCode = 200;
		if(is_dir($path))
		{
			$statusCode = $this->updateSoftwareVersion($path);
			foreach($this->yellow->toolbox->getDirectoryEntries($path, "/.*/", true, true, false) as $entry)
			{
				list($software, $version) = $this->getSoftwareVersionFromDirectory("$path$entry/");
				$statusCode = max($statusCode, $this->updateSoftwareInformation("$path$entry/", $version));
				$statusCode = max($statusCode, $this->updateSoftwareDocumentation("$path$entry/", $version));
			}
		} else {
			$statusCode = 500;
			echo "ERROR updating files: Can't find directory '$path'!\n";
		}
		return $statusCode;
	}
	
	// Update software version
	function updateSoftwareVersion($path)
	{
		$statusCode = 200;
		$fileName = $path.$this->yellow->config->get("updateVersionFile");
		if(is_file($fileName))
		{
			$data = $this->getSoftwareVersionFromRepository($path);
			$fileData = $this->yellow->toolbox->readFile($fileName);
			foreach($this->yellow->toolbox->getTextLines($fileData) as $line)
			{
				preg_match("/^\s*(.*?)\s*:\s*(.*?)\s*$/", $line, $matches);
				if(!empty($matches[1]) && !is_null($data[$matches[1]]))
				{
					list($version, $url) = explode(',', $matches[2]);
					$version = $data[$matches[1]];
					$fileDataNew .= "$matches[1]: $version,$url\n";
				} else {
					$fileDataNew .= $line;
				}
			}
			if(!$this->yellow->toolbox->createFile($fileName, $fileDataNew))
			{
				$statusCode = 500;
				echo "ERROR updating files: Can't write file '$fileName'!\n";
			}
			if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareVersion file:$fileName<br/>\n";
		}
		return $statusCode;
	}

	// Update software information
	function updateSoftwareInformation($path, $version)
	{
		$statusCode = 200;
		$fileName = $path.$this->yellow->config->get("updateInformationFile");
		if(is_file($fileName) && !empty($version))
		{
			$fileData = $this->yellow->toolbox->readFile($fileName);
			foreach($this->yellow->toolbox->getTextLines($fileData) as $line)
			{
				preg_match("/^\s*(.*?)\s*:\s*(.*?)\s*$/", $line, $matches);
				if(lcfirst($matches[1])=="version")
				{
					$fileDataNew .= "Version: $version\n";
				} else {
					$fileDataNew .= $line;
				}
			}
			if(!$this->yellow->toolbox->createFile($fileName, $fileDataNew))
			{
				$statusCode = 500;
				echo "ERROR updating files: Can't write file '$fileName'!\n";
			}
			if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareInformation file:$fileName<br/>\n";
		}
		return $statusCode;
	}

	// Update software documentation
	function updateSoftwareDocumentation($path, $version)
	{
		$statusCode = 200;
		$fileName = $path.$this->yellow->config->get("updateDocumentationFile");
		if(is_file($fileName) && !empty($version))
		{
			$fileData = $this->yellow->toolbox->readFile($fileName);
			foreach($this->yellow->toolbox->getTextLines($fileData) as $line)
			{
				preg_match("/^(.*?)([0-9\.]+)$/", $line, $matches);
				if(!empty($matches[1]) && !empty($matches[2]) && !$found)
				{
					$fileDataNew .= "$matches[1]$version\n";
					$found = true;
				} else {
					$fileDataNew .= $line;
				}
			}
			if(!$this->yellow->toolbox->createFile($fileName, $fileDataNew))
			{
				$statusCode = 500;
				echo "ERROR updating files: Can't write file '$fileName'!\n";
			}
			if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareDocumentation file:$fileName<br/>\n";
		}
		return $statusCode;
	}
	
	// Update software archives
	function updateSoftwareArchives($path)
	{
		$statusCode = 200;
		if(is_dir($path))
		{
			$pathZipArchive = $path.$this->yellow->config->get("releaseZipArchiveDir");
			$fileNameInformation = $this->yellow->config->get("updateInformationFile");
			$fileIgnore = $this->yellow->config->get("releaseZipFileIgnore");
			foreach($this->yellow->toolbox->getDirectoryEntries($path, "/.*/", true, true, false) as $entry)
			{
				if("$path$entry/"==$pathZipArchive) continue;
				if(!is_file("$path$entry/$fileNameInformation")) continue;
				$zip = new ZipArchive();
				$fileNameZipArchive = "$pathZipArchive$entry.zip";
				if($zip->open($fileNameZipArchive, ZIPARCHIVE::OVERWRITE)===true)
				{
					$fileNames = $this->yellow->toolbox->getDirectoryEntries($path.$entry, "/.*/", true, false);
					foreach($fileNames as $fileName)
					{
						if(preg_match("#($fileIgnore)#", $fileName)) continue;
						$zip->addFile($fileName, substru($fileName, strlenu($path)));
					}
					if(!$zip->close())
					{
						$statusCode = 500;
						echo "ERROR updating files: Can't write file '$fileNameZipArchive'!\n";
					}
				} else {
					$statusCode = 500;
					echo "ERROR updating files: Can't write file '$fileNameZipArchive'!\n";					
				}
				if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareArchives file:$fileNameZipArchive<br/>\n";
			}
		}
		return $statusCode;
	}
	
	// Return software version from repository
	function getSoftwareVersionFromRepository($path)
	{
		$data = array();
		foreach($this->yellow->toolbox->getDirectoryEntries($path, "/.*/", true, true) as $entry)
		{
			list($software, $version) = $this->getSoftwareVersionFromDirectory($entry);
			if(!empty($software) && !empty($version)) $data[$software] = $version;
		}
		return $data;
	}
	
	// Return software version from directory
	function getSoftwareVersionFromDirectory($path)
	{
		$software = $version = "";
		foreach($this->yellow->toolbox->getDirectoryEntries($path, "/\.(php|css)/", false, false) as $entry)
		{
			$fileType = $this->yellow->toolbox->getFileExtension($entry);
			$fileData = $this->yellow->toolbox->readFile($entry, 4096);
			foreach($this->yellow->toolbox->getTextLines($fileData) as $line)
			{
				if($fileType=="php")
				{
					preg_match("/^\s*(.*?)\s+(.*?)$/", $line, $matches);
					if($matches[1]=="class" && !strempty($matches[2])) $software = $matches[2];
					if($matches[1]=="const" && preg_match("/\"([0-9\.]+)\"/", $line, $matches)) $version = $matches[1];
					if($matches[1]=="function") break;
				} else {
					preg_match("/^\/\*\s*(.*?)\s*:\s*(.*?)\s*\*\/$/", $line, $matches);
					if(lcfirst($matches[1])=="theme" && !strempty($matches[2])) $software = $matches[2];
					if(lcfirst($matches[1])=="version" && !strempty($matches[2])) $version = $matches[2];
					if(!empty($line) && $line[0]!='/') break;
				}
			}
		}
		return array($software, $version);
	}
}

$yellow->plugins->register("release", "YellowRelease", YellowRelease::Version);
?>