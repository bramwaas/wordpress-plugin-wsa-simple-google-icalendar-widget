<?php
/*
 * Classloader.php
 *
 * very simple classloader for this project created to load classes only when needed.
 * 2.5.1 
 * @package Simple Google iCalendar Block
 * @author Bram Waasdorp <bram@waasdorpsoekhan.nl>
 * @copyright Copyright (c) 2024 - 2024, Bram Waasdorp
 * 
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
class Classloader {
    
    /**
     * register classloader.
     *
     * @return  void
     *
     * @since 2.5.1
     */
     public static function register()
        {
            spl_autoload_register(__NAMESPACE__ .'\Classloader::load');
        }
        /**
         * load class
         *
         * @param   string $class class to load
         * @return  boolean true on succes.
         *
         * @since 2.5.1
         */
        public static function load($class)
        {
//          echo '<!-- Classloader::load' . PHP_EOL . 'Class:' . $class;
//          echo PHP_EOL . '__DIR__:' . __DIR__;
//          echo PHP_EOL . '__NAMESPACE__:' . __NAMESPACE__;
         
         $file = str_replace([__NAMESPACE__, '\\'], [__DIR__, DIRECTORY_SEPARATOR], $class).'.php';
//          echo PHP_EOL . '$file:' . $file;
//          echo PHP_EOL . ' -->' . PHP_EOL;
            if (file_exists($file)) {
                require $file;
//           echo '<!-- Classloader::load' . PHP_EOL . 'Class:' . $class;
//           echo PHP_EOL . '$file:' . $file;
//           echo PHP_EOL . 'loaded -->' . PHP_EOL;
                return true;
            }
            return false;
        }
    
}
