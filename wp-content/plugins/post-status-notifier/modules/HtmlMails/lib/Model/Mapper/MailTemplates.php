<?php
/**
 * AmazonSimpleAffiliate (ASA2)
 * For more information see http://www.wp-amazon-plugin.com/
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: MailTemplates.php 397 2015-08-16 20:09:46Z timoreithde $
 */ 
class Psn_Module_HtmlMails_Model_Mapper_MailTemplates extends IfwPsn_Wp_Model_Mapper_Abstract
{
    protected static $_instance;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getSingular()
    {
        return 'mail_template';
    }

    public function getPlural()
    {
        return 'mail_templates';
    }

    public function getPerPageId($prefix = '')
    {
        return $prefix . 'per_page_' . $this->getSingular();
    }
}
