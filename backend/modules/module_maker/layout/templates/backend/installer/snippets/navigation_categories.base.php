		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigation{$camel_case_name}Id = $this->setNavigation($navigationModulesId, '{$camel_case_name}');
		$this->setNavigation(
			$navigation{$camel_case_name}Id, '{$camel_case_name}', '{$underscored_name}/index',
			array('{$underscored_name}/add', '{$underscored_name}/edit')
		);
		$this->setNavigation(
			$navigation{$camel_case_name}Id, 'Categories', '{$underscored_name}/categories',
			array('{$underscored_name}/add_category', '{$underscored_name}/edit_category')
		);