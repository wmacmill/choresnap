<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Admin.php 206 2013-09-10 22:04:24Z timoreithde $
 */ 
class IfwPsn_Wp_Proxy_Admin
{
    /**
     * Alias for get_admin_url()
     *
     * @param null|string $blog_id
     * @param string $blog_id
     * @param string $scheme
     * @return string
     */
    public static function getUrl($blog_id = null, $blog_id = '', $scheme = 'admin')
    {
        return get_admin_url($blog_id, $blog_id, $scheme);
    }

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param $controller
     * @param string $action
     * @param null $page
     * @param array $extra
     * @return string
     */
    public static function getMenuUrl(IfwPsn_Wp_Plugin_Manager $pm, $controller, $action='index', $page=null, $extra = array())
    {
        if ($page == null) {
            $page = $pm->getPathinfo()->getDirname();
        }

        $urlOptions = array_merge(array(
            $pm->getConfig()->getControllerKey() => $controller,
            $pm->getConfig()->getActionKey() => $action,
            'page' => $page
        ), $extra);

        $router = IfwPsn_Zend_Controller_Front::getInstance()->initRouter($pm)->getRouter();
        return $router->assemble($urlOptions, 'requestVars');
    }

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param $controller
     * @param string $action
     * @param null $page
     * @param array $extra
     * @return string
     */
    public static function getAdminPageUrl(IfwPsn_Wp_Plugin_Manager $pm, $page, $controller, $action='index', $extra = array())
    {
        $urlOptions = array_merge(array(
            $pm->getConfig()->getControllerKey() => $controller,
            $pm->getConfig()->getActionKey() => $action,
            'adminpage' => $page
        ), $extra);

        $router = IfwPsn_Zend_Controller_Front::getInstance()->initRouter($pm)->getRouter();
        return $router->assemble($urlOptions, 'requestVars');
    }


    public static function getOptionsBaseUrl()
    {
        return 'options-general.php';
    }

    public static function getAdminPageBaseUrl()
    {
        return IfwPsn_Wp_Proxy_Blog::getSiteUrl() . '/wp-admin/admin.php';
    }
}
