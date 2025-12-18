<?php
/**
 * @version $Id: Log.php 
 * @package simpleicalblock
 * @copyright Copyright (C) 2025 -2026 A.H.C. Waasdorp, All rights reserved.
 * @license GNU General Public License version 3 or later
 * @author url: https://www.waasdorpsoekhan.nl
 * @author email contact@waasdorpsoekhan.nl
 * @developer A.H.C. Waasdorp
 * Log to standard error Log for plugin simpleicalblock
 * @since  3.0
 * 3.0.0 remove messages to front-end, replaced by Log, distribute long messages over more log lines.
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;
// no direct access
defined('ABSPATH') or die ('Restricted access');

class Log
{
/**
 * Describes PSR-3 log levels.
 */
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
 /**
  * max length log message line
  */   
   const SIB_MSG_LEN = 1024; 
    /**
     * Mapping array to map a PSR-3 level to an ascending integer Joomla priority.
     *
     * @var    array
     * @since  3.0.0
     */
    protected static $priorityMap = [
        self::EMERGENCY => 1,
        self::ALERT     => 2,
        self::CRITICAL  => 4,
        self::ERROR     => 8,
        self::WARNING   => 16,
        self::NOTICE    => 32,
        self::INFO      => 64,
        self::DEBUG     => 128,
        'ALL'=> 30719,        
    ];
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message or Stringable
     * @param mixed[] $context
     *
     */
    static function log($level, $message, array $context = [])
    {
        if ( ( WP_DEBUG && WP_DEBUG_LOG) ) {
            if (!is_string($message)) $message = print_r ($message, true);
            if (empty(self::$priorityMap[$level])) $level = self::NOTICE;
            $minlevelnr = (empty(self::$priorityMap[$level])) ? self::$priorityMap[self::ERROR] : self::$priorityMap[WP_DEBUG_MINIMUM_LEVEL] ;
            if ((!empty($message)) && (!empty($context))) $message = self::interpolate($message,$context);
            if (empty($context['category'])) $context['category'] = 'simple-ical-block';
            if ( $minlevelnr >= self::$priorityMap[$level] ) {
    //            $content = date('Y-m-d H:i:sP' ) . Chr(9);
                $content = strtoupper( $level ) . Chr(9);
    //            $content .= 'clientipadddress' .Chr(9);
                $content .= strtolower( $context['category']) . Chr(9);
                $content .= $message;
                if (self::SIB_MSG_LEN >= strlen($content)){
                    error_log( $content );
                }
                else {
                    $messages = str_split($content,self::SIB_MSG_LEN);
                    foreach ($messages as $key=>$val) {
                        error_log( $val );
                    }
                }
            }
        }
    }
    /**
     * Interpolates context values into the message placeholders.
     * v3.0.0 20251214 start with copy from andrewwoods https://github.com/andrewwoods/wp-debug-logger/tree/main  
     *
     * @param string $message The content for the debug log.
     * @param array $context
     *
     * @return string
     */
    static function interpolate( string $message, array $context = array() ) : string {
        
        $replace = array();
        foreach ( $context as $key => $val ) {
            // check that the value can be cast to string
            if ( ! is_array( $val ) && ( ! is_object( $val ) || method_exists( $val, '__toString' ) ) ) {
                $replace[ '{' . $key . '}' ] = $val;
            }
        }
        
        // interpolate replacement values into the message and return
        return strtr( $message, $replace );
    }
    
}

