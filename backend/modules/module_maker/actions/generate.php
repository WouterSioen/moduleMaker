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
	 * $backendPath:		the path to the backend part of the module (string)
	 * $frontendPath:		the path to the frontend part of the module (string)
	 * $variables:			A part of the records variable used for string replacement (array)
	 * 
	 * @var mixed
	 */
	private $backendPath, $frontendPath, $variables;

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

		// initialize some variables
		$this->backendPath = BACKEND_MODULES_PATH . '/' . $this->record['underscored_name'] . '/';
		$this->frontendPath = FRONTEND_MODULES_PATH . '/' . $this->record['underscored_name'] . '/';
		$this->variables = (array) $this->record;
		unset($this->variables['fields']);

		$this->generateFolders();
		$this->generateBaseFiles();
		$this->generateInstallerFiles();
		$this->generateBackendFiles();
		$this->generateBackendActions();
		$this->display();
	}

	/**
	 * Generates the backend actions (and templates) (index, add, edit and delete)
	 */
	protected function generateBackendActions()
	{
		// generate index
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/action/index.base.php',
			$this->variables,
			$this->backendPath . 'actions/index.php'
		);

		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/action/index.base.tpl',
			$this->variables,
			$this->backendPath . 'layout/templates/index.tpl'
		);

		// generate add action
		// create some custom variables
		$this->variables['load_form_add'] = BackendModuleMakerModel::generateLoadForm($this->record, false);
		$this->variables['validate_form_add'] = BackendModuleMakerModel::generateValidateForm($this->record, false);
		$this->variables['build_item_add'] = BackendModuleMakerModel::generateBuildItem($this->record, false);

		// build and save the file
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/action/add.base.php',
			$this->variables,
			$this->backendPath . 'actions/add.php'
		);

		// unset the custom variables
		unset($this->variables['load_form_add']);
		unset($this->variables['validate_form_add']);
		unset($this->variables['build_item_add']);

		// generate add template
		// create a variables
		/*$this->variables['template_add'] = BackendModuleMakerModel::generateTemplate($this->record, false);

		// build and save the file
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/action/add.base.tpl',
			$this->variables,
			$this->backendPath . 'layout/templates/add.tpl'
		);

		// unset the custom variable
		unset($this->variables['template_add']);*/

		// generate edit
		

		// generate delete
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/action/delete.base.php',
			$this->variables,
			$this->backendPath . 'actions/delete.php'
		);
	}

	/**
	 * Generates the backend files (module.js & model.php)
	 */
	protected function generateBackendFiles()
	{
		// generate module.js file
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/base/javascript.base.js',
			$this->variables,
			$this->backendPath . 'js/' . $this->record['underscored_name'] . '.js'
		);

		// generate model.php file
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/base/model.base.php',
			$this->variables,
			$this->backendPath . 'engine/model.php'
		);
	}

	/**
	 * Generates the basic files (info.xml and config.php in the document root.)
	 */
	protected function generateBaseFiles()
	{
		// generate info.xml file
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/base/info.base.xml',
			$this->variables,
			$this->backendPath . 'info.xml'
		);

		// generate config.php file
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/base/config.base.php',
			$this->variables,
			$this->backendPath . 'config.php'
		);
	}

	/**
	 * Generates the folder structure for the module
	 */
	protected function generateFolders()
	{
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

	/**
	 * Generates the installer files (installer.php, install.sql and locale.xml)
	 */
	protected function generateInstallerFiles()
	{
		// generate installer.php
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/base/installer.base.php',
			$this->variables,
			$this->backendPath . 'installer/installer.php'
		);

		// generate locale.xml
		BackendModuleMakerModel::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/base/locale.base.xml',
			$this->variables,
			$this->backendPath . 'installer/data/locale.xml'
		);

		// generate install.sql
		$sql = BackendModuleMakerModel::generateSQL($this->record['underscored_name'], $this->record['fields']);
		BackendModuleMakerModel::makeFile($this->backendPath . 'installer/data/install.sql', $sql);
	}
}
