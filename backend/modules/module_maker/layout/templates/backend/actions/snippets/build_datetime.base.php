				$item['{$underscored_label}'] = BackendModel::getUTCDate(
					null,
					BackendModel::getUTCTimestamp(
						$this->frm->getField('{$underscored_label}_date'),
						$this->frm->getField('{$underscored_label}_time')
					)
				);
