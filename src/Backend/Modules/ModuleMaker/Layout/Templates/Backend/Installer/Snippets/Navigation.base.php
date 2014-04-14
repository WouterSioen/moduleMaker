		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationclassnameId = $this->setNavigation(
			$navigationModulesId,
			'{$camel_case_name}',
			'{$underscored_name}/index',
			array('{$underscored_name}/add','{$underscored_name}/edit')
		);