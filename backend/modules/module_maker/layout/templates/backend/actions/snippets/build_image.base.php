
				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/{$underscored_label}';

				// create folders if needed
{$create_folders}				if(!SpoonDirectory::exists($imagePath . '/source')) SpoonDirectory::create($imagePath . '/source');

				// image provided?
				if($fields['{$underscored_label}']->isFilled())
				{
					// build the image name
					$item['{$underscored_label}'] = {$file_name_function} . '.' . $fields['{$underscored_label}']->getExtension();

					// upload the image & generate thumbnails
					$fields['{$underscored_label}']->generateThumbnails($imagePath, $item['{$underscored_label}']);
				}
