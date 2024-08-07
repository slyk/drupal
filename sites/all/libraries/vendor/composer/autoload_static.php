<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit07159bef9823f094e4468520420ad1d1
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
        '71b863b70552e163466c17fc2beb6539' => __DIR__ . '/..' . '/alantiller/directus-php-sdk/src/Directus.php',
    );

    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'tps_directus\\' => 13,
            'tps_amqp\\' => 9,
        ),
        'p' => 
        array (
            'phpseclib3\\' => 11,
        ),
        'S' => 
        array (
            'Slations\\DirectusSdk\\' => 21,
        ),
        'P' => 
        array (
            'PhpAmqpLib\\' => 11,
            'ParagonIE\\ConstantTime\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'tps_directus\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../modules/TooPro/tps_directus',
        ),
        'tps_amqp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../modules/TooPro/tps_amqp',
        ),
        'phpseclib3\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'Slations\\DirectusSdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/alantiller/directus-php-sdk/src',
        ),
        'PhpAmqpLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-amqplib/php-amqplib/PhpAmqpLib',
        ),
        'ParagonIE\\ConstantTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/paragonie/constant_time_encoding/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit07159bef9823f094e4468520420ad1d1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit07159bef9823f094e4468520420ad1d1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit07159bef9823f094e4468520420ad1d1::$classMap;

        }, null, ClassLoader::class);
    }
}
