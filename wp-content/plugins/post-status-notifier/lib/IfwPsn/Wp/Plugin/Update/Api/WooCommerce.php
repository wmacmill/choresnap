<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: WooCommerce.php 429 2015-05-25 14:02:06Z timoreithde $
 * @package   
 */ 
class IfwPsn_Wp_Plugin_Update_Api_WooCommerce extends IfwPsn_Wp_Plugin_Update_Api_Abstract
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    protected $_allowedEndpoints = array('am-software-api', 'upgrade-api');

    protected $_productId;

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm, $productId)
    {
        $this->_pm = $pm;
        $this->_productId = $productId;
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
        // slug
        $pluginSlug = $this->_pm->getSlugFilenamePath();

        if (!isset($args->slug) || ($args->slug != $pluginSlug)) {
            return false;
        }

        // Get the current version
        $plugin_info = get_site_transient('update_plugins');

        if (!empty($this->_pm->getConfig()->debug->update)) {
            $this->_pm->getLogger()->debug('Plugin info check:');
            $this->_pm->getLogger()->debug(var_export($plugin_info, true));
        }

        $current_version = $plugin_info->checked[$pluginSlug];

        if (apply_filters('ifw_woocommerce_is_slug_activated-' . $pluginSlug, false)) {

            $activationData = apply_filters('ifw_woocommerce_get_activation_data-'. $pluginSlug, array());

            $request = $this->_getRequest('upgrade-api');

            if ($request instanceof IfwPsn_Wp_Http_Request) {
                $request
                    ->addData('request', 'plugininformation')
                    ->addData('plugin_name', $pluginSlug)
                    ->addData('version', $current_version)
                    ->addData('software_version', $current_version)
                    ->addData('activation_email', $activationData['email'])
                    ->addData('api_key', $activationData['license'])
                    ->addData('domain', $this->_getPlatform())
                    ->addData('instance', $this->_getInstance($activationData['license'], $activationData['email']));
            }

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
    }

    /**
     * @param $updateData
     * @return mixed
     */
    public function getUpdateData($updateData)
    {
        // slug
        $pluginSlug = $this->_pm->getSlugFilenamePath();

        if (!is_plugin_active($pluginSlug) ||
            !$this->_pm->isPremium() ||
            !property_exists($updateData, 'checked') ||
            empty($updateData->checked) ) {
            return $updateData;
        }

        if (!empty($this->_pm->getConfig()->debug->update)) {
            $this->_pm->getLogger()->debug(' --- Update check data '. $pluginSlug . ' --- ');
            $this->_pm->getLogger()->debug(var_export($updateData, true));
        }

        if ((!property_exists($updateData, 'checked') || empty($updateData->checked)) &&
            (int)$this->_pm->getConfig()->plugin->updateTest == 0) {
            return $updateData;
        }

        if (apply_filters('ifw_woocommerce_is_slug_activated-' . $pluginSlug, false)) {

            $activationData = apply_filters('ifw_woocommerce_get_activation_data-'. $pluginSlug, array());
            $localVersion = $updateData->checked[$pluginSlug];

            $request = $this->_getRequest('upgrade-api');

            if ($request instanceof IfwPsn_Wp_Http_Request) {
                $request
                    ->addData('request', 'pluginupdatecheck')
                    ->addData('plugin_name', $pluginSlug)
                    ->addData('version', $localVersion)
                    ->addData('software_version', $localVersion)
                    ->addData('activation_email', $activationData['email'])
                    ->addData('api_key', $activationData['license'])
                    ->addData('domain', $this->_getPlatform())
                    ->addData('instance', $this->_getInstance($activationData['license'], $activationData['email']));
            }

            $response = $request->send();

            if ($response->isSuccess()) {

                $responseBody = $response->getBody();
                $remoteData = unserialize($responseBody);

                if (!empty($this->_pm->getConfig()->debug->update)) {
                    $this->_pm->getLogger()->debug('Update check response:');
                    $this->_pm->getLogger()->debug(var_export($remoteData, true));
                }

                if (is_object($remoteData) && !empty($remoteData) && isset($remoteData->new_version) && !empty($remoteData->new_version)) {

                    $remoteVersion = new IfwPsn_Util_Version((string)$remoteData->new_version);

                    if ($remoteVersion->isGreaterThan($localVersion)) {
                        // Feed the update data into WP updater
                        $updateData->response[$pluginSlug] = $remoteData;
                    }
                }
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
        // slug
        $pluginSlug = $this->_pm->getSlugFilenamePath();

        if ($this->_pm->isPremium()) {
            if (!apply_filters('ifw_woocommerce_is_slug_activated-' . $pluginSlug, false)) {
                if ($this->_pm->getAccess()->isNetworkAdmin()) {
                    $licensePage = network_admin_url($this->_pm->getConfig()->plugin->licensePageNetwork);
                } else {
                    $licensePage = admin_url($this->_pm->getConfig()->plugin->licensePage);
                }
                printf('<div style="padding: 5px 10px; border: 1px dashed red; margin-top: 10px;"><span class="dashicons dashicons-info"></span> %s</div>',
                    sprintf( __('<b>License issue:</b> You have to <a href="%s">active your license</a> to be able to receive updates.', 'ifw'), $licensePage) );
            }
        }
    }

    /**
     * @param $licence_key
     * @param $email
     * @return IfwPsn_Wp_Http_Response|string
     */
    public function getLicenseStatus($licence_key, $email)
    {
        $response = '';
        $request = $this->_getRequest();

        if ($request instanceof IfwPsn_Wp_Http_Request) {
            $request
                ->addData('request', 'status')
                ->addData('email', $email)
                ->addData('licence_key', $licence_key)
                ->addData('platform', $this->_getPlatform())
                ->addData('instance', $this->_getInstance($licence_key, $email));
            ;

            $response = $request->send();
        }

        return $response;
    }

    /**
     * @param $licence_key
     * @param $email
     * @param $version
     * @return IfwPsn_Wp_Http_Response|string
     */
    public function activate($licence_key, $email, $version)
    {
        $response = '';
        $request = $this->_getRequest();

        if ($request instanceof IfwPsn_Wp_Http_Request) {
            $request
                ->addData('request', 'activation')
                ->addData('email', $email)
                ->addData('licence_key', $licence_key)
                ->addData('software_version', $version)
                ->addData('platform', $this->_getPlatform())
                ->addData('instance', $this->_getInstance($licence_key, $email));
            ;

            $response = $request->send();
        }

        return $response;
    }

    /**
     * @param $licence_key
     * @param $email
     * @return IfwPsn_Wp_Http_Response|string
     */
    public function deactivate($licence_key, $email)
    {
        $response = '';
        $request = $this->_getRequest();

        if ($request instanceof IfwPsn_Wp_Http_Request) {
            $request
                ->addData('request', 'deactivation')
                ->addData('email', $email)
                ->addData('licence_key', $licence_key)
                ->addData('platform', $this->_getPlatform())
                ->addData('instance', $this->_getInstance($licence_key, $email));
            ;

            $response = $request->send();
        }

        return $response;
    }

    /**
     * @return IfwPsn_Wp_Http_Request
     */
    protected function _getRequest($endpoint = 'am-software-api')
    {
        $result = null;

        if ($this->_isAllowedEndpoint($endpoint)) {

            $url = $this->_pm->getConfig()->plugin->updateServer;
            $url = add_query_arg('wc-api', $endpoint, $url);
            $url = esc_url_raw($url);

            $request = new IfwPsn_Wp_Http_Request();
            $request->setSendMethod('get');

            $request->setUrl($url);
            $request->addData('product_id', $this->_getProductId());

            $result = $request;
        }

        return $result;
    }

    /**
     * @param $endpoint
     * @return bool
     */
    protected function _isAllowedEndpoint($endpoint)
    {
        return in_array($endpoint, $this->_allowedEndpoints);
    }

    /**
     * A unique, password like hash. Unique for one activation on one platform.
     *
     * @param $licence_key
     * @param $email
     * @return string
     */
    protected function _getInstance($licence_key, $email)
    {
        $format = '%s/%s@%s';
        return md5(sprintf($format, $licence_key, $email, $this->_getPlatform()));
    }

    /**
     * @return string|void
     */
    protected function _getPlatform()
    {
        return IfwPsn_Wp_Proxy_Blog::getUrl();
    }

    protected function _getProductId()
    {
        return urlencode($this->_productId);
    }
}
