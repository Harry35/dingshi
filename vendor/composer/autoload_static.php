<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7ef1858a9b356f19cd64a40be7dd9cbb
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fzaninotto/faker/src/Faker',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7ef1858a9b356f19cd64a40be7dd9cbb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7ef1858a9b356f19cd64a40be7dd9cbb::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7ef1858a9b356f19cd64a40be7dd9cbb::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
