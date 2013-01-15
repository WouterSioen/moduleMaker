<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the awesome module module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class AwesomeModuleInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('awesome_module');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'awesome_module');

		$this->setActionRights(1, 'awesome_module', 'index');

		// add extra's
		$subnameID = $this->insertExtra('awesome_module', 'block', 'AwesomeModule', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationclassnameId = $this->setNavigation(
			$navigationModulesId,
			'AwesomeModule',
			'awesome_module/index',
			array('awesome_module/add','awesome_module/edit')
		);
	}
}
