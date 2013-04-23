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
 * @author {$author_name} <{$author_email}>
 */
class Backend{$camel_case_name}Model
{
	const QRY_DATAGRID_BROWSE =
		'SELECT i.id, i.created_on{$datagrid_extra}
		 FROM {$underscored_name} AS i
		 WHERE i.language = ?{$datagrid_order}';
{$datagrid_categories}
	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getContainer()->get('database')->delete('{$underscored_name}', 'id = ?', (int) $id);
	}
{$delete_category}
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
			 FROM {$underscored_name} AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}
{$exists_category}
	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*{$select_extra}
			 FROM {$underscored_name} AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}
{$get_category}{$getMaxSequence}{$getUrl}{$get_url_category}
	/**
	 * Insert an item in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insert(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getContainer()->get('database')->insert('{$underscored_name}', $item);
	}
{$insert_category}
	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $item
	 */
	public static function update($id, array $item)
	{
		$item['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->update(
			'{$underscored_name}', $item, 'id = ?', (int) $id
		);
	}
{$update_category}}
