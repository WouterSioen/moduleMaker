
				// the file path
				$filePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/files';

				// create folders if needed
				if(!SpoonDirectory::exists($filePath . '/source')) SpoonDirectory::create($filePath . '/source');

				// file provided?
				if($fields['{$underscored_label}']->isFilled())
				{
					// build the file name

					/**
					 * @TODO when meta is added, use the meta in the file name
					 */
					$item['{$underscored_label}'] = time() . '.' . $fields['{$underscored_label}']->getExtension();

					// upload the file
					$fields['{$underscored_label}']->moveFile($filePath, $item['{$underscored_label}']);
				}
