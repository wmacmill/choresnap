<?php
/**
 * Abstract controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: PsnDeferredsendingAbstractController.php 334 2014-11-08 13:46:08Z timoreithde $
 * @package  IfwPsn_Wp
 */
abstract class DeferredSending_PsnDeferredsendingAbstractController extends PsnApplicationController
{
    /**
     * (non-PHPdoc)
     * @see IfwPsn_Vendor_Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        if ($this->_request->getActionName() == 'index') {

            $this->enqueueScripts();

            if (isset($_POST['action']) && $_POST['action'] != '-1') {
                $action = $this->_request->getPost('action');
            } elseif (isset($_POST['action2']) && $_POST['action2'] != '-1') {
                $action = $this->_request->getPost('action2');
            } else {
                $action = false;
            }

            if ( $action == 'delete' && is_array($this->_request->getPost($this->_itemPostId)) ) {
                // bulk action delete
                $this->_bulkDelete($this->_request->getPost($this->_itemPostId));

            } else if ( $action == 'reset' ) {
                // bulk action export
                $this->_reset();

            } else if ( $action == 'export' && is_array($this->_request->getPost($this->_itemPostId)) ) {
                // bulk action export
                $this->_bulkExport($this->_request->getPost($this->_itemPostId));
            }

        }
    }

    public function onBootstrap()
    {
        if ($this->_request->getActionName() == 'index') {

            if ($this->_pm->hasOption('psn_deferred_sending_log_sent')) {
                $this->view->isLog = true;
            } else {
                $this->view->isLog = false;
            }

            $this->_perPage = new IfwPsn_Wp_Plugin_Screen_Option_PerPage($this->_pm, __('Items per page', 'ifw'), $this->_perPageName);
        }
    }

    /**
     * Deletes a rule
     */
    public function deleteAction($id = null, $redirect = true)
    {
        if ($id === null) {
            $id = (int)$this->_request->get('id');
        } else {
            $id = (int)$id;
        }

        $item = IfwPsn_Wp_ORM_Model::factory($this->_modelName)->find_one($id);

        if ($item instanceof IfwPsn_Wp_ORM_Model) {
            $item->delete();
        }

        if ($redirect) {
            $this->_gotoIndex();
        }
    }

    /**
     * @param array $items
     */
    protected function _bulkDelete(array $items)
    {
        foreach($items as $id) {
            $this->deleteAction($id, false);
        }

        $this->_gotoIndex();
    }

    protected function _reset()
    {
        /**
         * @var wpdb
         */
        global $wpdb;

        $r = new ReflectionProperty($this->_modelName, '_table');
        $table = $r->getValue();

        $wpdb->query(sprintf('TRUNCATE TABLE %s', $wpdb->prefix . $table));

        $this->_gotoIndex();
    }

    public function enqueueScripts()
    {
        IfwPsn_Wp_Proxy_Script::loadAdmin('jquery-ui-dialog');
        IfwPsn_Wp_Proxy_Style::loadAdmin('wp-jquery-ui');
        IfwPsn_Wp_Proxy_Style::loadAdmin('wp-jquery-ui-dialog');
    }
}
