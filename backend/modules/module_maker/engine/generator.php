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
					$field['create_folders'] .= self::generateSnippet('/backend/actions/snippets/create_folder.base.php', array('option' => $option));
				}

				// add the function used to create the filename
				if($module['metaField']) $field['file_name_function'] = '$this->meta->getUrl()';
				else $field['file_name_function'] = 'time()';
			}

			// when there is a snippet provided for the datatype, use it. This falls back to a default snippet
			if(file_exists(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/build_' . $field['type'] . '.base.php'))
			{
				$return .= self::generateSnippet('backend/actions/snippets/build_' . $field['type'] . '.base.php', $field);
			}
			else $return .= self::generateSnippet('backend/actions/snippets/build_simple.base.php', $field);
		}

		// add sequence, categories or meta if necessary
		if($module['useSequence']) $return .= self::generateSnippet('backend/actions/snippets/build_sequence.base.php', $module);
		if($module['useCategories']) $return .= self::generateSnippet('backend/actions/snippets/build_category.base.php');
		if($module['metaField']) $return .= self::generateSnippet('backend/actions/snippets/build_meta.base.php');

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
		$content = self::generateSnippet($template, $variables);

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
		$extras = $navigation = '';
		if($module['useSequence']) $extras .= "\n\t\t\$this->setActionRights(1, '" . $module['underscored_name'] . "', 'sequence');";
		if($module['useCategories'])
		{
			$extras .= self::generateSnippet('backend/installer/snippets/categories.base.php', $module);
			$navigation = self::generateSnippet('backend/installer/snippets/navigation_categories.base.php', $module);
		}
		else
		{
			$navigation = self::generateSnippet('backend/installer/snippets/navigation.base.php', $module);
		}
		if($module['searchFields'])
		{
			$extras .= self::generateSnippet('backend/installer/snippets/search.base.php', $module);
		}

		return array($extras, $navigation);
	}

	/**
	 * Generates a part of the loadForm() function for the backend add/edit actions
	 * @TODO: refactor me, I'm nasty
	 * 
	 * @param array $module				The array containing all info about the module
	 * @param boolean $isEdit			Should we generate it for the edit action?
	 * @return string
	 */
	public static function generateLoadForm($module, $isEdit)
	{
		$return = '';

		// Add the meta field as a title field
		if($module['metaField'])
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
			// don't add the metafield, it's already added
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
				$default = ", \$this->record['" . $field['underscored_label'] . "']";
			}
			elseif($field['default'] !== '')
			{
				if($field['type'] == 'number') $default = ', ' . $default;
				elseif($field['type'] == 'dropdown') $default = ", BL::lbl('" . BackendModuleMakerHelper::buildCamelCasedName($default) . "')";
				else $default = ", '" . $default . "'";
			}
			$field['default'] = $default;
			$field['default_time'] = ($default && $field['type'] == 'datetime') ? ", date('H:i', \$this->record['" . $field['underscored_label'] . "'])" : '';

			// when there is a snippet provided for the datatype, use it. This falls back to a default snippet
			if(file_exists(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/add_' . $field['type'] . '.base.php'))
			{
				$return .= self::generateSnippet('backend/actions/snippets/add_' . $field['type'] . '.base.php', $field);
			}
			else $return .= self::generateSnippet('backend/actions/snippets/add_simple.base.php', $field);
		}

		// add the tags field if necessary
		if($module['useTags'])
		{
			if($isEdit) $return .= self::generateSnippet('backend/actions/snippets/load_tags_edit.base.php', $module);
			else $return .= self::generateSnippet('backend/actions/snippets/load_tags_add.base.php', $module);
		}

		// add the categories
		if($module['useCategories'])
		{
			if($isEdit) $return .= self::generateSnippet('backend/actions/snippets/load_categories_edit.base.php', $module);
			else $return .= self::generateSnippet('backend/actions/snippets/load_categories_add.base.php', $module);
		}

		// Add the meta if necessary
		if($module['metaField'])
		{
			$metaField = $module['fields'][$module['metaField']];
			$metaField['module'] = $module['camel_case_name'];

			if($isEdit) $return .= self::generateSnippet('backend/actions/snippets/load_meta_edit.base.php', $metaField);
			else $return .= self::generateSnippet('backend/actions/snippets/load_meta_add.base.php', $metaField);
		}

		// return the string we build up
		return $return;
	}

	/**
	 * Generates the save tags code
	 * 
	 * @param array $module
	 * @return string
	 */
	public static function generateSaveTags($module)
	{
		if(!$module['useTags']) return '';

		return self::generateSnippet('backend/actions/snippets/save_tags.base.php');
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

		return self::generateSnippet('backend/actions/snippets/search_index.base.php', array('fields' => $searchString));
	}

	/**
	 * Generates (and writes) a file based on a certain template
	 * 
	 * @param string $template				The path to the template
	 * @param array $variables				The variables to assign to the template
	 * @return string						The generated snippet
	 */
	public static function generateSnippet($template, $variables = null)
	{
		// get the content of the file
		$content = BackendModuleMakerModel::readFile(BACKEND_MODULE_PATH . '/layout/templates/' . $template);

		// replace the variables
		if($variables && is_array($variables) && !empty($variables))
		{
			foreach($variables AS $key => $value)
			{
				if(!is_array($value)) $content = str_replace('{$' . $key . '}', $value, $content);
			}
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

		if($module['metaField']) $return .= " `meta_id` int(11) NOT NULL,\n";
		if($module['useCategories']) $return .= " `category_id` int(11) NOT NULL,\n";

		$return .= " `language` varchar(5) NOT NULL,\n";

		// add the fields to the sql
		foreach($module['fields'] as $field)
		{
			$return .= ' ' . $field['sql'] . "\n";
		}

		// add some more basic fields
		$return .= " `created_on` datetime NOT NULL,\n";
		$return .= " `edited_on` datetime NOT NULL,\n";

		if($module['useSequence']) $return .= " `sequence` int(11) NOT NULL,\n";

		// add primary key and row settings
		$return .= " PRIMARY KEY (`id`)\n";
		$return .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

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
		$return = $returnSide = $returnTitle = '';

		// first add the meta field (if necessary)
		if($module['metaField'])
		{
			$metaField = $module['fields'][$module['metaField']];

			$returnTitle = self::generateSnippet('backend/templates/snippets/meta.base.tpl', $metaField);
		}

		// loop through fields and add items
		foreach($module['fields'] as &$field)
		{
			if($field['meta']) continue;

			$field['required_html'] = ($field['required']) ? '<abbr title="{$lblRequiredField}">*</abbr>' : '';

			if($field['type'] == 'editor' || $field['type'] == 'text' || $field['type'] == 'number' || $field['type'] == 'password')
			{
				$return .= self::generateSnippet('backend/templates/snippets/' . $field['type'] . '.base.tpl', $field);
			}
			else $returnSide .= self::generateSnippet('backend/templates/snippets/' . $field['type'] . '.base.tpl', $field);

			unset($field['required_html']);
		}

		// add tags and categories
		if($module['useTags']) $returnSide .= self::generateSnippet('backend/templates/snippets/tags.base.tpl');
		if($module['useCategories']) $returnSide .= self::generateSnippet('backend/templates/snippets/category.base.tpl');

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
		$returnTop = $returnBottom = '';

		if($module['metaField'])
		{
			$returnTop .= "\n\t\t\t<li><a href=\"#tabSEO\">{\$lblSEO|ucfirst}</a></li>";
			$returnBottom .= self::generateSnippet('backend/templates/snippets/seo.base.tpl');
		}

		return array($returnTop, $returnBottom);
	}

	/**
	 * Generates a part of the validateForm() function for the backend add/edit actions
	 * @TODO: add validation to image size
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
					$return .= self::generateSnippet('backend/actions/snippets/validate_required.base.php', array('underscored_label' => $field['underscored_label'] . '_date'));
					$return .= self::generateSnippet('backend/actions/snippets/validate_required.base.php', array('underscored_label' => $field['underscored_label'] . '_time'));
				}
				elseif(!($field['type'] == 'image' || $field['type'] == 'file') && !$isEdit)
				{
					$return .= self::generateSnippet('backend/actions/snippets/validate_required.base.php', $field);
				}
			}

			// when there is a snippet provided to validate this form type, use it
			if(file_exists(BACKEND_MODULE_PATH . '/layout/templates/backend/actions/snippets/validate_' . $field['type'] . '.base.php'))
			{
				$return .= self::generateSnippet('backend/actions/snippets/validate_' . $field['type'] . '.base.php', $field);
			}

			if($field['type'] == 'image' && $field['required'] && !$isEdit) $return .= "\t\t\telse \$fields['" . $field['underscored_label'] . "'" . "]->addError(BL::err('FieldIsRequired'));\n";
		}

		// add validate meta if necessary
		if($module['metaField'])
		{
			$return .= self::generateSnippet('backend/actions/snippets/validate_meta.base.php');
		}

		// return the string we build up
		return $return;
	}
}