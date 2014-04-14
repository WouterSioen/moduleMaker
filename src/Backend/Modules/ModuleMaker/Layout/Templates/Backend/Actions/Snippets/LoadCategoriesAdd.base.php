
		// get categories
		$categories = Backend{$camel_case_name}Model::getCategories();
		$this->frm->addDropdown('category_id', $categories);
