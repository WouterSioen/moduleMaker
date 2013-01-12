<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the module_maker module
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class ModuleMakerInstaller extends ModuleInstaller
{
	public function install()
	{
		// install the module in the database
		$this->addModule('module_maker');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'module_maker');

		$this->setActionRights(1, 'module_maker', 'add');
		$this->setActionRights(1, 'module_maker', 'add_step2');
		$this->setActionRights(1, 'module_maker', 'add_step3');

		// add extra's
		$module_makerID = $this->insertExtra('module_maker', 'block', 'Module_Maker', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationModule_MakerId = $this->setNavigation($navigationModulesId, 'ModuleMaker', 'module_maker/add', array('module_maker/add_step2', 'module_maker/add_step3'));
	}
}
