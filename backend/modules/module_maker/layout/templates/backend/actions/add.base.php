<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author {$author_name} <{$author_email}>
 */
class Backend{$camel_case_name}Add extends BackendBaseActionAdd
{
	/**
	 * Execute the actions
	 */
	public function execute()
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$this->frm = new BackendForm('add');

{$load_form_add}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();{$parse_meta}
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validation
			$fields = $this->frm->getFields();

{$validate_form_add}
			if($this->frm->isCorrect())
			{
				// build the item
				$item['language'] = BL::getWorkingLanguage();
{$build_item_add}
				// insert it
				$item['id'] = Backend{$camel_case_name}Model::insert($item);
{$search_index}
				BackendModel::triggerEvent(
					$this->getModule(), 'after_add', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
				);
			}
		}
	}
}
