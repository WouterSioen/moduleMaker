<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This file contains a lot of helper functions for the module maker
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerHelper
{
	/**
	 * Creates a valid class name
	 *
	 * @param string $name		The given name.
	 * @return string
	 */
	public static function buildCamelCasedName($name)
	{
		// replace spaces by underscores
		$name = str_replace(' ', '_', $name);

		// lowercase
		$name = strtolower($name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z0-9_\s]/", "", $name);

		// split the name on _
		$parts = explode('_', $name);

		// the new name
		$newName = '';

		// loop trough the parts to ucfirst it
		foreach($parts as $part) $newName.= ucfirst($part);

		// return
		return $newName;
	}

	/**
	 * Creates a lower Camel Cased Name
	 *
	 * @param string $name		The given name.
	 * @return string
	 */
	public static function buildLowerCamelCasedName($name)
	{
		// replace spaces by underscores
		$name = str_replace(' ', '_', $name);

		// lowercase
		$name = strtolower($name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z0-9_\s]/", "", $name);

		// split the name on _
		$parts = explode('_', $name);

		// the new name
		$newName = '';

		// loop trough the parts to ucfirst it
		foreach($parts as $key => $part)
		{
			if($key) $newName.= ucfirst($part);
			else $newName .= $part;
		}

		// return
		return $newName;
	}

	/**
	 * Creates an underscored version off the classname
	 *
	 * @param string $name		The given name.
	 * @return string
	 */
	public static function buildUnderscoredName($name)
	{
		// lowercase
		$name = strtolower($name);

		// replace spaces by underscores
		$name = str_replace(' ', '_', $name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z0-9_\s]/", "", $name);

		// return
		return $name;
	}

	/**
	 * Check if datatime is valid
	 *
	 * @param string  $dateTime
	 * @return boolean
	 */
	public static function isValidDateTime($dateTime)
	{
		if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches))
		{
			if(checkdate($matches[2], $matches[3], $matches[1]))
			{
				return true;
			}
		}

		return false;
	}
}
