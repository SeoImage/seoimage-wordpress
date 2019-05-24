<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use ImageSeoWP\Exception\NoRenameFile;

/**
 * @since 1.0.0
 */
class AjaxMediaReport
{

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->reportImageServices   = imageseo_get_service('ReportImage');
        $this->optionServices   = imageseo_get_service('Option');
        $this->renameFileServices   = imageseo_get_service('RenameFile');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }

        add_action('admin_post_imageseo_report_attachment', [$this, 'adminPostReportAttachment']);
        add_action('wp_ajax_imageseo_report_attachment', [$this, 'ajaxReportAttachment']);

        add_action('admin_post_imageseo_rename_attachment', [$this, 'adminPostRenameAttachment']);
        // add_action('wp_ajax_imageseo_rename_attachment', [$this, 'ajaxReportAttachment']);
    }

    /**
     * @return int
     */
    protected function getAttachmentId()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return (int) $_GET['attachment_id'];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return(int) $_POST['attachment_id'];
        }
    }

    /**
     * @param array $query
     * @return array
     */
    protected function generateReportAttachment($query = [])
    {
        if (!isset($_GET['attachment_id']) && !isset($_POST['attachment_id'])) {
            return [
                'success' => false
            ];
        }

        $attachmentId = $this->getAttachmentId();
		$get_cache_request = apply_filters('imageseo_get_cache_request', true);
		$report = $this->reportImageServices->getReportByAttachmentId($attachmentId);
        if ($report && $get_cache_request) {
            return [
                "success" => true,
                "result" => $report
            ];
		}
		        
		return $this->reportImageServices->generateReportByAttachmentId($attachmentId, $query);

	}
	
	public function getForceReport(){
		$force = false;
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset( $_GET['force'] )  && $_GET['force'] == 1 ) {
            $force = true;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['force'] ) && $_POST['force'] == 1 ) {
            $force = true;
		}
		
		return $force;
	}

    /**
     * @return void
     */
    public function adminPostReportAttachment()
    {
		
		$force = $this->getForceReport();
        $response = $this->generateReportAttachment(['force' => $force]);
        $urlRedirect = admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit');
        if (!$response['success']) {
            wp_redirect($urlRedirect);
            return;
        }

        $this->reportImageServices->updateAltAttachmentWithReport($this->getAttachmentId(), $response['result']);

        wp_redirect($urlRedirect);
    }

    /**
     * @return void
     */
    public function adminPostRenameAttachment()
    {
        $this->renameFileServices->renameAttachment($this->getAttachmentId());
        wp_redirect(admin_url('post.php?post=' . $this->getAttachmentId() . '&action=edit'));
    }

    /**
     * @return void
     */
    public function ajaxReportAttachment()
    {
        $currentBulk = (int) $_POST['current'];
        $total = (int) $_POST['total'];

        try {
            $response = $this->generateReportAttachment();
        } catch (\Exception $e) {
            wp_send_json_error([
                "code" => "error_generate_report"
            ]);
            exit;
        }
        if (!$response['success']) {
            wp_send_json_error($response);
            exit;
        }

        $attachmentId = $this->getAttachmentId();
        $report = $response['result'];

        $updateAlt = (isset($_POST['update_alt']) && $_POST['update_alt'] === 'true') ? true : false;
        $updateAltNotEmpty = (isset($_POST['update_alt_not_empty']) && $_POST['update_alt_not_empty'] === 'true') ? true : false;
        $renameFile = (isset($_POST['rename_file']) && $_POST['rename_file'] === 'true') ? true : false;
        $currentAlt = $this->reportImageServices->getAlt($attachmentId);
        $currentFile =  wp_get_attachment_image_src($attachmentId, 'small');
        $altGenerate = $this->reportImageServices->getAltValueAttachmentWithReport($report);

        $currentNameFile = '';
        if (!empty($currentFile)) {
            $currentNameFile = basename($currentFile[0]);
        }


        if ($updateAlt) {
            if (!$currentAlt || $updateAltNotEmpty) {
                $this->reportImageServices->updateAltAttachmentWithReport($attachmentId, $report);
            }
        }

		$newFilePath = false;
        if ($renameFile) {
			$this->renameFileServices->renameAttachment($attachmentId);
			$file =  wp_get_attachment_image_src($attachmentId, 'small');
			if (!empty($file)) {
				$newFilePath = basename($file[0]);
			}
        }

        $file =  wp_get_attachment_image_src($attachmentId, 'small');

        if ($currentBulk+1 < $total) {
            update_option('_imageseo_current_processed', $currentBulk);
        } elseif ($currentBulk+1 === $total) {
            delete_option('_imageseo_current_processed');
        }

        $srcFile = '';
        $nameFile = '';
        if (!empty($file)) {
            $srcFile = $file[0];
            $nameFile = basename($srcFile);
		}
		
		if(!$newFilePath){
			$basenameWithoutExt = explode('.', $nameFile)[0];
			try {
				$newFilePath = sprintf( '%s.%s', $this->renameFileServices->getNameFileWithAttachmentId($attachmentId), explode('.', $nameFile)[1] );
			} catch (NoRenameFile $e) {
				$newFilePath = $nameFile;
			}
		}

        wp_send_json_success([
            'src' => $report['src'],
            'current_alt' => $currentAlt,
			'alt_generate' => $altGenerate,
			'file_generate' => $newFilePath,
            'file' => $srcFile,
            'current_name_file' => $currentNameFile,
            'name_file' => $nameFile

        ]);
    }
}
