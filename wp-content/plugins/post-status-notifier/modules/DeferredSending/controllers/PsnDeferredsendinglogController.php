<?php
/**
 * Log controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: PsnDeferredsendinglogController.php 337 2014-11-09 14:27:46Z timoreithde $
 * @package  IfwPsn_Wp
 */
require_once 'PsnDeferredsendingAbstractController.php';

class DeferredSending_PsnDeferredsendingLogController extends DeferredSending_PsnDeferredsendingAbstractController
{
    /**
     * DB model class name
     */
    protected $_modelName = 'Psn_Module_DeferredSending_Model_MailQueueLog';

    protected $_perPageName = 'psn_mailqueuelog_per_page';

    /**
     * @var string
     */
    protected $_itemPostId = 'mailqueuelog';



    /**
     * (non-PHPdoc)
     * @see IfwPsn_Vendor_Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $page = $this->_navigation->findOneByModule('deferredsending'); /* @var $page Zend_Navigation_Page */
        if ( $page ) {
            $page->setActive();
        }
    }

    /**
     * 
     */
    public function indexAction()
    {
        // set up contextual help
        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Mail queue', 'psn_def'))
            ->setHelp($this->_getHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $listTable = new Psn_Module_DeferredSending_ListTable_MailQueueLog($this->_pm);
        $listTable->setItemsPerPage($this->_perPage->getOption());

        $this->view->listTable = $listTable;

        $this->view->dbModel = new Psn_Module_DeferredSending_Model_MailQueueLog();
    }

    /**
     *
     */
    public function exportAction()
    {
        $this->_export($this->_request->get('id'));
    }

    /**
     * @param array $items
     */
    protected function _bulkExport($items)
    {
        $this->_export($items);
    }

    /**
     * @param $rules
     */
    protected function _export($rules)
    {
        $options = $this->_exportOptions;
        $options['filename'] = sprintf($options['filename'], date('Y-m-d_H_i_s'));

        IfwPsn_Wp_ORM_Model::export($this->_modelName, $rules, $options);
    }

    /**
     *
     * @return string
     */
    protected function _getHelpText()
    {
        return sprintf(__('Please consider the documentation page <a href="%s" target="_blank">%s</a> for more information.', 'ifw'),
            'http://docs.ifeelweb.de/post-status-notifier/mailqueue.html',
            __('Mailqueue', 'psn_def'));
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

    protected function _gotoIndex()
    {
        $this->_gotoRoute('deferredsendinglog', 'index', 'post-status-notifier', array('mod' => 'deferredsending'));
    }
}
