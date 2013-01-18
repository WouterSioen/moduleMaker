
				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/images';

				// create folders if needed
{$create_folders}				if(!SpoonDirectory::exists($imagePath . '/source')) SpoonDirectory::create($imagePath . '/source');

				// image provided?
				if($fields['{$underscored_label}']->isFilled())
				{
					// build the image name

					/**
					 * @TODO when meta is added, use the meta in the image name
					 */
					$item['{$underscored_label}'] = time() . '.' . $fields['{$underscored_label}']->getExtension();

					// upload the image & generate thumbnails
					$fields['{$underscored_label}']->generateThumbnails($imagePath, $item['{$underscored_label}']);
				}
