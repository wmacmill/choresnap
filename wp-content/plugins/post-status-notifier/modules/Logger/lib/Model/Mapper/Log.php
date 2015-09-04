<?php
/**
 * AmazonSimpleAffiliate (ASA2)
 * For more information see http://www.wp-amazon-plugin.com/
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Log.php 398 2015-08-17 21:50:58Z timoreithde $
 */ 
class Psn_Module_Logger_Model_Mapper_Log extends IfwPsn_Wp_Model_Mapper_Abstract
{
    protected static $_instance;

    /**
     * @return Psn_Module_Logger_Model_Mapper_Log
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return string
     */
    public function getSingular()
    {
        return 'log';
    }

    /**
     * @return string
     */
    public function getPlural()
    {
        return 'logs';
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function getPerPageId($prefix = '')
    {
        return $prefix . 'per_page_' . $this->getSingular();
    }

}
