<?php

namespace ImageSeoWP\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Helpers\ServerSoftware;

class Support
{
    public function __construct()
    {
        $this->renameFileServices = imageseo_get_service('RenameFile');
        $this->htaccessService = imageseo_get_service('Htaccess');
    }

    public function hooks()
    {
        add_action('admin_post_imageseo_backup_attachment', [$this, 'backupAttachment']);
        add_action('admin_post_imageseo_reset_htaccess', [$this, 'resetHtaccess']);
    }

    public function backupAttachment()
    {
        $redirectUrl = admin_url('admin.php?page=imageseo-options&tab=support');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_backup_attachment')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!isset($_POST['attachmentId'])) {
            wp_redirect($redirectUrl);
            exit;
        }
        $attachmentId = (int) $_POST['attachmentId'];
        $fileBackup = get_post_meta($attachmentId, '_imageseo_rename_file_backup', true);

        if (empty($fileBackup)) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!isset($fileBackup['backup_filename'])) {
            wp_redirect($redirectUrl);
            exit;
        }

        $this->renameFileServices->updateFilename($attachmentId, $fileBackup['backup_filename'], null, ['backup' => true]);

        delete_post_meta($attachmentId, '_imageseo_rename_file_backup');

        wp_redirect($redirectUrl);
    }

    public function resetHtaccess()
    {
        $redirectUrl = admin_url('admin.php?page=imageseo-options&tab=support');

        if (!wp_verify_nonce($_GET['_wpnonce'], 'imageseo_reset_htaccess')) {
            wp_redirect($redirectUrl);
            exit;
        }

        if (!current_user_can('manage_options')) {
            wp_redirect($redirectUrl);
            exit;
        }

        delete_option('_imageseo_redirect_images');

        if (ServerSoftware::isApache() && $this->htaccessService->isWritable()) {
            $this->htaccessService->save('');
        }

        wp_redirect($redirectUrl);
    }
}
