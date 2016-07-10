<?php
// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Release plugin
class YellowRelease
{
	const Version = "0.6.8";

	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("releasePluginsDir", "/Users/Shared/Github/yellow-plugins/");
		$this->yellow->config->setDefault("releaseThemesDir", "/Users/Shared/Github/yellow-themes/");
		$this->yellow->config->setDefault("releaseFileIgnore", "README.md|plugin.jpg|theme.jpg");
		$this->yellow->config->setDefault("releaseZipArchiveDir", "zip/");
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
			$statusCode = max($statusCode, $this->updateSoftwareVersion($path));
			$statusCode = max($statusCode, $this->updateSoftwareArchive($path));
			$path = rtrim($this->yellow->config->get("releaseThemesDir"), '/').'/';
			$statusCode = max($statusCode, $this->updateSoftwareVersion($path));
			$statusCode = max($statusCode, $this->updateSoftwareArchive($path));
		} else {
			$path = rtrim($path, '/').'/';
			$statusCode = max($statusCode, $this->updateSoftwareVersion($path));
			$statusCode = max($statusCode, $this->updateSoftwareArchive($path));
		}
		echo "Yellow $command: Release files ".($statusCode!=200 ? "not " : "")."updated\n";
		return $statusCode;
	}
	
	// Update software version
	function updateSoftwareVersion($path)
	{
		$statusCode = 200;
		if(is_dir($path))
		{
			$fileNameVersion = $path.$this->yellow->config->get("updateVersionFile");
			$fileNameInformation = $this->yellow->config->get("updateInformationFile");
			if(is_file($fileNameVersion))
			{
				$data = $this->getSoftwareVersionFromRepository($path);
				$fileData = $this->yellow->toolbox->readFile($fileNameVersion);
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
				if(!$this->yellow->toolbox->createFile($fileNameVersion, $fileDataNew))
				{
					$statusCode = 500;
					echo "ERROR updating files: Can't write file '$fileNameVersion'!\n";
				}
				if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareVersion file:$fileNameVersion<br/>\n";
			}
			foreach($this->yellow->toolbox->getDirectoryEntries($path, "/.*/", true, true, false) as $entry)
			{
				$fileName = "$path$entry/$fileNameInformation";
				if(is_file($fileName))
				{
					$fileDataNew = $version = "";
					$fileData = $this->yellow->toolbox->readFile($fileName);
					foreach($this->yellow->toolbox->getTextLines($fileData) as $line)
					{
						preg_match("/^\s*(.*?)\s*:\s*(.*?)\s*$/", $line, $matches);
						if(lcfirst($matches[1])=="plugin" || lcfirst($matches[1])=="theme") $version = $data[$matches[2]];
						if(lcfirst($matches[1])=="version" && !empty($version))
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
					if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareVersion file:$fileName<br/>\n";
				}
			}
		} else {
			$statusCode = 500;
			echo "ERROR updating files: Can't find directory '$path'!\n";
		}
		return $statusCode;
	}
	
	// Update software archive
	function updateSoftwareArchive($path)
	{
		$statusCode = 200;
		if(is_dir($path))
		{
			$fileNameInformation = $this->yellow->config->get("updateInformationFile");
			$pathZipArchive = $path.$this->yellow->config->get("releaseZipArchiveDir");
			$fileIgnore = $this->yellow->config->get("releaseFileIgnore");
			foreach($this->yellow->toolbox->getDirectoryEntries($path, "/.*/", true, true, false) as $entry)
			{
				if("$path$entry/" == $pathZipArchive) continue;
				if(!is_file("$path$entry/$fileNameInformation")) continue;
				$zip = new ZipArchive();
				$fileNameZipArchive = "$pathZipArchive$entry.zip";
				if($zip->open($fileNameZipArchive, ZIPARCHIVE::OVERWRITE) === true)
				{
					$zip->addEmptyDir($entry);
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
				if(defined("DEBUG") && DEBUG>=2) echo "YellowRelease::updateSoftwareArchive file:$fileNameZipArchive<br/>\n";
			}
		}
		return $statusCode;
	}
	
	// Return software version from repository
	function getSoftwareVersionFromRepository($path)
	{
		$data = array();
		foreach($this->yellow->toolbox->getDirectoryEntriesRecursive($path, "/\.(php|css)/", false, false) as $entry)
		{
			$key = $value = "";
			$fileType = $this->yellow->toolbox->getFileExtension($entry);
			$fileData = $this->yellow->toolbox->readFile($entry, 4096);
			foreach($this->yellow->toolbox->getTextLines($fileData) as $line)
			{
				if($fileType == "php")
				{
					preg_match("/^\s*(.*?)\s+(.*?)$/", $line, $matches);
					if($matches[1]=="class" && !strempty($matches[2])) $key = $matches[2];
					if($matches[1]=="const" && preg_match("/\"([0-9\.]+)\"/", $line, $matches)) $value = $matches[1];
					if($matches[1]=="function") break;
				} else {
					preg_match("/^\/\*\s*(.*?)\s*:\s*(.*?)\s*\*\/$/", $line, $matches);
					if(lcfirst($matches[1])=="theme" && !strempty($matches[2])) $key = $matches[2];
					if(lcfirst($matches[1])=="version" && !strempty($matches[2])) $value = $matches[2];
					if(!empty($line) && $line[0]!= '/') break;
				}
			}
			if(!empty($key) && !empty($value)) $data[$key] = $value;
		}
		return $data;
	}
}

$yellow->plugins->register("release", "YellowRelease", YellowRelease::Version);
?>