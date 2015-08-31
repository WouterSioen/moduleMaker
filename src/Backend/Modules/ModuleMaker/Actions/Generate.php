<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\Action;
use Backend\Core\Engine\Model;
use Backend\Modules\ModuleMaker\Engine\Model as BackendModuleMakerModel;
use Backend\Modules\ModuleMaker\Engine\Generator as BackendModuleMakerGenerator;

/**
 * This is the Generate action
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 * @author Arend Pijls <arend.pijls@wijs.be>
 */
class Generate extends Action
{
    /**
     * The module we're working on
     *
     * @var array
     */
    private $record;

    /**
     * Some variables used in multiple functions
     * $backendPath:        the path to the Backend part of the module (string)
     * $frontendPath:       the path to the Frontend part of the module (string)
     * $templatesPath       The path where our templates and Snippets are stored (string)
     * $variables:          A part of the records variable used for string replacement (array)
     *
     * @var mixed
     */
    private $backendPath, $frontendPath, $variables;

    /**
     * Execute the action
     */
    public function execute()
    {
        // If step 1 isn't entered, redirect back to the first step of the wizard
        $this->record = \SpoonSession::get('module');
        if (!$this->record || !array_key_exists('title', $this->record)) $this->redirect(Model::createURLForAction('Add'));

        // If there are no fields added, redirect back to the second step of the wizard
        if (!array_key_exists('fields', $this->record) || empty($this->record['fields'])) $this->redirect(Model::createURLForAction('AddStep2'));

        parent::execute();

        // initialize some variables
        $this->backendPath = BACKEND_MODULES_PATH . '/' . $this->record['camel_case_name'] . '/';
        $this->frontendPath = FRONTEND_MODULES_PATH . '/' . $this->record['camel_case_name'] . '/';
        $this->variables = (array) $this->record;
        unset($this->variables['fields']);

        $this->generateFolders();
        $this->generateBaseFiles();
        $this->generateInstallerFiles();

        // Backend
        $this->generateBackendFiles();
        $this->generateBackendModel();
        $this->generateBackendActions();
        $this->generateBackendCategoryActions();

        // Frontend
        $this->generateFrontendFiles();
        $this->generateFrontendModel();
        $this->generateFrontendActions();
        $this->generateFrontendCategoryActions();
        $this->generateFrontendCategoryWidget();

        $this->parse();
        $this->display();
    }

    /**
     * Generates the Backend Actions (and templates) (index, add, edit and delete)
     */
    protected function generateBackendActions()
    {
        $this->variables['sequence_extra'] = '';
        if ($this->record['useSequence']) {
            $this->variables['sequence_extra'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Actions/Snippets/Sequence.base.php'
            );
        }
        $this->variables['meta_field'] = $this->record['fields'][(int) $this->record['metaField']]['underscored_label'];

        // generate index
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/Index.base.php', $this->variables, $this->backendPath . 'Actions/Index.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Backend/Layout/Templates/Index.base.tpl', $this->variables, $this->backendPath . 'Layout/Templates/Index.tpl'
        );

        // generate add action
        // create some custom variables
        $this->variables['load_form_add'] = BackendModuleMakerGenerator::generateLoadForm($this->record, false);
        $this->variables['validate_form_add'] = BackendModuleMakerGenerator::generateValidateForm($this->record, false);
        $this->variables['build_item_add'] = BackendModuleMakerGenerator::generateBuildItem($this->record, false);
        $this->variables['search_index'] = BackendModuleMakerGenerator::generateSearchIndex($this->record);
        $this->variables['save_tags'] = BackendModuleMakerGenerator::generateSaveTags($this->record);
        $this->variables['parse_meta'] = BackendModuleMakerGenerator::generateSnippet(
            'Backend/Actions/Snippets/ParseMeta.base.php'
        );

        // get variables for multiple images
        list($this->variables['multiFilesJs'], $this->variables['multiFilesLoad'], $this->variables['multiFilesSave']) = BackendModuleMakerGenerator::generateMultiFiles($this->record, false);

