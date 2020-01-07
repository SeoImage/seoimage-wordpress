<?php

namespace ImageSeoWP\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

abstract class AltFormat
{
    /**
     * @var string
     */
    const ALT_SIMPLE = '[keyword_1] - [keyword_2]';

    /**
     * @var string
     */
    const ALT_POST_TITLE = '[post_title] - [keyword_1]';

    /**
     * @var string
     */
    const ALT_SITE_TITLE = '[site_title] - [keyword_1]';

    /**
     * @var string
     */
    const ALT_PRODUCT_WOOCOMMERCE = '[product_title] - [keyword_1]';

    /**
     * @static
     *
     * @return array
     */
    public static function getFormats()
    {
        $formats = [
            [
                'format'      => self::ALT_SIMPLE,
                'description' => __('We use Artificial Intelligence to generate SEO friendly keywords for your alternative texts. We recommend you to use this format for SEO.', 'imageseo'),
            ],
            [
                'format'      => self::ALT_POST_TITLE,
                'description' => __('We will use your post title and generate one SEO friendly keyword.', 'imageseo'),
            ],
            [
                'format'      => self::ALT_SITE_TITLE,
                'description' => [
                    __('We will use your site title and generate one SEO friendly keyword.', 'imageseo'),
                ],
            ],
        ];

        if (
            is_plugin_active('woocommerce/woocommerce.php')
        ) {
            $formats[] = [
                'format'      => self::ALT_PRODUCT_WOOCOMMERCE,
                'description' => '',
            ];
        }

        return apply_filters('imageseo_alt_formats', $formats);
    }
}