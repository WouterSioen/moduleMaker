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
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerAddStep2 extends BackendBaseActionAdd
{
	/**
	 * The module we're working on
	 * 
	 * @var array
	 */
	private $record;

	/**
	 * The datagrid with all the fields
	 * 
	 * @var BackendDataGrid
	 */
	private $datagrid;

	/**
	 * Execute the actions
	 */
	public function execute()
	{
		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

		parent::execute();

		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	private function loadDataGrid()
	{
		// add a fields array key to the record to make sure the datagrid can be crated
		if(!array_key_exists('fields', $this->record)) $this->record['fields'] = array();

		// add the key of each items to the values. This will be used for a link to the delete_field action
		foreach($this->record['fields'] as $key => $field)
		{
			$this->record['fields'][$key]['key'] = $key;
		}

		// if the record has fields, create a datagrid with all the fields
		$this->datagrid = new BackendDataGridArray($this->record['fields']);
		$this->datagrid->addColumn('delete', null, BL::lbl('Delete'), BackendModel::createURLForAction('delete_field') . '&amp;id=[key]', BL::lbl('Delete'));
		$this->datagrid->setColumnsHidden(array('key', 'underscored_label', 'camel_cased_label', 'sql'));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}
}
