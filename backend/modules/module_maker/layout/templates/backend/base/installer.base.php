<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the {$title} module
 *
 * @author {$author_name} <{$author_email}>
 */
class {$camel_case_name}Installer extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('{$underscored_name}');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, '{$underscored_name}');

		$this->setActionRights(1, '{$underscored_name}', 'index');

		// add extra's
		$subnameID = $this->insertExtra('{$underscored_name}', 'block', '{$camel_case_name}', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationclassnameId = $this->setNavigation(
			$navigationModulesId,
			'{$camel_case_name}',
			'{$underscored_name}/index',
			array('{$underscored_name}/add','{$underscored_name}/edit')
		);
	}
}
