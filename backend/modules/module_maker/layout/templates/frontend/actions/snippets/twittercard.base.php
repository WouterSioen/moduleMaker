
		/**
		 * @TODO add the image
		 * $this->addMetaData(array('property' => 'twitter:image', 'content' => FRONTEND_FILES_URL . ''), true, 'property');
		 */
		$this->addMetaData(array('property' => 'twitter:creator', 'content' => '@{$twitter}'), true, 'property');
		$this->addMetaData(array('property' => 'twitter:site', 'content' => '@{$twitter}'), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:card', 'content' => 'summary'), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:url', 'content' => SITE_URL . FrontendNavigation::getURLForBlock('{$underscored_name}', 'detail') . '/' . $this->record['url']), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:title', 'content' => $this->record['meta_title']), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:description', 'content' => $this->record['meta_title']), true, 'property');
