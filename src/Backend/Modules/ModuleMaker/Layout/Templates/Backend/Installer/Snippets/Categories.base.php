
		$this->setActionRights(1, '{$camel_case_name}', 'Categories');
		$this->setActionRights(1, '{$camel_case_name}', 'AddCategory');
		$this->setActionRights(1, '{$camel_case_name}', 'EditCategory');
		$this->setActionRights(1, '{$camel_case_name}', 'DeleteCategory');
		$this->setActionRights(1, '{$camel_case_name}', 'SequenceCategories');

		$this->insertExtra('{$camel_case_name}', 'block', '{$camel_case_name}Category', 'category', null, 'N', 1002);
		$this->insertExtra('{$camel_case_name}', 'widget', 'Categories', 'categories', null, 'N', 1003);
