<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add step 3-action, it will display a form to add special fields to a module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerAddStep4 extends BackendBaseActionAdd
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
		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

		// If there are no fields added, redirect back to the second step of the wizard
		if(!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(BackendModel::createURLForAction('add_step2'));

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
		$this->frm = new BackendForm('add_step4');
		$this->frm->addCheckbox('twitter', array_key_exists('twitter', $this->record));
		$this->frm->addText('twitter_name', array_key_exists('twitter', $this->record) ? $this->record['twitter'] : null);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign('item', $this->record);

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

			$frmFields = $this->frm->getFields();

			// validate form
			if($frmFields['twitter']->isChecked())
			{
				// we need fields when search is ticked
				$frmFields['twitter_name']->isFilled(BL::err('FieldIsRequired'));
			}

			if($this->frm->isCorrect())
			{
				// if this field is checked, let's add a boolean searchable true to the chosen fields
				if($frmFields['twitter']->isChecked())
				{
					$this->record['twitter'] = $frmFields['twitter_name']->getValue();
				}
				else
				{
					if(array_key_exists('twitter', $this->record)) unset($this->record['twitter']);
				}

				// save the object in our session
				SpoonSession::set('module', $this->record);
				$this->redirect(BackendModel::createURLForAction('generate'));
			}
		}
	}
}
