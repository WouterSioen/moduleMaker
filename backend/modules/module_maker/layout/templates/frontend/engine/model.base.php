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
class Frontend{$camel_case_name}Model
{

{$datagrid_categories}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*{$select_extra}
			 FROM {$underscored_name} AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}
