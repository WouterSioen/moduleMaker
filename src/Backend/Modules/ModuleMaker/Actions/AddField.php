<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\ModuleMaker\Engine\Helper as BackendModuleMakerHelper;

/**
 * This is the add field-action, it will display a form to create a new field
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class AddField extends ActionAdd
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
        // If step 1 isn't entered, redirect back to the first step of the wizard
        $this->record = \SpoonSession::get('module');
        if (!$this->record || !array_key_exists('title', $this->record)) $this->redirect(Model::createURLForAction('Add'));

        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Parses the SQL for a field
     * @TODO: Don't save multicheckbox as ENUM (multiple options can be checked)
     *
     * @param  array  $field
     * @return string
     */
    protected function generateSQL($field)
    {
        $required = $field['required'] ? ' NOT NULL' : '';
        $default = '';

        if ($field['default'] !== '') {
            if ($field['type'] == 'number') $default = " DEFAULT " . $field['default'];
            else $default = " DEFAULT '" . $field['default'] . "'";
        }

        $type = '';

        switch ($field['type']) {
            case 'editor':
                $type = 'text';
                break;
            case 'number':
                $type = 'int(11)';
                break;
            case 'author':
                $type = 'int(11)';
                break;
            case 'datetime':
                $type = 'datetime';
                break;
            case 'checkbox':
                $options = explode(',', $field['options']);
                $type = "ENUM('Y','N')";
                break;
            case 'multicheckbox':
                $options = explode(',', $field['options']);
                $type = 'ENUM(';
                foreach ($options AS $option) {
                    $type .= "'" . $option . "',";
                }
                $type = rtrim($type, ',');
                $type .= ')';
                break;
            case 'radiobutton':
                $options = explode(',', $field['options']);
                $type = 'ENUM(';
                foreach ($options AS $option) {
                    $type .= "'" . $option . "',";
                }
                $type = rtrim($type, ',');
                $type .= ')';
                break;
            case 'dropdown':
                $options = explode(',', $field['options']);
                $type = 'ENUM(';
                foreach ($options AS $option) {
                    $type .= "'" . $option . "',";
                }
                $type = rtrim($type, ',');
                $type .= ')';
                break;
            case 'image_caption':
                $return = "`" . $field['underscored_label'] . "` varchar(255)" . $required . $default . ",\n";
                $return .= " `" . $field['underscored_label'] . "_caption` varchar(255)" . $default . ',';

                return $return;
                break;
            default:
                // types like text, password, file or image all map to a varchar
                $type = 'varchar(255)';
                break;
        }

        return "`" . $field['underscored_label'] . "` " . $type . $required . $default . ',';
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        /**
         * @TODO: use labels
         */
        $types = array(
            'text' => 'text',
            'editor' => 'editor',
            'number' => 'number',
            'datetime' => 'datetime',
            'password' => 'password',
            'checkbox' => 'checkbox',
            'multicheckbox' => 'multicheckbox',
            'radiobutton' => 'radiobutton',
            'dropdown' => 'dropdown',
            'author' => 'author',
            'file' => 'file',
            'image' => 'image'
        );

        $this->frm = new Form('add_field');
        $this->frm->addText('label', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addDropDown('type', $types, null);
        $this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');
        $this->frm->addCheckbox('caption');
        $this->frm->addCheckbox('required');
        $this->frm->addText('default');
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
            $fields['label']->isFilled(Language::err('FieldIsRequired'));

            // get existing fields
            $this->record = \SpoonSession::get('module');
            if (array_key_exists('fields', $this->record)) {
                foreach ($this->record['fields'] as $field) {
                    // check if we already have a type with the same label
                    if (strtolower($field['label']) == strtolower($fields['label']->getValue())) {
                        $fields['label']->addError(Language::err('LabelAlreadyExist'));
                        break;
                    }
                }
            }

            // for certain types, the options field is required
            $type = $fields['type']->getValue();
            if ($type == 'dropdown' || $type == 'multicheckbox' || $type == 'radiobutton') {
                $fields['tags']->isFilled(Language::err('FieldIsRequired'));

                // check if the default field is one of the options
                if ($fields['default']->isFilled()) {
                    $options = explode(',', $fields['tags']->getValue());
                    if (!in_array($fields['default']->getValue(), $options)) {
                        $fields['default']->addError(Language::err('DefaultShouldBeAnOption'));
                    }
                }
                // radiobuttons should have a default option
                elseif ($type == 'radiobutton') $fields['default']->addError(Language::err('FieldIsRequired'));
            }

            // if the type is images, the options should be in the form 200x200 seperated by a comma
            if ($type == 'image') {
                $fields['tags']->isFilled(Language::err('FieldIsRequired'));
                $tags = explode(',', $fields['tags']->getValue());

                // loop all tags and check on format, example (400x400)
                foreach ($tags as $tag) {
                    if (!preg_match('\'([1-9][0-9]*x[1-9][0-9]*|x[1-9][0-9]*|[1-9][0-9]*x)\'', $tag)) {
                        $fields['tags']->addError(Language::err('ImageSizeNotWellFormed'));
                        break;
                    }
                }
            }

            // check if the default value is valid
            if ($fields['default']->isFilled()) {
                // get default value
                $defaultValue = $fields['default']->getValue();

                // check the default values
                if ($type == 'text' || $type == 'password' || $type == 'file' || $type == 'image') {
                    if (strlen($defaultValue) > 255) $fields['default']->addError(Language::err('Max255Characters'));
                } elseif ($type == 'number') {
                    if (!is_numeric($defaultValue)) $fields['default']->addError(Language::err('FieldIsNotNumeric'));
                } elseif ($type == 'datetime') {
                    if (!BackendModuleMakerHelper::isValidDateTime($defaultValue)) $fields['default']->addError(Language::err('FieldIsNotAValidDateTime'));
                } elseif ($type == 'checkbox') {
                    if (strtoupper($defaultValue) != 'Y' && strtoupper($defaultValue) != 'N') $fields['default']->addError(Language::err('MustBeAYOrAN'));
                }
            }

            if ($this->frm->isCorrect()) {
                // create the item
                $item['label'] = strtolower($fields['label']->getValue());
                $item['type'] = $type;
                $item['options'] = $fields['tags']->getValue();
                $item['required'] = $fields['required']->isChecked();
                $item['default'] = $fields['default']->getValue();
                $item['camel_cased_label'] = BackendModuleMakerHelper::buildCamelCasedName($item['label']);
                $item['underscored_label'] = BackendModuleMakerHelper::buildUnderscoredName($item['label']);
                $item['lower_ccased_label'] = BackendModuleMakerHelper::buildLowerCamelCasedName($item['label']);
                $item['meta'] = false;
                $item['searchable'] = false;

                if ($item['type'] == 'image' && $fields['caption']->isChecked()) {
                    $item['type'] = 'image_caption';
                }

                // generate the SQL for the field
                $item['sql'] = $this->generateSQL($item);

                // if the record has no fields key yet, add it
                if (!array_key_exists('fields', $this->record)) $this->record['fields'] = array();

                // add the item to the fields array of the record
                $this->record['fields'][] = $item;

                // save
                \SpoonSession::set('module', $this->record);
                $this->redirect(Model::createURLForAction('AddStep2'));
            }
        }
    }
}
