<?php

namespace Backend\Modules\{$camel_case_name}\Engine;

/**
 * In this file we store all helper functions that we will be using in the {$title} module
 *
 * @author {$author_name} <{$author_email}>
 */
class Helper
{
    /**
     * The imagesizes for the mutliple image upload
     *
     * @var array
     */
    static $imageSizes = array(
        '100x100' => array('width' => 100, 'height' => 100, 'allowEnlargement' => true, 'forceOriginalAspectRatio' => false),
        '600x450' => array('width' => 600, 'height' => 450, 'allowEnlargement' => true, 'forceOriginalAspectRatio' => false),
        '900x600' => array('width' => 900, 'height' => 600, 'allowEnlargement' => true, 'forceOriginalAspectRatio' => false)
    );

    /**
     * The thumbnailsize for the temporary images
     *
     * @var array
     */
    static $tempFileSizes = array(
        '100x100' => array('width' => 100, 'height' => 100, 'allowEnlargement' => true, 'forceOriginalAspectRatio' => false)
    );

    /**
     * Returns the biggest images size
     *
     * @return array
     */
    static function getBiggestImageSize()
    {
        return end(self::$imageSizes);
    }

    /**
     * Gets the nth size of the image
     *
     * @param int $n
     * @return array
     */
    public static function getNthImageSize($n)
    {
        $associativeFirstItem = array_slice(self::$imageSizes, $n - 1, $n - 1);
        $arrayValues =  array_values($associativeFirstItem);
        return array_shift($arrayValues);
    }

    /**
     * Returns the biggest images size
     *
     * @return array
     */
    static function getSmallestImageSize()
    {
        $arrayValues = array_values(self::$imageSizes);
        return array_shift($arrayValues);
    }

    /**
     * Image Rename
     * @TODO: this function should be in the core
     *
     * @param string $module Module name.
     * @param string $currentFilename Current filename.
     * @param string $newFilename New filename.
     * @param string $newSubDirectory New subdirectory.
     * @param string $currentSubDirectory Current subdirectory.
     * @param array $fileSizes Possible file sizes.
     * @param bool $createMissingFromSource Create the missing files from the source.
     */
    public static function imageRename($module, $currentFilename, $newFilename, $newSubDirectory = '', $currentSubDirectory = '', $fileSizes = null, $createMissingFromSource = false)
    {
        // get fileSizes var from model
        if (empty($fileSizes)) {
            $model = get_class_vars('Backend' . \SpoonFilter::toCamelCase($module) . 'Model');
            $fileSizes = $model['fileSizes'];
        }

        // add source dir to directories
        $directories = array_keys($fileSizes);

        $sourceSource = FRONTEND_FILES_PATH . '/' . $module . (empty($currentSubDirectory) ? '/' : $currentSubDirectory . '/') . 'source/' . $currentFilename;
        $sourceDestination = FRONTEND_FILES_PATH . '/' . $module . (empty($newSubDirectory) ? '/' : $newSubDirectory . '/') . 'source/' . $newFilename;
        if (\SpoonFile::exists($sourceSource)) {
            \SpoonFile::move($sourceSource, $sourceDestination);
        }

        // loop all directories
        foreach ($directories as $sizeDir) {
            $source = FRONTEND_FILES_PATH . '/' . $module . (empty($currentSubDirectory) ? '/' : $currentSubDirectory . '/') . $sizeDir . '/' . $currentFilename;
            $destination = FRONTEND_FILES_PATH . '/' . $module . (empty($newSubDirectory) ? '/' : $newSubDirectory . '/') . $sizeDir . '/' . $newFilename;
            if (\SpoonFile::exists($source)) {
                \SpoonFile::move($source, $destination);
            } elseif ($createMissingFromSource && \SpoonFile::exists($sourceDestination)) {
                // create a thumbnail
                $thumbnail = new \SpoonThumbnail(
                    $sourceDestination,
                    $fileSizes[$sizeDir]['width'],
                    $fileSizes[$sizeDir]['height']
                );
                $thumbnail->setForceOriginalAspectRatio($fileSizes[$sizeDir]['forceOriginalAspectRatio']);
                $thumbnail->setAllowEnlargement($fileSizes[$sizeDir]['allowEnlargement']);
                $thumbnail->parseToFile($destination);
            }
        }
    }
}
