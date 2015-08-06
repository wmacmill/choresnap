<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Factory.php 401 2015-02-22 22:52:23Z timoreithde $
 * @package   
 */ 
class IfwPsn_Wp_Plugin_Update_Api_Factory 
{
    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @return IfwPsn_Wp_Plugin_Update_Api_Envato|IfwPsn_Wp_Plugin_Update_Api_WooCommerce
     */
    public static function get(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $updateApiName = $pm->getConfig()->plugin->updateApi;

        switch ($updateApiName) {

            case 'woocommerce':

                require_once $pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Update/Api/WooCommerce.php';
                $updateApi = new IfwPsn_Wp_Plugin_Update_Api_WooCommerce($pm, $pm->getConfig()->plugin->id);
                break;

            default:
                require_once $pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Update/Api/Envato.php';
                $updateApi = new IfwPsn_Wp_Plugin_Update_Api_Envato($pm);
        }

        return $updateApi;
    }
}
