<?php

/**
 * This is the modules-action, it will display the overview of modules.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @author Arend Pijls <arend.pijls@wijs.be>
 */
class BackendModuleMakerCreateZip extends BackendBaseActionIndex
{
	/**
	 * Data grids.
	 *
	 * @var BackendDataGrid
	 */
	private $dataGridInstalledModules, $dataGridInstallableModules;

	/**
	 * Modules that are or or not installed.
	 * This is used as a source for the data grids.
	 *
	 * @var array
	 */
	private $installedModules = array(), $installableModules = array();

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();

		if($this->getParameter('module') !== NULL)
		{
			$dir = "/Users/arendpijls/repos/moduleMaker/frontend/modules/second_hand/";
			$di = new RecursiveDirectoryIterator($dir);
			foreach (new RecursiveIteratorIterator($di) as $filename => $file)
			{
				$files[] = str_replace('/Users/arendpijls/repos/moduleMaker/', '', $filename);
			}

			Spoon::dump(BackendModuleMakerHelper::create_zip($files, '/Users/arendpijls/repos/moduleMaker/test.zip'), true);
		}
		else
		{
			$this->loadDataGridInstalled();
			$this->loadDataGridInstallable();

			$this->parse();
			$this->display();
		}
	}

	/**
	 * Load the data for the 2 data grids.
	 */
	private function loadData()
	{
		// get all manageable modules
		$modules = BackendExtensionsModel::getModules();

		// split the modules in 2 separate data grid sources
		foreach($modules as $module)
		{
			if($module['installed']) $this->installedModules[] = $module;
			else $this->installableModules[] = $module;
		}
	}

	/**
	 * Load the data grid for installable modules.
	 */
	private function loadDataGridInstallable()
	{
		// create datagrid
		$this->dataGridInstallableModules = new BackendDataGridArray($this->installableModules);

		$this->dataGridInstallableModules->setSortingColumns(array('raw_name'));
		$this->dataGridInstallableModules->setHeaderLabels(array('raw_name' => SpoonFilter::ucfirst(BL::getLabel('Name'))));
		$this->dataGridInstallableModules->setColumnsHidden(array('installed', 'name', 'cronjobs_active'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('detail_module'))
		{
			$this->dataGridInstallableModules->setColumnURL('raw_name', BackendModel::createURLForAction('detail_module') . '&amp;module=[raw_name]');
			$this->dataGridInstallableModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('detail_module') . '&amp;module=[raw_name]', BL::lbl('Details'));
		}

		// add create zip column
		$this->dataGridInstallableModules->addColumn('install', null, ucfirst(BL::lbl('CreateZip')), BackendModel::createURLForAction('create_zip', 'module_maker') . '&amp;module=[raw_name]', ucfirst(BL::lbl('CreateZip')));
		$this->dataGridInstallableModules->setColumnConfirm('install', sprintf(BL::msg('ConfirmModuleInstall'), '[raw_name]'), null, SpoonFilter::ucfirst(BL::lbl('CreateZip')) . '?');
	}

	/**
	 * Load the data grid for installed modules.
	 */
	private function loadDataGridInstalled()
	{
		// create datagrid
		$this->dataGridInstalledModules = new BackendDataGridArray($this->installedModules);

		$this->dataGridInstalledModules->setSortingColumns(array('name'));
		$this->dataGridInstalledModules->setColumnsHidden(array('installed', 'raw_name', 'cronjobs_active'));

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('detail_module'))
		{
			$this->dataGridInstalledModules->setColumnURL('name', BackendModel::createURLForAction('detail_module', 'extensions') . '&amp;module=[raw_name]');
			$this->dataGridInstalledModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('detail_module', 'extensions') . '&amp;module=[raw_name]', BL::lbl('Details'));
		}

		// add create zip column
		$this->dataGridInstalledModules->addColumn('install', null, ucfirst(BL::lbl('CreateZip')), BackendModel::createURLForAction('create_zip', 'module_maker') . '&amp;module=[raw_name]', ucfirst(BL::lbl('CreateZip')));
		$this->dataGridInstalledModules->setColumnConfirm('install', sprintf(BL::msg('ConfirmModuleInstall'), '[raw_name]'), null, SpoonFilter::ucfirst(BL::lbl('CreateZip')) . '?');
	}

	/**
	 * Parse the datagrids and the reports.
	 */
	protected function parse()
	{
		parent::parse();

		// parse data grid
		$this->tpl->assign('dataGridInstallableModules', (string) $this->dataGridInstallableModules->getContent());
		$this->tpl->assign('dataGridInstalledModules', (string) $this->dataGridInstalledModules->getContent());

		// parse installer warnings
		$this->tpl->assign('warnings', (array) SpoonSession::get('installer_warnings'));
	}
}
