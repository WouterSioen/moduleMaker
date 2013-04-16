<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @author {$author_name} <{$author_email}>
 */
class Backend{$camel_case_name}EditCategory extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->getData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->id = $this->getParameter('id', 'int');
		if($this->id == null || !Backend{$camel_case_name}Model::existsCategory($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('categories') . '&error=non-existing'
			);
		}

		$this->record = Backend{$camel_case_name}Model::getCategory($this->id);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editCategory');
		$this->frm->addText('title', $this->record['title']);

		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
		$this->meta->setUrlCallback('Backend{$camel_case_name}Model', 'getURLForCategory', array($this->record['id']));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// assign the data
		$this->tpl->assign('item', $this->record);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['language'] = $this->record['language'];
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['meta_id'] = $this->meta->save(true);

				// update the item
				Backend{$camel_case_name}Model::updateCategory($item);
				BackendModel::triggerEvent($this->getModule(), 'after_edit_category', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(
					BackendModel::createURLForAction('categories') . '&report=edited-category&highlight=row-' . $item['id']
				);
			}
		}
	}
}
