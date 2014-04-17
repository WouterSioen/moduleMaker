
        // meta
        $this->meta = new Meta($this->frm, $this->record['meta_id'], '{$underscored_label}', true);
        $this->meta->setUrlCallBack('Backend\Modules\{$camel_case_name}\Engine\Model', 'getUrl', array($this->record['id']));
