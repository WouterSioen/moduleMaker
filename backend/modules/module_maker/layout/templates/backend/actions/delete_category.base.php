<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a category
 *
 * @author {$author_name} <{$author_email}>
 */
class Backend{$camel_case_name}DeleteCategory extends BackendBaseActionDelete
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
				BackendModel::createURLForAction('categories') . '&error=non-existing'
			);
		}

		// fetch the category
		$this->record = (array) Backend{$camel_case_name}Model::getCategory($this->id);

		// delete item
		Backend{$camel_case_name}Model::deleteCategory($this->id);
		BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

		// category was deleted, so redirect
		$this->redirect(
			BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title'])
		);
	}
}
