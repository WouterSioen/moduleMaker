<?php

namespace Backend\Modules\{$camel_case_name}\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author {$author_name} <{$author_email}>
 */
class Edit extends ActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
{$multiFilesJs}
        $this->loadData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the item data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('id', 'int', null);
        if($this->id == null || !Backend{$camel_case_name}Model::exists($this->id))
        {
            $this->redirect(
                Model::createURLForAction('index') . '&error=non-existing'
            );
        }

        $this->record = Backend{$camel_case_name}Model::get($this->id);{$load_data_edit}
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        // create form
        $this->frm = new Form('edit');

{$multiFilesLoad}{$load_form_edit}
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();{$parse_meta}

        $this->tpl->assign('item', $this->record);
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if($this->frm->isSubmitted())
        {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

{$validate_form_edit}
            if($this->frm->isCorrect())
            {
                $item['id'] = $this->id;
                $item['language'] = Language::getWorkingLanguage();

{$build_item_edit}
                Backend{$camel_case_name}Model::update($item);
                $item['id'] = $this->id;
{$multiFilesSave}{$save_tags}{$search_index}
                Model::triggerEvent(
                    $this->getModule(), 'after_edit', $item
                );
                $this->redirect(
                    Model::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']
                );
            }
        }
    }
}
