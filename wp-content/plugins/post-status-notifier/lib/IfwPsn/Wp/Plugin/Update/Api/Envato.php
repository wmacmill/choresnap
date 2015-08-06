<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Envato.php 429 2015-05-25 14:02:06Z timoreithde $
 * @package   
 */ 
class IfwPsn_Wp_Plugin_Update_Api_Envato extends IfwPsn_Wp_Plugin_Update_Api_Abstract
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var IfwPsn_Wp_Http_Request
     */
    protected $_request;


    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;
    }

    /**
     * Request for plugin information
     *
     * @param $def
     * @param $action
     * @param $args
     * @return mixed
     */
    public function getPluginInformation($def, $action, $args)
    {
        $result = '';
        $plugin_slug = $this->_pm->getSlug();

        if (!isset($args->slug) || ($args->slug != $plugin_slug)) {
            return false;
        }

        // Get the current version
        $plugin_info = get_site_transient('update_plugins');

        if (!empty($this->_pm->getConfig()->debug->update)) {
            $this->_pm->getLogger()->debug('Plugin info check:');
            $this->_pm->getLogger()->debug(var_export($plugin_info, true));
        }

        $current_version = $plugin_info->checked[$this->_pm->getPathinfo()->getFilenamePath()];

        // create request
        $request = $this->_getRequest();

        $request
            ->addData('action', $action)
            ->addData('slug', $plugin_slug)
            ->addData('version', $current_version)
            ->addData('license', $this->_getLicenseCode())
            ->addData('lang', IfwPsn_Wp_Proxy_Blog::getLanguage())
        ;

        $response = $request->send();

        if ($response->isSuccess()) {

            $responseBody = $response->getBody();
            $result = unserialize($responseBody);

            if ($result === false) {
                $result = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
            }

        } else {

            $result = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="javascript:void(0)" onclick="document.location.reload(); return false;">Try again</a>'), $response->getErrorMessage());
        }

        if (!empty($this->_pm->getConfig()->debug->update)) {
            $this->_pm->getLogger()->debug(' --- Plugin info check response --- ');
            $this->_pm->getLogger()->debug(var_export($response, true));
        }

        return $result;
    }

    /**
     * @param $updateData
     * @return mixed
     */
    public function getUpdateData($updateData)
    {
        $plugin_slug = $this->_pm->getSlug();

        if (!is_plugin_active($this->_pm->getSlugFilenamePath()) ||
            !$this->_pm->isPremium() ||
            !property_exists($updateData, 'checked') ||
            empty($updateData->checked) ) {
            return $updateData;
        }

        if (!empty($this->_pm->getConfig()->debug->update)) {
            $this->_pm->getLogger()->debug(' --- Update check data '. $plugin_slug . ' --- ');
            $this->_pm->getLogger()->debug(var_export($updateData, true));
        }

        // create request
        $request = $this->_getRequest();

        $request
            ->addData('action', 'plugin_update_check')
            ->addData('slug', $plugin_slug)
            ->addData('version', $updateData->checked[$this->_pm->getPathinfo()->getFilenamePath()])
            ->addData('lang', IfwPsn_Wp_Proxy_Blog::getLanguage())
        ;

        if ($this->_pm->isPremium()) {
            $license = $this->_getLicenseCode();
            $request->addData('license', $license);
        }

        $response = $request->send();

        if ($this->_pm->isPremium() && $response->isSuccess()) {

            $responseBody = $response->getBody();
            $responseBody = unserialize($responseBody);

            if (!empty($this->_pm->getConfig()->debug->update)) {
                $this->_pm->getLogger()->debug('Update check response:');
                $this->_pm->getLogger()->debug(var_export($responseBody, true));
            }

            if (is_object($responseBody) && !empty($responseBody)) {
                // Feed the update data into WP updater
                $updateData->response[$this->_pm->getPathinfo()->getFilenamePath()] = $responseBody;
            }
        }

        return $updateData;
    }

    /**
     * Fires at the end of the update message container in each row of the plugins list table.
     *
     * @param array $plugin_data An array of plugin data.
     * @param $meta_data
     */
    public function getUpdateInlineMessage($plugin_data, $meta_data)
    {
        $plugin_slug = $this->_pm->getSlug();

        $request = $this->_getRequest();

        $request
            ->addData('action', 'plugin_update_message')
            ->addData('slug', $plugin_slug)
            ->addData('version', $plugin_data['Version'])
            ->addData('lang', IfwPsn_Wp_Proxy_Blog::getLanguage())
        ;

        if ($this->_pm->isPremium()) {
            $license = $this->_getLicenseCode();
            $request->addData('license', $license);
        }

        $response = $request->send();

        if ($response->isSuccess()) {
            if ($this->_pm->isPremium() && empty($license)) {
                printf('<div style="padding: 5px 10px; border: 1px dashed red; margin-top: 10px;">%s</div>',
                    sprintf( __('You have to enter your plugin <b>license code</b> in the <a href="%s">plugin options</a> to be able to download this update!', 'ifw'), $this->_pm->getConfig()->plugin->optionsPage) );
            }
            echo $response->getBody();
        }
    }

    /**
     * @return IfwPsn_Wp_Http_Request
     */
    protected function _getRequest()
    {
        if ($this->_request === null) {
            $this->_request = new IfwPsn_Wp_Http_Request();

            $this->_request->setUrl($this->_pm->getConfig()->plugin->updateServer);
            $this->_request->addData('api-key', md5(IfwPsn_Wp_Proxy_Blog::getUrl()));
            $this->_request->addData('referrer', IfwPsn_Wp_Proxy_Blog::getUrl());

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $this->_request->addData('browser_user_agent', $_SERVER['HTTP_USER_AGENT']);
            }
        }

        return $this->_request;
    }

    /**
     * @return mixed|void
     */
    protected function _getLicenseCode()
    {
        return IfwPsn_Wp_Proxy_Filter::apply('envato_license_code', $this->_pm->getOptionsManager()->getOption('license_code'));
    }

}
