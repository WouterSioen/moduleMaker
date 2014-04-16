<?php

namespace Backend\Modules\ModuleMaker\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class Add extends ActionAdd
{
	/**
	 * The module we're working on
	 * 
	 * @var array
	 */
	private $record;

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
		$this->record = \SpoonSession::get('module');

		$this->frm = new Form('add');
		$this->frm->addText('title', $this->record ? $this->record['title'] : null, null, 'inputText title', 'inputTextError title');
		$this->frm->addTextArea('description', $this->record ? $this->record['description'] : null);
		$this->frm->addText('author_name', $this->record ? $this->record['author_name'] : null);
		$this->frm->addText('author_url', $this->record ? $this->record['author_url'] : null);
		$this->frm->addText('author_email', $this->record ? $this->record['author_email'] : null);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
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
			$fields['title']->isFilled(BL::err('TitleIsRequired'));
			$fields['description']->isFilled(BL::err('FieldIsRequired'));
			$fields['author_name']->isFilled(BL::err('FieldIsRequired'));
			$fields['author_url']->isFilled(BL::err('FieldIsRequired'));
			$fields['author_email']->isFilled(BL::err('FieldIsRequired'));

			// cleanup the modulename
			$title = preg_replace('/[^A-Za-z ]/', '', $fields['title']->getValue());

			// check if there is already a module with this name
			if(BackendExtensionsModel::existsModule($title)) $fields['title']->addError(BL::err('DuplicateModuleName'));

			if($this->frm->isCorrect())
			{
				$this->record['title'] = $title;
				$this->record['description'] = trim($fields['description']->getValue());
				$this->record['author_name'] = $fields['author_name']->getValue();
				$this->record['author_url'] = $fields['author_url']->getValue();
				$this->record['author_email'] = $fields['author_email']->getValue();
				$this->record['camel_case_name'] = BackendModuleMakerHelper::buildCamelCasedName($title);
				$this->record['underscored_name'] = BackendModuleMakerHelper::buildUnderscoredName($title);

				\SpoonSession::set('module', $this->record);

				$this->redirect(BackendModel::createURLForAction('add_step2'));
			}
		}
	}
}
