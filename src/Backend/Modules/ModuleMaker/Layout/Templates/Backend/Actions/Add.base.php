<?php

namespace Backend\Modules\{$camel_case_name}\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author {$author_name} <{$author_email}>
 */
class Add extends ActionAdd
{
    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();
{$multiFilesJs}
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $this->frm = new Form('add');

{$multiFilesLoad}{$load_form_add}
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();{$parse_meta}
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

{$validate_form_add}
            if ($this->frm->isCorrect()) {
                // build the item
                $item['language'] = Language::getWorkingLanguage();
{$build_item_add}
                // insert it
                $item['id'] = Backend{$camel_case_name}Model::insert($item);
{$multiFilesSave}{$save_tags}{$search_index}
                Model::triggerEvent(
                    $this->getModule(), 'after_add', $item
                );
                $this->redirect(
                    Model::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
                );
            }
        }
    }
}
