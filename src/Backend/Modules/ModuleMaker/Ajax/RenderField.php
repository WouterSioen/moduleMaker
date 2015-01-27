<?php

namespace Backend\Modules\ModuleMaker\Ajax;

use Backend\Core\Engine\Base\AjaxAction;

/**
 * Renders a field
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class RenderField extends AjaxAction
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
