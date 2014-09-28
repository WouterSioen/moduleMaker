<?php

namespace Backend\Modules\ModuleMaker\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridArray;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\ModuleMaker\Engine\Helper as BackendModuleMakerHelper;

/**
 * This is the modules-action, it will display the overview of modules.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @author Arend Pijls <arend.pijls@wijs.be>
 */
class CreateZip extends ActionIndex
{
    /**
     * Data grids.
     *
     * @var BackendDataGrid
     */
    private $dataGridInstalledModules, $dataGridInstallableModules;

    /**
     * Modules that are or or not installed.
     * This is used as a source for the data grids.
     *
     * @var array
     */
    private $installedModules = array(), $installableModules = array();

    /**
     * Execute the action.
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();

        if ($this->getParameter('module') !== NULL) {
            $module = $this->getParameter('module');
            $files = array();

            // frontend files
            $frontendDir = FRONTEND_PATH . "/Modules/" . $module . "/";

            // check if dir exist
            if (file_exists($frontendDir)) {
                $dir = new \RecursiveDirectoryIterator($frontendDir);
                foreach (new \RecursiveIteratorIterator($dir) as $filename => $file) {
                    $files[] = str_replace(PATH_WWW . '/', '', $filename);
                }
            }

            // backend files
            $backendDir = BACKEND_PATH . "/Modules/" . $module . "/";

            // check if dir exist
            if (file_exists($backendDir)) {
                $dir = new \RecursiveDirectoryIterator($backendDir);
                foreach (new \RecursiveIteratorIterator($dir) as $filename => $file) {
                    $files[] = str_replace(PATH_WWW . '/', '', $filename);
                }
            }

            // we found some files
            if (!empty($files)) {
                // create zip
                if (BackendModuleMakerHelper::createZip($files, PATH_WWW . '/' . $module . '.zip')) {
                    // download zip
                    header('Content-Type: application/zip');
                    header('Content-disposition: attachment; filename=' . $module . '.zip');
                    header('Content-Length: ' . filesize(PATH_WWW . '/' . $module . '.zip'));
                    readfile(PATH_WWW . '/' . $module . '.zip');

                    // delete temp file
                    \Spoonfile::delete(PATH_WWW . '/' . $module . '.zip');
                    exit();
                }
            } else {
                $this->redirect(Model::createURLForAction('create_zip') . '&error=non-existing');
            }
        } else {
            $this->loadDataGridInstalled();
            $this->loadDataGridInstallable();

            $this->parse();
            $this->display();
        }
    }

    /**
     * Load the data for the 2 data grids.
     */
    private function loadData()
    {
        // get all manageable modules
        $modules = BackendExtensionsModel::getModules();

        // split the modules in 2 separate data grid sources
        foreach ($modules as $module) {
            if ($module['installed']) $this->installedModules[] = $module;
            else $this->installableModules[] = $module;
        }
    }

    /**
     * Load the data grid for installable modules.
     */
    private function loadDataGridInstallable()
    {
        // create datagrid
        $this->dataGridInstallableModules = new DataGridArray($this->installableModules);

        $this->dataGridInstallableModules->setSortingColumns(array('raw_name'));
        $this->dataGridInstallableModules->setHeaderLabels(array('raw_name' => \SpoonFilter::ucfirst(Language::getLabel('Name'))));
        $this->dataGridInstallableModules->setColumnsHidden(array('installed', 'name', 'cronjobs_active'));

        // check if this action is allowed
        if (Authentication::isAllowedAction('detail_module')) {
            $this->dataGridInstallableModules->setColumnURL(
                'raw_name', Model::createURLForAction('detail_module') . '&amp;module=[raw_name]'
            );
            $this->dataGridInstallableModules->addColumn(
                'details', null, Language::lbl('Details'),
                Model::createURLForAction('detail_module') . '&amp;module=[raw_name]',
                Language::lbl('Details')
            );
        }

        // add create zip column
        $this->dataGridInstallableModules->addColumn(
            'install', null, ucfirst(Language::lbl('CreateZip')),
            Model::createURLForAction('create_zip', 'module_maker') . '&amp;module=[raw_name]',
            ucfirst(Language::lbl('CreateZip'))
        );
    }

    /**
     * Load the data grid for installed modules.
     */
    private function loadDataGridInstalled()
    {
        // create datagrid
        $this->dataGridInstalledModules = new DataGridArray($this->installedModules);

        $this->dataGridInstalledModules->setSortingColumns(array('name'));
        $this->dataGridInstalledModules->setColumnsHidden(array('installed', 'raw_name', 'cronjobs_active'));

        // check if this action is allowed
        if (Authentication::isAllowedAction('detail_module')) {
            $this->dataGridInstalledModules->setColumnURL(
                'name', Model::createURLForAction('detail_module', 'extensions') . '&amp;module=[raw_name]'
            );
            $this->dataGridInstalledModules->addColumn(
                'details', null, Language::lbl('Details'),
                Model::createURLForAction('detail_module', 'extensions') . '&amp;module=[raw_name]',
                Language::lbl('Details')
            );
        }

        // add create zip column
        $this->dataGridInstalledModules->addColumn(
            'install', null, ucfirst(Language::lbl('CreateZip')),
            Model::createURLForAction('create_zip', 'module_maker') . '&amp;module=[raw_name]',
            ucfirst(Language::lbl('CreateZip'))
        );
    }

    /**
     * Parse the datagrids and the reports.
     */
    protected function parse()
    {
        parent::parse();

        // parse data grid
        $this->tpl->assign('dataGridInstallableModules', (string) $this->dataGridInstallableModules->getContent());
        $this->tpl->assign('dataGridInstalledModules', (string) $this->dataGridInstalledModules->getContent());
    }
}
