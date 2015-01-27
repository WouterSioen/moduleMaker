<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;

/**
 * This action will delete an field from the current record
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class DeleteField extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // If step 1 isn't entered, redirect back to the first step of the wizard
        $this->record = \SpoonSession::get('module');
        if (!$this->record || !array_key_exists('title', $this->record)) $this->redirect(Model::createURLForAction('Add'));

        // If there are no fields added, redirect back to the second step of the wizard
        if (!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(Model::createURLForAction('AddStep2') . '&amp;error=non-existing');

        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && array_key_exists($this->id, $this->record['fields'])) {
            unset($this->record['fields'][$this->id]);
            \SpoonSession::set('module', $this->record);
            $this->redirect(Model::createURLForAction('AddStep2') . '&report=deleted');
        }

        // something went wrong
        else $this->redirect(Model::createURLForAction('AddStep2') . '&error=non-existing');
    }
}
