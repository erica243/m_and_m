<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaf08859d7071e232b0d6076acf3c1926
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitaf08859d7071e232b0d6076acf3c1926::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitaf08859d7071e232b0d6076acf3c1926::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitaf08859d7071e232b0d6076acf3c1926::$classMap;

        }, null, ClassLoader::class);
    }
}
