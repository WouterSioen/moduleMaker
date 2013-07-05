<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the test module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendTestModel
{
	const QRY_DATAGRID_BROWSE =
		'SELECT i.id, i.title, UNIX_TIMESTAMP(i.created_on) AS created_on
		 FROM test AS i
		 WHERE i.language = ?';

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getContainer()->get('database')->delete('test', 'id = ?', (int) $id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM test AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM test AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Retrieve the unique URL for an item
	 *
	 * @param string $url
	 * @param int[optional] $id	The id of the item to ignore.
	 * @return string
	 */
	public static function getURL($url, $id = null)
	{
		$url = SpoonFilter::urlise((string) $url);
		$db = BackendModel::getContainer()->get('database');

		// new item
		if($id === null)
		{
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM test AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURL($url);
			}
		}
		// current item should be excluded
		else
		{
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM test AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURL($url, $id);
			}
		}

		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insert(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getContainer()->get('database')->insert('test', $item);
	}

	/**
	 * Updates an item
	 *
	 * @param array $item
	 */
	public static function update(array $item)
	{
		$item['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->update(
			'test', $item, 'id = ?', (int) $item['id']
		);
	}
}
