<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all functions that generate blocks of code for the modulemaker module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerGenerator
{
	/**
	 * Generates a part of the add/edit action that builds the item
	 * 
	 * @param array $module				The array containing all info about the module
	 * @param boolean $isEdit			Should we generate it for the edit action?
	 * @return string
	 */
	public static function generateBuildItem($module, $isEdit)
	{
		$return = '';

		// loop through fields
		foreach($module['fields'] as $field)
		{
			// for images, create the code to create folders for each image size
			if($field['type'] == 'image')
			{
				// loop through the options, they contain the image sizes
				$options = explode(',', $field['options']);
				$field['create_folders'] = '';
				foreach($options as $option)
				{
					$field['create_folders'] .= "\t\t\t\tif(!SpoonDirectory::exists(\$imagePath . '/" . $option . "')) SpoonDirectory::create(\$imagePath . '/" . $option . "');\n";
				}

				// add the function used to create the filename
				if($module['metaField'] !== false) $field['file_name_function'] = '$this->meta->getUrl()';
				else $field['file_name_function'] = 'time()';
			}

			// when there is a snippet provided for the datatype, use it. This falls back to a default snippet
			if(file_exists(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/build_' . $field['type'] . '.base.php'))
			{
				$return .= self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/build_' . $field['type'] . '.base.php', $field);
			}
			else
			{
				$return .= self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/build_simple.base.php', $field);
			}
		}

		// add meta if necessary
		if($module['metaField'] !== false)
		{
			$return .= "\n\t\t\t\t\$item['meta_id'] = \$this->meta->save();\n";
		}

		// return the string we build up
		return $return;
	}

	/**
	 * Generates (and writes) a file based on a certain template
	 * 
	 * @param string $template				The path to the template
	 * @param array $variables				The variables to assign to the template
	 * @param string $path					The path to the file
	 */
	public static function generateFile($template, $variables, $path)
	{
		// get the content of the file
		$content = BackendModuleMakerModel::readFile($template);

		// replace the variables
		foreach($variables AS $key => $value)
		{
			$content = str_replace('{$' . $key . '}', $value, $content);
		}

		// write the file
		BackendModuleMakerModel::makeFile($path, $content);
	}

	/**
	 * Generates extra installer code
	 * 
	 * @param array $module
	 * @return string
	 */
	public static function generateInstall($module)
	{
		$return = '';
		if($module['searchFields'] !== false)
		{
			$return .= self::generateSnippet(
				BACKEND_MODULE_PATH . '/layout/templates/backend/installer/snippets/search.base.php',
				array('module_name' => $module['underscored_name'])
			);
		}

		return $return;
	}

	/**
	 * Generates a part of the loadForm() function for the backend add/edit actions
	 * 
	 * @param array $module				The array containing all info about the module
	 * @param boolean $isEdit			Should we generate it for the edit action?
	 * @return string
	 */
	public static function generateLoadForm($module, $isEdit)
	{
		$return = '';

		// Add the meta field as a title field
		if($module['metaField'] !== false)
		{
			$metaField = $module['fields'][$module['metaField']];

			// create the default value
			$default = '';
			if($isEdit)
			{
				$default = " ,\$this->record['" . $metaField['underscored_label'] . "']";
			}
			elseif($metaField['default'] !== '')
			{
				if($metaField['type'] == 'number') $default = ' ,' . $metaField['default'];
				elseif($metaField['type'] == 'dropdown') $default = ", BL::lbl('" . BackendModuleMakerHelper::buildCamelCasedName($metaField['default']) . "')";
				else $default = " ,'" . $metaField['default'] . "'";
			}

			$return .= "\t\t\$this->frm->addText('" . $metaField['underscored_label'] . "'" . $default . (($default) ? '' : ', null') . ", null, 'inputText title', 'inputTextError title');\n";
		}

		// loop through fields and create and addField statement for each field
		foreach($module['fields'] as $field)
		{
			//don't add the metafield, it's already added
			if($field['meta']) continue;

			// for fields with multiple options: add them
			if($field['type'] == 'multicheckbox' || $field['type'] == 'radiobutton')
			{
				$return .= "\n\t\t// build array with options for the " . $field['label'] . ' ' . $field['type'] . "\n";

				// split the options on the comma and add them to an array
				$options = explode(',', $field['options']);
				foreach($options as $option)
				{
					$return .= "\t\t\$" . $field['type'] . $field['camel_cased_label'] . "Values[] = array('label' => BL::lbl('" . BackendModuleMakerHelper::buildCamelCasedName($option) . "'), 'value' => '" . $option . "');\n";
				}
			}
			elseif($field['type'] == 'dropdown')
			{
				$return .= "\n\t\t// build array with options for the " . $field['label'] . ' ' . $field['type'] . "\n";

				// split the options on the comma and add them to an array
				$options = explode(',', $field['options']);
				$return .= "\t\t\$" . $field['type'] . $field['camel_cased_label'] . 'Values = array(';
				foreach($options as $option)
				{
					$return .= "BL::lbl('" . BackendModuleMakerHelper::buildCamelCasedName($option) . "'), ";
				}
				$return = rtrim($return, ', ');
				$return .= ");\n";
			}

			// create the default value
			$default = '';
			if($isEdit)
			{
				$default = " ,\$this->record['" . $field['underscored_label'] . "']";
			}
			elseif($field['default'] !== '')
			{
				if($field['type'] == 'number') $default = ' ,' . $field['default'];
				elseif($field['type'] == 'dropdown') $default = ", BL::lbl('" . BackendModuleMakerHelper::buildCamelCasedName($field['default']) . "')";
				else $default = " ,'" . $field['default'] . "'";
			}

			// create the add statements
			switch ($field['type'])
			{
				case 'editor':
					$return .= "\t\t\$this->frm->addEditor('" . $field['underscored_label'] . "'" . $default . ");\n";
					break;
				case 'datetime':
					$return .= "\t\t\$this->frm->addDate('" . $field['underscored_label'] . "_date'" . $default . ");\n";
					$return .= "\t\t\$this->frm->addTime('" . $field['underscored_label'] . "_time'" . $default . ");\n";
					break;
				case 'password':
					$return .= "\t\t\$this->frm->addPassword('" . $field['underscored_label'] . "'" . $default . ")->setAttributes(array('autocomplete' => 'off'));\n";
					break;
				case 'checkbox':
					$return .= "\t\t\$this->frm->addCheckbox('" . $field['underscored_label'] . "'" . $default . ");\n";
					break;
				case 'multicheckbox':
					$return .= "\t\t\$this->frm->addMultiCheckbox('" . $field['underscored_label'] . "', $" . $field['type'] . $field['camel_cased_label'] . 'Values' . $default . ");\n";
					break;
				case 'radiobutton':
					$return .= "\t\t\$this->frm->addRadioButton('" . $field['underscored_label'] . "', $" . $field['type'] . $field['camel_cased_label'] . 'Values' . $default . ");\n";
					break;
				case 'dropdown':
					$return .= "\t\t\$this->frm->addDropdown('" . $field['underscored_label'] . "', $" . $field['type'] . $field['camel_cased_label'] . 'Values' . $default . ");\n";
					break;
				case 'file':
					$return .= "\t\t\$this->frm->addFile('" . $field['underscored_label'] . "');\n";
					break;
				case 'image':
					$return .= "\t\t\$this->frm->addImage('" . $field['underscored_label'] . "');\n";
					break;
				default:
					$return .= "\t\t\$this->frm->addText('" . $field['underscored_label'] . "'" . $default . ");\n";
					break;
			}
		}

		// Add the meta if necessary
		if($module['metaField'] !== false)
		{
			$metaField = $module['fields'][$module['metaField']];

			$return .= "\n\t\t// meta\n\t\t\$this->meta = new BackendMeta(\$this->frm, " . (($isEdit) ? "\$this->record['meta_id']" : 'null') . ", '" . $metaField['underscored_label'] . "', true);";
		}

		// return the string we build up
		return $return;
	}

	/**
	 * Generates the searchindex code
	 * 
	 * @param array $module
	 * @return string
	 */
	public static function generateSearchIndex($module)
	{
		if($module['searchFields'] === false) return '';

		$searchFields = explode(',', $module['searchFields']);
		$searchString = '';

		foreach($searchFields as $key)
		{
			$searchString .= '\'' . $module['fields'][$key]['underscored_label'] . '\' => $item[\'' . $module['fields'][$key]['underscored_label'] . '\'], ';
		}

		$searchString = rtrim($searchString, ', ');

		return self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/search_index.base.php', array('fields' => $searchString));
	}

	/**
	 * Generates (and writes) a file based on a certain template
	 * 
	 * @param string $template				The path to the template
	 * @param array $variables				The variables to assign to the template
	 * @return string						The generated snippet
	 */
	public static function generateSnippet($template, $variables)
	{
		// get the content of the file
		$content = BackendModuleMakerModel::readFile($template);

		// replace the variables
		foreach($variables AS $key => $value)
		{
			$content = str_replace('{$' . $key . '}', $value, $content);
		}

		return $content;
	}

	/**
	 * Generates the SQL for the new module based on the fields
	 * 
	 * @param string $moduleName This should be the underscored version
	 * @param array $module
	 * @return array
	 */
	public static function generateSQL($moduleName, $module)
	{
		// add create table statement
		$return = 'CREATE TABLE IF NOT EXISTS `' . $moduleName . "` (\n";

		// add basic field
		$return .= " `id` int(11) NOT NULL auto_increment,\n";

		if($module['metaField'] !== false) $return .= " `meta_id` int(11) NOT NULL,\n";

		$return .= " `language` varchar(5) NOT NULL,\n";

		// add the fields to the sql
		foreach($module['fields'] as $field)
		{
			$return .= ' ' . $field['sql'] . "\n";
		}

		// add some more basic fields
		$return .= " `created_on` datetime NOT NULL,\n";
		$return .= " `edited_on` datetime NOT NULL,\n";

		// add primary key and row settings
		$return .= " PRIMARY KEY (`id`)\n";
		$return .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

		return $return;
	}

	/**
	 * Generates a part of the backend add/edit templates
	 * 
	 * @param array $module				The array containing all info about the module
	 * @param boolean $isEdit			Should we generate it for the edit action?
	 * @return string
	 */
	public static function generateTemplate($module, $isEdit)
	{
		$return = '';
		$returnSide = '';
		$returnTitle = '';

		// first add the meta field (if necessary)
		if($module['metaField'] !== false)
		{
			$metaField = $module['fields'][$module['metaField']];

			$returnTitle = self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/templates/snippets/meta.base.tpl', $metaField);
		}

		// loop through fields and add items
		foreach($module['fields'] as &$field)
		{
			if($field['meta']) continue;

			$field['required_html'] = ($field['required']) ? '<abbr title="{$lblRequiredField}">*</abbr>' : '';

			if($field['type'] == 'editor' || $field['type'] == 'text' || $field['type'] == 'number' || $field['type'] == 'password')
			{
				$return .= self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/templates/snippets/' . $field['type'] . '.base.tpl', $field);
			}
			else
			{
				$returnSide .= self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/templates/snippets/' . $field['type'] . '.base.tpl', $field);
			}

			unset($field['required_html']);
		}

		// return the strings we build up
		return array($returnTitle, $return, $returnSide);
	}

	/**
	 * Generates a part of the backend add/edit templates
	 * 
	 * @param array $module				The array containing all info about the module
	 * @return array of strings
	 */
	public static function generateTemplateTabs($module)
	{
		$returnTop = '';
		$returnBottom = '';

		if($module['metaField'] !== false)
		{
			$returnTop .= "\n\t\t\t<li><a href=\"#tabSEO\">{\$lblSEO|ucfirst}</a></li>";
			$returnBottom .= self::generateSnippet(BACKEND_MODULE_PATH . '/layout/templates/backend/templates/snippets/seo.base.tpl', array());
		}

		return array($returnTop, $returnBottom);
	}

	/**
	 * Generates a part of the validateForm() function for the backend add/edit actions
	 * 
	 * @param array $module				The array containing all info about the module
	 * @param boolean $isEdit			Should we generate it for the edit action?
	 * @return string
	 */
	public static function generateValidateForm($module, $isEdit)
	{
		$return = '';

		// loop through fields and create and addField statement for each field
		foreach($module['fields'] as $field)
		{
			// check if required fields are filled
			if($field['required'])
			{
				if($field['type'] == 'datetime')
				{
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "_date']->isFilled(BL::err('FieldIsRequired'));\n";
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "_time']->isFilled(BL::err('FieldIsRequired'));\n";
				}
				elseif(!($field['type'] == 'image' || $field['type'] == 'file') && !$isEdit)
				{
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "']->isFilled(BL::err('FieldIsRequired'));\n";
				}
			}

			// check if fields are valid
			switch($field['type'])
			{
				case 'datetime':
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "_date']->isValid(BL::err('DateIsInvalid'));\n";
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "_time']->isValid(BL::err('TimeIsInvalid'));\n";
					break;
				case 'number':
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "']->isInteger(BL::err('InvalidInteger'));\n";
					break;
				case 'file':
					$return .= "\n\t\t\t// you probably should add some validation to the file type\n\n";
					break;
				case 'image':
					/**
					 * @TODO add validation to image size
					 */
					$return .= "\t\t\tif(\$fields['" . $field['underscored_label'] . "'" . ']->isFilled())' . "\n\t\t\t{\n\t\t\t\t";
					$return .= "\$fields['" . $field['underscored_label'] . "'" . "]->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));\n\t\t\t\t";
					$return .= "\$fields['" . $field['underscored_label'] . "'" . "]->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));\n\t\t\t}\n";
					if($field['required']) $return .= "\t\t\telse \$fields['" . $field['underscored_label'] . "'" . "]->addError(BL::err('FieldIsRequired'));\n";
					$return .= "\n";
					break;
			}
		}

		// add validate meta if necessary
		if($module['metaField'] !== false)
		{
			$return .= "\t\t\t// validate meta\n\t\t\t\$this->meta->validate();\n";
		}

		// return the string we build up
		return $return;
	}
}