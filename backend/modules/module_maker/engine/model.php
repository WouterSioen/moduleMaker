<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the modulemaker module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
class BackendModuleMakerModel
{
	/**
	 * Creates a valid class name
	 *
	 * @return	string
	 * @param	string $name		The given name.
	 */
	public static function buildCamelCasedName($name)
	{
		// replace spaces by underscores
		$name = str_replace(' ', '_', $name);

		// lowercase
		$name = strtolower($name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z0-9_\s]/", "", $name);

		// split the name on _
		$parts = explode('_', $name);

		// the new name
		$newName = '';

		// loop trough the parts to ucfirst it
		foreach($parts as $part) $newName.= ucfirst($part);

		// return
		return $newName;
	}

	/**
	 * Creates a lower Camel Cased Name
	 *
	 * @return	string
	 * @param	string $name		The given name.
	 */
	public static function buildLowerCamelCasedName($name)
	{
		// replace spaces by underscores
		$name = str_replace(' ', '_', $name);

		// lowercase
		$name = strtolower($name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z0-9_\s]/", "", $name);

		// split the name on _
		$parts = explode('_', $name);

		// the new name
		$newName = '';

		// loop trough the parts to ucfirst it
		foreach($parts as $key => $part)
		{
			if($key) $newName.= ucfirst($part);
			else $newName .= $part;
		}

		// return
		return $newName;
	}

	/**
	 * Creates an underscored version off the classname
	 *
	 * @return	string
	 * @param	string $name		The given name.
	 */
	public static function buildUnderscoredName($name)
	{
		// lowercase
		$name = strtolower($name);

		// replace spaces by underscores
		$name = str_replace(' ', '_', $name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z0-9_\s]/", "", $name);

		// return
		return $name;
	}

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
			if($field['type'] == 'checkbox')
			{
				$return .= "\t\t\t\t\$item['" . $field['underscored_label'] . "'] = \$fields['" . $field['underscored_label'] . "']->getChecked() ? 'Y' : 'N';\n";
			}
			elseif($field['type'] == 'image')
			{
				$return .= "\n\t\t\t\t// the image path\n";
				$return .= "\t\t\t\t\$imagePath = FRONTEND_FILES_PATH . '/' . \$this->getModule() . '/images';\n\n";
				$return .= "\t\t\t\t// create folders if needed\n";

				// loop through the options, they contain the image sizes
				$options = explode(',', $field['options']);
				foreach($options as $option)
				{
					$return .= "\t\t\t\tif(!SpoonDirectory::exists(\$imagePath . '/" . $option . "')) SpoonDirectory::create(\$imagePath . '/" . $option . "');\n";
				}
				$return .= "\t\t\t\tif(!SpoonDirectory::exists(\$imagePath . '/source')) SpoonDirectory::create(\$imagePath . '/source');\n\n";

				$return .= "\t\t\t\t// image provided?\n";
				$return .= "\t\t\t\tif(\$fields['" . $field['underscored_label'] . "']->isFilled())\n\t\t\t\t{\n";
				$return .= "\t\t\t\t\t// build the image name\n";

				/**
				 * @TODO when meta is added, use the meta in the image name
				 */
				$return .= "\t\t\t\t\t\$item['" . $field['underscored_label'] . "'] = time() . '.' . \$fields['" . $field['underscored_label'] . "']->getExtension();\n\n";
				$return .= "\t\t\t\t\t// upload the image & generate thumbnails\n";
				$return .= "\t\t\t\t\t\$fields['" . $field['underscored_label'] . "']->generateThumbnails(\$imagePath, \$item['" . $field['underscored_label'] . "']);\n";
				$return .= "\t\t\t\t}\n\n";
			}
			else
			{
				$return .= "\t\t\t\t\$item['" . $field['underscored_label'] . "'] = \$fields['" . $field['underscored_label'] . "']->getValue();\n";
			}
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
		$content = self::readFile($template);

		// replace the variables
		foreach($variables AS $key => $value)
		{
			$content = str_replace('{$' . $key . '}', $value, $content);
		}

		// write the file
		self::makeFile($path, $content);
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

