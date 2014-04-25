                $item['{$underscored_label}'] = Model::getUTCDate(
                    null,
                    Model::getUTCTimestamp(
                        $this->frm->getField('{$underscored_label}_date'),
                        $this->frm->getField('{$underscored_label}_time')
                    )
                );
