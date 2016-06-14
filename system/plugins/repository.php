<?php
// Copyright (c) 2013-2016 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Repository plugin
class YellowRepository
{
	const Version = "0.6.6";

	// Handle plugin initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("repositoryZipArchiveDir", "zip/");
		$this->yellow->config->setDefault("repositoryFileIgnore", "README.md|plugin.jpg|theme.jpg");
	}

	// Handle command help
	function onCommandHelp()
	{
		return "repository [DIRECTORY]\n";
	}
	
	// Handle command
	function onCommand($args)
	{
		list($name, $command) = $args;
		switch($command)
		{
			case "repository":	$statusCode = $this->repositoryCommand($args); break;
			default:			$statusCode = 0;
		}
		return $statusCode;
	}

	// Update repository
	function repositoryCommand($args)
	{
		$statusCode = 0;
		list($dummy, $command, $path) = $args;
		$statusCode = max($statusCode, $this->updateSoftwareArchive($path));
		$statusCode = max($statusCode, $this->updateSoftwareVersion($path));
		echo "Yellow $command: Repository ".($statusCode!=200 ? "not " : "")."updated\n";
		return $statusCode;
	}
	
	// Update software archive
	function updateSoftwareArchive($path)
	{
		$statusCode = 200;
		$path = rtrim(empty($path) ? getcwd() : $path, '/').'/';
		$pathZipArchive = $path.$this->yellow->config->get("repositoryZipArchiveDir");
		if(is_dir($pathZipArchive))
		{
			$fileIgnore = $this->yellow->config->get("repositoryFileIgnore");
			foreach($this->yellow->toolbox->getDirectoryEntries($path, "/.*/", true, true, false) as $entry)
			{
				if("$path$entry/" == $pathZipArchive) continue;
				$zip = new ZipArchive();
				$fileNameZipArchive = "$pathZipArchive$entry.zip";
				if($zip->open($fileNameZipArchive, ZIPARCHIVE::OVERWRITE) === true)
				{
					$fileNames = $this->yellow->toolbox->getDirectoryEntriesRecursive($path.$entry, "/.*/", true, false);
					foreach($fileNames as $fileName)
					{
						if(preg_match("#($fileIgnore)#", $fileName)) continue;
						$zip->addFile($fileName, substru($fileName, strlenu($path)));
					}
					$zip->close();
				} else {
					$statusCode = 500;
					echo "ERROR updating repository: Can't write file '$fileNameZipArchive'!\n";
				}
			}
		} else {
			$statusCode = 500;
			echo "ERROR updating repository: Can't find directory '$pathZipArchive'!\n";
		}
		return $statusCode;
	}
	
	// Update software version
	function updateSoftwareVersion($path)
	{
		$statusCode = 200;
		$path = rtrim(empty($path) ? getcwd() : $path, '/').'/';
		$fileNameVersion = $path.$this->yellow->config->get("commandlineVersionFile");;
		if(is_dir($path) && is_file("$fileNameVersion"))
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
				echo "ERROR updating repository: Can't write file '$fileNameVersion'!\n";
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

$yellow->plugins->register("repository", "YellowRepository", YellowRepository::Version);
?>