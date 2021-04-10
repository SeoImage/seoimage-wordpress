<?php

namespace ImageSeoWP\Services\File;

if (!defined('ABSPATH')) {
    exit;
}

use Cocur\Slugify\Slugify;
use ImageSeoWP\Exception\NoRenameFile;

class GenerateFilename
{
    public function __construct()
    {
        $this->reportImageService = imageseo_get_service('ReportImage');
    }

    protected function getDelimiter()
    {
        return apply_filters('imageseo_rename_delimiter', '-');
    }

    protected function generateNameFromReport($attachmentId, $params = [])
    {
        $report = $this->reportImageService->getReportByAttachmentId($attachmentId);

        if (!$report) {
            throw new NoRenameFile('No need to change');

            return;
        }

        $alts = $this->reportImageService->getAltsFromReport($report);
        $key = isset($params['key']) ? $params['key'] : 0;

        $value = '';
        if (isset($alts[$key])) {
            $value = $alts[$key]['name'];
        }

        $slugify = new Slugify(['separator' => $this->getDelimiter()]);

        return $slugify->slugify($value);
    }

    /**
     * @param int $attachmentId
     *
     * @return string
     */
    public function getFilenameByAttachmentId($attachmentId)
    {
        $file = wp_get_attachment_image_src($attachmentId, 'small');

        if (!$file) {
            $file = wp_get_attachment_image_src($attachmentId);
        }

        if (!$file) {
            return '';
        }

        $srcFile = $file[0];

        return basename($srcFile);
    }

    /**
     * @param int $attachmentId
     *
     * @return string
     */
    public function getExtensionFilenameByAttachmentId($attachmentId)
    {
        $splitFilename = explode('.', $this->getFilenameByAttachmentId($attachmentId));

        return array_pop($splitFilename);
    }

    /**
     * @param int   $attachmentId
     * @param array $excludeFilenames
     *
     * @return string
     */
    public function getNameFileWithAttachmentId($attachmentId, $excludeFilenames = [])
    {
        try {
            $newName = $this->generateNameFromReport($attachmentId);
        } catch (NoRenameFile $e) {
            throw new NoRenameFile('No need to change');
        }

        $filePath = get_attached_file($attachmentId);
        $splitName = explode('.', basename($filePath));
        $oldName = $splitName[0];

        if ($oldName === $newName) {
            throw new NoRenameFile('No need to change');
        }

        $generateUniqueFilename = $this->generateUniqueFilename([
            trailingslashit(dirname($filePath)), // Directory
            $splitName[count($splitName) - 1], // Ext
            $this->getDelimiter(), // Delimiter,
            $attachmentId,
            $excludeFilenames,
        ], $newName);

        return $this->validateUniqueFilename($attachmentId, $generateUniqueFilename);
    }

    /**
     * @param array  $data    (directory|extension|delimiter|attachmentId|excludeFilenames)
     * @param string $name
     * @param int    $counter
     *
     * @return string
     */
    public function generateUniqueFilename($data, $name, $counter = 1)
    {
        list($directory, $ext, $delimiter, $attachmentId, $excludeFilenames) = $data;
        if (!$excludeFilenames) {
            $excludeFilenames = [];
        }

        $numberTryName = apply_filters('imageseo_number_try_name_file', 7);

        if (!file_exists(sprintf('%s%s.%s', $directory, $name, $ext)) && !in_array($name, $excludeFilenames, true)) {
            return $name;
        }

        if ($counter < $numberTryName) {
            $name = $this->generateNameFromReport($attachmentId, [
                'key' => $counter,
            ]);
        } elseif ($counter >= $numberTryName) {
            $name = $this->generateNameFromReport($attachmentId);
            $name = sprintf('%s%s%s', get_bloginfo('title'), $delimiter, $name);
        }

        if (!file_exists(sprintf('%s%s.%s', $directory, $name, $ext)) && !in_array($name, $excludeFilenames, true)) {
            return $name;
        }

        if ($counter < $numberTryName) {
            return $this->generateUniqueFilename($data, $name, ++$counter);
        } else {
            return $this->generateUniqueFilename($data, sprintf('%s%s%s', $name, $delimiter, ($numberTryName + 2) - $counter), ++$counter);
        }

        return $name;
    }

    /**
     * @param int    $attachmentId
     * @param string $filename
     * @param int    $i
     *
     * @return string
     */
    public function validateUniqueFilename($attachmentId, $filename, $i = 2)
    {
        if (2 === $i && !$this->getAttachmentIdByFilenameImageSeo($filename)) {
            return $filename;
        }

        $newFilename = sprintf('%s-%s', $filename, $i);

        if (!$this->getAttachmentIdByFilenameImageSeo($newFilename)) {
            return $newFilename;
        }

        return $this->validateUniqueFilename($attachmentId, $filename, ++$i);
    }

    public function getAttachmentIdByFilenameImageSeo($filename)
    {
        global $wpdb;

        $metaKey = sprintf('_imageseo_filename_%s', $filename);
        $sqlQuery = "SELECT {$wpdb->posts}.*
            FROM {$wpdb->posts}
            INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND  {$wpdb->postmeta}.meta_key = '$metaKey' )
            WHERE 1=1
            LIMIT 1
        ";

        $posts = $wpdb->get_results($sqlQuery);
        if (empty($posts)) {
            return null;
        }

        return $posts[0];
    }
}
