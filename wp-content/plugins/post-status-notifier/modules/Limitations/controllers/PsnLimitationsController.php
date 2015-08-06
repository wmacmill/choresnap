<?php
/**
 * Index controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: PsnLimitationsController.php 353 2014-12-14 16:55:04Z timoreithde $
 * @package  IfwPsn_Wp
 */
class Limitations_PsnLimitationsController extends PsnApplicationController
{
    /**
     * DB model class name
     */
    const MODEL = 'Psn_Module_Limitations_Model_Limitations';

    /**
     * @var string
     */
    protected $_itemPostId = 'limitation';



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

            } else if ( $action == 'clear' ) {
                // bulk action clear
                IfwPsn_Wp_ORM_Model::factory(self::MODEL)->delete_many();
                $this->_gotoIndex();
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
        $help->setTitle(__('Limitations', 'psn_lmt'))
            ->setHelp($this->_getHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $listTable = new Psn_Module_Limitations_ListTable_Limitations($this->_pm);
        $listTable->setItemsPerPage($this->_perPage->getOption());

        $this->view->listTable = $listTable;
    }

    /**
     * Deletes a rule
     */
    public function deleteAction()
    {
        $tplId = (int)$this->_request->get('id');

        $item = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one((int)$this->_request->get('id'));
        $item->delete();

        $this->_gotoIndex();
    }

    /**
     * @param array $items
     */
    protected function _bulkDelete(array $items)
    {
        foreach($items as $id) {
            IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one((int)$id)->delete();
        }

        $this->_gotoIndex();
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

    protected function _gotoIndex()
    {
        $this->_gotoRoute('limitations', 'index', 'post-status-notifier', array('mod' => 'limitations'));
    }
}
