
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'SequenceCategories');

        $this->insertExtra($this->getModule(), 'block', '{$camel_case_name}Category', 'Category', null, 'N', 1002);
        $this->insertExtra($this->getModule(), 'widget', 'Categories', 'Categories', null, 'N', 1003);
