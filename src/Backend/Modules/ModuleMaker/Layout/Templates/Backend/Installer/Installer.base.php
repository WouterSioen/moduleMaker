<?php

namespace Backend\Modules\{$camel_case_name}\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the {$title} module
 *
 * @author {$author_name} <{$author_email}>
 */
class Installer extends ModuleInstaller
{
    public function install()
    {
        // import the sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('{$camel_case_name}');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Delete');{$install_extras}

        // add extra's
        $subnameID = $this->insertExtra($this->getModule(), 'block', '{$camel_case_name}');
        $this->insertExtra($this->getModule(), 'block', '{$camel_case_name}Detail', 'Detail');

{$backend_navigation}
    }
}
