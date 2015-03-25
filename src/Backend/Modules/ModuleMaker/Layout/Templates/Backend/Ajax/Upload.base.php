<?php

namespace Backend\Modules\{$camel_case_name}\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Core\Engine\TemplateModifiers;
use Backend\Modules\{$camel_case_name}\Engine\Helper as Backend{$camel_case_name}Helper;

/**
 * Uploads files to the server
 *
 * @author {$author_name} <{$author_email}>
 */
class Upload extends AjaxAction
{
    public function execute()
    {
        // Include the uploader class
        require_once PATH_LIBRARY . '/external/qqFileUploader.php';

        $pathSlug = '/{$camel_case_name}/uploaded_images';
        $uploadPath = FRONTEND_FILES_PATH . $pathSlug;
        $uploadURL = FRONTEND_FILES_URL . $pathSlug;

        // get the temporary image directory
        $sizes = Backend{$camel_case_name}Helper::$tempFileSizes;
        reset($sizes);
        $thumbFolder = key($sizes);

        // create directories, in case it doesn't exist yet
        \SpoonDirectory::create($uploadPath . '/source');
        \SpoonDirectory::create($uploadPath . '/' . $thumbFolder);
        \SpoonDirectory::create($uploadPath . '/chunks');

        // create uploader
        $uploader = new \qqFileUploader();

        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array();

        // Specify max file size in bytes.
        $uploader->sizeLimit = 1 * 1024 * 1024;

        // Specify the input name set in the javascript.
        $uploader->inputName = 'qqfile';

        // specify chunks folder
        $uploader->chunksFolder = $uploadPath . '/chunks';

        // get file extension
        $fileExtension = \SpoonFile::getExtension($uploader->getName());

        // create a filename ("<microtime>_<random md5>_<extension>")
        $fileName = str_replace('.', '', microtime(true)) . '_' . md5(mt_rand()) . '.' . $fileExtension;

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($uploadPath . '/source', $fileName);

        // get total num pars
        $numChunks = \SpoonFilter::getGetValue('qqtotalparts', null, null, 'int');

        // get current index
        $chunkIndex = \SpoonFilter::getGetValue('qqpartindex', null, null, 'int');

        $result['num'] = $numChunks;
        $result['Index'] = $chunkIndex;

        if ($chunkIndex === ($numChunks - 1)) {
            // To return a name used for uploaded file you can use the following line.
            $result['uploadName'] = $uploader->getUploadName();
            $result['uploadURL'] = $uploadURL;
            $result['originalFileName'] = TemplateModifiers::truncate($uploader->getName(), 40);

            // is svg?
            if ($fileExtension === 'svg') {
                $result['isSvg'] = true;
            } else {
                $result['isSvg'] = false;

                // increase memory limit. parseToFile fails on big images
                ini_set('memory_limit', '512M');

                // create a thumbnail
                $thumbnail = new \SpoonThumbnail($uploadPath . '/source/' . $fileName, 100, 100);
                $thumbnail->setForceOriginalAspectRatio(false);
                $thumbnail->parseToFile($uploadPath . '/100x100/' . $fileName);
            }

            if (\SpoonSession::exists('uploadedFiles')) {
                $uploadedFiles = \SpoonSession::get('uploadedFiles');
            } else {
                $uploadedFiles = array();
            }

            $uploadedFiles[] = $result;
            \SpoonSession::set('uploadedFiles', $uploadedFiles);
        }

        // output result
        header("Content-Type: text/plain");
        exit(json_encode($result));
    }
}
