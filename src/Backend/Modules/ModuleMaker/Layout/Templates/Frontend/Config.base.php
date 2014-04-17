<?php

namespace Frontend\Modules\{$camel_case_name};

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Config as BaseConfig;

/**
 * This is the configuration-object for the {$title} module
 *
 * @author {$author_name} <{$author_email}>
 */
final class Config extends BaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'Index';

	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();
}
