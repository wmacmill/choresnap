<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Log
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FactoryInterface.php 232 2014-03-17 23:45:57Z timoreithde $
 */

/**
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Log
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FactoryInterface.php 232 2014-03-17 23:45:57Z timoreithde $
 */
interface IfwPsn_Vendor_Zend_Log_FactoryInterface
{
    /**
     * Construct a IfwPsn_Vendor_Zend_Log driver
     *
     * @param  array|IfwPsn_Vendor_Zend_Config $config
     * @return IfwPsn_Vendor_Zend_Log_FactoryInterface
     */
    static public function factory($config);
}
