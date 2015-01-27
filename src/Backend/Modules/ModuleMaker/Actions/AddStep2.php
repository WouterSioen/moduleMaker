<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\DataGridArray;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class AddStep2 extends ActionAdd
{
    /**
     * The module we're working on
     *
     * @var array
     */
    private $record;

    /**
     * The datagrid with all the fields
     *
     * @var BackendDataGrid
     */
    private $datagrid;

    /**
     * We need at least one varchar for the meta
     *
     * @var boolean
     */
    private $allowSave = false;

    /**
     * Execute the actions
     */
    public function execute()
    {
        // If step 1 isn't entered, redirect back to the first step of the wizard
        $this->record = \SpoonSession::get('module');
        if (!$this->record || !array_key_exists('title', $this->record)) $this->redirect(Model::createURLForAction('add'));

        parent::execute();

        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    private function loadDataGrid()
    {
        // add a fields array key to the record to make sure the datagrid can be crated
        if (!array_key_exists('fields', $this->record)) $this->record['fields'] = array();

        // add the key of each items to the values. This will be used for a link to the delete_field action
        foreach ($this->record['fields'] as $key => $field) {
            $this->record['fields'][$key]['key'] = $key;
        }

        // check if we have a varchar
        foreach ($this->record['fields'] as $field) {
            if ($field['type'] == 'text') {
                $this->allowSave = true;
                break;
            }
        }

        // if the record has fields, create a datagrid with all the fields
        $this->datagrid = new DataGridArray($this->record['fields']);
        $this->datagrid->addColumn('delete', null, Language::lbl('Delete'), Model::createURLForAction('DeleteField') . '&amp;id=[key]', Language::lbl('Delete'));
        $this->datagrid->setColumnsHidden(array('key', 'underscored_label', 'camel_cased_label', 'sql'));
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();
        $this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
        $this->tpl->assign('varcharFound', $this->allowSave);
    }
}
