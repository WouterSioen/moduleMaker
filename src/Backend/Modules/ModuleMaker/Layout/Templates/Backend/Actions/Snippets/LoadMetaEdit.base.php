
		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], '{$underscored_label}', true);
		$this->meta->setUrlCallBack('Backend{$module}Model', 'getUrl', array($this->record['id']));
