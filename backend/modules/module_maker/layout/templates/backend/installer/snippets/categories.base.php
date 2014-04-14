
		$this->setActionRights(1, '{$underscored_name}', 'categories');
		$this->setActionRights(1, '{$underscored_name}', 'add_category');
		$this->setActionRights(1, '{$underscored_name}', 'edit_category');
		$this->setActionRights(1, '{$underscored_name}', 'delete_category');
		$this->setActionRights(1, '{$underscored_name}', 'sequence_categories');

		$this->insertExtra('{$underscored_name}', 'block', '{$camel_case_name}Category', 'category', null, 'N', 1002);
		$this->insertExtra('{$underscored_name}', 'widget', 'Categories', 'categories', null, 'N', 1003);
