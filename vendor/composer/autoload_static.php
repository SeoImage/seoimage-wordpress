<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit648e8f0c02b7ede0fc38e30f80e531ee
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'ImageSeo\\' => 9,
            'ImageSeoWP\\' => 11,
        ),
        'C' => 
        array (
            'Cocur\\Slugify\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ImageSeo\\' => 
        array (
            0 => __DIR__ . '/..' . '/imageseo/imageseo-php/src',
        ),
        'ImageSeoWP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Cocur\\Slugify\\' => 
        array (
            0 => __DIR__ . '/..' . '/cocur/slugify/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit648e8f0c02b7ede0fc38e30f80e531ee::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit648e8f0c02b7ede0fc38e30f80e531ee::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
