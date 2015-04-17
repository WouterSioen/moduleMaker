
                // get images from the hidden field
                $files = array();
                if ($fields['uploaded_images']->isFilled()) {
                    $images = json_decode(html_entity_decode($fields['uploaded_images']->getValue()));
                    $uploadedImages = array();
                    foreach ($images as $image) {
                        $files[$image->id] = $image->uploadName;
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
