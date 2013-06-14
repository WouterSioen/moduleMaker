<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the {$title} module
 *
 * @author Arend Pijls <arend.pijls@wijs.be>
 */
class Frontend{$camel_case_name}Model
{
	/**
	 * Fetches a certain item
	 *
	 * @param string $URL
	 * @return array
	 */
	public static function get($URL)
	{
		return (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
			 FROM {$underscored_name} AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE m.url = ?',
			array((int) $URL)
		);
	}

	/**
	 * Get all items (at least a chunk)
	 *
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getAll($limit = 10, $offset = 0)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			 FROM {$underscored_name} AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.language = ?
			 ORDER BY i.id DESC LIMIT ?, ?',
			array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

		// no results?
		if(empty($items)) return array();

		// return
		return $items;
	}

	/**
	 * Get the number of items
	 *
	 * @return int
	 */
	public static function getAllCount()
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(i.id) AS count
			 FROM {$underscored_name} AS i'
		);
	}

	/**
	 * Parse the search results for this module
	 *
	 * Note: a module's search function should always:
	 * 		- accept an array of entry id's
	 * 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	 *
	 *
	 * @param array $ids The ids of the found results.
	 * @return array
	 */
	public static function search(array $ids)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*, m.url
			 FROM {$underscored_name} AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
			array(FRONTEND_LANGUAGE), 'id'
		);

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = FrontendNavigation::getURLForBlock({$underscored_name}, 'detail') . '/' . $item['url'];
		}

		// return
		return $items;
	}
}
