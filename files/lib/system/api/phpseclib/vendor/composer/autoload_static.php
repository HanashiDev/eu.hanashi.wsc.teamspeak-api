<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4ab289022e37aa8698f8c3072b619243
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib3\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib3\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4ab289022e37aa8698f8c3072b619243::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4ab289022e37aa8698f8c3072b619243::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4ab289022e37aa8698f8c3072b619243::$classMap;

        }, null, ClassLoader::class);
    }
}