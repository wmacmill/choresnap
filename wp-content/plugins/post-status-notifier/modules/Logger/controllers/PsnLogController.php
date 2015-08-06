<?php
/**
 * Index controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: PsnLogController.php 307 2014-08-25 19:38:39Z timoreithde $
 * @package  IfwPsn_Wp
 */
class Logger_PsnLogController extends PsnApplicationController
{
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Vendor_Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {

        if ($this->_request->getActionName() == 'index') {

            $this->enqueueScripts();


            if ( $this->_request->getPost('action') == 'delete' && is_array($this->_request->getPost('log')) ) {

                // bulk action delete
                $this->_bulkDelete($this->_request->getPost('log'));

            } else if ( $this->_request->getPost('action') == 'clear') {

                // bulk clear log
                $this->_pm->getLogger(Psn_Logger_Bootstrap::LOG_NAME)->clear();

            } else if ( $this->_request->getPost('action') == 'clear_type_mail') {

                // bulk clear type mail
                $this->_pm->getLogger(Psn_Logger_Bootstrap::LOG_NAME)->clear(array('type' => Psn_Logger_Bootstrap::LOG_TYPE_SENT_MAIL));

            } else if ( $this->_request->getPost('action') == 'clear_type_log') {

                // bulk clear type log
                $this->_pm->getLogger(Psn_Logger_Bootstrap::LOG_NAME)->clear(array('type' => Psn_Logger_Bootstrap::LOG_TYPE_INFO));

            }
        }
    }

    public function onBootstrap()
    {
        if ($this->_request->getActionName() == 'index') {
            require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Screen/Option/PerPage.php';

            $this->_perPage = new IfwPsn_Wp_Plugin_Screen_Option_PerPage($this->_pm, __('Items per page', 'ifw'), 'psn_logs_per_page');
        }
    }

    /**
     * 
     */
    public function indexAction()
    {
        require_once $this->_pm->getPathinfo()->getRootLib() . 'IfwPsn/Wp/Plugin/Menu/Help.php';

        // set up contextual help
        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Log', 'psn_log'))
            ->setHelp($this->_getHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $listTable = new Psn_Module_Logger_ListTable_Log($this->_pm);
        $listTable->setItemsPerPage($this->_perPage->getOption());

        $this->view->listTable = $listTable;
    }

    /**
     * @param array $items
     */
    protected function _bulkDelete(array $items)
    {
        foreach($items as $id) {
            IfwPsn_Wp_ORM_Model::factory('Psn_Module_Logger_Model_Log')->find_one((int)$id)->delete();
        }
    }

    /**
     * @param array $priority
     */
    protected function _bulkClear($priority = null)
    {
        $this->_pm->getLogger()->clear($priority);
    }
    
    /**
     *
     * @return string
     */
    protected function _getHelpText()
    {
        return sprintf(__('Please consider the documentation page <a href="%s" target="_blank">%s</a> for more information.', 'ifw'),
            'http://docs.ifeelweb.de/post-status-notifier/log.html',
            __('Log', 'psn_log'));
    }
    
    /**
     *
     * @return string
     */
    protected function _getHelpSidebar()
    {
        $sidebar = '<p><b>' . __('For more information:', 'ifw') . '</b></p>';
        $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Plugin homepage', 'ifw') . '</a></p>', 
            $this->_pm->getEnv()->getHomepage());
        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Documentation', 'ifw') . '</a></p>',
                $this->_pm->getConfig()->plugin->docUrl);
        }
        return $sidebar;
    }

    public function enqueueScripts()
    {
        IfwPsn_Wp_Proxy_Script::loadAdmin('jquery');
        IfwPsn_Wp_Proxy_Script::loadAdmin('jquery-ui-core');
        IfwPsn_Wp_Proxy_Script::loadAdmin('jquery-ui-dialog');

        IfwPsn_Wp_Proxy_Style::loadAdmin('wp-jquery-ui-dialog');
    }
}
