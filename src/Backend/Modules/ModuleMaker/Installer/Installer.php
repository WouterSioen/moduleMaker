<?php

namespace Backend\Modules\ModuleMaker\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the module_maker module
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class Installer extends ModuleInstaller
{
    public function install()
    {
        // install the module in the database
        $this->addModule('ModuleMaker');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'ModuleMaker');

        $this->setActionRights(1, 'ModuleMaker', 'Add');
        $this->setActionRights(1, 'ModuleMaker', 'AddField');
        $this->setActionRights(1, 'ModuleMaker', 'AddStep2');
        $this->setActionRights(1, 'ModuleMaker', 'AddStep3');
        $this->setActionRights(1, 'ModuleMaker', 'AddStep4');
        $this->setActionRights(1, 'ModuleMaker', 'CreateZip');
        $this->setActionRights(1, 'ModuleMaker', 'DeleteField');
        $this->setActionRights(1, 'ModuleMaker', 'Generate');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationModule_MakerId = $this->setNavigation($navigationModulesId, 'ModuleMaker');
        $this->setNavigation($navigationModule_MakerId, 'CreateModule', 'module_maker/add', array('module_maker/add_step2', 'module_maker/add_field', 'module_maker/add_step3', 'module_maker/add_step4', 'module_maker/generate'));
        $this->setNavigation($navigationModule_MakerId, 'CreateZip', 'module_maker/create_zip');
    }
}
