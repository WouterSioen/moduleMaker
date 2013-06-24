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
 * @author Arend Pijls <arend.pijls@wijs.be>
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
	 * $templatesPath		The path where our templates and snippets are stored (string)
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
		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

		// If there are no fields added, redirect back to the second step of the wizard
		if(!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(BackendModel::createURLForAction('add_step2'));

		parent::execute();

		// initialize some variables
		$this->backendPath = BACKEND_MODULES_PATH . '/' . $this->record['underscored_name'] . '/';
		$this->frontendPath = FRONTEND_MODULES_PATH . '/' . $this->record['underscored_name'] . '/';
		$this->variables = (array) $this->record;
		unset($this->variables['fields']);

		$this->generateFolders();
		$this->generateBaseFiles();
		$this->generateInstallerFiles();

		// backend
		$this->generateBackendFiles();
		$this->generateBackendModel();
		$this->generateBackendActions();
		$this->generateBackendCategoryActions();

		// frontend
		$this->generateFrontendFiles();
		$this->generateFrontendModel();
		$this->generateFrontendActions();
		$this->generateFrontendCategoryActions();
		$this->generateFrontendCategoryWidget();

		$this->parse();
		$this->display();
	}

	/**
	 * Generates the backend actions (and templates) (index, add, edit and delete)
	 */
	protected function generateBackendActions()
	{
		$this->variables['sequence_extra'] = '';
		if($this->record['useSequence'])
		{
			$this->variables['sequence_extra'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/actions/snippets/sequence.base.php'
			);
		}

		// generate index
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/index.base.php', $this->variables, $this->backendPath . 'actions/index.php'
		);

		BackendModuleMakerGenerator::generateFile(
			'backend/layout/templates/index.base.tpl', $this->variables, $this->backendPath . 'layout/templates/index.tpl'
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
				'backend/actions/snippets/parse_meta.base.php'
			);
		}
		else $this->variables['parse_meta'] = '';

		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/add.base.php', $this->variables, $this->backendPath . 'actions/add.php'
		);

		// generate add template
		// create variables
		list($this->variables['template_title'], $this->variables['template'], $this->variables['template_side']) = BackendModuleMakerGenerator::generateTemplate($this->record, false);
		list($this->variables['template_tabs_top'], $this->variables['template_tabs_bottom']) = BackendModuleMakerGenerator::generateTemplateTabs($this->record);

		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			'backend/layout/templates/add.base.tpl', $this->variables, $this->backendPath . 'layout/templates/add.tpl'
		);

		// generate edit action
		// create some custom variables
		$this->variables['load_form_edit'] = BackendModuleMakerGenerator::generateLoadForm($this->record, true);
		$this->variables['validate_form_edit'] = BackendModuleMakerGenerator::generateValidateForm($this->record, true);
		$this->variables['build_item_edit'] = BackendModuleMakerGenerator::generateBuildItem($this->record, true);
		$this->variables['search_index'] = BackendModuleMakerGenerator::generateSearchIndex($this->record);

		// build and save the file
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/edit.base.php', $this->variables, $this->backendPath . 'actions/edit.php'
		);

		// generate edit template
		BackendModuleMakerGenerator::generateFile(
			'backend/layout/templates/edit.base.tpl', $this->variables, $this->backendPath . 'layout/templates/edit.tpl'
		);

		// generate delete
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/delete.base.php', $this->variables, $this->backendPath . 'actions/delete.php'
		);

		// unset the custom variables
		unset(
			$this->variables['sequence_extra'], $this->variables['load_form_add'], $this->variables['validate_form_add'],
			$this->variables['build_item_add'], $this->variables['load_form_edit'], $this->variables['validate_form_edit'],
			$this->variables['build_item_edit'], $this->variables['search_index'], $this->variables['parse_meta'],
			$this->variables['save_tags'], $this->variables['template_title'], $this->variables['template'],
			$this->variables['template_side'], $this->variables['template_tabs_top'], $this->variables['template_tabs_bottom']
		);
	}

	/**
	 * Generates the backend category actions (and templates) (categories, add_category, edit_category and delete_category)
	 */
	protected function generateBackendCategoryActions()
	{
		if(!$this->record['useCategories']) return;

		// generate categories
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/categories.base.php', $this->variables, $this->backendPath . 'actions/categories.php'
		);
		BackendModuleMakerGenerator::generateFile(
			'backend/layout/templates/categories.base.tpl', $this->variables, $this->backendPath . 'layout/templates/categories.tpl'
		);

		// generate add_category
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/add_category.base.php', $this->variables, $this->backendPath . 'actions/add_category.php'
		);
		BackendModuleMakerGenerator::generateFile(
			'backend/layout/templates/add_category.base.tpl', $this->variables, $this->backendPath . 'layout/templates/add_category.tpl'
		);

		// generate edit_category
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/edit_category.base.php', $this->variables, $this->backendPath . 'actions/edit_category.php'
		);
		BackendModuleMakerGenerator::generateFile(
			'backend/layout/templates/edit_category.base.tpl', $this->variables, $this->backendPath . 'layout/templates/edit_category.tpl'
		);

		// generate delete_category
		BackendModuleMakerGenerator::generateFile(
			'backend/actions/delete_category.base.php', $this->variables, $this->backendPath . 'actions/delete_category.php'
		);
	}

	/**
	 * Generates the backend files (module.js, sequence.php)
	 */
	protected function generateBackendFiles()
	{
		// generate module.js file
		BackendModuleMakerGenerator::generateFile(
			'backend/js/javascript.base.js', $this->variables, $this->backendPath . 'js/' . $this->record['underscored_name'] . '.js'
		);

		// add a sequence ajax action if necessary
		if($this->record['useSequence'])
		{
			BackendModuleMakerGenerator::generateFile(
				'backend/ajax/sequence.base.php', $this->variables, $this->backendPath . 'ajax/sequence.php'
			);
		}

		// add a sequence categories ajax action if necessary
		if($this->record['useCategories'])
		{
			BackendModuleMakerGenerator::generateFile(
				'backend/ajax/sequence_categories.base.php', $this->variables, $this->backendPath . 'ajax/sequence_categories.php'
			);
		}
	}

	/**
	 * Generates the backend model.php file
	 */
	protected function generateBackendModel()
	{
		// add the createURL function if there is a meta field
		if($this->record['metaField'] !== false)
		{
			$this->variables['getUrl'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/getUrl.base.php', $this->variables
			);
		}
		else $this->variables['getUrl'] = '';

		// add the getMaximumSequence function if sequencing is used
		if($this->record['useSequence'])
		{
			$this->variables['getMaxSequence'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/getMaxSequence.base.php', $this->variables
			);
		}
		else $this->variables['getMaxSequence'] = '';

		// add the extra parameters in the MySQL SELECT
		$this->variables['select_extra'] = '';
		foreach($this->record['fields'] as $field)
		{
			// datetime fields should be fetched as timestamps
			if($field['type'] == 'datetime') $this->variables['select_extra'] .= ', UNIX_TIMESTAMP(i.' . $field['underscored_label'] . ') AS ' . $field['underscored_label'];
		}

		// select the sequence for the datagrid if we have sequencing
		$this->variables['datagrid_extra'] = ($this->record['useSequence']) ? ', i.sequence' : '';
		$this->variables['datagrid_order'] = ($this->record['useSequence']) ? "\n\t\t ORDER BY i.sequence" : '';

		// create custom variables for the categories
		if($this->record['useCategories'])
		{
			$this->variables['datagrid_categories'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/datagridCategories.base.php', $this->variables
			);
			$this->variables['delete_category'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/deleteCategory.base.php', $this->variables
			);
			$this->variables['exists_category'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/existsCategory.base.php', $this->variables
			);
			$this->variables['get_category'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/getCategory.base.php', $this->variables
			);
			$this->variables['get_url_category'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/getUrlCategory.base.php', $this->variables
			);
			$this->variables['insert_category'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/insertCategory.base.php', $this->variables
			);
			$this->variables['update_category'] = BackendModuleMakerGenerator::generateSnippet(
				'backend/engine/snippets/updateCategory.base.php', $this->variables
			);
		}
		else
		{
			$this->variables['datagrid_categories'] = $this->variables['delete_category'] = $this->variables['exists_category'] = '';
			$this->variables['get_category'] = $this->variables['get_url_category'] = $this->variables['insert_category'] = '';
			$this->variables['update_category'] = '';
		}

		// generate the file
		BackendModuleMakerGenerator::generateFile(
			'backend/engine/model.base.php', $this->variables, $this->backendPath . 'engine/model.php'
		);

		unset($this->variables['getUrl'], $this->variables['getMaxSequence'], $this->variables['datagrid_extra'], $this->variables['datagrid_order']);
	}

	/**
	 * Generates the basic files (info.xml and config.php in the document root.)
	 */
	protected function generateBaseFiles()
	{
		// generate info.xml file
		BackendModuleMakerGenerator::generateFile(
			'backend/info.base.xml', $this->variables, $this->backendPath . 'info.xml'
		);

		// generate config.php file for the backend
		BackendModuleMakerGenerator::generateFile(
			'backend/config.base.php', $this->variables, $this->backendPath . 'config.php'
		);

		// generate config.php file for the frontend
		BackendModuleMakerGenerator::generateFile(
			'frontend/config.base.php', $this->variables, $this->frontendPath . 'config.php'
		);
	}

	/**
	 * Generates the backend actions (and templates) (index, add, edit and delete)
	 */
	protected function generateFrontendActions()
	{
		// use text field linked with the meta for the page title
		$this->variables['pageTitle'] = $this->record['fields'][(int) $this->record['metaField']]['underscored_label'];

		// generate index
		BackendModuleMakerGenerator::generateFile(
			'frontend/actions/index.base.php', $this->variables, $this->frontendPath . 'actions/index.php'
		);

		BackendModuleMakerGenerator::generateFile(
			'frontend/layout/templates/index.base.tpl', $this->variables, $this->frontendPath . 'layout/templates/index.tpl'
		);

		// generate detail
		BackendModuleMakerGenerator::generateFile(
			'frontend/actions/detail.base.php', $this->variables, $this->frontendPath . 'actions/detail.php'
		);

		BackendModuleMakerGenerator::generateFile(
			'frontend/layout/templates/detail.base.tpl', $this->variables, $this->frontendPath . 'layout/templates/detail.tpl'
		);

		unset($this->variables['pageTitle']);
	}


	/**
	 * Generates the frontend category action
	 */
	protected function generateFrontendCategoryActions()
	{
		if(!$this->record['useCategories']) return;

		// generate category action
		BackendModuleMakerGenerator::generateFile(
			'frontend/actions/category.base.php', $this->variables, $this->frontendPath . 'actions/category.php'
		);
		BackendModuleMakerGenerator::generateFile(
			'frontend/layout/templates/category.base.tpl', $this->variables, $this->frontendPath . 'layout/templates/category.tpl'
		);
	}

	/**
	 * Generates the frontend categories widget
	 */
	protected function generateFrontendCategoryWidget()
	{
		if(!$this->record['useCategories']) return;

		// generate categories widget
		BackendModuleMakerGenerator::generateFile(
			'frontend/widgets/categories.base.php', $this->variables, $this->frontendPath . 'widgets/categories.php'
		);
		BackendModuleMakerGenerator::generateFile(
			'frontend/layout/widgets/categories.base.tpl', $this->variables, $this->frontendPath . 'layout/widgets/categories.tpl'
		);
	}

	/**
	 * Generates the frontend files (module.js, sequence.php)
	 */
	protected function generateFrontendFiles()
	{
		// generate module.js file
		BackendModuleMakerGenerator::generateFile(
			'frontend/js/javascript.base.js', $this->variables, $this->frontendPath . 'js/' . $this->record['underscored_name'] . '.js'
		);
	}

	/**
	 * Generates the frontend model.php file
	 */
	protected function generateFrontendModel()
	{
		// create custom mysql queries for the sequence
		$this->variables['sequence_sorting'] = '';
		if($this->record['useSequence'])
		{
			$this->variables['sequence_sorting'] = 'i.sequence ASC, ';
		}

		// create custom variables for the categories
		if($this->record['useCategories'])
		{
			$this->variables['getAllByCategory'] = BackendModuleMakerGenerator::generateSnippet(
				'frontend/engine/snippets/getAllByCategory.base.php', $this->variables
			);

			$this->variables['getCategory'] = BackendModuleMakerGenerator::generateSnippet(
				'frontend/engine/snippets/getCategory.base.php', $this->variables
			);

			$this->variables['getCategoryCount'] = BackendModuleMakerGenerator::generateSnippet(
				'frontend/engine/snippets/getCategoryCount.base.php', $this->variables
			);
		}

		// check if search is enabled
		$this->variables['search'] = '';
		if($this->record['searchFields'])
		{
			// use text field linked with the meta for the page title
			$this->variables['meta_field'] = $this->record['fields'][(int) $this->record['metaField']]['underscored_label'];

			$this->variables['search'] = BackendModuleMakerGenerator::generateSnippet(
				'frontend/engine/snippets/search.base.php', $this->variables
			);
		}

		// generate the file
		BackendModuleMakerGenerator::generateFile(
			'frontend/engine/model.base.php', $this->variables, $this->frontendPath . 'engine/model.php'
		);

		unset(
			$this->variables['getAllByCategory'],
			$this->variables['getCategory'],
			$this->variables['getCategoryCount'],
			$this->variables['sequence_sorting'],
			$this->variables['meta_field'],
			$this->variables['search']
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
				'actions', 'ajax', 'js', 'cronjobs', 'engine',
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
				'actions', 'engine', 'widgets',
				'layout' => array('templates', 'widgets'),
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
		list($this->variables['install_extras'], $this->variables['backend_navigation']) = BackendModuleMakerGenerator::generateInstall($this->variables);

		// generate installer.php
		BackendModuleMakerGenerator::generateFile(
			'backend/installer/installer.base.php', $this->variables, $this->backendPath . 'installer/installer.php'
		);

		unset($this->variables['install_extras'], $this->variables['backend_navigation']);

		// generate locale.xml
		BackendModuleMakerGenerator::generateFile(
			'backend/installer/data/locale.base.xml', $this->variables, $this->backendPath . 'installer/data/locale.xml'
		);

		// generate install.sql
		$sql = BackendModuleMakerGenerator::generateSQL($this->record['underscored_name'], $this->record);
		if($this->record['useCategories'])
		{
			$sql .= BackendModuleMakerGenerator::generateSnippet(
				'backend/installer/snippets/categories.base.sql', $this->variables
			);
		}
		BackendModuleMakerModel::makeFile($this->backendPath . 'installer/data/install.sql', $sql);
	}

	/**
	 * Parses the data in the template
	 */
	protected function parse()
	{
		$this->tpl->assign('module', $this->record);

		// SpoonSession::delete('module');
	}
}
