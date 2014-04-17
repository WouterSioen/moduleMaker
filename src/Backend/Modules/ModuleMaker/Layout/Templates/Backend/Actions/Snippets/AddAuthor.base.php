        $this->frm->addDropdown(
            '{$underscored_label}',
            BackendUsersModel::getUsers()
            {$default}
        );
