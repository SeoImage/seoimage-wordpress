<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

class MediaLibraryReport
{
    public function __construct()
    {
        $this->renameFileService = imageseo_get_service('RenameFile');
        $this->reportImageService = imageseo_get_service('ReportImage');
        $this->altService = imageseo_get_service('Alt');
    }

    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }
        add_action('admin_post_imageseo_generate_alt', [$this, 'adminPostGenerateAlt']);
        add_action('admin_post_imageseo_rename_attachment', [$this, 'adminPostRenameAttachment']);
    }

    /**
     * @return int
     */
    protected function getAttachmentId()
    {
        if ('GET' === $_SERVER['REQUEST_METHOD']) {
            return (int) $_GET['attachment_id'];
        } elseif ('POST' === $_SERVER['REQUEST_METHOD']) {
            return (int) $_POST['attachment_id'];
        }
    }

    /**
     * @since 2.0
     *
     * @return void
     */
    public function adminPostGenerateAlt()
    {
        $redirectUrl = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_generate_alt')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        $response = $this->altService->generateForAttachmentId($this->getAttachmentId());
        wp_redirect($redirectUrl);
    }

    /**
     * @since 2.0
     *
     * @return void
     */
    public function adminPostRenameAttachment()
    {
        $redirectUrl = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_rename_attachment')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        $attachmentId = $this->getAttachmentId();

        // $this->reportImageService->generateReportByAttachmentId($attachmentId);

        // try {
        //     $filename = $this->renameFileService->getNameFileWithAttachmentId($attachmentId);
        // } catch (NoRenameFile $e) {
        //     wp_redirect($redirectUrl);

        //     return;
        // }

        // $extension = $this->renameFileService->getExtensionFilenameByAttachmentId($attachmentId);

        imageseo_get_service('UpdateFile')->updateFilename($attachmentId, sprintf('%s.%s', 'big-image', 'jpg'));
        // imageseo_get_service('UpdateFile')->updateFilename($attachmentId, sprintf('%s.%s', $filename, $extension));
        wp_redirect($redirectUrl);
    }
}
