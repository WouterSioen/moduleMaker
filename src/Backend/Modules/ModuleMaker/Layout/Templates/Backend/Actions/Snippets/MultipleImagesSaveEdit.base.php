
                // get images from the hidden field
                $files = array();
                if ($fields['uploaded_images']->isFilled()) {
                    $images = json_decode(html_entity_decode($fields['uploaded_images']->getValue()));
                    $uploadedImages = array();
                    foreach ($images as $image) {
                        $files[$image->id] = $image->uploadName;
                    }
                }

                // check existing images first
                foreach ($this->record['images'] as $key => $image) {
                    // if the file isn't in the uploaded files array anymore, delete it
                    if (!array_key_exists('_' . $key, $files)) {
                        Backend{$camel_case_name}Model::deleteImage($key);
                        Model::imageDelete(
                            $this->getModule(), $image['uploadName'], null,
                            Backend{$camel_case_name}Helper::$tempFileSizes
                        );
                    } else {
                        // update the sequence if it changed
                        $underscored = '_' . $key;
                        if ($this->record['images'][$key]['sequence'] != $images->$underscored->sequence){
                            Backend{$camel_case_name}Model::updateImage(array(
                                'id' => $key,
                                'sequence' => $images->$underscored->sequence
                            ));
                        }
                        unset($files['_' . $key]);
                    }
                }

                // get images from the session
                if (!empty($files)) {
                    $fromSession = \SpoonSession::get('uploadedFiles');

                    foreach ($fromSession as $sImage) {
                        // check if the file is available in the files array
                        if (in_array($sImage['uploadName'], $files) && empty($sImage['warning'])) {
                            $uploadedImages[] = $sImage['uploadName'];
                            Backend{$camel_case_name}Helper::imageRename(
                                $this->getModule(),
                                $sImage['uploadName'],
                                $sImage['uploadName'],
                                '',
                                '/uploaded_images',
                                Backend{$camel_case_name}Helper::$imageSizes,
                                true
                            );

                            $photoId = Backend{$camel_case_name}Model::insertImage(
                                array(
                                    '{$underscored_name}_id' => $item['id'],
                                    'name' => $sImage['uploadName'],
                                    'sequence' => $sImage['Index']
                                )
                            );
                        }
                        Model::imageDelete(
                            $this->getModule(), $sImage['uploadName'],
                            'uploaded_images', Backend{$camel_case_name}Helper::$tempFileSizes
                        );
                    }
                    \SpoonSession::delete('uploadedFiles');
                }
