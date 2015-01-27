<?php

namespace Backend\Modules\{$camel_case_name}\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;

/**
 * This is the delete-action, it deletes an item
 *
 * @author {$author_name} <{$author_email}>
 */
class Delete extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && Backend{$camel_case_name}Model::exists($this->id)) {
            parent::execute();
            $this->record = (array) Backend{$camel_case_name}Model::get($this->id);

            Backend{$camel_case_name}Model::delete($this->id);

            Model::triggerEvent(
                $this->getModule(), 'after_delete',
                array('id' => $this->id)
            );

            $this->redirect(
                Model::createURLForAction('Index') . '&report=deleted&var=' .
                urlencode($this->record['title'])
            );
        }
        else $this->redirect(Model::createURLForAction('Index') . '&error=non-existing');
    }
}
