<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Renders a field
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class BackendModuleMakerAjaxRenderField extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->output(self::OK, array(), 'field rendered');
	}
}
