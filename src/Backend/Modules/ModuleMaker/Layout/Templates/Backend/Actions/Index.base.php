<?php

namespace Backend\Modules\{$camel_case_name}\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;

/**
 * This is the index-action (default), it will display the overview of {$title} posts
 *
 * @author {$author_name} <{$author_email}>
 */
class Index extends ActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadDataGrid();

        $this->parse();
        $this->display();
    }

    /**
     * Load the dataGrid
     */
    protected function loadDataGrid()
    {
        $this->dataGrid = new DataGridDB(
            Backend{$camel_case_name}Model::QRY_DATAGRID_BROWSE,
            Language::getWorkingLanguage()
        );

        // reform date
        $this->dataGrid->setColumnFunction(
            array('Backend\Core\Engine\DataGridFunctions', 'getLongDate'),
            array('[created_on]'),
            'created_on',
            true
        );
{$sequence_extra}
        // check if this action is allowed
        if (Authentication::isAllowedAction('Edit')) {
            $this->dataGrid->addColumn(
                'edit',
                null,
                Language::lbl('Edit'),
                Model::createURLForAction('Edit') . '&amp;id=[id]',
                Language::lbl('Edit')
            );
            $this->dataGrid->setColumnURL(
                '{$meta_field}',
                Model::createURLForAction('Edit') . '&amp;id=[id]'
            );
        }
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        // parse the dataGrid if there are results
        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
