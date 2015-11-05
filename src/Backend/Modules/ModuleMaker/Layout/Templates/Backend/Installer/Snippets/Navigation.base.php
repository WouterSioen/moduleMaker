        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationclassnameId = $this->setNavigation(
            $navigationModulesId,
            $this->getModule(),
            '{$underscored_name}/index',
            array('{$underscored_name}/add','{$underscored_name}/edit')
        );
