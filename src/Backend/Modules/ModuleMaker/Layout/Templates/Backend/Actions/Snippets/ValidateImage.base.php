            if ($fields['{$underscored_label}']->isFilled()) {
                $fields['{$underscored_label}']->isAllowedExtension(
                    array('jpg', 'png', 'gif', 'jpeg'),
                    Language::err('JPGGIFAndPNGOnly')
                );
                $fields['{$underscored_label}']->isAllowedMimeType(
                    array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'),
                    Language::err('JPGGIFAndPNGOnly')
                );
            }
