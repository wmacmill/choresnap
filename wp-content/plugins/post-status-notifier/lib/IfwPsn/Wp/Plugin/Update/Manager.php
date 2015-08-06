<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Handles update questions
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Manager.php 401 2015-02-22 22:52:23Z timoreithde $
 */ 
class IfwPsn_Wp_Plugin_Update_Manager
{
    /**
     * @var IfwPsn_Wp_Plugin_Manager
     */
    protected $_pm;

    /**
     * @var IfwPsn_Wp_Plugin_Update_Patcher
     */
    private $_patcher;

    /**
     * @var IfwPsn_Util_Version
     */
    private $_presentVersion;




    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm)
    {
        $this->_pm = $pm;

        require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Update/Patcher.php';
        $this->_patcher = new IfwPsn_Wp_Plugin_Update_Patcher($pm, $this->getPresentVersion());
    }

    public function init()
    {
        if ($this->_pm->getConfig()->plugin->autoupdate == 1) {

            $updateApi = IfwPsn_Wp_Plugin_Update_Api_Factory::get($this->_pm);

            // check for custom update message
            IfwPsn_Wp_Proxy_Action::add('in_plugin_update_message-' . $this->_pm->getPathinfo()->getFilenamePath(), array($updateApi, 'getUpdateInlineMessage'), 10, 3);
            IfwPsn_Wp_Proxy_Filter::add('pre_set_site_transient_update_plugins', array($updateApi, 'getUpdateData'));

            if ($this->_pm->isPremium()) {
                // check for premium get update info
                IfwPsn_Wp_Proxy_Filter::add('plugins_api', array($updateApi, 'getPluginInformation'), 10, 3);
            }
        }

        $this->_pm->getBootstrap()->getOptionsManager()->registerExternalOption('present_version');
    }

    /**
     * @param $updateData
     * @return mixed
     */
    public function checkForPremiumUpdate($updateData)
    {
        $plugin_slug = $this->_pm->getPathinfo()->getDirname();

        if (!empty($this->_pm->getConfig()->debug->update)) {
            $this->_pm->getLogger()->debug(' --- Update check data --- ');
            $this->_pm->getLogger()->debug(var_export($updateData, true));
        }

        //Comment out these two lines during testing.
        if (empty($updateData->checked)) {
            return $updateData;
        }

        require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Update/Request.php';
        if (class_exists('IfwPsn_Wp_Plugin_Update_Request')) {

            $request = new IfwPsn_Wp_Plugin_Update_Request($this->_pm);

            $request->setAction('plugin_update_check')
                ->addData('slug', $plugin_slug)
                ->addData('version', $updateData->checked[$this->_pm->getPathinfo()->getFilenamePath()])
                ->addData('lang', IfwPsn_Wp_Proxy_Blog::getLanguage())
            ;

            if ($this->_pm->isPremium()) {
                $license = $this->_pm->getOptionsManager()->getOption('license_code');
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
        }

        return $updateData;
    }

    /**
     * @param $def
     * @param $action
     * @param $args
     * @return bool|mixed|WP_Error
     */
    public function getPluginInfo($def, $action, $args)
    {
        $result = '';
        $plugin_slug = $this->_pm->getPathinfo()->getDirname();

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

        require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Update/Request.php';
        if (class_exists('IfwPsn_Wp_Plugin_Update_Request')) {
            
            $request = new IfwPsn_Wp_Plugin_Update_Request($this->_pm);

            $request->setAction($action)
                ->addData('slug', $plugin_slug)
                ->addData('version', $current_version)
                ->addData('license', $this->_pm->getOptionsManager()->getOption('license_code'))
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
        }

        return $result;
    }

    /**
     * Fires at the end of the update message container in each row of the plugins list table.
     *
     * @param array $plugin_data An array of plugin data.
     * @param $meta_data
     */
    public function onPluginUpdateMessage($plugin_data, $meta_data)
    {
        $plugin_slug = $this->_pm->getPathinfo()->getDirname();

        $request = new IfwPsn_Wp_Plugin_Update_Request($this->_pm);

        $request->setAction('plugin_update_message')
            ->addData('slug', $plugin_slug)
            ->addData('version', $plugin_data['Version'])
            ->addData('lang', IfwPsn_Wp_Proxy_Blog::getLanguage())
        ;

        if ($this->_pm->isPremium()) {
            $license = $this->_pm->getOptionsManager()->getOption('license_code');
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
     * @return IfwPsn_Wp_Plugin_Update_Patcher
     */
    public function getPatcher()
    {
        return $this->_patcher;
    }

    /**
     * @return IfwPsn_Util_Version
     */
    public function getPresentVersion()
    {
        if ($this->_presentVersion == null) {
            require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Util/Version.php';
            $this->_presentVersion = new IfwPsn_Util_Version($this->_pm->getBootstrap()->getOptionsManager()->getOption('present_version'));
        }

        return $this->_presentVersion;
    }

    /**
     * Updates the plugin's option "present_version" to current plugin version
     */
    public function refreshPresentVersion()
    {
        $this->_pm->getBootstrap()->getOptionsManager()->updateOption('present_version', $this->_pm->getEnv()->getVersion());
    }
}
