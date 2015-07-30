<?php

namespace Backend\Modules\ModuleMaker\Engine;

/**
 * In this file we store all functions that generate blocks of code for the modulemaker module
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 * @author Arend Pijls <arend.pijls@wijs.be>
 */
class Generator
{
    /**
     * Generates a part of the add/edit action that builds the item
     *
     * @param  array   $module The array containing all info about the module
     * @param  boolean $isEdit Should we generate it for the edit action?
     * @return string
     */
    public static function generateBuildItem($module, $isEdit)
    {
        $return = '';

        // loop through fields
        foreach ($module['fields'] as $field) {
            $field['type'] = \SpoonFilter::toCamelCase($field['type']);

            // for images, create the code to create folders for each image size
            if ($field['type'] == 'Image' || $field['type'] == 'ImageCaption') {
                // loop through the options, they contain the image sizes
                $options = explode(',', $field['options']);
                $field['create_folders'] = '';
                foreach ($options as $option) {
                    $field['create_folders'] .= self::generateSnippet('/Backend/Actions/Snippets/CreateFolder.base.php', array('option' => $option));
                }

                // add the function used to create the filename
                $field['file_name_function'] = '$this->meta->getUrl()';
            }

            // when there is a snippet provided for the datatype, use it. This falls back to a default snippet
            if (file_exists(BACKEND_MODULES_PATH . '/ModuleMaker/Layout/Templates/Backend/Actions/Snippets/Build' . $field['type'] . '.base.php')) {
                $return .= self::generateSnippet('Backend/Actions/Snippets/Build' . $field['type'] . '.base.php', $field);
            } else $return .= self::generateSnippet('Backend/Actions/Snippets/BuildSimple.base.php', $field);
        }

        // add sequence, categories or meta if necessary
        if ($module['useSequence'] && !$isEdit) $return .= self::generateSnippet('Backend/Actions/Snippets/BuildSequence.base.php', $module);
        if ($module['useCategories']) $return .= self::generateSnippet('Backend/Actions/Snippets/BuildCategory.base.php');
        $return .= self::generateSnippet('Backend/Actions/Snippets/BuildMeta.base.php');

        // return the string we build up
        return $return;
    }

    /**
     * Generates (and writes) a file based on a certain template
     *
     * @param string $template  The path to the template
     * @param array  $variables The variables to assign to the template
     * @param string $path      The path to the file
     */
    public static function generateFile($template, $variables, $path)
    {
        $content = self::generateSnippet($template, $variables);

        // write the file
        Model::makeFile($path, $content);
    }

    /**
     * Generates extra installer code
     *
     * @param  array  $module
     * @return string
     */
    public static function generateInstall($module)
    {
        $extras = $navigation = '';
        if ($module['useSequence']) $extras .= "\n        \$this->setActionRights(1, '" . $module['underscored_name'] . "', 'Sequence');";
        if ($module['useCategories']) {
            $extras .= self::generateSnippet('Backend/Installer/Snippets/Categories.base.php', $module);
            $navigation = self::generateSnippet('Backend/Installer/Snippets/NavigationCategories.base.php', $module);
        } else {
            $navigation = self::generateSnippet('Backend/Installer/Snippets/Navigation.base.php', $module);
        }

        if ($module['searchFields']) {
            $extras .= self::generateSnippet('Backend/Installer/Snippets/Search.base.php', $module);
        }

        if ($module['multipleImages']) {
            $extras .= self::generateSnippet('Backend/Installer/Snippets/MultipleImages.base.php', $module);
        }

        return array($extras, $navigation);
    }

    /**
     * Generates a part of the loadData() function for the backend add/edit actions
     *
     * @param  array   $module The array containing all info about the module
     * @param  boolean $isEdit Should we generate it for the edit action?
     * @return string
     */
    public static function generateLoadData($module, $isEdit)
    {
        if ($isEdit && $module['multipleImages']) {
            return self::generateSnippet('Backend/Actions/Snippets/MultipleImagesGet.base.php', $module);
        }

        return '';
    }

