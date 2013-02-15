<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add field-action, it will display a form to create a new field
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerAddField extends BackendBaseActionAdd
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
		// If step 1 isn't entered, redirect back to the first step of the wizard
		$this->record = SpoonSession::get('module');
		if(!$this->record || !array_key_exists('title', $this->record)) $this->redirect(BackendModel::createURLForAction('add'));

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
			'file' => 'file',
			'image' => 'image'
		);

		$this->frm = new BackendForm('add_field');
		$this->frm->addText('label', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addDropDown('type', $types, null);
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');
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
	 * Parses the SQL for a field
	 * 
	 * @param array $field
	 * @return string
	 */
	protected function parseSQL($field)
	{
		$required = $field['required'] ? ' NOT NULL' : '';
		$default = '';

		if($field['default'] !== '')
		{
			if($field['type'] == 'number') $default = " DEFAULT " . $field['default'];
			else $default = " DEFAULT '" . $field['default'] . "'";
		}

		$type = '';

		switch ($field['type'])
		{
			case 'editor':
				$type = 'text';
				break;
			case 'number':
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
				foreach($options AS $option)
				{
					$type .= "'" . $option . "',";
				}
				$type = rtrim($type, ',');
				$type .= ')';
				break;
			case 'radiobutton':
				$options = explode(',', $field['options']);
				$type = 'ENUM(';
				foreach($options AS $option)
				{
					$type .= "'" . $option . "',";
				}
				$type = rtrim($type, ',');
				$type .= ')';
				break;
			case 'dropdown':
				$options = explode(',', $field['options']);
				$type = 'ENUM(';
				foreach($options AS $option)
				{
					$type .= "'" . $option . "',";
				}
				$type = rtrim($type, ',');
				$type .= ')';
				break;
			default:
				// types like text, password, file or image all map to a varchar
				$type = 'varchar(255)';
				break;
		}

		return "`" . $field['underscored_label'] . "` " . $type . $required . $default . ',';
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
			$fields['label']->isFilled(BL::err('FieldIsRequired'));

			// for certain types, the options field is required
			$type = $fields['type']->getValue();
			if($type == 'dropdown' || $type == 'multicheckbox' || $type == 'radiobutton')
			{
				$fields['tags']->isFilled(BL::err('FieldIsRequired'));

				// check if the default field is one of the options
				if($fields['default']->isFilled())
				{
					$options = explode(',', $fields['tags']->getValue());
					if(!in_array($fields['default']->getValue(), $options))
					{
						$fields['default']->addError(BL::err('DefaultShouldBeAnOption'));
					}
				}
			}

			// if the type is images, the options should be in the form 200x200 seperated by a comma
			if($type == 'image')
			{
				$fields['tags']->isFilled(BL::err('FieldIsRequired'));
				$fields['tags']->isValidAgainstRegexp('\'([1-9][0-9]*x[1-9][0-9]*[,])+([1-9][0-9]*x[1-9][0-9]*)\'', BL::err('ImageSizeNotWellFormed'));
			}

			/**
			 * @TODO validate the default option to the chosen datatype
			 */

			if($this->frm->isCorrect())
			{
				// create the item
				$item['label'] = $fields['label']->getValue();
				$item['type'] = $type;
				$item['options'] = $fields['tags']->getValue();
				$item['required'] = $fields['required']->isChecked();
				$item['default'] = $fields['default']->getValue();
				$item['camel_cased_label'] = BackendModuleMakerHelper::buildCamelCasedName($item['label']);
				$item['underscored_label'] = BackendModuleMakerHelper::buildUnderscoredName($item['label']);
				$item['lower_ccased_label'] = BackendModuleMakerHelper::buildLowerCamelCasedName($item['label']);

				// generate the SQL for the field
				$item['sql'] = $this->parseSQL($item);

				// if the record has no fields key yet, add it
				if(!array_key_exists('fields', $this->record)) $this->record['fields'] = array();

				// add the item to the fields array of the record
				$this->record['fields'][] = $item;

				// save
				SpoonSession::set('module', $this->record);
				$this->redirect(BackendModel::createURLForAction('add_step2'));
			}
		}
	}
}
