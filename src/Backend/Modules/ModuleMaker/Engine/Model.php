<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the modulemaker module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerModel
{
	/**
	 * Creates the directories from a given array
	 *
	 * @param array $dirs		The directories to create
	 */
	public static function makeDirs(array $dirs)
	{
		// the main dir
		$mainDir = '';

		// loop the directories
		foreach($dirs as $type => $dir)
		{
			// create a new dir if this is the main dir
			if($type == 'main')
			{
				mkdir($dir);
				$mainDir = $dir . '/';
				continue;
			}

			// loob the dir to check for subdirs if this isn't the main
			foreach($dir as $name => $subdir)
			{
				// no subdirs
				if(!is_array($subdir)) mkdir($mainDir . $subdir);
				// more subdirs
				else
				{
					// create new array to pass
					$tmpArray = array(
						'main' => $mainDir . $name,
						'sub' => $subdir
					);

					// make the dir
					self::makeDirs($tmpArray);
				}
			}
		}
	}

	/**
	 * Creates a file in a specific directory
	 *
	 * @param string $file				The file name.
	 * @param string[optional] $input		The input for the file.
	 */
	public static function makeFile($file, $input = null)
	{
		// create the file
		$oFile = fopen($file, 'w');

		// input?
		if($input !== null) fwrite($oFile, $input);

		// close the file
		fclose($oFile);
	}

	/**
	 * Reads the content of a file
	 *
	 * @param string $file		The file path.
	 * @return string
	 */
	public static function readFile($file)
	{
		// file exists?
		if(!file_exists($file)) throw new Exception('The given file(' . $file . ') does not exist.');

		// open the file
		$oFile = fopen($file, 'r');

		// read the file
		$rFile = fread($oFile, filesize($file));

		// close the file
		fclose($oFile);

		// return
		return $rFile;
	}
}