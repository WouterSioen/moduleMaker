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
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendTestDeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id == null || !BackendTestModel::existsCategory($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('categories') . '&error=non-existing'
			);
		}

		// fetch the category
		$this->record = (array) BackendTestModel::getCategory($this->id);

		// delete item
		BackendTestModel::deleteCategory($this->id);
		BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

		// category was deleted, so redirect
		$this->redirect(
			BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title'])
		);
	}
}
