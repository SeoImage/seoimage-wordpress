<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit46a5c8cc15f9576edeb95e147c229ec6
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit46a5c8cc15f9576edeb95e147c229ec6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit46a5c8cc15f9576edeb95e147c229ec6::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
