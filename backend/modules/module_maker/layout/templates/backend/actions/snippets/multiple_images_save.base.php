
				// get images from the hidden field
				$files = array();
				if($fields['uploaded_images']->isFilled())
				{
					$images = json_decode($fields['uploaded_images']->getValue());
					$uploadedImages = array();
					foreach($images as $image) $files[$image->id] = $image->uploadName;
				}

				// get images from the session
				if(!empty($files))
				{
					$fromSession = SpoonSession::get('uploadedFiles');
					foreach($fromSession as $sImage)
					{
						// check if the file is available in the files array
						if(in_array($sImage['uploadName'], $files) && empty($sImage['warning']))
						{
							$uploadedImages[] = $sImage['uploadName'];
							BackendModel::imageRename(
								$this->getModule(),
								$sImage['uploadName'],
								$sImage['uploadName'],
								'',
								'/uploaded_images',
								null,
								true
							);

							$photoId = Backend{$camel_case_name}Model::insert(
								array(
									'{$underscored_name}_id' => $item['id'],
									'filename' => $sImage['uploadName'],
									'sequence' => Backend{$camel_case_name}Model::getMaximumSequence($item['id']) + 1
								)
							);
						}
						BackendModel::imageDelete($this->getModule(), $sImage['uploadName'], 'uploaded_images', Backend{$camel_case_name}Model::$tempFileSizes);
					}
					SpoonSession::delete('uploadedFiles');
				}
