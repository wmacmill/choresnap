<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Interface.php 395 2015-02-19 22:33:40Z timoreithde $
 * @package   
 */
interface IfwPsn_Wp_Plugin_Update_Api_Interface 
{
    /**
     * Request for plugin information
     *
     * @param $def
     * @param $action
     * @param $args
     * @return mixed
     */
    public function getPluginInformation($def, $action, $args);

    /**
     * @param $updateData
     * @return mixed
     */
    public function getUpdateData($updateData);

    /**
     * Fires at the end of the update message container in each row of the plugins list table.
     *
     * @param array $plugin_data An array of plugin data.
     * @param $meta_data
     */
    public function getUpdateInlineMessage($plugin_data, $meta_data);
}
