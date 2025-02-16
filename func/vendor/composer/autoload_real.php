<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc12b4a1da4e78d9c20e4cf52899ccf6e
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitc12b4a1da4e78d9c20e4cf52899ccf6e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc12b4a1da4e78d9c20e4cf52899ccf6e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitc12b4a1da4e78d9c20e4cf52899ccf6e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
