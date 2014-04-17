

        // copy the qqFileUploader needed for multiple fileupload
        if (!\SpoonFile::exists(PATH_LIBRARY . '/external/qqFileUploader.php')) {
            copy(dirname(__FILE__) . '/data/qqFileUploader.php', PATH_LIBRARY . '/external/qqFileUploader.php');
        }
