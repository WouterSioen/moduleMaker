<?php

namespace Backend\Modules\{$camel_case_name}\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\{$camel_case_name}\Engine\Model as Backend{$camel_case_name}Model;

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @author {$author_name} <{$author_email}>
 */
class EditCategory extends ActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->getData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');
        if ($this->id == null || !Backend{$camel_case_name}Model::existsCategory($this->id)) {
            $this->redirect(
                Model::createURLForAction('categories') . '&error=non-existing'
            );
        }

        $this->record = Backend{$camel_case_name}Model::getCategory($this->id);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new Form('editCategory');
        $this->frm->addText('title', $this->record['title']);

        $this->meta = new Meta($this->frm, $this->record['meta_id'], 'title', true);
        $this->meta->setUrlCallback('Backend\Modules\{$camel_case_name}\Engine\Model', 'getURLForCategory', array($this->record['id']));
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign the data
        $this->tpl->assign('item', $this->record);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(Language::err('TitleIsRequired'));
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item = array();
                $item['id'] = $this->id;
                $item['language'] = $this->record['language'];
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['meta_id'] = $this->meta->save(true);

                // update the item
                Backend{$camel_case_name}Model::updateCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    Model::createURLForAction('categories') . '&report=edited-category&var=' .
                    urlencode($item['title']) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}
