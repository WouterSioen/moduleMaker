<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\ModuleMaker\Engine\Helper as BackendModuleMakerHelper;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class Add extends ActionAdd
{
    /**
     * The module we're working on
     *
     * @var array
     */
    private $record;

    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();

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
        $this->record = \SpoonSession::get('module');

        $this->frm = new Form('add');
        $this->frm->addText('title', $this->record ? $this->record['title'] : null, null, 'inputText title', 'inputTextError title');
        $this->frm->addTextArea('description', $this->record ? $this->record['description'] : null);
        $this->frm->addText('author_name', $this->record ? $this->record['author_name'] : null);
        $this->frm->addText('author_url', $this->record ? $this->record['author_url'] : null);
        $this->frm->addText('author_email', $this->record ? $this->record['author_email'] : null);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();
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
            $fields['title']->isFilled(Language::err('TitleIsRequired'));
            $fields['description']->isFilled(Language::err('FieldIsRequired'));
            $fields['author_name']->isFilled(Language::err('FieldIsRequired'));
            $fields['author_url']->isFilled(Language::err('FieldIsRequired'));
            $fields['author_email']->isFilled(Language::err('FieldIsRequired'));

            // cleanup the modulename
            $title = preg_replace('/[^A-Za-z ]/', '', $fields['title']->getValue());

            // check if there is already a module with this name
            if (BackendExtensionsModel::existsModule($title)) $fields['title']->addError(Language::err('DuplicateModuleName'));

            if ($this->frm->isCorrect()) {
                $this->record['title'] = $title;
                $this->record['description'] = trim($fields['description']->getValue());
                $this->record['author_name'] = $fields['author_name']->getValue();
                $this->record['author_url'] = $fields['author_url']->getValue();
                $this->record['author_email'] = $fields['author_email']->getValue();
                $this->record['camel_case_name'] = BackendModuleMakerHelper::buildCamelCasedName($title);
                $this->record['underscored_name'] = BackendModuleMakerHelper::buildUnderscoredName($title);

                \SpoonSession::set('module', $this->record);

                $this->redirect(Model::createURLForAction('AddStep2'));
            }
        }
    }
}
