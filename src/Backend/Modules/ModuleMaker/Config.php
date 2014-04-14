<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object for the modulemaker module
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
final class BackendModuleMakerConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'add';

	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();

	/**
	 * Check if all required settings have been set
	 *
	 * @param string $module The module.
	 */
	public function __construct($module)
	{
		parent::__construct($module);

		$this->loadEngineFiles();
	}

	/**
	 * Loads additional engine files
	 */
	private function loadEngineFiles()
	{
		require_once 'engine/helper.php';
		require_once 'engine/generator.php';
	}
}