    /**
     * Generates a part of the loadForm() function for the backend add/edit actions
     * @TODO: refactor me, I'm nasty
     *
     * @param  array   $module The array containing all info about the module
     * @param  boolean $isEdit Should we generate it for the edit action?
     * @return string
     */
    public static function generateLoadForm($module, $isEdit)
    {
        $return = '';

        // Add the meta field as a title field
        $metaField = $module['fields'][(int) $module['metaField']];

        // create the default value
        $default = '';
        if ($isEdit) {
            $default = ", \$this->record['" . $metaField['underscored_label'] . "']";
        } elseif ($metaField['default'] !== '') {
            if ($metaField['type'] == 'number') $default = ', ' . $metaField['default'];
            elseif ($metaField['type'] == 'dropdown') $default = ", Language::lbl('" . Helper::buildCamelCasedName($metaField['default']) . "')";
            else $default = ", '" . $metaField['default'] . "'";
        }

        $return .= "        \$this->frm->addText('" . $metaField['underscored_label'] . "'" . $default . (($default) ? '' : ', null') . ", null, 'inputText title', 'inputTextError title');\n";

        // loop through fields and create and addField statement for each field
        foreach ($module['fields'] as $field) {
            $field['type'] = \SpoonFilter::toCamelCase($field['type']);

            // don't add the metafield, it's already added
            if ($field['meta']) continue;

            // for fields with multiple options: add them
            if ($field['type'] == 'Multicheckbox' || $field['type'] == 'Radiobutton') {
                $return .= "\n        // build array with options for the " . $field['label'] . ' ' . $field['type'] . "\n";

                // split the options on the comma and add them to an array
                $options = explode(',', $field['options']);
                foreach ($options as $option) {
                    $return .= "        \$" . $field['type'] . $field['camel_cased_label'] . "Values[] = array('label' => Language::lbl('" . Helper::buildCamelCasedName($option) . "'), 'value' => '" . $option . "');\n";
                }
            } elseif ($field['type'] == 'Dropdown') {
                $return .= "\n        // build array with options for the " . $field['label'] . ' ' . $field['type'] . "\n";

                // split the options on the comma and add them to an array
                $options = explode(',', $field['options']);
                $return .= "        \$" . $field['type'] . $field['camel_cased_label'] . 'Values = array(';
                foreach ($options as $option) {
                    $return .= "Language::lbl('" . Helper::buildCamelCasedName($option) . "'), ";
                }
                $return = rtrim($return, ', ');
                $return .= ");\n";
            }

            // create the default value
            $default = '';
            if ($isEdit) {
                if ($field['type'] == 'ImageCaption') $default = ", \$this->record['" . $field['underscored_label'] . "_caption']";
                elseif ($field['type'] == 'Checkbox') $default = ", \$this->record['" . $field['underscored_label'] . "'] == 'Y'";
                else $default = ", \$this->record['" . $field['underscored_label'] . "']";
            } elseif ($field['type'] == 'Author') $default = ', BackendAuthentication::getUser()->getUserId()';
            elseif ($field['default'] !== '') {
                if ($field['type'] == 'Number') $default = ', ' . $field['default'];
                elseif ($field['type'] == 'Dropdown') $default = ", Language::lbl('" . Helper::buildCamelCasedName($field['default']) . "')";
                elseif ($field['type'] == 'Checkbox') $default = ($field['default'] == 'N') ? ', false': ', true';
                else $default = ", '" . $field['default'] . "'";
            }
            $field['default'] = $default;
            $field['default_time'] = ($default && $field['type'] == 'Datetime') ? ", date('H:i', \$this->record['" . $field['underscored_label'] . "'])" : '';

            // when there is a snippet provided for the datatype, use it. This falls back to a default snippet
            if (file_exists(BACKEND_MODULES_PATH . '/ModuleMaker/Layout/Templates/Backend/Actions/Snippets/Add' . $field['type'] . '.base.php')) {
                $return .= self::generateSnippet('Backend/Actions/Snippets/Add' . $field['type'] . '.base.php', $field);
            } else $return .= self::generateSnippet('Backend/Actions/Snippets/AddSimple.base.php', $field);
        }

        // add the tags field if necessary
        if ($module['useTags']) {
            if ($isEdit) $return .= self::generateSnippet('Backend/Actions/Snippets/LoadTagsEdit.base.php', $module);
            else $return .= self::generateSnippet('Backend/Actions/Snippets/LoadTagsAdd.base.php', $module);
        }

        // add the categories
        if ($module['useCategories']) {
            if ($isEdit) $return .= self::generateSnippet('Backend/Actions/Snippets/LoadCategoriesEdit.base.php', $module);
            else $return .= self::generateSnippet('Backend/Actions/Snippets/LoadCategoriesAdd.base.php', $module);
        }

        // Add the meta
        $metaField = $module['fields'][(int) $module['metaField']];
        $metaField['module'] = $module['camel_case_name'];
        $metaField['camel_case_name'] = $module['camel_case_name'];

        if ($isEdit) $return .= self::generateSnippet('Backend/Actions/Snippets/LoadMetaEdit.base.php', $metaField);
        else $return .= self::generateSnippet('Backend/Actions/Snippets/LoadMetaAdd.base.php', $metaField);

        // return the string we build up
        return $return;
    }

