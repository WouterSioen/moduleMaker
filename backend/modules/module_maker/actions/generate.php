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
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/actions/index.base.php',
			$this->variables,
			$this->backendPath . 'actions/index.php'
		);

		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/templates/index.base.tpl',
			$this->variables,
			$this->backendPath . 'layout/templates/index.tpl'
		);

		// generate add action
		// create some custom variables
		$this->variables['load_form_add'] = BackendModuleMakerGenerator::generateLoadForm($this->record, false);
		$this->variables['validate_form_add'] = BackendModuleMakerGenerator::generateValidateForm($this->record, false);
		$this->variables['build_item_add'] = BackendModuleMakerGenerator::generateBuildItem($this->record, false);
		$this->variables['search_index'] = BackendModuleMakerGenerator::generateSearchIndex($this->record);
		$this->variables['save_tags'] = BackendModuleMakerGenerator::generateSaveTags($this->record);
		if($this->record['metaField'] !== false)
		{
			$this->variables['parse_meta'] = BackendModuleMakerGenerator::generateSnippet(
				BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/parse_meta.base.php',
				array()
			);
		}
		else $this->variables['parse_meta'] = '';

		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/actions/add.base.php',
			$this->variables,
			$this->backendPath . 'actions/add.php'
		);

		// unset the custom variables
		unset($this->variables['load_form_add']);
		unset($this->variables['validate_form_add']);
		unset($this->variables['build_item_add']);

		// generate add template
		// create a variables
		list($this->variables['template_title'], $this->variables['template'], $this->variables['template_side']) = BackendModuleMakerGenerator::generateTemplate($this->record, false);
		list($this->variables['template_tabs_top'], $this->variables['template_tabs_bottom']) = BackendModuleMakerGenerator::generateTemplateTabs($this->record);

		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/templates/add.base.tpl',
			$this->variables,
			$this->backendPath . 'layout/templates/add.tpl'
		);

		// generate edit action
		// create some custom variables
		$this->variables['load_form_edit'] = BackendModuleMakerGenerator::generateLoadForm($this->record, true);
		$this->variables['validate_form_edit'] = BackendModuleMakerGenerator::generateValidateForm($this->record, true);
		$this->variables['build_item_edit'] = BackendModuleMakerGenerator::generateBuildItem($this->record, true);
		$this->variables['search_index'] = BackendModuleMakerGenerator::generateSearchIndex($this->record);

		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/actions/edit.base.php',
			$this->variables,
			$this->backendPath . 'actions/edit.php'
		);

		// unset the custom variables
		unset($this->variables['load_form_edit']);
		unset($this->variables['validate_form_edit']);
		unset($this->variables['build_item_edit']);
		unset($this->variables['search_index']);
		unset($this->variables['parse_meta']);

		// generate edit template
		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/templates/edit.base.tpl',
			$this->variables,
			$this->backendPath . 'layout/templates/edit.tpl'
		);

		// unset the custom variables
		unset($this->variables['template_title']);
		unset($this->variables['template']);
		unset($this->variables['template_side']);
		unset($this->variables['template_tabs_top']);
		unset($this->variables['template_tabs_bottom']);

		// generate delete
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/actions/delete.base.php',
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
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/js/javascript.base.js',
			$this->variables,
			$this->backendPath . 'js/' . $this->record['underscored_name'] . '.js'
		);

		// add the createURL function if there is a meta field
		if($this->record['metaField'] !== false)
		{
			$this->variables['getUrl'] = BackendModuleMakerGenerator::generateSnippet(
				BACKEND_MODULE_PATH . '/layout/templates/backend/engine/snippets/getUrl.base.php',
				$this->variables
			);
		}
		else
		{
			$this->variables['getUrl'] == '';
		}

		// add the extra parameters in the MySQL SELECT
		$this->variables['select_extra'] = '';
		foreach($this->record['fields'] as $field)
		{
			// datetime fields should be fetched as timestamps
			if($field['type'] == 'datetime') $this->variables['select_extra'] .= ', UNIX_TIMESTAMP(i.' . $field['underscored_label'] . ') AS ' . $field['underscored_label'];
		}

		// generate model.php file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/engine/model.base.php',
			$this->variables,
			$this->backendPath . 'engine/model.php'
		);

		unset($this->variables['getUrl']);
	}

	/**
	 * Generates the basic files (info.xml and config.php in the document root.)
	 */
	protected function generateBaseFiles()
	{
		// generate info.xml file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/info.base.xml',
			$this->variables,
			$this->backendPath . 'info.xml'
		);

		// generate config.php file
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/config.base.php',
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
				'installer' => array('data'),
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
		$this->variables['install_extras'] = BackendModuleMakerGenerator::generateInstall($this->record);

		// generate installer.php
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/installer/installer.base.php',
			$this->variables,
			$this->backendPath . 'installer/installer.php'
		);

		unset($this->variables['install_extras']);

		// generate locale.xml
		BackendModuleMakerGenerator::generateFile(
			BACKEND_MODULE_PATH . '/layout/templates/backend/installer/data/locale.base.xml',
			$this->variables,
			$this->backendPath . 'installer/data/locale.xml'
		);

		// generate install.sql
		$sql = BackendModuleMakerGenerator::generateSQL($this->record['underscored_name'], $this->record);
		BackendModuleMakerModel::makeFile($this->backendPath . 'installer/data/install.sql', $sql);
	}
}
