<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the Test module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendTestModel
{
	const QRY_DATAGRID_BROWSE =
		'SELECT i.id, i.created_on
		 FROM test AS i
		 WHERE i.language = ?';

	const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT c.id, c.title, COUNT(i.id) AS num_items, c.sequence
		 FROM test_categories AS c
		 LEFT OUTER JOIN test AS i ON c.id = i.category_id AND i.language = c.language
		 WHERE c.language = ?
		 GROUP BY c.id
		 ORDER BY c.sequence ASC';

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
	 * Delete a specific category
	 *
	 * @param int $id
	 */
	public static function deleteCategory($id)
	{
		$db = BackendModel::getContainer()->get('database');
		$item = self::getCategory($id);

		if(!empty($item))
		{
			$db->delete('meta', 'id = ?', array($item['meta_id']));
			$db->delete('test_categories', 'id = ?', array((int) $id));
			$db->update('test_questions', array('category_id' => null), 'category_id = ?', array((int) $id));
		}
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
	 * Does the category exist?
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM test_categories AS i
			 WHERE i.id = ? AND i.language = ?
			 LIMIT 1',
			 array((int) $id, BL::getWorkingLanguage()));
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
	 * Get all the categories
	 *
	 * @param bool[optional] $includeCount
	 * @return array
	 */
	public static function getCategories($includeCount = false)
	{
		$db = BackendModel::getContainer()->get('database');

		if($includeCount)
		{
			return (array) $db->getPairs(
				'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
				 FROM test_categories AS i
				 LEFT OUTER JOIN test AS p ON i.id = p.category_id AND i.language = p.language
				 WHERE i.language = ?
				 GROUP BY i.id',
				 array(BL::getWorkingLanguage()));
		}

		return (array) $db->getPairs(
			'SELECT i.id, i.title
			 FROM test_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

	/**
	 * Fetch a category
	 *
	 * @param int $id
	 * @return array
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM test_categories AS i
			 WHERE i.id = ? AND i.language = ?',
			 array((int) $id, BL::getWorkingLanguage()));
	}

	/**
	 * Get the maximum sequence for a category
	 *
	 * @return int
	 */
	public static function getMaximumCategorySequence()
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM test_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

	/**
	 * Retrieve the unique URL for a category
	 *
	 * @param string $url
	 * @param int[optional] $id The id of the category to ignore.
	 * @return string
	 */
	public static function getURLForCategory($url, $id = null)
	{
		$url = SpoonFilter::urlise((string) $url);
		$db = BackendModel::getContainer()->get('database');

		// new category
		if($id === null)
		{
			if((bool) $db->getVar(
				'SELECT 1
				 FROM test_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURLForCategory($url);
			}
		}
		// current category should be excluded
		else
		{
			if((bool) $db->getVar(
				'SELECT 1
				 FROM test_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURLForCategory($url, $id);
			}
		}

		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getContainer()->get('database')->insert('test', $data);
	}

	/**
	 * Insert a category in the database
	 *
	 * @param array $item
	 * @param array[optional] $meta The metadata for the category to insert.
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		return BackendModel::getContainer()->get('database')->insert('test_categories', $item);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function update($id, array $data)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->update(
			'test', $data, 'id = ?', (int) $id
		);
	}

	/**
	 * Update a certain category
	 *
	 * @param array $item
	 */
	public static function updateCategory(array $item)
	{
		BackendModel::getContainer()->get('database')->update(
			'test_categories', $item, 'id = ?', array($item['id'])
		);
	}
}