        // build and save the file
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/Add.base.php', $this->variables, $this->backendPath . 'Actions/Add.php'
        );

        // generate add template
        // create variables
        list($this->variables['template_title'], $this->variables['template'], $this->variables['template_side']) = BackendModuleMakerGenerator::generateTemplate($this->record, false);
        list($this->variables['template_tabs_top'], $this->variables['template_tabs_bottom']) = BackendModuleMakerGenerator::generateTemplateTabs($this->record);

        // build and save the file
        BackendModuleMakerGenerator::generateFile(
            'Backend/Layout/Templates/Add.base.tpl', $this->variables, $this->backendPath . 'Layout/Templates/Add.tpl'
        );

        // generate edit action
        // create some custom variables
        $this->variables['load_data_edit'] = BackendModuleMakerGenerator::generateLoadData($this->record, true);
        $this->variables['load_form_edit'] = BackendModuleMakerGenerator::generateLoadForm($this->record, true);
        $this->variables['validate_form_edit'] = BackendModuleMakerGenerator::generateValidateForm($this->record, true);
        $this->variables['build_item_edit'] = BackendModuleMakerGenerator::generateBuildItem($this->record, true);
        $this->variables['search_index'] = BackendModuleMakerGenerator::generateSearchIndex($this->record);

        // get variables for multiple images
        list($this->variables['multiFilesJs'], $this->variables['multiFilesLoad'], $this->variables['multiFilesSave']) = BackendModuleMakerGenerator::generateMultiFiles($this->record, true);

        // build and save the file
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/Edit.base.php', $this->variables, $this->backendPath . 'Actions/Edit.php'
        );

        // generate edit template
        list($this->variables['template_title'], $this->variables['template'], $this->variables['template_side']) = BackendModuleMakerGenerator::generateTemplate($this->record, true);
        BackendModuleMakerGenerator::generateFile(
            'Backend/Layout/Templates/Edit.base.tpl', $this->variables, $this->backendPath . 'Layout/Templates/Edit.tpl'
        );

        // generate delete
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/Delete.base.php', $this->variables, $this->backendPath . 'Actions/Delete.php'
        );

        // unset the custom variables
        unset(
            $this->variables['sequence_extra'], $this->variables['load_form_add'], $this->variables['validate_form_add'],
            $this->variables['build_item_add'], $this->variables['load_form_edit'], $this->variables['validate_form_edit'],
            $this->variables['build_item_edit'], $this->variables['search_index'], $this->variables['parse_meta'],
            $this->variables['save_tags'], $this->variables['template_title'], $this->variables['template'],
            $this->variables['template_side'], $this->variables['template_tabs_top'], $this->variables['template_tabs_bottom'],
            $this->variables['multiFilesJs'], $this->variables['multiFilesLoad'], $this->variables['multiFilesSave'],
            $this->variables['meta_field']
        );
    }

    /**
     * Generates the Backend category Actions (and templates) (categories, add_category, edit_category and delete_category)
     */
    protected function generateBackendCategoryActions()
    {
        if (!$this->record['useCategories']) return;

        // generate categories
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/Categories.base.php', $this->variables, $this->backendPath . 'Actions/Categories.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Backend/Layout/Templates/Categories.base.tpl', $this->variables, $this->backendPath . 'Layout/Templates/Categories.tpl'
        );

        // generate add_category
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/AddCategory.base.php', $this->variables, $this->backendPath . 'Actions/AddCategory.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Backend/Layout/Templates/AddCategory.base.tpl', $this->variables, $this->backendPath . 'Layout/Templates/AddCategory.tpl'
        );

        // generate edit_category
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/EditCategory.base.php', $this->variables, $this->backendPath . 'Actions/EditCategory.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Backend/Layout/Templates/EditCategory.base.tpl', $this->variables, $this->backendPath . 'Layout/Templates/EditCategory.tpl'
        );

        // generate delete_category
        BackendModuleMakerGenerator::generateFile(
            'Backend/Actions/DeleteCategory.base.php', $this->variables, $this->backendPath . 'Actions/DeleteCategory.php'
        );
    }

    /**
     * Generates the Backend files (module.js, sequence.php)
     */
    protected function generateBackendFiles()
    {
        // generate module.js file
        if ($this->record['multipleImages']) {
            $this->variables['multiJs'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Js/Snippets/Multifiles.base.js', $this->variables
            );
        } else $this->variables['multiJs'] = '';
        $this->variables['do_meta'] = BackendModuleMakerGenerator::generateSnippet(
            'Backend/Js/Snippets/DoMeta.base.js', $this->record['fields'][$this->record['metaField']]
        );
        BackendModuleMakerGenerator::generateFile(
            'Backend/Js/Javascript.base.js', $this->variables, $this->backendPath . 'Js/' . $this->record['camel_case_name'] . '.js'
        );

        // classes needed to register module specific services
        BackendModuleMakerGenerator::generateFile(
            'Backend/DependencyInjection/ModuleExtension.base.php',
            $this->variables,
            $this->backendPath . 'DependencyInjection/' . $this->record['camel_case_name'] . 'Extension.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Backend/Resources/config/services.base.yml',
            $this->variables,
            $this->backendPath . 'Resources/config/services.yml'
        );

        unset($this->variables['multiJs'], $this->variables['do_meta']);

        // add a sequence Ajax action if necessary
        if ($this->record['useSequence']) {
            BackendModuleMakerGenerator::generateFile(
                'Backend/Ajax/Sequence.base.php', $this->variables, $this->backendPath . 'Ajax/Sequence.php'
            );
        }

        // add a sequence categories Ajax action if necessary
        if ($this->record['useCategories']) {
            BackendModuleMakerGenerator::generateFile(
                'Backend/Ajax/SequenceCategories.base.php', $this->variables, $this->backendPath . 'Ajax/SequenceCategories.php'
            );
        }

        // add an upload Ajax action if necessary
        if ($this->record['multipleImages']) {
            BackendModuleMakerGenerator::generateFile(
                'Backend/Ajax/Upload.base.php', $this->variables, $this->backendPath . 'Ajax/Upload.php'
            );
        }

        // if we use the fineuploader, we should copy the needed js and css files
        if ($this->record['multipleImages']) {
            \SpoonDirectory::copy(
                BACKEND_MODULES_PATH . '/ModuleMaker/Layout/Templates/Backend/Js/fineuploader', $this->backendPath . 'Js/fineuploader'
            );
            BackendModuleMakerGenerator::generateFile(
                'Backend/Layout/Css/fineuploader.css', null, $this->backendPath . 'Layout/Css/fineuploader.css'
            );
        }
    }

    /**
     * Generates the Backend model.php file
     */
    protected function generateBackendModel()
    {
        // add the createURL function
        $this->variables['get_url'] = BackendModuleMakerGenerator::generateSnippet(
            'Backend/Engine/Snippets/GetUrl.base.php', $this->variables
        );
        $this->variables['meta_field'] = $this->record['fields'][(int) $this->record['metaField']]['underscored_label'];

        // add the getMaximumSequence function if sequencing is used
        if ($this->record['useSequence']) {
            $this->variables['get_max_sequence'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/GetMaxSequence.base.php', $this->variables
            );
        } else $this->variables['get_max_sequence'] = '';

        if ($this->record['multipleImages']) {
            $this->variables['insert_image'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/InsertImage.base.php', $this->variables
            );
            $this->variables['update_image'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/UpdateImage.base.php', $this->variables
            );
            $this->variables['get_images'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/GetImages.base.php', $this->variables
            );
            $this->variables['delete_image'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/DeleteImage.base.php', $this->variables
            );
        } else $this->variables['insert_image'] = $this->variables['update_image'] = $this->variables['get_images'] = $this->variables['delete_image'] = '';

        // add the extra parameters in the MySQL SELECT
        $this->variables['select_extra'] = '';
        foreach ($this->record['fields'] as $field) {
            // datetime fields should be fetched as timestamps
            if ($field['type'] == 'datetime') $this->variables['select_extra'] .= ', UNIX_TIMESTAMP(i.' . $field['underscored_label'] . ') AS ' . $field['underscored_label'];
        }

        // select the sequence for the datagrid if we have sequencing
        $this->variables['datagrid_extra'] = ($this->record['useSequence']) ? ', i.sequence' : '';
        $this->variables['datagrid_order'] = ($this->record['useSequence']) ? "\n         ORDER BY i.sequence" : '';

        // create custom variables for the categories
        if ($this->record['useCategories']) {
            $this->variables['datagrid_categories'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/DatagridCategories.base.php', $this->variables
            );
            $this->variables['delete_category'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/DeleteCategory.base.php', $this->variables
            );
            $this->variables['exists_category'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/ExistsCategory.base.php', $this->variables
            );
            $this->variables['get_category'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/GetCategory.base.php', $this->variables
            );
            $this->variables['get_url_category'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/GetUrlCategory.base.php', $this->variables
            );
            $this->variables['insert_category'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/InsertCategory.base.php', $this->variables
            );
            $this->variables['update_category'] = BackendModuleMakerGenerator::generateSnippet(
                'Backend/Engine/Snippets/UpdateCategory.base.php', $this->variables
            );
        } else {
            $this->variables['datagrid_categories'] = $this->variables['delete_category'] = $this->variables['exists_category'] = '';
            $this->variables['get_category'] = $this->variables['get_url_category'] = $this->variables['insert_category'] = '';
            $this->variables['update_category'] = '';
        }

        // generate the file
        BackendModuleMakerGenerator::generateFile(
            'Backend/Engine/Model.base.php', $this->variables, $this->backendPath . 'Engine/Model.php'
        );

        unset(
            $this->variables['get_url'], $this->variables['get_max_sequence'], $this->variables['datagrid_extra'],
            $this->variables['datagrid_order'], $this->variables['insert_image'], $this->variables['update_image'],
            $this->variables['get_images'], $this->variables['delete_image'], $this->variables['meta_field']
        );

        // generate the helper class if necessary
        if ($this->record['multipleImages']) {
            BackendModuleMakerGenerator::generateFile(
                'Backend/Engine/Helper.base.php', $this->variables, $this->backendPath . 'Engine/Helper.php'
            );
        }
    }

    /**
     * Generates the basic files (info.xml and config.php in the document root.)
     */
    protected function generateBaseFiles()
    {
        // generate info.xml file
        BackendModuleMakerGenerator::generateFile(
            'Backend/info.base.xml', $this->variables, $this->backendPath . 'info.xml'
        );

        // generate config.php file for the Backend
        BackendModuleMakerGenerator::generateFile(
            'Backend/Config.base.php', $this->variables, $this->backendPath . 'Config.php'
        );

        // generate config.php file for the Frontend
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Config.base.php', $this->variables, $this->frontendPath . 'Config.php'
        );
    }

    /**
     * Generates the folder structure for the module
     */
    protected function generateFolders()
    {
        // the Backend
        $backendDirs = array(
            'main' => $this->backendPath,
            'sub' => array(
                'Actions', 'Ajax', 'DependencyInjection', 'Js', 'Cronjobs', 'Engine',
                'Installer' => array('Data'),
                'Layout' => array('Templates', 'Css'),
                'Resources' => array('config'),
            )
        );

        // make the Backend directories
        BackendModuleMakerModel::makeDirs($backendDirs);

        // the Frontend
        $frontendDirs = array(
            'main' => $this->frontendPath,
            'sub' => array(
                'Actions', 'Engine', 'Widgets',
                'Layout' => array('Templates', 'Widgets'),
                'Js',
            )
        );

        // make the Frontend directories
        BackendModuleMakerModel::makeDirs($frontendDirs);
    }

    /**
     * Generates the Backend Actions (and templates) (index, add, edit and delete)
     */
    protected function generateFrontendActions()
    {
        // use text field linked with the meta for the page title
        $this->variables['pageTitle'] = $this->record['fields'][(int) $this->record['metaField']]['underscored_label'];

        // generate index
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Actions/Index.base.php', $this->variables, $this->frontendPath . 'Actions/Index.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Layout/Templates/Index.base.tpl', $this->variables, $this->frontendPath . 'Layout/Templates/Index.tpl'
        );

        // create twittercard variable if necessary
        if (array_key_exists('twitter', $this->record)) {
            $this->variables['twitterCard'] = BackendModuleMakerGenerator::generateSnippet(
                'Frontend/Actions/Snippets/Twittercard.base.php', $this->record
            );
        } else $this->variables['twitterCard'] = '';

        // generate detail
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Actions/Detail.base.php', $this->variables, $this->frontendPath . 'Actions/Detail.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Layout/Templates/Detail.base.tpl', $this->variables, $this->frontendPath . 'Layout/Templates/Detail.tpl'
        );

        unset($this->variables['pageTitle'], $this->variables['twitterCard']);
    }

    /**
     * Generates the Frontend category action
     */
    protected function generateFrontendCategoryActions()
    {
        if (!$this->record['useCategories']) return;

        // generate category action
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Actions/Category.base.php', $this->variables, $this->frontendPath . 'Actions/Category.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Layout/Templates/Category.base.tpl', $this->variables, $this->frontendPath . 'Layout/Templates/Category.tpl'
        );
    }

    /**
     * Generates the Frontend categories widget
     */
    protected function generateFrontendCategoryWidget()
    {
        if (!$this->record['useCategories']) return;

        // generate categories widget
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Widgets/Categories.base.php', $this->variables, $this->frontendPath . 'Widgets/Categories.php'
        );
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Layout/Widgets/Categories.base.tpl', $this->variables, $this->frontendPath . 'Layout/Widgets/Categories.tpl'
        );
    }

    /**
     * Generates the Frontend files (module.js, sequence.php)
     */
    protected function generateFrontendFiles()
    {
        // generate module.js file
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Js/Javascript.base.js', $this->variables, $this->frontendPath . 'Js/' . $this->record['underscored_name'] . '.js'
        );
    }

    /**
     * Generates the Frontend model.php file
     */
    protected function generateFrontendModel()
    {
        // create custom mysql queries for the sequence
        $this->variables['sequence_sorting'] = '';
        if ($this->record['useSequence']) {
            $this->variables['sequence_sorting'] = 'i.sequence ASC, ';
        }

        // create custom variables for the categories
        if ($this->record['useCategories']) {
            $this->variables['getAllByCategory'] = BackendModuleMakerGenerator::generateSnippet(
                'Frontend/Engine/Snippets/getAllByCategory.base.php', $this->variables
            );
            $this->variables['getAllCategories'] = BackendModuleMakerGenerator::generateSnippet(
                'Frontend/Engine/Snippets/getAllCategories.base.php', $this->variables
            );
            $this->variables['getCategory'] = BackendModuleMakerGenerator::generateSnippet(
                'Frontend/Engine/Snippets/getCategory.base.php', $this->variables
            );
            $this->variables['getCategoryCount'] = BackendModuleMakerGenerator::generateSnippet(
                'Frontend/Engine/Snippets/getCategoryCount.base.php', $this->variables
            );
        } else {
            $this->variables['getAllByCategory'] = $this->variables['getCategory'] = $this->variables['getCategoryCount'] = '';
            $this->variables['getAllCategories'] = '';
        }

        // check if search is enabled
        $this->variables['search'] = '';
        if ($this->record['searchFields']) {
            // use text field linked with the meta for the page title
            $this->variables['meta_field'] = $this->record['fields'][(int) $this->record['metaField']]['underscored_label'];

            $this->variables['search'] = BackendModuleMakerGenerator::generateSnippet(
                'Frontend/Engine/Snippets/Search.base.php', $this->variables
            );
        }

        // generate the file
        BackendModuleMakerGenerator::generateFile(
            'Frontend/Engine/Model.base.php', $this->variables, $this->frontendPath . 'Engine/Model.php'
        );

        unset(
            $this->variables['getAllByCategory'], $this->variables['getAllCategories'], $this->variables['getCategory'],
            $this->variables['getCategoryCount'], $this->variables['sequence_sorting'], $this->variables['meta_field'],
            $this->variables['search']
        );
    }

    /**
     * Generates the installer files (installer.php, install.sql and locale.xml)
     */
    protected function generateInstallerFiles()
    {
        list($this->variables['install_extras'], $this->variables['backend_navigation']) = BackendModuleMakerGenerator::generateInstall($this->variables);

        // generate installer.php
        BackendModuleMakerGenerator::generateFile(
            'Backend/Installer/Installer.base.php', $this->variables, $this->backendPath . 'Installer/Installer.php'
        );

        if ($this->record['multipleImages']) {
            BackendModuleMakerGenerator::generateFile(
                'Backend/Installer/Data/qqFileUploader.php', null, $this->backendPath . 'Installer/Data/qqFileUploader.php'
            );
        }

        unset($this->variables['install_extras'], $this->variables['Backend_navigation']);

        // generate locale.xml
        BackendModuleMakerGenerator::generateFile(
            'Backend/Installer/Data/locale.base.xml', $this->variables, $this->backendPath . 'Installer/Data/locale.xml'
        );

        // generate install.sql
        $sql = BackendModuleMakerGenerator::generateSQL($this->record['underscored_name'], $this->record);
        if ($this->record['useCategories']) {
            $sql .= BackendModuleMakerGenerator::generateSnippet(
                'Backend/Installer/Snippets/Categories.base.sql', $this->variables
            );
        }
        if ($this->record['multipleImages']) {
            $sql .= BackendModuleMakerGenerator::generateSnippet(
                'Backend/Installer/Snippets/MultipleImages.base.sql', $this->variables
            );
        }
        BackendModuleMakerModel::makeFile($this->backendPath . 'Installer/Data/install.sql', $sql);
    }

    /**
     * Parses the data in the template
     */
    protected function parse()
    {
        $this->tpl->assign('module', $this->record);

        \SpoonSession::delete('module');
    }
}
