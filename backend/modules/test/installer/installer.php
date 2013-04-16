<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the Test module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class TestInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('test');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'test');

		$this->setActionRights(1, 'test', 'index');
		$this->setActionRights(1, 'test', 'add');
		$this->setActionRights(1, 'test', 'edit');
		$this->setActionRights(1, 'test', 'delete');
		$this->setActionRights(1, 'test', 'categories');
		$this->setActionRights(1, 'test', 'add_category');
		$this->setActionRights(1, 'test', 'edit_category');
		$this->setActionRights(1, 'test', 'delete_category');

		// add extra's
		$subnameID = $this->insertExtra('test', 'block', 'Test', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationTestId = $this->setNavigation($navigationModulesId, 'Test');
		$this->setNavigation(
			$navigationTestId, 'Test', 'test/index',
			array('test/add', 'test/edit')
		);
		$this->setNavigation(
			$navigationTestId, 'Categories', 'test/categories',
			array('test/add_category', 'test/edit_category')
		);
	}
}
