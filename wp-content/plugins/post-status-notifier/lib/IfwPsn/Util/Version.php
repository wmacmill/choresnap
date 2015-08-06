<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Version compare helper class
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id: Version.php 215 2014-01-08 01:37:51Z timoreithde $
 * @package   IfwPsn_Util
 */ 
class IfwPsn_Util_Version 
{
    private $_version;

    /**
     * @param $version
     */
    public function __construct($version)
    {
        $this->_version = $version;
    }

    /**
     * @param $version
     * @return bool
     */
    public function isGreaterThan($version)
    {
        return version_compare($this->_version, $version) === 1;
    }

    /**
     * @param $version
     * @return bool
     */
    public function isGreaterOrEqualThan($version)
    {
        return $this->isGreaterThan($version) or $this->equals($version);
    }

    /**
     * @param $version
     * @return bool
     */
    public function isLessThan($version)
    {
        return version_compare($this->_version, $version) === -1;
    }

    /**
     * @param $version
     * @return bool
     */
    public function isLessOrEqualThan($version)
    {
        return $this->isLessThan($version) or $this->equals($version);
    }

    /**
     * @param $version
     * @return bool
     */
    public function equals($version)
    {
        return version_compare($this->_version, $version) === 0;
    }
}
