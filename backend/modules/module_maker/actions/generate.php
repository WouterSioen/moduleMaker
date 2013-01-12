<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Generate action
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class BackendModuleMakerGenerate extends BackendBaseAction
{
	/**
	 * The module we're working on
	 * 
	 * @var array
	 */
	private $record;

	/**
	 * Some variables used in multiple functions
	 * 
	 * @var string
	 */
	private $backendPath, $frontendPath;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

		// If there are no fields added, redirect back to the second step of the wizard
		if(!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(BackendModel::createURLForAction('add_step2'));
Spoon::dump(BACKEND_MODULE_PATH);
		$this->generateFolders();
		$this->generateBaseFiles();
		$this->display();
	}

	/**
	 * Generates the basic files
	 */
	protected function generateBaseFiles()
	{
		// generate info.xml file
		$template = BACKEND_MODULE_PATH . '/templates/backend/base/info_xml.tpl';

		// generate sql
		$sql = BackendModuleMakerModel::generateSQL($this->record['underscored_name'], $this->record['fields']);
		$this->makeFile($this->backendPath . 'installer/data/install.sql', $sql);

		// generate 
		Spoon::dump($this->record);
	}

	/**
	 * Generates the folder structure for the module
	 */
	protected function generateFolders()
	{
		$this->backendPath = BACKEND_MODULES_PATH . '/' . $this->record['underscored_name'];

		// the backend
		$backendDirs = array(
			'main' => $this->backendPath,
			'sub' => array(
				'actions', 'js',
				'engine' => array('cronjobs'),
				'installer',
				'layout' => array('templates')
			)
		);

		// make the backend directories
		BackendModuleMakerModel::makeDirs($backendDirs);

		$this->frontendPath = FRONTEND_MODULES_PATH . '/' . $this->record['underscored_name'];

		// the frontend
		$frontendDirs = array(
			'main' => $this->frontendPath,
			'sub' => array(
				'actions', 'engine',
				'layout' => array('templates'),
				'js'
			)
		);

		// make the frontend directories
		BackendModuleMakerModel::makeDirs($frontendDirs);
	}
}
