<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the categories-action, it will display the overview of categories
 *
 * @author {$author_name} <{$author_email}>
 */
class Backend{$camel_case_name}Categories extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(
			Backend{$camel_case_name}Model::QRY_DATAGRID_BROWSE_CATEGORIES,
			BL::getWorkingLanguage()
		);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit_category'))
		{
			$this->dataGrid->addColumn(
				'edit', null, BL::lbl('Edit'),
				BackendModel::createURLForAction('edit_category') . '&amp;id=[id]',
				BL::lbl('Edit')
			);
		}
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		$this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
	}
}
