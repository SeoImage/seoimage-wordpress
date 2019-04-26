<?php

namespace ImageSeoWP\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

/**
 *
 * @since 1.0.0
 */
class MediaLibrary
{

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->optionServices = imageseo_get_service('Option');
        $this->reportImageServices   = imageseo_get_service('ReportImage');
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!imageseo_allowed()) {
            return;
        }


        add_filter('manage_media_columns', [$this, 'manageMediaColumns']);
        add_filter('attachment_fields_to_edit', [$this, 'fieldsEdit'], 999, 2);
        add_action('attachment_fields_to_save', [$this, 'saveDataPinterest'], 10, 2);
        add_action('manage_media_custom_column', [$this, 'manageMediaCustomColumn'], 10, 2);

        add_action('wp_ajax_imageseo_media_alt_update', [$this, 'ajaxAltUpdate']);

        add_action('admin_init', [$this, 'metaboxReport']);
        // add_action('admin_menu', [$this, 'addMediaPage']);
        add_action('add_attachment', [$this, 'addAttachment']);
    }

    /**
     * @since 1.0.0
     * @return void
     */
    public function addMediaPage()
    {
        add_media_page('Image SEO', 'Image SEO', 'manage_options', 'imageseo_media_files', [ $this, 'adminMediaFiles' ]);
    }

    /**
     * @since 1.0.0
     * @return void
     */
    public function adminMediaFiles()
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/media_library.php';
    }

    /**
     * @since 1.0.0
     * @param int $postId
     * @return void
     */
    public function addAttachment($attachmentId)
    {
        $activeWriteReport = $this->optionServices->getOption('active_alt_write_upload');

        if (!$activeWriteReport) {
            return;
        }

        $response = $this->reportImageServices->generateReportByAttachmentId($attachmentId);
        if (!$response['success']) {
            wp_redirect($urlRedirect);
        }

        $this->reportImageServices->updateAltAttachmentWithReport($attachmentId, $response['result']);
    }


    /**
     * @since 2.1.0
     * @return void
     */
    public function ajaxAltUpdate()
    {
        $postId = absint($_POST['post_id']);
        $alt = wp_strip_all_tags($_POST['alt']);

        if (!empty($_POST['alt'])) {
            update_post_meta($postId, '_wp_attachment_image_alt', $alt);
        }
    }

    /**
     * @since 1.0.0
     * @param array $formFields
     * @param object $post
     * @return array
     */
    public function fieldsEdit($formFields, $post)
    {
        global $pagenow;

        if ('post.php' === $pagenow) {
            $formFields['imageseo-data-pin-description'] = array(
                'label'         => __('Pinterest description', 'imageseo'),
                'input'         => 'text',
                'value' 		=> get_post_meta($post->ID, '_imageseo_data_pin_description', true),
                'show_in_edit'  => true,
                'show_in_modal' => true,
                'helps' => '&lt;img src="#" data-pin-description="My description" /&gt;'
            );
            $formFields['imageseo-data-pin-url'] = array(
                'label'         => __('Pinterest URL', 'imageseo'),
                'input'         => 'text',
                'value' 		=> get_post_meta($post->ID, '_imageseo_data_pin_url', true),
                'show_in_edit'  => true,
                'show_in_modal' => true,
                'helps' => '&lt;img src="#" data-pin-url="https://imageseo.io" /&gt;'
            );
            $formFields['imageseo-data-pin-id'] = array(
                'label'         => __('Pinterest ID', 'imageseo'),
                'input'         => 'text',
                'value' 		=> get_post_meta($post->ID, '_imageseo_data_pin_id', true),
                'show_in_edit'  => true,
                'show_in_modal' => true,
                'helps' => '&lt;img src="#" data-pin-id="id-pin" /&gt;'
            );
            $formFields['imageseo-data-pin-media'] = array(
                'label'         => __('Pinterest Media', 'imageseo'),
                'input'         => 'text',
                'value' 		=> get_post_meta($post->ID, '_imageseo_data_pin_media', true),
                'show_in_edit'  => true,
                'show_in_modal' => true,
                'helps' => '&lt;img src="#"  data-pin-media="https://example.com/my-image.jpg" /&gt;'
            );
            return $formFields;
        }




        $formFields['imageseo-has-report'] = array(
            'label'         => __('ImageSEO Report', 'imageseo'),
            'input'         => 'html',
            'html'          => '<a id="imageseo-' . $post->ID . '" href="' . esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')) . '" class="button">' . __('View report', 'imageseo') . '</a>',
            'show_in_edit'  => true,
            'show_in_modal' => true,
        );

        return $formFields;
    }

    /**
     * @since 1.0.8
     * @return void
     */
    public function saveDataPinterest($post, $attachment)
    {
        if (isset($attachment['imageseo-data-pin-description'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_description', $attachment['imageseo-data-pin-description']);
        }
        if (isset($attachment['imageseo-data-pin-url'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_url', $attachment['imageseo-data-pin-url']);
        }
        if (isset($attachment['imageseo-data-pin-id'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_id', $attachment['imageseo-data-pin-id']);
        }
        if (isset($attachment['imageseo-data-pin-media'])) {
            update_post_meta($post['ID'], '_imageseo_data_pin_media', $attachment['imageseo-data-pin-media']);
        }

        return $post;
    }

    /**
     * @since 1.0.0
     * @return void
     */
    public function metaboxReport()
    {
        add_meta_box(
            'imageseo-report',
            __('Report ImageSEO', 'imageseo'),
            [$this, 'viewMetaboxReport'],
            'attachment',
            'normal'
        );
    }

    /**
     * @since 1.0.0
     * @param object $post
     * @return void
     */
    public function viewMetaboxReport($post)
    {
        include_once IMAGESEO_TEMPLATES_ADMIN_METABOXES . '/report.php';
    }

    /**
     * Activate array
     * @since 1.0.0
     * @return void
     */
    public function manageMediaColumns($columns)
    {
        $columns['imageseo'] = __('ImageSEO', 'imageseo');

        return $columns;
    }



    /**
     * @since  1.0
     * @param string $columnName   Name of the custom column.
     * @param int    $attachment_id Attachment ID.
     * @return void
     */
    public function manageMediaCustomColumn($columnName, $attachmentId)
    {
        if ($columnName !== 'imageseo') {
            return;
        }

        $alt = wp_strip_all_tags($this->reportImageServices->getAlt($attachmentId));
        $haveAlreadyReport = $this->reportImageServices->haveAlreadyReportByAttachmentId($attachmentId);
        $autoWriteAlt = $this->optionServices->getOption('active_alt_write_with_report');

        $text = __('Generate alt', 'imageseo');
        if ($haveAlreadyReport && !empty($alt)) {
            $text = __('(Re) Rewrite alt', 'imageseo');
        } ?>
        <div class="media-column-imageseo">
            <?php
            if (empty($alt)) {
                ?>
                <div class="media-column-imageseo--no_alt">
                    <span class="dashicons dashicons-dismiss"></span>
                    <span class="text"><?php esc_html_e('This image has not alt attribute !', 'imageseo'); ?>
                </div>
                <?php
            } ?>
					<div class="media-column-imageseo--actions">
						<a id="imageseo-rename-file<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_rename_attachment&attachment_id=' . $attachmentId)); ?>" class="button button-primary">
							<?php echo __('Rename file', 'imageseo'); ?>
						</a>
						<a id="imageseo-analyze-<?php echo $attachmentId; ?>" href="<?php echo esc_url(admin_url('admin-post.php?action=imageseo_report_attachment&attachment_id=' . $attachmentId)); ?>" class="button button-primary">
							<?php echo $text; ?>
						</a>
					</div>
				<div id="wrapper-imageseo-<?php echo $attachmentId; ?>" class="wrapper-imageseo-input-alt">
					<input
						type="text"
						name="imageseo-alt"
						data-id="<?php echo $attachmentId; ?>"
						class="imageseo-alt-ajax large-text"
						id="imageseo-alt-<?php echo $attachmentId; ?>"
						value="<?php echo $alt; ?>"
						placeholder="<?php echo esc_html('Enter alt attribute', 'imageseo'); ?>"
					/>
					<button class="button" data-id="<?php echo $attachmentId; ?>">
						<span><?php _e('Submit', 'imageseo'); ?></span>
						<div class="imageseo-loading imageseo-loading--library" style="display:none"></div>
					</button>
				</div>
			</div>
        <?php
    }
}