		// loop through fields and create and addField statement for each field
		foreach($module['fields'] as $field)
		{
			// for fields with multiple options: add them
			if($field['type'] == 'multicheckbox' || $field['type'] == 'radiobutton')
			{
				$return .= "\n\t\t// build array with options for the " . $field['label'] . ' ' . $field['type'] . "\n";

				// split the options on the comma and add them to an array
				$options = explode(',', $field['options']);
				foreach($options as $option)
				{
					$return .= "\t\t\$" . $field['type'] . $field['camel_cased_label'] . "Values[] = array('label' => BL::lbl('" . self::buildCamelCasedName($option) . "'), 'value' => '" . $option . "');\n";
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
					$return .= "BL::lbl('" . self::buildCamelCasedName($option) . "'), ";
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
				elseif($field['type'] == 'dropdown') $default = ", BL::lbl('" . self::buildCamelCasedName($field['default']) . "')";
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
					$return .= "\t\t\$this->frm->addPassword('" . $field['underscored_label'] . "'" . $default . ")->setAttributes(array('autocomplete' => 'off');\n";
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

		// return the string we build up
		return $return;
	}

	/**
	 * Generates the SQL for the new module based on the fields
	 * 
	 * @param string $moduleName This should be the underscored version
	 * @param array $fields
	 * @return array
	 */
	public static function generateSQL($moduleName, $fields)
	{
		// add create table statement
		$return = 'CREATE TABLE IF NOT EXISTS `' . $moduleName . "` (\n";

		// add basic field
		$return .= " `id` int(11) NOT NULL auto_increment,\n";
		$return .= " `language` varchar(5) NOT NULL,\n";

		// add the fields to the sql
		foreach($fields as $field)
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

		// loop through fields and add items
		foreach($module['fields'] as $field)
		{
			if($field['type'] == 'editor' || $field['type'] == 'text' || $field['type'] == 'number' || $field['type'] == 'password')
			{
				$return .= "\t\t\t\t\t\t<div class=\"box\">\n\t\t\t\t\t\t\t<div class=\"heading\">\n";
				$return .= "\t\t\t\t\t\t\t\t<h3>\n\t\t\t\t\t\t\t\t\t<label for=\"" . $field['lower_ccased_label'] . '">';
				$return .= '{$lbl' . $field['camel_cased_label'] . '|ucfirst}';
				if($field['required']) $return .= '<abbr title="{$lblRequiredField}">*</abbr>';
				$return .= "</label>\n\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"options" . (($field['type'] == 'editor') ? 'RTE' : '') . "\">\n";
				$return .= "\t\t\t\t\t\t\t\t{\$txt" . $field['camel_cased_label'] . "} {\$txt" . $field['camel_cased_label'] . "Error}\n";
				$return .= "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\n";
			}
			elseif($field['type'] == 'image' || $field['type'] == 'file')
			{
				$returnSide .= "\t\t\t\t\t\t\t<div class=\"box\">\n\t\t\t\t\t\t\t\t<div class=\"heading\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<h3>\n\t\t\t\t\t\t\t\t\t\t<label for=\"" . $field['lower_ccased_label'] . '">';
				$returnSide .= '{$lbl' . $field['camel_cased_label'] . '|ucfirst}';
				if($field['required']) $returnSide .= '<abbr title="{$lblRequiredField}">*</abbr>';
				$returnSide .= "</label>\n\t\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"options\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t{\$file" . $field['camel_cased_label'] . "} {\$file" . $field['camel_cased_label'] . "Error}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\n";
			}
			elseif($field['type'] == 'radiobutton' || $field['type'] == 'multicheckbox')
			{
				$returnSide .= "\t\t\t\t\t\t\t<div class=\"box\">\n\t\t\t\t\t\t\t\t<div class=\"heading\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<h3>\n\t\t\t\t\t\t\t\t\t\t{\$lbl" . $field['camel_cased_label'] . "|ucfirst}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"options\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<ul class=\"inputList\">\n\t\t\t\t\t\t\t\t\t\t{iteration:" . $field['underscored_label'] . "}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t\t<li>\n\t\t\t\t\t\t\t\t\t\t\t{\$" . $field['underscored_label'] . '.' . (($field['type'] == 'radiobutton') ? 'rbt' : 'chk') . $field['camel_cased_label'] . "}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t\t\t<label for=\"{\$" . $field['lower_ccased_label'] . '.id}">{$' . $field['underscored_label'] . ".label}</label>\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t\t</li>\n\t\t\t\t\t\t\t\t\t\t{/iteration:" . $field['underscored_label'] . "}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\n";
			}
			elseif($field['type'] == 'dropdown')
			{
				$returnSide .= "\t\t\t\t\t\t\t<div class=\"box\">\n\t\t\t\t\t\t\t\t<div class=\"heading\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<h3>\n\t\t\t\t\t\t\t\t\t\t<label for=\"" . $field['lower_ccased_label'] . '">';
				$returnSide .= '{$lbl' . $field['camel_cased_label'] . '|ucfirst}';
				if($field['required']) $returnSide .= '<abbr title="{$lblRequiredField}">*</abbr>';
				$returnSide .= "</label>\n\t\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"options\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t{\$ddm" . $field['camel_cased_label'] . "} {\$ddm" . $field['camel_cased_label'] . "Error}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\n";
			}
			elseif($field['type'] == 'datetime')
			{
				$returnSide .= "\t\t\t\t\t\t\t<div class=\"box\">\n\t\t\t\t\t\t\t\t<div class=\"heading\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<h3>\n\t\t\t\t\t\t\t\t\t\t<label for=\"" . $field['lower_ccased_label'] . 'Date">';
				$returnSide .= '{$lbl' . $field['camel_cased_label'] . '|ucfirst}';
				if($field['required']) $returnSide .= '<abbr title="{$lblRequiredField}">*</abbr>';
				$returnSide .= "</label>\n\t\t\t\t\t\t\t\t\t</h3>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"options\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<div class=\"oneLiner\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t\t<p>{\$txt" . $field['camel_cased_label'] . "Date} {\$txt" . $field['camel_cased_label'] . "DateError}</p>\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t\t<p><label for=\"" . $field['lower_ccased_label'] . "Time\">{\$lblAt}</label></p>\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t\t<p>{\$txt" . $field['camel_cased_label'] . "Time} {\$txt" . $field['camel_cased_label'] . "TimeError}</p>\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\n";
			}
			elseif($field['type'] == 'checkbox')
			{
				$returnSide .= "\t\t\t\t\t\t\t<div class=\"box\">\n\t\t\t\t\t\t\t\t<div class=\"heading\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t<h3>\n\t\t\t\t\t\t\t\t\t\t{\$lbl" . $field['camel_cased_label'] . "|ucfirst}</h3>\n";
				$returnSide .= "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"options\">\n";
				$returnSide .= "\t\t\t\t\t\t\t\t\t{\$chk" . $field['camel_cased_label'] . "} <label for=\"" . $field['lower_ccased_label'] . '">';
				$returnSide .= '{$lbl' . $field['camel_cased_label'] . '|ucfirst}';
				if($field['required']) $returnSide .= '<abbr title="{$lblRequiredField}">*</abbr>';
				$returnSide .= "</label> {\$chk" . $field['camel_cased_label'] . "Error}\n";
				$returnSide .= "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\n";
			}
		}

		// return the strings we build up
		return array($return, $returnSide);
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
			if($field['required'] == true)
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
			switch ($field['type'])
			{
				case 'datetime':
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "_date']->isValid(BL::err('DateIsInvalid'));\n";
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "_time']->isValid(BL::err('TimeIsInvalid'));\n";
					break;
				case 'number':
					$return .= "\t\t\t\$fields['" . $field['underscored_label'] . "']->isInteger(BL::err('InvalidInteger'));\n";
					break;
				case 'file':
					$return .= "\n\t\t\t// you probably should add some validation to the file type\n";
					break;
				case 'image':
					/**
					 * @TODO add validation to image size
					 */
					$return .= "\t\t\tif(\$fields['" . $field['underscored_label'] . "'" . ']->isFilled())' . "\n\t\t\t{\n\t\t\t\t";
					$return .= "\$fields['" . $field['underscored_label'] . "'" . "]->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));\n\t\t\t\t";
					$return .= "\$fields['" . $field['underscored_label'] . "'" . "]->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));\n\t\t\t}\n\n";
					$return .= "";
					break;
			}
		}

		// return the string we build up
		return $return;
	}

	/**
	 * Creates the directories from a given array
	 *
	 * @param	array $dirs		The directories to create
	 */
	public static function makeDirs(array $dirs)
	{
		// the main dir
		$mainDir = '';

		// loop the directories
		foreach($dirs as $type => $dir)
		{
			// create a new dir if this is the main dir
			if($type == 'main')
			{
				mkdir($dir);
				$mainDir = $dir . '/';
				continue;
			}

			// loob the dir to check for subdirs if this isn't the main
			foreach($dir as $name => $subdir)
			{
				// no subdirs
				if(!is_array($subdir)) mkdir($mainDir . $subdir);
				// more subdirs
				else
				{
					// create new array to pass
					$tmpArray = array(
									'main' => $mainDir . $name,
									'sub' => $subdir
					);

					// make the dir
					self::makeDirs($tmpArray);
				}
			}
		}
	}

	/**
	 * Creates a file in a specific directory
	 *
	 * @param	string $file				The file name.
	 * @param	string[optional] $input		The input for the file.
	 */
	public static function makeFile($file, $input = null)
	{
		// create the file
		$oFile = fopen($file, 'w');

		// input?
		if($input !== null) fwrite($oFile, $input);

		// close the file
		fclose($oFile);
	}

	/**
	 * Reads the content of a file
	 *
	 * @return	string
	 * @param	string $file		The file path.
	 */
	public static function readFile($file)
	{
		// file exists?
		if(!file_exists($file)) throw new Exception('The given file(' . $file .') does not exist.');

		// open the file
		$oFile = fopen($file, 'r');

		// read the file
		$rFile = fread($oFile, filesize($file));

		// close the file
		fclose($oFile);

		// return
		return $rFile;
	}
}