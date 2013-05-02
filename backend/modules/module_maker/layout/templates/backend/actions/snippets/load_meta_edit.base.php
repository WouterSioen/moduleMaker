		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], '{$underscored_label}', true);
		$this->meta->setUrlCallBack('Backend{$camel_case_name}Model', 'getUrl', array($this->record['id']));