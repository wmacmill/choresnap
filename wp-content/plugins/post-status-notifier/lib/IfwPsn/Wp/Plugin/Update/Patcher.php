<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) ifeelweb.de
 * @version   $Id: Patcher.php 477 2015-10-16 22:07:10Z timoreithde $
 * @package   
 */ 
class IfwPsn_Wp_Plugin_Update_Patcher 
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var array
     */
    protected $_patches = array();

    /**
     * @var IfwPsn_Util_Version
     */
    private $_presentVersion;

    /**
     * @var array
     */
    private $_executionErrors = array();


    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param IfwPsn_Util_Version $presentVersion
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm, IfwPsn_Util_Version $presentVersion)
    {
        $this->_pm = $pm;
        $this->_presentVersion = $presentVersion;
    }

    /**
     *
     * @throws IfwPsn_Wp_Plugin_Bootstrap_Exception
     */
    public function autoUpdate()
    {
        $transient = get_transient($this->_pm->getAbbrLower() . '_auto_update');

        if (empty($transient)) {
            if ($this->isPatchesAvailable()) {
                $this->run();
                $this->_pm->getBootstrap()->getUpdateManager()->refreshPresentVersion();
            }
            set_transient($this->_pm->getAbbrLower() . '_auto_update', 86400);
        }
    }

    /**
     * @param \IfwPsn_Wp_Plugin_Update_Patch_Interface $patch
     */
    public function addPatch(IfwPsn_Wp_Plugin_Update_Patch_Interface $patch)
    {
        array_push($this->_patches, $patch);
    }

    /**
     * @return bool
     */
    public function hasPatches()
    {
        return count($this->_patches) > 0;
    }

    /**
     * @return bool
     */
    public function isPatchesAvailable()
    {
        if ($this->_presentVersion->isLessThan($this->_pm->getEnv()->getVersion()) && $this->hasPatches()) {
            return true;
        }

        return false;
    }

    /**
     * Runs all added patches
     */
    public function run()
    {
        /**
         * @var $patch IfwPsn_Wp_Plugin_Update_Patch_Interface
         */
        foreach ($this->_patches as $patch) {
            try {
                $patch->execute($this->_presentVersion, $this->_pm);
            } catch (IfwPsn_Wp_Plugin_Update_Patch_Exception $e) {
                $this->_addExecutionError(
                    sprintf(__('An error occured in patch "%s"', 'ifw'), $patch->getName()) .': '. $e->getMessage());
            } catch (Exception $e) {
                $this->_addExecutionError(
                    sprintf(__('An unexpected error occured in patch "%s"', 'ifw'), $patch->getName()) .': '. $e->getMessage());
            }
        }
    }

    /**
     * @param $error
     */
    protected function _addExecutionError($error)
    {
        array_push($this->_executionErrors, $error);
    }

    /**
     * @return bool
     */
    public function hasExecutionErrors()
    {
        return count($this->_executionErrors) > 0;
    }

    /**
     * @return array
     */
    public function getExecutionErrors()
    {
        return $this->_executionErrors;
    }

}
