<?php

namespace Backend\Modules\ModuleMaker;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the moduleMaker
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Add';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();
}
