<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete an field from the current record
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerDeleteField extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

		// If there are no fields added, redirect back to the second step of the wizard
		if(!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(BackendModel::createURLForAction('add_step2') . '&amp;error=non-existing');

		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && array_key_exists($this->id, $this->record['fields']))
		{
			unset($this->record['fields'][$this->id]);
			SpoonSession::set('module', $this->record);
			$this->redirect(BackendModel::createURLForAction('add_step2') . '&report=deleted');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('add_step2') . '&error=non-existing');
	}
}
