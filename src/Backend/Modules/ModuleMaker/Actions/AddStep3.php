<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;

/**
 * This is the add step 3-action, it will display a form to add special fields to a module
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class AddStep3 extends ActionAdd
{
    /**
     * The module we're working on
     *
     * @var array
     */
    private $record;

    /**
     * The selected meta field, The selected search fields
     *
     * @var int
     */
    private $selectedMeta, $selectedSearch;

    /**
     * Execute the actions
     */
    public function execute()
    {
        // If step 1 isn't entered, redirect back to the first step of the wizard
        $this->record = \SpoonSession::get('module');

        if (!$this->record || !array_key_exists('title', $this->record)) $this->redirect(Model::createURLForAction('Add'));

        // If there are no fields added, redirect back to the second step of the wizard
        if (!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(Model::createURLForAction('AddStep2'));

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
        // create all variables needed for meta
        $fields = array();
        $this->selectedMeta = false;

        foreach ($this->record['fields'] as $key => $field) {
            if ($field['type'] == 'text') $fields[$key] = $field['label'];
            if (array_key_exists('meta', $field) && $field['meta'] == true) $this->selectedMeta = $key;
        }

        // create all variables needed for searchindex
        $searchFields = array();
        $this->selectedSearch = false;

        foreach ($this->record['fields'] as $key => $field) {
            if ($field['type'] == 'text' || $field['type'] == 'editor') {
                $searchFields[$key] = array(
                    'label' => ucfirst($field['label']),
                    'value' => $key
                );
            }
            if (array_key_exists('searchable', $field) && $field['searchable'] == true) $this->selectedSearch = $key;
        }

        // create the form
        $this->frm = new Form('add_step3');
        $this->frm->addCheckbox('meta', ($this->selectedMeta !== false));
        $this->frm->addDropDown('meta_field', $fields, $this->selectedMeta);

        $this->frm->addCheckbox('search', ($this->selectedSearch !== false));
        $this->frm->addMultiCheckbox('search_fields', $searchFields, $this->selectedSearch);

        $this->frm->addCheckbox('tags', (array_key_exists('useTags', $this->record) && $this->record['useTags']));
        $this->frm->addCheckbox('sequence', (array_key_exists('useSequence', $this->record) && $this->record['useSequence']));
        $this->frm->addCheckbox('categories', (array_key_exists('useCategories', $this->record) && $this->record['useCategories']));
        $this->frm->addCheckbox('multiple_images', (array_key_exists('multipleImages', $this->record) && $this->record['multipleImages']));
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('meta', ($this->selectedMeta !== false));

        if ($this->frm->isSubmitted() && $this->frm->getField('search')->isChecked()) {
            $this->tpl->assign('search', true);
        } else $this->tpl->assign('search', ($this->selectedSearch !== false));
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            $frmFields = $this->frm->getFields();

            // validate form
            if ($frmFields['search']->isChecked()) {
                // we need fields when search is ticked
                $frmFields['search_fields']->isFilled(Language::err('FieldIsRequired'));
            }

            if ($this->frm->isCorrect()) {
                // set all fields to searchable false
                foreach ($this->record['fields'] as &$field) $field['searchable'] = false;

                // get meta value
                $metaField = $frmFields['meta_field']->getValue();
                $this->record['fields'][$metaField]['meta'] = true;
                $this->record['metaField'] = $metaField;

                // set meta type required
                $this->record['fields'][$metaField]['required'] = true;

                // if this field is checked, let's add a boolean searchable true to the chosen fields
                if ($frmFields['search']->isChecked()) {
                    $searchFields = $frmFields['search_fields']->getValue();
                    foreach ($searchFields as $searchField) {
                        $this->record['fields'][$searchField]['searchable'] = true;
                    }
                    $this->record['searchFields'] = implode(',',$searchFields);
                } else $this->record['searchFields'] = false;

                $this->record['useTags'] = ($frmFields['tags']->isChecked());
                $this->record['useSequence'] = ($frmFields['sequence']->isChecked());
                $this->record['useCategories'] = ($frmFields['categories']->isChecked());
                $this->record['multipleImages'] = ($frmFields['multiple_images']->isChecked());

                // save the object in our session
                \SpoonSession::set('module', $this->record);
                $this->redirect(Model::createURLForAction('AddStep4'));
            }
        }
    }
}
