<?php

namespace Backend\Modules\{$camel_case_name}\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;

/**
 * This action will delete a category
 *
 * @author {$author_name} <{$author_email}>
 */
class DeleteCategory extends ActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id == null || !Backend{$camel_case_name}Model::existsCategory($this->id))
		{
			$this->redirect(
				Model::createURLForAction('categories') . '&error=non-existing'
			);
		}

		// fetch the category
		$this->record = (array) Backend{$camel_case_name}Model::getCategory($this->id);

		// delete item
		Backend{$camel_case_name}Model::deleteCategory($this->id);
		Model::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

		// category was deleted, so redirect
		$this->redirect(
			Model::createURLForAction('categories') . '&report=deleted-category&var=' .
			urlencode($this->record['title'])
		);
	}
}
