<?php
/**
 * Admin menu bootstrap 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Bootstrap.php 2 2013-03-30 16:04:33Z timoreithde $
 */
require_once dirname(__FILE__) . '/controllers/PsnApplicationController.php';

class Psn_Admin_Menu_Bootstrap extends IfwPsn_Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResources()
    {
    }
}