    /**
     * Generates the snippets for the multifiles
     *
     * @param  array   $module
     * @param  boolean $isEdit
     * @return array
     */
    public static function generateMultiFiles($module, $isEdit)
    {
        if (!$module['multipleImages']) return array('', '', '');

        $js = self::generateSnippet('Backend/Actions/Snippets/MultipleImagesJs.base.php', $module);
        if ($isEdit) $load = self::generateSnippet('Backend/Actions/Snippets/MultipleImagesLoadEdit.base.php', $module);
        else $load = self::generateSnippet('Backend/Actions/Snippets/MultipleImagesLoad.base.php', $module);
        if ($isEdit) $save = self::generateSnippet('Backend/Actions/Snippets/MultipleImagesSaveEdit.base.php', $module);
        else $save = self::generateSnippet('Backend/Actions/Snippets/MultipleImagesSave.base.php', $module);
        return array($js, $load, $save);
    }

    /**
     * Generates the save tags code
     *
     * @param  array  $module
     * @return string
     */
    public static function generateSaveTags($module)
    {
        if (!$module['useTags']) return '';

        return self::generateSnippet('Backend/Actions/Snippets/SaveTags.base.php');
    }

    /**
     * Generates the searchindex code
     *
     * @param  array  $module
     * @return string
     */
    public static function generateSearchIndex($module)
    {
        if ($module['searchFields'] === false) return '';

        $searchFields = explode(',', $module['searchFields']);
        $searchString = '';

        foreach ($searchFields as $key) {
            $searchString .= '\'' . $module['fields'][$key]['underscored_label'] . '\' => $item[\'' . $module['fields'][$key]['underscored_label'] . '\'], ';
        }

        $searchString = rtrim($searchString, ', ');

        return self::generateSnippet('Backend/Actions/Snippets/SearchIndex.base.php', array('fields' => $searchString));
    }

    /**
     * Generates (and writes) a file based on a certain template
     *
     * @param  string $template  The path to the template
     * @param  array  $variables The variables to assign to the template
     * @return string The generated snippet
     */
    public static function generateSnippet($template, $variables = null)
    {
        // get the content of the file
        $content = Model::readFile(BACKEND_MODULES_PATH . '/ModuleMaker/Layout/Templates/' . $template);

        // replace the variables
        if ($variables && is_array($variables) && !empty($variables)) {
            foreach ($variables AS $key => $value) {
                if (!is_array($value)) $content = str_replace('{$' . $key . '}', $value, $content);
            }
        }

        return $content;
    }

    /**
     * Generates the SQL for the new module based on the fields
     *
     * @param  string $moduleName This should be the underscored version
     * @param  array  $module
     * @return array
     */
    public static function generateSQL($moduleName, $module)
    {
        // add create table statement
        $return = 'CREATE TABLE IF NOT EXISTS `' . $moduleName . "` (\n";

        // add basic field
        $return .= " `id` int(11) NOT NULL auto_increment,\n";
        $return .= " `meta_id` int(11) NOT NULL,\n";

        if ($module['useCategories']) $return .= " `category_id` int(11) NOT NULL,\n";

        $return .= " `language` varchar(5) NOT NULL,\n";

        // add the fields to the sql
        foreach ($module['fields'] as $field) {
            $return .= ' ' . $field['sql'] . "\n";
        }

        // add some more basic fields
        $return .= " `created_on` datetime NOT NULL,\n";
        $return .= " `edited_on` datetime NOT NULL,\n";

        if ($module['useSequence']) $return .= " `sequence` int(11) NOT NULL,\n";

        // add primary key and row settings
        $return .= " PRIMARY KEY (`id`)\n";
        $return .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        return $return;
    }

