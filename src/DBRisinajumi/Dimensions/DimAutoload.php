<?php
namespace DBRisinajumi\Dimensions;

class DimAutoload
{
    public static function register()
    {

        return spl_autoload_register(array('\\DBRisinajumi\\Dimensions\\DimAutoload', 'load'));
    }

    public static function load($class_name)
    {
        $sFile = ltrim($class_name, '\\');
        $sFile = dirname(dirname(dirname(__FILE__))).'/'.str_replace('\\', '/', $class_name).'.php';
        if (file_exists($sFile)) {
            require $sFile;

            return true;
        }

        return false;
    }
}

