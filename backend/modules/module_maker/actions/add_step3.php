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
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class BackendModuleMakerAddStep3 extends BackendBaseActionAdd
{
	/**
	 * The module we're working on
	 * 
	 * @var array
	 */
	private $record;

	/**
	 * The selected meta field, The selected search fields
	 * 
	 * @var int
	 */
	private $selectedMeta, $selectedSearch;

	/**
	 * Execute the actions
	 */
	public function execute()
	{
		parent::execute();

		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

		// If there are no fields added, redirect back to the second step of the wizard
		if(!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(BackendModel::createURLForAction('add_step2'));

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
		// create all variables needed for meta
		$fields = array();
		$this->selectedMeta = false;

		foreach($this->record['fields'] as $key => $field)
		{
			if($field['type'] == 'text') $fields[$key] = $field['label'];
			if(array_key_exists('meta', $field) && $field['meta'] == true) $this->selectedMeta = $key;
		}

		// create all variables needed for searchindex
		$searchFields = array();
		$this->selectedSearch = false;

		foreach($this->record['fields'] as $key => $field)
		{
			if($field['type'] == 'text' || $field['type'] == 'editor')
			{
				$searchFields[$key] = array(
					'label' => ucfirst($field['label']),
					'value' => $key
				);
			}
			if(array_key_exists('searchable', $field) && $field['searchable'] == true) $this->selectedSearch = $key;
		}

		// create the form
		$this->frm = new BackendForm('add_step3');
		$this->frm->addCheckbox('meta', ($this->selectedMeta !== false));
		$this->frm->addDropDown('meta_field', $fields, $this->selectedMeta);

		$this->frm->addCheckbox('search', ($this->selectedSearch !== false));
		$this->frm->addMultiCheckbox('search_fields', $searchFields, $this->selectedSearch);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('meta', ($this->selectedMeta !== false));
		$this->tpl->assign('search', ($this->selectedSearch !== false));
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
			if($frmFields['search']->isChecked())
			{
				// we need fields when search is ticked
				$frmFields['search_fields']->isFilled(BL::err('FieldIsRequired'));
			}

			if($this->frm->isCorrect())
			{
				// set all fields to meta false
				foreach($this->record['fields'] as &$field)
				{
					$field['meta'] = false;
					$field['searchable'] = false;
				}

				// if this field is checked, let's add a boolean meta true to the chosen field
				if($frmFields['meta']->isChecked())
				{
					$metaField = $frmFields['meta_field']->getValue();
					$this->record['fields'][$metaField]['meta'] = true;
					$this->record['metaField'] = $metaField;
				}
				else $this->record['metaField'] = false;

				// if this field is checked, let's add a boolean searchable true to the chosen fields
				if($frmFields['search']->isChecked())
				{
					$searchFields = $frmFields['search_fields']->getValue();
					foreach($searchFields as $searchField)
					{
						$this->record['fields'][$searchField]['searchable'] = true;
					}
					$this->record['searchFields'] = implode(',',$searchFields);
				}
				else $this->record['searchFields'] = false;

				// save the object in our session
				SpoonSession::set('module', $this->record);
				$this->redirect(BackendModel::createURLForAction('add_step4'));
			}
		}
	}
}
