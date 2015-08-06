<?php
/**
 * Index controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: PsnDeferredsendingController.php 337 2014-11-09 14:27:46Z timoreithde $
 * @package  IfwPsn_Wp
 */
require_once 'PsnDeferredsendingAbstractController.php';

class DeferredSending_PsnDeferredsendingController extends DeferredSending_PsnDeferredsendingAbstractController
{
    /**
     * DB model class name
     */
    protected $_modelName = 'Psn_Module_DeferredSending_Model_MailQueue';

    protected $_perPageName = 'psn_mailqueue_per_page';

    /**
     * @var string
     */
    protected $_itemPostId = 'mailqueue';



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

        $listTable = new Psn_Module_DeferredSending_ListTable_MailQueue($this->_pm);
        $listTable->setItemsPerPage($this->_perPage->getOption());

        $this->view->listTable = $listTable;

        if ($this->_pm->hasOption('psn_deferred_sending_log_sent')) {
            $this->view->isLog = true;
        } else {
            $this->view->isLog = false;
        }

        $this->view->dbModel = new Psn_Module_DeferredSending_Model_MailQueue();
    }

    public function runAction()
    {
        Psn_Module_DeferredSending_Mailqueue_Handler::getInstance()->run();

        $this->_gotoIndex();
    }

    /**
     * Imports templates
     */
    public function importAction()
    {
        $items = $this->_getImportedItems($_FILES['importfile']['tmp_name'], $this->_exportOptions['item_name_singular']);

        $result = IfwPsn_Wp_ORM_Model::import($this->_modelName, $items, array(
            'prefix' => esc_attr($this->_request->get('import_prefix'))
        ));

        $this->_gotoIndex();
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
        $this->_gotoRoute('deferredsending', 'index', 'post-status-notifier', array('mod' => 'deferredsending'));
    }
}
