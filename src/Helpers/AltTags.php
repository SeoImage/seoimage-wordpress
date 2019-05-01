<?php

namespace ImageSeoWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class AltTags
{

    /**
     * @var string
     */
    const ALT_AUTO_CONTEXT = '%alt_auto_context%';

    /**
     * @var string
     */
    const ALT_AUTO_REPRESENTATION = '%alt_auto_representation%';

    /**
     * @var string
     */
    const SITE_TITLE = '%site_title%';

    /**
     * Get tags constant
     * @static
     * @return array
     */
    public static function getTags()
    {
        return [
            self::SITE_TITLE,
            self::ALT_AUTO_CONTEXT,
            self::ALT_AUTO_REPRESENTATION
        ];
    }
}
