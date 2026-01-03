<?php
/*
 * Classloader.php
 *
 * very simple classloader for this project created to load classes only when needed.
 * works for PSR-4 name conventions and use namespace in all php sources
 * 3.0.0
 * @package Simple Google iCalendar Widget
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2024 - 2026, Bram Waasdorp
 *
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
// no direct access
defined('ABSPATH') or die ('Restricted access');

class Classloader
{

    /**
     * register classloader.
     *
     * @return void
     *
     * @since 2.6.0
     */
    public static function register()
    {
        spl_autoload_register(__NAMESPACE__ . '\Classloader::load');
    }

    /**
     * load class (if contained in plugins namespace)
     *
     * @param string $class class to load
     * 
     * @return boolean true on succes.
     *        
     * @since 2.6.0
     */
    public static function load($class)
    {
        if (stripos($class,  __NAMESPACE__) !== 0) return false;
        $file = str_replace([
            __NAMESPACE__,
            '\\'
        ], [
            __DIR__,
            DIRECTORY_SEPARATOR
        ], $class) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
