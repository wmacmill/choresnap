<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Log.php 397 2015-08-16 20:09:46Z timoreithde $
 */ 
class Psn_Module_Logger_Model_Log extends IfwPsn_Wp_Plugin_Logger_Model
{
    /**
     * @var string
     */
    public static $_table = 'psn_log';

    /**
     * @return string
     */
    public static function getSingular()
    {
        return 'log';
    }

    /**
     * @return string
     */
    public static function getPlural()
    {
        return 'logs';
    }
}