    /**
     * Generates a part of the backend add/edit templates
     *
     * @param  array   $module The array containing all info about the module
     * @param  boolean $isEdit Should we generate it for the edit action?
     * @return string
     */
    public static function generateTemplate($module, $isEdit)
    {
        $return = $returnSide = $returnTitle = '';

        // first add the meta field
        $metaField = $module['fields'][(int) $module['metaField']];
        if ($isEdit) $returnTitle = self::generateSnippet('Backend/Layout/Templates/Snippets/MetaEdit.base.tpl', $metaField);
        else $returnTitle = self::generateSnippet('Backend/Layout/Templates/Snippets/Meta.base.tpl', $metaField);

        // loop through fields and add items
        foreach ($module['fields'] as $field) {
            $field['type'] = \SpoonFilter::toCamelCase($field['type']);

            if ($field['meta']) continue;

            $field['required_html'] = ($field['required']) ? '<abbr title="{$lblRequiredField}">*</abbr>' : '';

            // if it's an image of a file and it's an edit action, we want to show a preview
            if ($isEdit && ($field['type'] == 'Image' || $field['type'] == 'File' || $field['type'] == 'ImageCaption')) {
                $field['module'] = $module['underscored_name'];
                if ($field['type'] == 'Image') {
                    $imageSizes = explode(',', $field['options']);
                    $field['image_size'] = $imageSizes[0];
                }
                $return .= self::generateSnippet('Backend/Layout/Templates/Snippets/' . $field['type'] . 'Edit.base.tpl', $field);
            } elseif ($field['type'] == 'Editor' || $field['type'] == 'Text' || $field['type'] == 'Number' || $field['type'] == 'Password') {
                $return .= self::generateSnippet('Backend/Layout/Templates/Snippets/' . $field['type'] . '.base.tpl', $field);
            } else $returnSide .= self::generateSnippet('Backend/Layout/Templates/Snippets/' . $field['type'] . '.base.tpl', $field);

            unset($field['required_html']);
        }

        // add tags and categories
        if ($module['useTags']) $returnSide .= self::generateSnippet('Backend/Layout/Templates/Snippets/Tags.base.tpl');
        if ($module['useCategories']) $returnSide .= self::generateSnippet('Backend/Layout/Templates/Snippets/Category.base.tpl');

        // return the strings we build up
        return array($returnTitle, $return, $returnSide);
    }

    /**
     * Generates a part of the backend add/edit templates
     *
     * @param  array $module The array containing all info about the module
     * @return array of strings
     */
    public static function generateTemplateTabs($module)
    {
        $returnTop = $returnBottom = '';

        $returnTop .= "\n            <li><a href=\"#tabSEO\">{\$lblSEO|ucfirst}</a></li>";
        $returnBottom .= self::generateSnippet('Backend/Layout/Templates/Snippets/Seo.base.tpl');

        if ($module['multipleImages']) {
            $returnTop .= "\n            <li><a href=\"#tabImages\">{\$lblImages|ucfirst}</a></li>";
            $returnBottom .= self::generateSnippet('Backend/Layout/Templates/Snippets/Images.base.tpl');
        }

        return array($returnTop, $returnBottom);
    }

    /**
     * Generates a part of the validateForm() function for the backend add/edit actions
     * @TODO: add validation to image size
     *
     * @param  array   $module The array containing all info about the module
     * @param  boolean $isEdit Should we generate it for the edit action?
     * @return string
     */
    public static function generateValidateForm($module, $isEdit)
    {
        $return = '';

        // loop through fields and create and addField statement for each field
        foreach ($module['fields'] as $field) {
            $field['type'] = \SpoonFilter::toCamelCase($field['type']);

            // check if required fields are filled
            if ($field['required']) {
                if ($field['type'] == 'Datetime') {
                    $return .= self::generateSnippet('Backend/Actions/Snippets/ValidateRequired.base.php', array('underscored_label' => $field['underscored_label'] . '_date'));
                    $return .= self::generateSnippet('Backend/Actions/Snippets/ValidateRequired.base.php', array('underscored_label' => $field['underscored_label'] . '_time'));
                } elseif (!($field['type'] == 'Image' || $field['type'] == 'File')) {
                    $return .= self::generateSnippet('Backend/Actions/Snippets/ValidateRequired.base.php', $field);
                }
            }

            // when there is a snippet provided to validate this form type, use it
            if (file_exists(BACKEND_MODULES_PATH . 'ModuleMaker/Layout/Templates/Backend/Actions/Snippets/Validate' . $field['type'] . '.base.php')) {
                $return .= self::generateSnippet('Backend/Actions/Snippets/Validate' . $field['type'] . '.base.php', $field);
            }

            if (($field['type'] == 'Image' || $field['type'] == 'File') && $field['required'] && !$isEdit) $return .= "            else \$fields['" . $field['underscored_label'] . "'" . "]->addError(Language::err('FieldIsRequired'));\n";
        }

        // add validate category if necessary
        if ($module['useCategories'] !== false) {
            $return .= self::generateSnippet('Backend/Actions/Snippets/ValidateRequired.base.php', array('underscored_label' => 'category_id'));
        }

        // add validate meta
        $return .= self::generateSnippet('Backend/Actions/Snippets/ValidateMeta.base.php');

        // return the string we build up
        return $return;
    }
}
